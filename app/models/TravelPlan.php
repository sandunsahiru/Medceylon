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
                    GROUP_CONCAT(DISTINCT dt.type_name) AS destination_types
                  FROM traveldestinations d
                  JOIN provinces p ON d.province_id = p.province_id
                  LEFT JOIN destination_type_mapping dm ON d.destination_id = dm.destination_id
                  LEFT JOIN destination_types dt ON dm.type_id = dt.type_id
                  WHERE 1=1";

        $params = [];
        $types = '';
        $conditions = [];

        // Dynamically build conditions
        if (!empty($filters['province_id'])) {
            $conditions[] = "p.province_id = ?";
            $params[] = $filters['province_id'];
            $types .= 'i';
        }

        if (isset($filters['wheelchair']) && in_array($filters['wheelchair'], ['No', 'Yes'])) {
            $conditions[] = "d.wheelchair_accessibility = ?";
            $params[] = $filters['wheelchair'];
            $types .= 's';
        }

        if (!empty($filters['type_id'])) {
            $conditions[] = "dm.type_id = ?";
            $params[] = $filters['type_id'];
            $types .= 'i';
        }

        if (isset($filters['cost']) && in_array($filters['cost'], ['Low', 'Medium', 'High'])) {
            $conditions[] = "d.cost_category = ?";
            $params[] = $filters['cost'];
            $types .= 's';
        }

        // Append all dynamic conditions
        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        // Now group
        $query .= " GROUP BY d.destination_id";

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

    public function saveCompletePlan($userId, $accommodationId, $planData)
    {
        try {
            $this->db->begin_transaction();

            
            $this->db->query("DELETE FROM travel_plans WHERE user_id = $userId");

            // Insert each destination in the plan
            foreach ($planData['items'] as $item) {
                $sql = "INSERT INTO travel_plans 
                        (user_id, destination_id, check_in, check_out, travel_time_hours, time_spent_hours, sequence) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param(
                    "iissddi",
                    $userId,
                    $item['destination_id'],
                    $item['start_date'],
                    $item['end_date'],
                    $item['travel_time_hours'],
                    $item['time_spent_hours'],
                    $item['sequence']
                );
                $stmt->execute();
                $stmt->close();
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in saveCompletePlan: " . $e->getMessage());
            throw $e;
        }
    }

    public function createTrip($userId, $tripName, $startDate, $endDate, $totalDuration)
    {
        $sql = "INSERT INTO trips (user_id, name, start_date, end_date, total_duration_hours) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssd", $userId, $tripName, $startDate, $endDate, $totalDuration);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function addDestinationToTrip($tripId, $destinationId, $checkIn, $checkOut, $sequence, $travelTime, $timeSpent)
    {
        $sql = "INSERT INTO travel_plans 
                (trip_id, user_id, destination_id, check_in, check_out, sequence, travel_time_hours, time_spent_hours) 
                VALUES (?, (SELECT user_id FROM trips WHERE trip_id = ?), ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiissidd", $tripId, $tripId, $destinationId, $checkIn, $checkOut, $sequence, $travelTime, $timeSpent);
        return $stmt->execute();
    }
    
    public function getUserTrips($userId)
    {
        $sql = "SELECT t.*, 
                    (SELECT COUNT(*) FROM travel_plans WHERE trip_id = t.trip_id) AS destination_count
                FROM trips t
                WHERE t.user_id = ?
                ORDER BY t.start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getTripDetails($tripId)
    {
        // Get trip header
        $sql = "SELECT * FROM trips WHERE trip_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        $trip = $stmt->get_result()->fetch_assoc();
        
        // Get destinations
        $sql = "SELECT tp.*, td.destination_name, td.image_path, td.description
                FROM travel_plans tp
                JOIN traveldestinations td ON tp.destination_id = td.destination_id
                WHERE tp.trip_id = ?
                ORDER BY tp.sequence";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $tripId);
        $stmt->execute();
        $destinations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return [
            'trip' => $trip,
            'destinations' => $destinations
        ];
    }
    
    public function migrateExistingPlans()
    {
        // 1. Group existing plans by user and date ranges that likely belong together
        $sql = "SELECT user_id, 
                    DATE(MIN(check_in)) as start_date,
                    DATE(MAX(check_out)) as end_date,
                    GROUP_CONCAT(travel_plan_id) as plan_ids
                FROM travel_plans
                WHERE trip_id IS NULL
                GROUP BY user_id, DATE(check_in)";
        
        $result = $this->db->query($sql);
        $groups = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($groups as $group) {
            // 2. Create a trip for each group
            $tripId = $this->createTrip(
                $group['user_id'],
                "Migrated Trip",
                $group['start_date'],
                $group['end_date'],
                0 // We'll calculate this later
            );
            
            // 3. Update the existing plans with trip_id
            $planIds = explode(',', $group['plan_ids']);
            $sequence = 1;
            $totalHours = 0;
            
            foreach ($planIds as $planId) {
                // Get the existing plan
                $plan = $this->db->query("SELECT * FROM travel_plans WHERE travel_plan_id = $planId")->fetch_assoc();
                
                // Calculate duration if not exists
                $hours = $plan['travel_time_hours'] ?? 0;
                $hours += $plan['time_spent_hours'] ?? 
                        (strtotime($plan['check_out']) - strtotime($plan['check_in'])) / 3600;
                
                $totalHours += $hours;
                
                // Update with trip_id and sequence
                $this->db->query("UPDATE travel_plans 
                                SET trip_id = $tripId, 
                                    sequence = $sequence,
                                    travel_time_hours = $hours
                                WHERE travel_plan_id = $planId");
                $sequence++;
            }
            
            // Update trip with total duration
            $this->db->query("UPDATE trips SET total_duration_hours = $totalHours WHERE trip_id = $tripId");
        }
    }

    public function getDestinationById($destinationId)
    {
        try {
            $sql = "SELECT * FROM traveldestinations WHERE destination_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $destinationId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDestinationById: " . $e->getMessage());
            throw new \Exception("Failed to retrieve destination");
        }
    }

    public function calculateTravelPlan($accommodation, $destinationIds)
    {
        $averageSpeed = 40; // km/h
        $maxDailyHours = 8; // Maximum hours per day
        
        $plan = [];
        $currentDate = new \DateTime($accommodation['check_out']);
        $previousLocation = [
            'latitude' => $accommodation['latitude'],
            'longitude' => $accommodation['longitude']
        ];

        foreach ($destinationIds as $index => $destinationId) {
            $destination = $this->getDestinationById($destinationId);
            if (!$destination) {
                throw new \Exception("Destination not found: $destinationId");
            }

            // Calculate distance from previous location
            $distance = DistanceHelper::calculateDistanceInKm(
                $previousLocation['latitude'],
                $previousLocation['longitude'],
                $destination['latitude'],
                $destination['longitude']
            );

            // Calculate travel time (hours)
            $travelTime = $distance / $averageSpeed;
            
            // Total time needed (travel + visit)
            $totalTime = $travelTime + $destination['minimum_hours_spent'];
            
            // Determine dates
            $startDate = clone $currentDate;
            $endDate = clone $startDate;
            
            if ($totalTime > $maxDailyHours) {
                $endDate->modify('+1 day');
            }
            
            // Add to plan
            $plan[] = [
                'destination_id' => $destination['destination_id'],
                'destination_name' => $destination['destination_name'],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'travel_time_hours' => round($travelTime, 2),
                'time_spent_hours' => $destination['minimum_hours_spent'],
                'sequence' => $index + 1
            ];
            
            // Update for next iteration
            $currentDate = $endDate;
            $previousLocation = [
                'latitude' => $destination['latitude'],
                'longitude' => $destination['longitude']
            ];
        }

        // Calculate total trip time
        $totalTripTime = array_reduce($plan, function($carry, $item) {
            return $carry + $item['travel_time_hours'] + $item['time_spent_hours'];
        }, 0);

        return [
            'items' => $plan,
            'total_trip_time_hours' => round($totalTripTime, 2)
        ];
    }

    public function saveMultiDestinationPlan($userId, $planData) {
        try {
            $this->db->begin_transaction();
    
            // 1. Create a new trip record
            $tripName = "Multi-Destination Trip " . date('Y-m-d');
            $tripStartDate = $planData['items'][0]['start_date'] ?? date('Y-m-d');
            $tripEndDate = end($planData['items'])['end_date'] ?? date('Y-m-d');
            
            // Calculate total duration
            $totalDuration = array_reduce($planData['items'], function($carry, $item) {
                return $carry + ($item['travel_time_hours'] ?? 0) + ($item['time_spent_hours'] ?? 0);
            }, 0);
    
            $tripId = $this->createTrip(
                $userId,
                $tripName,
                $tripStartDate,
                $tripEndDate,
                $totalDuration
            );

            // Clear existing plans
                $deleteStmt = $this->db->prepare("DELETE FROM travel_plans WHERE user_id = ?");
                $deleteStmt->bind_param("i", $userId);
                $deleteStmt->execute();
                $deleteStmt->close();
            
    
            // 3. Insert each destination with the trip_id
            $insertSql = "INSERT INTO travel_plans (
                user_id, 
                destination_id, 
                check_in,      
                check_out,     
                travel_time_hours, 
                time_spent_hours, 
                sequence,
                trip_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $insertStmt = $this->db->prepare($insertSql);
    
            foreach ($planData['items'] as $item) {
                $insertStmt->bind_param(
                    "iissddii",
                    $userId,
                    $item['destination_id'],
                    $item['start_date'],
                    $item['end_date'],
                    $item['travel_time_hours'],
                    $item['time_spent_hours'],
                    $item['sequence'],
                    $tripId
                );
                
                if (!$insertStmt->execute()) {
                    throw new \Exception("Insert failed: " . $insertStmt->error);
                }
            }
    
            $insertStmt->close();
            $this->db->commit();
            return $tripId; // Return the trip ID for reference
    
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Save error: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Add this helper method
    private function isValidDate($dateStr) {
        if (empty($dateStr)) return false;
        try {
            new \DateTime($dateStr);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    



    public function getMultiDestinationPlan($userId)
    {
        try {
            $sql = "SELECT tp.*, td.destination_name, td.image_path, 
                        td.description, td.opening_time, td.closing_time
                    FROM travel_plans tp
                    JOIN traveldestinations td ON tp.destination_id = td.destination_id
                    WHERE tp.user_id = ? AND tp.trip_id IS NULL
                    ORDER BY tp.sequence";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getMultiDestinationPlan: " . $e->getMessage());
            throw new \Exception("Failed to retrieve travel plan");
        }
    }

    public function getUserActiveAccommodation($userId)
    {
        try {
            $sql = "SELECT 
                        rb.booking_id,
                        rb.patient_id as user_id,
                        rb.room_id,
                        rb.check_in_date as check_in,
                        rb.check_out_date as check_out,
                        r.provider_id,
                        ap.name as provider_name,
                        ap.latitude,
                        ap.longitude
                    FROM room_bookings rb
                    JOIN rooms r ON rb.room_id = r.room_id
                    JOIN accommodationproviders ap ON r.provider_id = ap.provider_id
                    WHERE rb.patient_id = ? 
                    AND rb.check_out_date >= CURDATE()
                    AND rb.status = 'Successful'
                    ORDER BY rb.check_in_date ASC
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null; // Return null instead of throwing exception
            }
            
            $booking = $result->fetch_assoc();
            
            return [
                'name' => $booking['provider_name'] . " (Room Booking)",
                'check_out' => $booking['check_out'],
                'latitude' => $booking['latitude'],
                'longitude' => $booking['longitude']
            ];
            
        } catch (\Exception $e) {
            error_log("Error in getUserActiveAccommodation: " . $e->getMessage());
            return null; // Return null on error
        }
    }

    public function createNewTravelPlan($userId) {
        $this->db->query(
            "INSERT INTO travel_plans (user_id) VALUES (:user_id)",
            [':user_id' => $userId]
        );
        return $this->db->lastInsertId();
    }
    
    public function addDestinationToPlan($travelId, $destinationId, $startDate, $endDate) {
        return $this->db->query(
            "INSERT INTO travel_plan_destinations 
             (travel_id, destination_id, start_date, end_date)
             VALUES (:travel_id, :dest_id, :start, :end)",
            [
                ':travel_id' => $travelId,
                ':dest_id' => $destinationId,
                ':start' => $startDate,
                ':end' => $endDate
            ]
        );
    }
}