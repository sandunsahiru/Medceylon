<?php
// models/PatientModel.php

class PatientModel {
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

    // Get all patients
    public function getAllPatients() {
        $stmt = $this->db->prepare("
            SELECT 
                u.user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS name,
                u.email,
                u.profile_picture AS avatar,
                c.country_name AS country,
                u.nationality,
                CONCAT(d.first_name, ' ', d.last_name) AS doctor,
                hr.diagnosis AS `condition`,
                hr.treatment_plan AS status
            FROM users u
            LEFT JOIN countries c ON u.nationality = c.country_code
            LEFT JOIN appointments a ON u.user_id = a.patient_id
            LEFT JOIN doctors doc ON a.doctor_id = doc.doctor_id
            LEFT JOIN users d ON doc.user_id = d.user_id
            LEFT JOIN healthrecords hr ON a.appointment_id = hr.appointment_id
            WHERE u.role_id = (SELECT role_id FROM roles WHERE role_name = 'Patient')
            GROUP BY u.user_id
        ");
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process the data to match your existing structure
        foreach ($patients as &$patient) {
            $patient['avatar'] = $patient['avatar'] ?: 'default_avatar.png';
            $patient['country'] = $patient['country'] ?: 'Unknown';
            $patient['status'] = $patient['status'] ?: 'Unknown';
            $patient['doctor'] = $patient['doctor'] ?: 'No Doctor Assigned';
            $patient['condition'] = $patient['condition'] ?: 'No Diagnosis';
        }

        return $patients;
    }

    // **Add the getUpcomingAppointments method**
    public function getUpcomingAppointments($patientId) {
        $stmt = $this->db->prepare("
            SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.appointment_status,
                   d.doctor_id, u.first_name AS doctor_first_name, u.last_name AS doctor_last_name,
                   s.name AS specialization
            FROM appointments a
            JOIN doctors d ON a.doctor_id = d.doctor_id
            JOIN users u ON d.user_id = u.user_id
            LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
            LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
            WHERE a.patient_id = :patient_id AND a.appointment_date >= CURDATE()
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute(['patient_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // **Add the getNotifications method**
    public function getNotifications($patientId) {
        $stmt = $this->db->prepare("
            SELECT notification_text, date_created, is_read
            FROM notifications
            WHERE user_id = :user_id
            ORDER BY date_created DESC
            LIMIT 5
        ");
        $stmt->execute(['user_id' => $patientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Other methods as needed...
}
?>
