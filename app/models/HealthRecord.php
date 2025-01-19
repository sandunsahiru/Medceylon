<?php
namespace App\Models;

class HealthRecord {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function getPatientRecords($patientId) {
        $query = "SELECT hr.record_id, hr.patient_id, hr.doctor_id, hr.appointment_id, 
                  hr.diagnosis, hr.treatment_plan, hr.prescriptions, hr.test_results, 
                  hr.date_created, hr.date_modified,
                  CONCAT(u.first_name, ' ', u.last_name) as doctor_name,
                  h.name as hospital_name,
                  a.appointment_date,
                  a.appointment_time
                  FROM healthrecords hr
                  JOIN doctors d ON hr.doctor_id = d.doctor_id
                  JOIN users u ON d.user_id = u.user_id
                  JOIN appointments a ON hr.appointment_id = a.appointment_id
                  JOIN hospitals h ON d.hospital_id = h.hospital_id
                  WHERE hr.patient_id = ?
                  ORDER BY hr.date_created DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function addRecord($data) {
        $query = "INSERT INTO healthrecords (patient_id, doctor_id, appointment_id, 
                  diagnosis, treatment_plan, prescriptions, test_results, date_created) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                  
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiissss", 
            $data['patient_id'],
            $data['doctor_id'],
            $data['appointment_id'],
            $data['diagnosis'],
            $data['treatment_plan'],
            $data['prescriptions'],
            $data['test_results']
        );
        return $stmt->execute();
    }
}