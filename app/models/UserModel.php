<?php
namespace App\Models;

class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllCaregivers($filter = null, $sort = null) {
        $roleId = 6; // Caregiver role_id
    
        $query = "SELECT u.*, 
                    (SELECT AVG(rating) FROM caregiver_ratings WHERE caregiver_id = u.user_id) AS average_rating
                  FROM users u
                  WHERE u.role_id = ?";
    
        if ($filter) {
            if ($filter == 'experience') {
                $query .= " AND u.experience_years >= 5";
            } elseif ($filter == 'young') {
                $query .= " AND u.age <= 30";
            }
        }
    
        if ($sort) {
            if ($sort == 'experience') {
                $query .= " ORDER BY u.experience_years DESC";
            } elseif ($sort == 'young') {
                $query .= " ORDER BY u.age ASC";
            } elseif ($sort == 'rating') {
                $query .= " ORDER BY average_rating DESC";
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
