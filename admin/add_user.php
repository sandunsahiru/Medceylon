<?php
// Include database connection
include './includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $country = $_POST['country'];
    $profilePicture = null;

    // Handle file upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $uploadsDir = 'uploads/';
        $profilePicture = $uploadsDir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicture);
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, nationality) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss",$name, $email, $password, $country);

    if ($stmt->execute()) {
        echo "User added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: index.php");
} else {
    echo "Invalid request.";
}
?>
