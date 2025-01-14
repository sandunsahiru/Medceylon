<?php
namespace App\Models;

class Appointment {
   protected $db;
   
   public function __construct() {
       global $db;
       $this->db = $db;
   }
   
   public function getPatientAppointments($patientId, $limit = 20) {
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
   }
   
   public function getAppointmentDetails($appointmentId) {
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
   }
   
   public function bookAppointment($data) {
       $this->db->begin_transaction();
       try {
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

           $query = "INSERT INTO appointments (
               patient_id, doctor_id, appointment_date, appointment_time, 
               consultation_type, reason_for_visit, medical_history, 
               appointment_status, booking_date
           ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Scheduled', NOW())";
                    
           $stmt = $this->db->prepare($query);
           $stmt->bind_param("iisssss", 
               $data['patient_id'], 
               $data['doctor_id'], 
               $data['date'],
               $data['time'],
               $data['consultation_type'],
               $data['reason'],
               $data['medical_history']
           );
           $stmt->execute();
           $appointmentId = $this->db->insert_id;
           
           if (!empty($data['documents'])) {
               $this->saveAppointmentDocuments($appointmentId, $data['documents']);
           }
           
           $this->db->commit();
           return $appointmentId;
       } catch (\Exception $e) {
           $this->db->rollback();
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
                   $stmt->bind_param("iss", $appointmentId, $file_ext, $db_path);
                   $stmt->execute();
               }
           }
       }
   }
}