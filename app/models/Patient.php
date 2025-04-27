<?php
namespace App\Models;

class Patient
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function getProfile($userId)
    {
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

    public function updateProfile($userId, $data)
    {
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

    public function deleteAccount($userId)
    {
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

    public function getCities()
    {
        try {
            return $this->db->query("SELECT * FROM cities ORDER BY city_name");
        } catch (\Exception $e) {
            error_log("Error in getCities: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCountries()
    {
        try {
            return $this->db->query("SELECT * FROM countries ORDER BY country_name");
        } catch (\Exception $e) {
            error_log("Error in getCountries: " . $e->getMessage());
            throw $e;
        }
    }
    // Add these methods to your Patient class

    public function getMedicalReports($patientId)
    {
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

    public function getMedicalReport($reportId)
    {
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

    

    
    public function assignPaymentPlan($userId, $planId)
    {
        $query = "UPDATE users SET payment_plan_id = ? WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_Param('ii', $planId, $userId);
        $stmt->execute();
    }

    public function showPaymentPlans()
    {
        $query = "SELECT * FROM payment_plans";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;

    }

    public function getPatientPaymentPlan($userId){
        $query = "SELECT p.plan_name, u.payment_plan_id FROM users u
                  JOIN payment_plans p ON u.payment_plan_id = p.id
                  WHERE u.user_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
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

/**
 * Get the user's latest trip
 * 
 * @param int $userId The user ID
 * @return array|false The latest trip or false if none found
 */
public function getLatestTrip($userId) {
    try {
        $query = "SELECT * FROM trips 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        return $result->fetch_assoc();
    } catch (\Exception $e) {
        error_log("Error in getLatestTrip: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all destinations for a specific trip
 * 
 * @param int $tripId The trip ID
 * @return array The destinations for this trip
 */
public function getTripDestinations($tripId) {
    try {
        $query = "SELECT tp.*, td.* 
                  FROM travel_plans tp
                  JOIN traveldestinations td ON tp.destination_id = td.destination_id
                  WHERE tp.trip_id = ?
                  ORDER BY tp.sequence ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $destinations = [];
        while ($row = $result->fetch_assoc()) {
            $destinations[] = $row;
        }
        
        return $destinations;
    } catch (\Exception $e) {
        error_log("Error in getTripDestinations: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a specific destination by ID
 * 
 * @param int $destinationId The destination ID
 * @return array|false The destination or false if not found
 */
public function getDestination($destinationId) {
    try {
        $query = "SELECT * FROM traveldestinations WHERE destination_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $destinationId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        return $result->fetch_assoc();
    } catch (\Exception $e) {
        error_log("Error in getDestination: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all trips for a user
 * 
 * @param int $userId The user ID
 * @return array The user's trips
 */
public function getUserTrips($userId) {
    try {
        $query = "SELECT * FROM trips WHERE user_id = ? ORDER BY start_date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $trips = [];
        while ($row = $result->fetch_assoc()) {
            $trips[] = $row;
        }
        
        return $trips;
    } catch (\Exception $e) {
        error_log("Error in getUserTrips: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a specific trip by ID
 * 
 * @param int $tripId The trip ID
 * @return array|false The trip or false if not found
 */
public function getTrip($tripId) {
    try {
        $query = "SELECT * FROM trips WHERE trip_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        return $result->fetch_assoc();
    } catch (\Exception $e) {
        error_log("Error in getTrip: " . $e->getMessage());
        return false;
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