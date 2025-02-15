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
    // Add these methods to your Patient class

public function getMedicalReports($patientId) {
    try {
        $query = "SELECT * FROM medical_reports WHERE patient_id = ? ORDER BY upload_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        return $stmt->get_result();
    } catch (\Exception $e) {
        error_log("Error in getMedicalReports: " . $e->getMessage());
        throw $e;
    }
}

public function getMedicalReport($reportId) {
    try {
        $query = "SELECT * FROM medical_reports WHERE report_id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    } catch (\Exception $e) {
        error_log("Error in getMedicalReport: " . $e->getMessage());
        throw $e;
    }
}

public function saveMedicalReport($data) {
    try {
        $this->db->begin_transaction();

        $query = "INSERT INTO medical_reports (patient_id, report_name, report_type, description, file_path) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "issss",
            $data['patient_id'],
            $data['report_name'],
            $data['report_type'],
            $data['description'],
            $data['file_path']
        );

        if (!$stmt->execute()) {
            throw new \Exception($stmt->error);
        }

        $this->db->commit();
        return true;
    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("Error in saveMedicalReport: " . $e->getMessage());
        throw $e;
    }
}

public function deleteMedicalReport($reportId, $patientId) {
    try {
        $this->db->begin_transaction();

        $query = "DELETE FROM medical_reports WHERE report_id = ? AND patient_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $reportId, $patientId);

        if (!$stmt->execute()) {
            throw new \Exception($stmt->error);
        }

        $this->db->commit();
        return true;
    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("Error in deleteMedicalReport: " . $e->getMessage());
        throw $e;
    }
}
}