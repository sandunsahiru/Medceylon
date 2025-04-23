<?php

namespace App\Controllers;

class CaregiverRequestController {
    public function sendRequest($id) {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in.";
            exit();
        }

        $patient_id = $_SESSION['user_id'];
        $caregiver_id = intval($id);
        $message = $_POST['message'] ?? 'Hi, I would like you to be my caregiver.';

        $stmt = $db->prepare("INSERT INTO caregiver_requests (patient_id, caregiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $patient_id, $caregiver_id, $message);
        $stmt->execute();

        header("Location: /Medceylon/caregiver/profile/$id");
        exit();
    }

    public function viewRequests() {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        $caregiver_id = $_SESSION['user_id'];
        $sql = "SELECT r.request_id, r.patient_id, r.message, r.status, u.first_name, u.last_name 
                FROM caregiver_requests r
                JOIN users u ON r.patient_id = u.user_id
                WHERE r.caregiver_id = ?
                ORDER BY r.requested_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $requests = $stmt->get_result();

        require '../app/views/caregiver/requests.php';
    }

    public function respond($request_id) {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        $status = $_POST['status'];

        $stmt = $db->prepare("UPDATE caregiver_requests SET status = ? WHERE request_id = ?");
        $stmt->bind_param("si", $status, $request_id);
        $stmt->execute();

        header("Location: /Medceylon/caregiver/requests");
        exit();
    }
}
