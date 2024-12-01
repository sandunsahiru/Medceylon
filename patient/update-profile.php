<?php
// update-profile.php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $query = "UPDATE users SET 
                  first_name = ?,
                  last_name = ?,
                  email = ?,
                  phone_number = ?,
                  date_of_birth = ?,
                  gender = ?,
                  address_line1 = ?,
                  address_line2 = ?,
                  city_id = ?,
                  nationality = ?,
                  passport_number = ?
                  WHERE user_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssssssssssi",
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone_number'],
            $_POST['date_of_birth'],
            $_POST['gender'],
            $_POST['address_line1'],
            $_POST['address_line2'],
            $_POST['city_id'],
            $_POST['nationality'],
            $_POST['passport_number'],
            $_SESSION['user_id']
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            throw new Exception("Failed to update profile");
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }

    header('Location: profile.php');
    exit;
}
?>