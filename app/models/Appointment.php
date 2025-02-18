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
                     p.first_name, p.last_name,
                     p.email, p.phone_number
                     FROM appointments a
                     JOIN users p ON a.patient_id = p.user_id
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
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.reason_for_visit,
                d.doctor_id,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                h.name as hospital_name,
                s.name as specialization
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC 
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
    
    // Get appointments by doctor ID
    public function getDoctorAppointments($doctorId) {
        try {
            $query = "SELECT 
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.reason_for_visit,
                p.first_name as patient_first_name, 
                p.last_name as patient_last_name,
                p.email as patient_email,
                p.phone_number as patient_phone,
                d.doctor_id,
                u.first_name as doctor_first_name,
                u.last_name as doctor_last_name
                FROM appointments a
                JOIN users p ON a.patient_id = p.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
           
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting doctor appointments: " . $e->getMessage());
            throw $e;
        }
    }
   
    // Get appointment details
    public function getAppointmentDetails($appointmentId) {
        try {
            $query = "SELECT 
                a.*,
                d.doctor_id,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                p.first_name as patient_first_name,
                p.last_name as patient_last_name,
                s.name as specialization, 
                h.name as hospital_name,
                orig.appointment_date as previous_date,
                orig.appointment_time as previous_time,
                orig.appointment_status as previous_status
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                JOIN users p ON a.patient_id = p.user_id
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
                    'id' => $result['doctor_id'],
                    'first_name' => $result['doctor_first_name'],
                    'last_name' => $result['doctor_last_name']
                ],
                'patient' => [
                    'first_name' => $result['patient_first_name'],
                    'last_name' => $result['patient_last_name']
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
   
    public function bookAppointment($data) {
        $this->db->begin_transaction();
        try {
            error_log("Starting bookAppointment with data: " . print_r($data, true));
    
            // Check if slot is still available
            if (isset($data['appointment_time'])) {
                $checkQuery = "SELECT appointment_id FROM appointments 
                              WHERE doctor_id = ? 
                              AND appointment_date = ? 
                              AND appointment_time = ? 
                              AND appointment_status NOT IN ('Canceled', 'Rejected')";
                
                $checkStmt = $this->db->prepare($checkQuery);
                if (!$checkStmt) {
                    throw new \Exception("Failed to prepare time slot check query: " . $this->db->error);
                }
    
                $checkDoctorId = $data['doctor_id'];
                $checkDate = $data['preferred_date'];
                $checkTime = $data['appointment_time'];
                
                $checkStmt->bind_param("iss", $checkDoctorId, $checkDate, $checkTime);
                $checkStmt->execute();
                
                if ($checkStmt->get_result()->num_rows > 0) {
                    throw new \Exception("This time slot is no longer available");
                }
            }
    
            // Insert appointment
            $query = "INSERT INTO appointments (
                patient_id, 
                doctor_id,
                appointment_date,
                appointment_time,
                consultation_type,
                reason_for_visit,
                medical_history,
                appointment_status,
                notes,
                booking_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                throw new \Exception("Database prepare error");
            }
    
            // Prepare all variables before binding
            $patientId = $data['patient_id'];
            $doctorId = $data['doctor_id'];
            $appointmentDate = $data['preferred_date'];
            $appointmentTime = $data['appointment_time'];
            $consultationType = $data['consultation_type'];
            $reasonForVisit = $data['reason_for_visit'] ?? '';
            $medicalHistory = '';
            $status = 'Asked';
            $notes = isset($data['referring_doctor_id']) ? 
                    "Referred by Doctor ID: " . $data['referring_doctor_id'] : null;
    
            error_log("Binding parameters: " . print_r([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'date' => $appointmentDate,
                'time' => $appointmentTime,
                'type' => $consultationType,
                'reason' => $reasonForVisit,
                'history' => $medicalHistory,
                'status' => $status,
                'notes' => $notes
            ], true));
    
            $stmt->bind_param("iisssssss", 
                $patientId,
                $doctorId,
                $appointmentDate,
                $appointmentTime,
                $consultationType,
                $reasonForVisit,
                $medicalHistory,
                $status,
                $notes
            );
    
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                throw new \Exception("Failed to insert appointment: " . $stmt->error);
            }
    
            $appointmentId = $this->db->insert_id;
            error_log("Successfully created appointment with ID: " . $appointmentId);
    
            // Handle documents if present
            if (!empty($data['documents'])) {
                $this->saveAppointmentDocuments($appointmentId, $data['documents']);
            }
    
            $this->db->commit();
            return $appointmentId;
    
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in bookAppointment: " . $e->getMessage());
            throw $e;
        }
    }
    
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
                    $docType = $file_ext;
                    $stmt->bind_param("iss", $appointmentId, $docType, $db_path);
                    $stmt->execute();
                }
            }
        }
    }
}