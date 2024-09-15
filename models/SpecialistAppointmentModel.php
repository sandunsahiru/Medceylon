<?php
// models/SpecialistAppointmentModel.php

class SpecialistAppointmentModel {
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

    public function getDoctorIdByUserId($userId) {
        $stmt = $this->db->prepare("SELECT doctor_id FROM doctors WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        return $doctor ? $doctor['doctor_id'] : null;
    }

    public function getSummaryData($userId) {
        $doctorId = $this->getDoctorIdByUserId($userId);

        if (!$doctorId) {
            return [
                'new_appointments' => 0,
                'completed_appointments' => 0,
                'patients' => 0
            ];
        }

        // Fetch new appointments count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS new_appointments
            FROM appointments
            WHERE doctor_id = :doctor_id AND appointment_status = 'Scheduled'
        ");
        $stmt->execute(['doctor_id' => $doctorId]);
        $newAppointments = $stmt->fetchColumn();

        // Fetch completed appointments count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS completed_appointments
            FROM appointments
            WHERE doctor_id = :doctor_id AND appointment_status = 'Completed'
        ");
        $stmt->execute(['doctor_id' => $doctorId]);
        $completedAppointments = $stmt->fetchColumn();

        // Fetch distinct patient count
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT patient_id) AS patients
            FROM appointments
            WHERE doctor_id = :doctor_id
        ");
        $stmt->execute(['doctor_id' => $doctorId]);
        $patients = $stmt->fetchColumn();

        return [
            'new_appointments' => $newAppointments,
            'completed_appointments' => $completedAppointments,
            'patients' => $patients
        ];
    }

    public function getNewAppointments($userId) {
        $doctorId = $this->getDoctorIdByUserId($userId);

        if (!$doctorId) {
            return [];
        }

        // Fetch new appointments with patient details
        $stmt = $this->db->prepare("
            SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.reason_for_visit,
                   p.first_name, p.last_name, p.email, p.profile_picture, c.country_name
            FROM appointments a
            JOIN users p ON a.patient_id = p.user_id
            LEFT JOIN countries c ON p.nationality = c.country_code
            WHERE a.doctor_id = :doctor_id AND a.appointment_status = 'Scheduled'
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute(['doctor_id' => $doctorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
