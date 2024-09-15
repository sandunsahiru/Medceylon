<?php
// models/BookAppointmentModel.php

class BookAppointmentModel {
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

    // Get available doctors
    public function getAvailableDoctors() {
        $stmt = $this->db->prepare("
            SELECT d.doctor_id, u.first_name, u.last_name, s.name AS specialization
            FROM doctors d
            JOIN users u ON d.user_id = u.user_id
            LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
            LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
            ORDER BY u.first_name, u.last_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Book an appointment
    public function bookAppointment($patientId, $doctorId, $appointmentDate, $appointmentTime, $reasonForVisit) {
        try {
            // Check if the appointment slot is available
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM appointments
                WHERE doctor_id = :doctor_id AND appointment_date = :appointment_date AND appointment_time = :appointment_time
            ");
            $stmt->execute([
                'doctor_id' => $doctorId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime
            ]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Slot is already booked
                return false;
            }

            // Insert the appointment
            $stmt = $this->db->prepare("
                INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason_for_visit, appointment_status)
                VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :reason_for_visit, 'Scheduled')
            ");
            $stmt->execute([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'reason_for_visit' => $reasonForVisit
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Handle exception
            return false;
        }
    }
}
?>
