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
            $sql = "SELECT * FROM traveldestinations";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (\Exception $e) {
            error_log("Error in getAlldestinations: " . $e->getMessage());
            throw new \Exception("Failed to retrieve destinations");
        }  
    }

    public function getFilteredDestinations($filters)
    {
        try {
            $query = "SELECT 
                        d.*, 
                        p.province_name, 
                        di.district_name, 
                        t.town_name
                    FROM traveldestinations d
                    JOIN towns t ON d.town_id = t.town_id
                    JOIN districts di ON t.district_id = di.district_id
                    JOIN provinces p ON di.province_id = p.province_id
                    WHERE 1=1";

            $params = [];
            $types = '';

            if (!empty($filters['province_id'])) {
                $query .= " AND p.province_id = ?";
                $params[] = $filters['province_id'];
                $types .= 'i';
            }

            if (!empty($filters['district_id'])) {
                $query .= " AND di.district_id = ?";
                $params[] = $filters['district_id'];
                $types .= 'i';
            }

            if (!empty($filters['town_id'])) {
                $query .= " AND t.town_id = ?";
                $params[] = $filters['town_id'];
                $types .= 'i';
            }

            if (!empty($filters['distance'])) {
                $query .= " AND d.distance <= ?";
                $params[] = $filters['distance'];
                $types .= 'd'; // double (float)
            }

            if (isset($filters['wheelchair']) && ($filters['wheelchair'] === '0' || $filters['wheelchair'] === '1')) {
                $query .= " AND d.wheelchair_accessibility = ?";
                $params[] = $filters['wheelchair'];
                $types .= 'i';
            }

            if (!empty($filters['type_id'])) {
                $query .= " AND d.type_id = ?";
                $params[] = $filters['type_id'];
                $types .= 'i';
            }

            if (!empty($filters['budget'])) {
                $query .= " AND d.entry_fee <= ?";
                $params[] = $filters['budget'];
                $types .= 'd';
            }

            $stmt = $this->db->prepare($query);

            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }

            $destinations = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return $destinations;

        } catch (\Exception $e) {
            error_log("Error in getFilteredDestinations: " . $e->getMessage());
            throw new \Exception("Failed to filter destinations");
        }
    }




    public function sortByType($typeId){
        try{
            $sql = "SELECT t.* FROM traveldestinations t
            JOIN destination_type_mapping d
            ON t.type_id = d.type_id
            WHERE type_id = ? ";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $typeId);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }catch (\Exception $e) {
            error_log("Error in sortByType: " . $e->getMessage());
            throw new \Exception("Failed to Sort destinations by Type");
        }
    }

    public function sortByWheelchairAccess($access)
    {
        try{
            $sql = "SELECT * FROM traveldestinations WHERE wheelchair_accessibility = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $access);
            $stmt->execute();
            $result = $stmt->get_result();

            return $result->fetch_all(MYSQLI_ASSOC);
        }catch (\Exception $e) {
            error_log("Error in sortByWheelchairAccess: " . $e->getMessage());
            throw new \Exception("Failed to Sort destinations by Wheelchair Accessibility");
        } 
    }

    public function getDestinationTypes()
    {
        try{
            $sql = "SELECT * FROM destination_types";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getDestinationTypes: " . $e->getMessage());
            throw new \Exception("Failed to retrieve Destination Types");
        }

    }

    public function getAllProvinces()
    {
        try{
            $sql = "SELECT * FROM provinces";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getAllProvinces: " . $e->getMessage());
            throw new \Exception("Failed to retrieve Provinces");
        }
    }

    public function getDistricts($provinceId)
    {
        try{
            $sql = "SELECT * FROM districts WHERE province_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $provinceId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getDistricts: " . $e->getMessage());
            throw new \Exception("Failed to retrieve districts");
        }
    }

    public function getTowns($districtId)
    {
        try{
            $sql = "SELECT * FROM towns WHERE district_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $districtId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getTowns: " . $e->getMessage());
            throw new \Exception("Failed to retrieve Towns");
        }
    }

    public function markTravelPlanCompleted($travel_plan_id) {
        try {
            $sql = "UPDATE travel_plans 
                    SET status = 'Completed'
                    WHERE travel_plan_id = ?";
    
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
            error_log("Error in markTravelPlanCompleted: " . $e->getMessage());
            return false;
        }
    }
    
    public function addTravelMemories($travel_plan_id, $note, $rating, $photos) {
        try {
            // Begin transaction
            $this->db->begin_transaction();
            
            // Insert memory data
            $sql = "INSERT INTO travel_memories (travel_plan_id, note, rating, created_at) 
                    VALUES (?, ?, ?, NOW())";
                    
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
            
            $stmt->bind_param("isi", $travel_plan_id, $note, $rating);
            
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
            
            $memory_id = $stmt->insert_id;
            $stmt->close();
            
            // Insert photos if any
            if (!empty($photos)) {
                $sql = "INSERT INTO memory_photos (memory_id, photo_path) VALUES (?, ?)";
                $stmt = $this->db->prepare($sql);
                
                if (!$stmt) {
                    throw new \Exception("Prepare failed for photos: " . $this->db->error);
                }
                
                foreach ($photos as $photo) {
                    $stmt->bind_param("is", $memory_id, $photo);
                    
                    if (!$stmt->execute()) {
                        throw new \Exception("Execute failed for photo insert: " . $stmt->error);
                    }
                }
                
                $stmt->close();
            }
            
            // Update travel plan to have memories
            $sql = "UPDATE travel_plans SET has_memories = 1 WHERE travel_plan_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Prepare failed for plan update: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $travel_plan_id);
            
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed for plan update: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Commit transaction
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log("Error in addTravelMemories: " . $e->getMessage());
            return false;
        }
    }
    
    public function getTravelMemories($travel_plan_id) {
        try {
            $sql = "SELECT m.memory_id, m.note, m.rating, m.created_at, 
                           d.destination_name
                    FROM travel_memories m
                    JOIN travel_plans t ON m.travel_plan_id = t.travel_plan_id
                    JOIN traveldestinations d ON t.destination_id = d.destination_id
                    WHERE m.travel_plan_id = ?";
                    
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $travel_plan_id);
            
            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $memory = $result->fetch_assoc();
            $stmt->close();
            
            if ($memory) {
                // Get photos for this memory
                $sql = "SELECT photo_id, photo_path FROM memory_photos WHERE memory_id = ?";
                $stmt = $this->db->prepare($sql);
                
                if (!$stmt) {
                    throw new \Exception("Prepare failed for photos: " . $this->db->error);
                }
                
                $stmt->bind_param("i", $memory['memory_id']);
                
                if (!$stmt->execute()) {
                    throw new \Exception("Execute failed for photos: " . $stmt->error);
                }
                
                $photoResult = $stmt->get_result();
                $photos = $photoResult->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                $memory['photos'] = $photos;
            }
            
            return $memory;
            
        } catch (\Exception $e) {
            error_log("Error in getTravelMemories: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Update the getAllTravelPlans method to include memory info
    public function getAllTravelPlans($userId) {
        try {
            $sql = "SELECT d.destination_name, d.province_id, d.image_path, 
                           t.stay_duration, t.check_in, t.check_out, 
                           t.travel_plan_id, t.destination_id, p.province_name,
                           t.has_memories,
                           CASE
                               WHEN CURDATE() < t.check_in THEN 'Pending'
                               WHEN CURDATE() BETWEEN t.check_in AND t.check_out THEN 'Ongoing'
                               WHEN CURDATE() > t.check_out THEN 'Completed'
                           END AS status,
                           (SELECT m.rating FROM travel_memories m WHERE m.travel_plan_id = t.travel_plan_id LIMIT 1) as rating
                    FROM traveldestinations d 
                    JOIN travel_plans t ON d.destination_id = t.destination_id
                    JOIN provinces p ON d.province_id = p.province_id
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
            error_log("Execute failed in hasOverlappingPlan: " . $stmt->error);
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