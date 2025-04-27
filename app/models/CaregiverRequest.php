<?php
namespace App\Models;

class CaregiverRequest {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 🟢 Patient sends a request to caregiver
    public function sendRequest($patientId, $caregiverId) {
        $stmt = $this->db->prepare("INSERT INTO caregiver_requests (patient_id, caregiver_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $patientId, $caregiverId);
        return $stmt->execute();
    }

    // 🟢 Get all pending/accepted/rejected requests for a caregiver
    public function getRequestsForCaregiver($caregiverId) {
        $stmt = $this->db->prepare(
            "SELECT cr.request_id, cr.status, u.first_name, u.last_name, u.email
            FROM caregiver_requests cr
            INNER JOIN users u ON cr.patient_id = u.user_id
            WHERE cr.caregiver_id = ?"
        );
        $stmt->bind_param("i", $caregiverId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🟢 Caregiver accepts/rejects a request
    public function respondToRequest($requestId, $status) {
        $stmt = $this->db->prepare("UPDATE caregiver_requests SET status = ? WHERE request_id = ?");
        $stmt->bind_param("si", $status, $requestId);
        return $stmt->execute();
    }

    // 🟢 Check if patient-caretaker relationship is accepted
    public function isAccepted($patientId, $caregiverId) {
        $stmt = $this->db->prepare("SELECT status FROM caregiver_requests WHERE patient_id = ? AND caregiver_id = ? AND status = 'Accepted'");
        $stmt->bind_param("ii", $patientId, $caregiverId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? true : false;
    }

    // 🟢 Check if patient already sent a pending request to caregiver
    public function hasPendingRequest($patientId, $caregiverId) {
        $stmt = $this->db->prepare("SELECT * FROM caregiver_requests WHERE patient_id = ? AND caregiver_id = ? AND status = 'Pending'");
        $stmt->bind_param("ii", $patientId, $caregiverId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // 🟢 Get requests filtered by status
    public function getRequestsByStatus($caregiverId, $status) {
        $stmt = $this->db->prepare("
            SELECT cr.*, CONCAT(u.first_name, ' ', u.last_name) AS patient_name, u.phone_number, u.email
            FROM caregiver_requests cr
            INNER JOIN users u ON cr.patient_id = u.user_id
            WHERE cr.caregiver_id = ? AND cr.status = ?
        ");
        $stmt->bind_param("is", $caregiverId, $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 🟢 Get caregiver's average rating
    public function getAverageRating($caregiverId) {
        $stmt = $this->db->prepare("SELECT AVG(rating) as avg_rating FROM caregiver_ratings WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiverId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return number_format($result['avg_rating'] ?? 0, 1);
    }
}
