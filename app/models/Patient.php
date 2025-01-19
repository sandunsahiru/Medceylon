<?php
namespace App\Models;

class Patient {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    public function getProfile($userId) {
        try {
            $query = "SELECT u.*, c.city_name, co.country_name
                      FROM users u
                      LEFT JOIN cities c ON u.city_id = c.city_id
                      LEFT JOIN countries co ON u.nationality = co.country_code
                      WHERE u.user_id = ?";
                      
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getProfile: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateProfile($userId, $data) {
        try {
            $this->db->begin_transaction();
            
            $query = "UPDATE users SET 
                      first_name = ?,
                      last_name = ?,
                      email = ?,
                      phone_number = ?,
                      date_of_birth = ?,
                      gender = ?,
                      address_line1 = ?,
                      address_line2 = ?,
                      city_id = ?,
                      nationality = ?,
                      passport_number = ?
                      WHERE user_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "sssssssssssi",
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone_number'],
                $data['date_of_birth'],
                $data['gender'],
                $data['address_line1'],
                $data['address_line2'],
                $data['city_id'],
                $data['nationality'],
                $data['passport_number'],
                $userId
            );
            
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateProfile: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteAccount($userId) {
        try {
            $this->db->begin_transaction();
            
            $query = "UPDATE users SET is_active = 0 WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in deleteAccount: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCities() {
        try {
            return $this->db->query("SELECT * FROM cities ORDER BY city_name");
        } catch (\Exception $e) {
            error_log("Error in getCities: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCountries() {
        try {
            return $this->db->query("SELECT * FROM countries ORDER BY country_name");
        } catch (\Exception $e) {
            error_log("Error in getCountries: " . $e->getMessage());
            throw $e;
        }
    }
}