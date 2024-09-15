<?php
// models/PatientProfileModel.php

class PatientProfileModel {
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

    public function getPatientProfile($patientId) {
        $stmt = $this->db->prepare("
            SELECT u.first_name, u.last_name, u.email, u.phone_number, u.date_of_birth, u.address_line1, u.address_line2, u.city_id, u.profile_picture, c.city_name
            FROM users u
            LEFT JOIN cities c ON u.city_id = c.city_id
            WHERE u.user_id = :patient_id
        ");
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePatientProfile($patientId, $profileData) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                phone_number = :phone, 
                address_line1 = :address1, 
                address_line2 = :address2, 
                city_id = :city_id
            WHERE user_id = :patient_id
        ");
        
        $stmt->bindParam(':first_name', $profileData['first_name']);
        $stmt->bindParam(':last_name', $profileData['last_name']);
        $stmt->bindParam(':email', $profileData['email']);
        $stmt->bindParam(':phone', $profileData['phone_number']);
        $stmt->bindParam(':address1', $profileData['address_line1']);
        $stmt->bindParam(':address2', $profileData['address_line2']);
        $stmt->bindParam(':city_id', $profileData['city_id']);
        $stmt->bindParam(':patient_id', $patientId);
        
        return $stmt->execute();
    }
}
?>
