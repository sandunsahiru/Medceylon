<?php

namespace App\Models;

class Accommodation {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }

    
public function getAllAccommodations($province_id = null, $budget = null)
{
    try {
        $sql = "SELECT a.provider_id, a.name, a.contact_info, a.address_line1, 
                       a.address_line2, a.city_id, a.image_path, a.cost_category, p.province_id, p.province_name
                FROM accommodationproviders a 
                JOIN provinces p ON a.province_id = p.province_id
                WHERE 1=1";
        
        $params = [];
        $types = '';
        
        if ($province_id) {
            $sql .= " AND p.province_id = ?";
            $params[] = $province_id;
            $types .= 'i';
        }
        
        if ($budget) {
            $sql .= " AND a.cost_category = ?";
            $params[] = $budget;
            $types .= 's';
        }
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (\Exception $e) {
        error_log("Error in getAllAccommodations: " . $e->getMessage());
        throw new \Exception("Failed to retrieve Accommodations");
    }  
}

    public function getAllDetails($providerId)
    {
        try {
            $sql = "SELECT 
                        ap.provider_id, 
                        ap.name, 
                        ap.contact_info, 
                        ap.address_line1, 
                        ap.address_line2, 
                        ap.city_id, 
                        ap.image_path,
                        r.room_id,
                        r.room_type,
                        r.room_count,
                        r.cost_per_night,
                        r.services_offered
                    FROM accommodationproviders ap
                    LEFT JOIN rooms r ON ap.provider_id = r.provider_id
                    WHERE ap.provider_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $providerId);
            $stmt->execute();
            $result = $stmt->get_result();

            $providerDetails = null;
            $rooms = [];

            while ($row = $result->fetch_assoc()) {
                if (!$providerDetails) {
                    // Initialize provider details from the first row
                    $providerDetails = [
                        'provider_id' => $row['provider_id'],
                        'name' => $row['name'],
                        'contact_info' => $row['contact_info'],
                        'address_line1' => $row['address_line1'],
                        'address_line2' => $row['address_line2'],
                        'city_id' => $row['city_id'],
                        'image_path' => $row['image_path'],
                        'rooms' => []
                    ];
                }

                // If room_id is not null, add the room to the list
                if (!is_null($row['room_id'])) {
                    $rooms[] = [
                        'room_id' => $row['room_id'],
                        'room_type' => $row['room_type'],
                        'room_count' => $row['room_count'],
                        'cost_per_night' => $row['cost_per_night'],
                        'services_offered' => $row['services_offered']
                    ];
                }
            }

            if (!$providerDetails) {
                throw new \Exception("Accommodation provider not found");
            }

            $providerDetails['rooms'] = $rooms;

            return $providerDetails;
        } catch (\Exception $e) {
            error_log("Error in getAllDetails with JOIN: " . $e->getMessage());
            throw new \Exception("Failed to retrieve accommodation details");
        }
    }


    public function createBooking($patientId, $providerId, $roomType, $checkInDate, $checkOutDate, $totalPrice, $specialRequests) {
        try {
            // 1. Find matching room_id
            $roomQuery = "SELECT room_id, available_room_count FROM rooms WHERE provider_id = ? AND room_type = ? LIMIT 1";
            $roomStmt = $this->db->prepare($roomQuery);
            $roomStmt->bind_param("is", $providerId, $roomType);
            $roomStmt->execute();
            $roomResult = $roomStmt->get_result();
    
            if ($roomResult->num_rows === 0) {
                throw new \Exception("No matching room found for provider and type.");
            }
    
            $room = $roomResult->fetch_assoc();
            $roomId = $room['room_id'];
            $availableCount = $room['available_room_count'];
    
            if ($availableCount <= 0) {
                throw new \Exception("No available rooms of this type.");
            }
    
            // 2. Insert into room_bookings
            $insertSql = "INSERT INTO room_bookings (patient_id, room_id, check_in_date, check_out_date, total_price, special_requests)
                          VALUES (?, ?, ?, ?, ?, ?)";
            $insertStmt = $this->db->prepare($insertSql);
            $insertStmt->bind_param("iissds", $patientId, $roomId, $checkInDate, $checkOutDate, $totalPrice, $specialRequests);
            $insertStmt->execute();
    
            return $insertStmt->insert_id;
            
        } catch (\Exception $e) {
            error_log("Error in createBooking: " . $e->getMessage());
            error_log("MySQL Error: " . $this->db->error);
            throw new \Exception("Failed to create booking");
        }
    }
    
    

    public function getAllBookings($userId) {
        try {
            $sql = "SELECT rb.booking_id, rb.check_in_date, rb.check_out_date, 
                           rb.total_price, rb.status, rb.booking_date,
                           r.room_type, r.cost_per_night,
                           ap.name AS accommodation_name, ap.image_path, p.province_name
                    FROM room_bookings rb
                    JOIN rooms r ON rb.room_id = r.room_id
                    JOIN accommodationproviders ap ON r.provider_id = ap.provider_id
                    JOIN provinces p ON ap.province_id = p.province_id
                    WHERE rb.patient_id = ?
                    ORDER BY rb.booking_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getAllBookings: " . $e->getMessage());
            throw new \Exception("Failed to retrieve bookings");
        }
    }

    public function getAllProvinces(){
        try {
            $sql = "SELECT * FROM provinces";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getAllProvinces: " . $e->getMessage());
            throw new \Exception("Failed to retrieve provinces");
        }
    }

    // Add this method to Accommodation.php
public function hasActiveBooking($patientId) {
    try {
        $sql = "SELECT COUNT(*) as count 
                FROM room_bookings 
                WHERE patient_id = ? 
                AND status IN ('Pending', 'Successful')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    } catch (\Exception $e) {
        error_log("Error in hasActiveBooking: " . $e->getMessage());
        throw new \Exception("Failed to check active bookings");
    }
}

    public function deleteBooking($bookingId, $patientId) {
        try {
            // First verify the booking belongs to the patient and is pending
            $sql = "SELECT status FROM room_bookings 
                    WHERE booking_id = ? AND patient_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $bookingId, $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new \Exception("Booking not found or doesn't belong to user");
            }
            
            $booking = $result->fetch_assoc();
            if ($booking['status'] !== 'Pending') {
                throw new \Exception("Only pending bookings can be deleted");
            }
            
            // Delete the booking
            $deleteSql = "DELETE FROM room_bookings WHERE booking_id = ?";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->bind_param("i", $bookingId);
            $deleteStmt->execute();
            
            return $deleteStmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in deleteBooking: " . $e->getMessage());
            throw new \Exception("Failed to delete booking");
        }
    }
}