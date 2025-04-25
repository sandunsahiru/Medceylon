<?php
namespace App\Models;

class CaregiverRatingModel {
    private $conn;

    public function __construct($mysqli) {
        $this->conn = $mysqli;
    }

    public function save($data) {
        $stmt = $this->conn->prepare("INSERT INTO caregiver_ratings (patient_id, caregiver_id, rating, review) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $data['patient_id'], $data['caregiver_id'], $data['rating'], $data['review']);
        $stmt->execute();
    }

    public function getAverageRating($caregiver_id) {
        $stmt = $this->conn->prepare("SELECT AVG(rating) as avg FROM caregiver_ratings WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['avg'] !== null ? round($result['avg'], 1) : null;
    }

    public function getReviews($caregiver_id) {
        $stmt = $this->conn->prepare("SELECT r.rating, r.review, r.rated_at, u.first_name FROM caregiver_ratings r JOIN users u ON r.patient_id = u.user_id WHERE caregiver_id = ? ORDER BY rated_at DESC");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
