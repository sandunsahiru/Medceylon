<?php
// models/MyAppointmentsModel.php

class MyAppointmentsModel {
    private $db;

    public function __construct() {
        // Initialize database connection
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=medceylon', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set charset to UTF-8
            $this->db->exec("SET NAMES 'utf8mb4';");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Get appointments for the patient
    public function getAppointmentsByPatientId($patientId) {
        $stmt = $this->db->prepare("
            SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.appointment_status,
                   a.reason_for_visit,
                   d.doctor_id, u.first_name AS doctor_first_name, u.last_name AS doctor_last_name,
                   s.name AS specialization
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.doctor_id
            JOIN users u ON d.user_id = u.user_id
            LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
            LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
            WHERE a.patient_id = :patient_id
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute(['patient_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
