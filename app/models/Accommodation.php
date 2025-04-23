<?php

namespace App\Models;

class Accommodation {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function getAllAccommodations()
    {
        try {
            $sql = "SELECT a.provider_id, a.name, a.contact_info, a.address_line1, a.address_line2, a.city_id, a.services_offered, a.image_path, a.cost_per_night, c.city_name
            FROM accommodationproviders a JOIN cities c
            ON a.city_id = c.city_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (\Exception $e) {
            error_log("Error in getAllAccommodations: " . $e->getMessage());
            throw new \Exception("Failed to retrieve Accommodations");
        }  
    }

    public function getRelatedTown()
    {
        try{
            $sql = "SELECET city_name FROM cities c 
            JOIN accommodationproviders a 
            ON c.city_id = a.city_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);

        } catch (\Exception $e) {
            error_log("Error in getRelatedTown: " . $e->getMessage());
            throw new \Exception("Failed to retrieve Town");
        }

    }

    public function createBooking($patientId, $providerId, $checkInDate, $checkOutDate, $accommodationType, $specialRequests) {
        try {
            $sql = "INSERT INTO accommodationassistance (patient_id, accommodation_provider_id, check_in_date, check_out_date, accommodation_type, special_requests) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("iissss", $patientId, $providerId, $checkInDate, $checkOutDate, $accommodationType, $specialRequests);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in createBooking: " . $e->getMessage());
            throw new \Exception("Failed to create booking");
        }
    }

    public function getAllBookings($userId) {
        try {
            $sql = "SELECT aa.*, ap.name AS accommodation_name, ap.contact_info, ap.address_line1, ap.address_line2, ap.city_id, c.city_name, ap.image_path
                    FROM accommodationassistance aa 
                    JOIN accommodationproviders ap ON aa.accommodation_provider_id = ap.provider_id 
                    JOIN cities c ON ap.city_id = c.city_id 
                    WHERE aa.patient_id = ?";
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
}