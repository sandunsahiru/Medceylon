<?php
// delete-profile.php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user_id = $_SESSION['user_id'];
        
        // Update is_active to 0 instead of deleting
        $query = "UPDATE users SET is_active = 0 WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            session_destroy();
            header('Location: ../login.php?message=Account+deactivated+successfully');
            exit;
        } else {
            throw new Exception("Failed to deactivate account");
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error deactivating account: " . $e->getMessage();
        header('Location: profile.php');
        exit;
    }
}
?>