<?php

namespace App\Controllers;

use App\Models\User;

class CaregiverMessageController {

    public function list() {
        global $db;

        $experience = $_GET['experience'] ?? '';
        $age = $_GET['age'] ?? '';
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $sql = "SELECT u.user_id, u.first_name, u.last_name, c.experience_years, u.age, u.profile_picture
                FROM users u 
                JOIN caretakers c ON u.user_id = c.user_id
                WHERE u.role_id = 5 AND u.is_active = 1";

        if ($experience !== '') {
            $sql .= " AND c.experience_years >= " . intval($experience);
        }

        if ($age !== '') {
            $sql .= " AND u.age <= " . intval($age);
        }

        if ($search !== '') {
            $search = $db->real_escape_string($search);
            $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE '%$search%'";
        }

        if ($sort === 'experience') {
            $sql .= " ORDER BY c.experience_years DESC";
        } elseif ($sort === 'youngest') {
            $sql .= " ORDER BY u.age ASC";
        }

        $result = $db->query($sql);

        require '../app/views/caregiver/list.php';
    }

    public function viewProfile($id) {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        $stmt = $db->prepare("SELECT u.first_name, u.last_name, c.experience_years
                              FROM users u 
                              JOIN caretakers c ON u.user_id = c.user_id
                              WHERE u.user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $caregiver = $stmt->get_result()->fetch_assoc();

        require '../app/views/caregiver/profile.php';
    }

    public function sendMessage($id) {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['user_id'])) {
            $sender_id = $_SESSION['user_id'];
            $receiver_id = $id;
            $message = $_POST['message'];

            $stmt = $db->prepare("INSERT INTO caregiver_messages (sender_id, receiver_id, message)
                                  VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
            $stmt->execute();
        }

        header("Location: /Medceylon/caregiver/chat/$id");
        exit();
    }

    public function viewChat($id) {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in to view the chat.";
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $id = intval($id);

        $stmt = $db->prepare("SELECT * FROM caregiver_messages 
                              WHERE (sender_id = ? AND receiver_id = ?) 
                                 OR (sender_id = ? AND receiver_id = ?)
                              ORDER BY sent_at ASC");
        $stmt->bind_param("iiii", $user_id, $id, $id, $user_id);
        $stmt->execute();
        $messages = $stmt->get_result();

        require '../app/views/caregiver/chat.php';
    }

    public function dashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(dirname(__DIR__)));
        }

        $conn = require ROOT_PATH . '/app/config/database.php';
        $caregiver_id = $_SESSION['user_id'];

        $stmt1 = $conn->prepare("SELECT DISTINCT u.user_id, u.first_name, u.last_name, m.sender_id
                         FROM messages m
                         JOIN conversations c ON m.conversation_id = c.conversation_id
                         JOIN users u ON m.sender_id = u.user_id
                         WHERE c.participant1_id = ? OR c.participant2_id = ?");
        $stmt1->bind_param("ii", $caregiver_id, $caregiver_id);
        $stmt1->execute();
        $messages = $stmt1->get_result();


      
        $stmt2 = $conn->prepare("SELECT cr.*, u.first_name, u.last_name
                                 FROM caregiver_requests cr
                                 JOIN users u ON cr.patient_id = u.user_id
                                 WHERE cr.caregiver_id = ?
                                 ORDER BY cr.request_id DESC");
        $stmt2->bind_param("i", $caregiver_id);
        $stmt2->execute();
        $requests = $stmt2->get_result();

        require ROOT_PATH . '/app/views/caregiver/dashboard.php';
    }
}
