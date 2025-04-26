<?php
namespace App\Models;

class CaregiverRating {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addRating($caregiverId, $patientId, $rating, $review) {
        $stmt = $this->db->prepare("INSERT INTO caregiver_ratings (caregiver_id, patient_id, rating, review) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $caregiverId, $patientId, $rating, $review);
        return $stmt->execute();
    }

    public function getAverageRating($caregiverId) {
        $stmt = $this->db->prepare("SELECT AVG(rating) AS average_rating FROM caregiver_ratings WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiverId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['average_rating'] ?? 0;
    }

    public function getReviews($caregiverId) {
        $stmt = $this->db->prepare("SELECT rating, review, rating_date FROM caregiver_ratings WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiverId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
