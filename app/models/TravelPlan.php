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
            $sql = "SELECT destination_id,destination_name, province, description, image_path FROM traveldestinations";
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
        } catch (\Exception $e) {
            error_log("Error in getAllTravelPlans: " . $e->getMessage());
            throw new \Exception("Failed to retrieve travel plans");
        }
    }

    public function addTravelPlan($planData) {
        try {
            $this->db->begin_transaction();

            $sql = "INSERT INTO travel_plans (user_id, destination_id, check_in, check_out, stay_duration)
                    VALUES (?, ?, ?, ?, DATEDIFF(?, ?))";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iissss", 
                $planData['user_id'],
                $planData['destination_id'],
                $planData['start_date'],
                $planData['end_date'],
                $planData['end_date'],
                $planData['start_date']
            );

            if (!$stmt->execute()) {
                throw new \Exception("Error adding travel plan");
            }

            $planId = $stmt->insert_id;
            $this->db->commit();
            
            return ['success' => true, 'plan_id' => $planId];
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in addTravelPlan: " . $e->getMessage());
            throw new \Exception("Failed to add travel plan");
        }
    }

    public function editTravelPlan($travel_plan_id, $startDate, $endDate) {
        try {
            $this->db->begin_transaction();

            $sql = "UPDATE travel_plans 
                    SET check_in = ?, 
                        check_out = ?, 
                        stay_duration = DATEDIFF(?, ?) 
                    WHERE travel_plan_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssssi", 
                $startDate,
                $endDate,
                $endDate,
                $startDate,
                $travel_plan_id
            );

            if (!$stmt->execute()) {
                throw new \Exception("Error updating travel plan");
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in editTravelPlan: " . $e->getMessage());
            throw new \Exception("Failed to update travel plan");
        }
    }

    public function deleteTravelPlan($travel_plan_id) {
        try {
            $this->db->begin_transaction();

            $sql = "DELETE FROM travel_plans WHERE travel_plan_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $travel_plan_id);

            if (!$stmt->execute()) {
                throw new \Exception("Error deleting travel plan");
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in deleteTravelPlan: " . $e->getMessage());
            throw new \Exception("Failed to delete travel plan");
        }
    }

    public function getTravelPlanDetails($planId) {
        try {
            $sql = "SELECT t.*, d.destination_name, d.province, d.description, d.image_path
                    FROM travel_plans t
                    JOIN traveldestinations d ON t.destination_id = d.destination_id
                    WHERE t.travel_plan_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $planId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getTravelPlanDetails: " . $e->getMessage());
            throw new \Exception("Failed to retrieve travel plan details");
        }
    }

    public function validateDates($startDate, $endDate) {
        // Validate date format
        $startDateTime = \DateTime::createFromFormat('Y-m-d', $startDate);
        $endDateTime = \DateTime::createFromFormat('Y-m-d', $endDate);
        
        if (!$startDateTime || !$endDateTime) {
            throw new \Exception("Invalid date format");
        }

        // Ensure end date is after start date
        if ($endDateTime <= $startDateTime) {
            throw new \Exception("End date must be after start date");
        }

        // Ensure dates are not in the past
        $today = new \DateTime();
        if ($startDateTime < $today) {
            throw new \Exception("Start date cannot be in the past");
        }

        return true;
    }

    public function checkDestinationAvailability($destinationId, $startDate, $endDate) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM travel_plans 
                    WHERE destination_id = ? 
                    AND ((check_in BETWEEN ? AND ?) 
                    OR (check_out BETWEEN ? AND ?))";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("issss", 
                $destinationId, 
                $startDate, 
                $endDate,
                $startDate, 
                $endDate
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];

            return $count === 0;
        } catch (\Exception $e) {
            error_log("Error in checkDestinationAvailability: " . $e->getMessage());
            throw new \Exception("Failed to check destination availability");
        }
    }
}
?>