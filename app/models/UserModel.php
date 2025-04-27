<?php
namespace App\Models;

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllCaregivers($filter = null, $sort = null) {
        $roleId = 6; // Caregiver role_id
    
        $query = "SELECT * FROM users WHERE role_id = ?";
    
        // Filtering
        if ($filter) {
            if ($filter == 'experience') {
                $query .= " AND experience_years >= 5";
            } elseif ($filter == 'young') {
                $query .= " AND age <= 30";
            }
        }
    
        // Sorting
        if ($sort) {
            if ($sort == 'experience') {
                $query .= " ORDER BY experience_years DESC";
            } elseif ($sort == 'young') {
                $query .= " ORDER BY age ASC";
            }
        }
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    

    public function getUserById($userId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
