<?php
namespace App\Models;

class Vehicle {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAvailableByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE vehicle_type = ? AND is_available = 1");
        $stmt->bind_param("s", $type);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function assignToRequest($vehicle_id) {
        $stmt = $this->db->prepare("UPDATE vehicles SET is_available = 0 WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
    }

    public function freeVehicle($vehicle_id) {
        $stmt = $this->db->prepare("UPDATE vehicles SET is_available = 1 WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
    }

    public function getById($vehicle_id) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE vehicle_id = ?");
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function getAvailableVehicleCounts() {
        $sql = "SELECT vehicle_type, COUNT(*) as count 
                FROM vehicles 
                WHERE is_available = 1 
                GROUP BY vehicle_type";
    
        $result = $this->db->query($sql);
        $counts = [];
    
        while ($row = $result->fetch_assoc()) {
            $counts[$row['vehicle_type']] = $row['count'];
        }
    
        return $counts;
    }
    
}
