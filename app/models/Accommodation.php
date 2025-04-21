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
            $sql = "SELECT a.provider_id, a.name, a.contact_info, a.address_line1, a.address_line2, a.city_id, a.services_offered, a.image_path, c.city_name
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
}