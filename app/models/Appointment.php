<?php

namespace App\Models;

class Appointment {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }

    // Get upcoming appointments
    public function getUpcomingAppointments($doctorId, $limit = 10) {
        try {
            $query = "SELECT a.*, 
                     u.first_name, u.last_name,
                     u.email, u.phone_number
                     FROM appointments a
                     JOIN users u ON a.patient_id = u.user_id
                     WHERE a.doctor_id = ?
                     AND a.appointment_date >= CURDATE()
                     AND a.appointment_status = 'Scheduled'
                     ORDER BY a.appointment_date ASC, a.appointment_time ASC
                     LIMIT ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $doctorId, $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting upcoming appointments: " . $e->getMessage());
            throw $e;
        }
    }

    // Update appointment status
    public function updateStatus($appointmentId, $newStatus) {
        try {
            $query = "UPDATE appointments 
                     SET appointment_status = ?,
                         updated_at = NOW()
                     WHERE appointment_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $newStatus, $appointmentId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to update appointment status");
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error updating appointment status: " . $e->getMessage());
            throw $e;
        }
    }
   
    // Get patient appointments
    public function getPatientAppointments($patientId, $limit = 20) {
        try {
            $query = "SELECT 
                a.appointment_id, a.appointment_date, a.appointment_time,
                a.appointment_status, a.consultation_type, a.reason_for_visit,
                u.first_name, u.last_name, s.name as specialization,
                h.name as hospital_name
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                JOIN specializations s ON ds.specialization_id = s.specialization_id
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date, a.appointment_time 
                LIMIT ?";
           
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting patient appointments: " . $e->getMessage());
            throw $e;
        }
    }
   
    // Get appointment details
    public function getAppointmentDetails($appointmentId) {
        try {
            $query = "SELECT 
                a.*, u.first_name, u.last_name,
                s.name as specialization, h.name as hospital_name,
                orig.appointment_date as previous_date,
                orig.appointment_time as previous_time,
                orig.appointment_status as previous_status
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                JOIN specializations s ON ds.specialization_id = s.specialization_id
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN appointments orig ON a.rescheduled_from = orig.appointment_id
                WHERE a.appointment_id = ?";
           
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $appointmentId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            return [
                'doctor' => [
                    'first_name' => $result['first_name'],
                    'last_name' => $result['last_name']
                ],
                'specialization' => $result['specialization'],
                'hospital' => $result['hospital_name'],
                'appointment' => [
                    'date' => date('F j, Y', strtotime($result['appointment_date'])),
                    'time' => date('g:i A', strtotime($result['appointment_time'])),
                    'status' => $result['appointment_status'],
                    'consultation_type' => $result['consultation_type'],
                    'reason_for_visit' => $result['reason_for_visit']
                ]
            ];
        } catch (\Exception $e) {
            error_log("Error getting appointment details: " . $e->getMessage());
            throw $e;
        }
    }
   
    // Book appointment (updated to handle both regular and specialist bookings)
    public function bookAppointment($data) {
        $this->db->begin_transaction();
        try {
            // Check if booking is for a specific time slot
            if (isset($data['time'])) {
                // Check if slot is still available
                $checkQuery = "SELECT appointment_id FROM appointments 
                            WHERE doctor_id = ? 
                            AND appointment_date = ? 
                            AND appointment_time = ? 
                            AND appointment_status NOT IN ('Canceled', 'Rejected')";
               
                $checkStmt = $this->db->prepare($checkQuery);
                $checkStmt->bind_param("iss", $data['doctor_id'], $data['date'], $data['time']);
                $checkStmt->execute();
               
                if ($checkStmt->get_result()->num_rows > 0) {
                    throw new \Exception("This time slot is no longer available");
                }
            }

            // Insert appointment with appropriate status
            $query = "INSERT INTO appointments (
                patient_id, doctor_id, appointment_date, appointment_time, 
                consultation_type, reason_for_visit, medical_history, 
                appointment_status, notes, booking_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    
            $time = $data['time'] ?? NULL;
            $reason = $data['reason'] ?? NULL;
            $status = isset($data['specialist_id']) ? 'Asked' : 'Scheduled';
            $notes = isset($data['referring_doctor_id']) ? 
                    "Referred by Doctor ID: " . $data['referring_doctor_id'] : NULL;

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iisssssss", 
                $data['patient_id'], 
                $data['doctor_id'] ?? $data['specialist_id'], 
                $data['date'] ?? $data['preferred_date'],
                $time,
                $data['consultation_type'],
                $reason,
                $data['medical_history'],
                $status,
                $notes
            );
            $stmt->execute();
            $appointmentId = $this->db->insert_id;
           
            // Handle documents if present
            if (!empty($data['documents'])) {
                $this->saveAppointmentDocuments($appointmentId, $data['documents']);
            }
           
            $this->db->commit();
            return $appointmentId;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error booking appointment: " . $e->getMessage());
            throw $e;
        }
    }

    // Save appointment documents
    private function saveAppointmentDocuments($appointmentId, $documents) {
        $upload_dir = ROOT_PATH . '/public/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($documents['tmp_name'] as $key => $tmp_name) {
            if ($documents['error'][$key] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($documents['name'][$key], PATHINFO_EXTENSION));
                $new_name = uniqid('doc_') . '.' . $file_ext;
                $file_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $file_path)) {
                    $query = "INSERT INTO appointmentdocuments 
                            (appointment_id, document_type, file_path) 
                            VALUES (?, ?, ?)";
                           
                    $stmt = $this->db->prepare($query);
                    $db_path = 'uploads/' . $new_name;
                    $stmt->bind_param("iss", $appointmentId, $file_ext, $db_path);
                    $stmt->execute();
                }
            }
        }
    }
}