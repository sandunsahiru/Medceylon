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

    // 👤 View Caregiver Profile
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

    // 💬 Send a Message to Caregiver
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

    // 💬 View Chat History with Patient
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

    // 📥 Caregiver Dashboard: See All Received Messages
    public function dashboard() {
        global $db;
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id'])) {
            echo "You must be logged in as a caregiver.";
            exit();
        }

        $caregiver_id = $_SESSION['user_id'];

        $stmt = $db->prepare("SELECT DISTINCT sender_id, u.first_name, u.last_name
                              FROM caregiver_messages cm
                              JOIN users u ON cm.sender_id = u.user_id
                              WHERE cm.receiver_id = ?
                              ORDER BY cm.sent_at DESC");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $messages = $stmt->get_result();

        require '../app/views/caregiver/dashboard.php';
    }
}
