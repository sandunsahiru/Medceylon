<?php

namespace App\Models;

class TravelPlan {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAllDestinations()
    {
        try {
            $sql = "SELECT destination_id,destination_name, province, description, image_path,opening_time, closing_time, entry_fee FROM traveldestinations";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (\Exception $e) {
            error_log("Error in getAlldestinations: " . $e->getMessage());
            throw new \Exception("Failed to retrieve destinations");
        }

        
    }

    public function getAllTravelPlans($userId) {
        
        try {
            $sql = "SELECT d.destination_name, d.province, d.image_path, 
                           t.stay_duration, t.check_in, t.check_out, 
                           t.travel_plan_id, t.destination_id 
                    FROM traveldestinations d 
                    JOIN travel_plans t ON d.destination_id = t.destination_id
                    WHERE t.user_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
            error_log("Rows fetched: " . $result->num_rows);
            
        } catch (\Exception $e) {
            error_log("Error in getAllTravelPlans: " . $e->getMessage());
            throw new \Exception("Failed to retrieve travel plans");
        }
    }


    public function addTravelPlan($user_id, $destination_id, $startDate, $endDate)
    {
        try {
            $sql = "INSERT INTO travel_plans (user_id, destination_id, check_in, check_out, stay_duration)
            VALUES (?, ?, ?, ?, DATEDIFF(?, ?))";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }

            $stmt->bind_param("iissss", $user_id, $destination_id, $startDate, $endDate, $endDate, $startDate);

            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }

            $stmt->close();

        } catch (\Exception $e) {
            error_log("Error in addTravelPlan: " . $e->getMessage());
            throw new \Exception("Failed to add travel plan");
        }
    }

    public function hasOverlappingPlan($user_id, $start_date, $end_date)
    {
        $sql = "SELECT COUNT(*) as overlap_count FROM travel_plans 
                WHERE user_id = ?
                AND (
                    (check_in <= ? AND check_out >= ?) OR
                    (check_in <= ? AND check_out >= ?) OR
                    (check_in >= ? AND check_out <= ?)
                )";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("issssss",
            $user_id,
            $start_date, $start_date,
            $end_date, $end_date,
            $start_date, $end_date
        );

        if (!$stmt->execute()) {
            throw new \Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();

        return $data['overlap_count'] > 0;
    }



    public function deleteTravelPlan($travel_plan_id) {
        try {
            $sql = "DELETE FROM travel_plans WHERE travel_plan_id = ?";
            $stmt = $this->db->prepare($sql);
    
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
    
            $stmt->bind_param("i", $travel_plan_id);
    
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
    
            $stmt->close();
            return true;
    
        } catch (\Exception $e) {
            error_log("Error in deleteTravelPlan: " . $e->getMessage());
            return false;
        }
    }
    
    
    

    public function editTravelPlan($travel_plan_id, $startDate, $endDate) {
        try {
            $sql = "UPDATE travel_plans 
                    SET check_in = ?, 
                        check_out = ?, 
                        stay_duration = DATEDIFF(?, ?) 
                    WHERE travel_plan_id = ?";
    
            $stmt = $this->db->prepare($sql);
    
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
    
            $stmt->bind_param("ssssi", $startDate, $endDate, $endDate, $startDate, $travel_plan_id);
    
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
    
            $stmt->close();
            return true;
    
        } catch (\Exception $e) {
            error_log("Error in editTravelPlan: " . $e->getMessage());
            return false;
        }
    }
    
    

}