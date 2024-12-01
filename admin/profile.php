<?php
// Include the database connection
include('./includes/db_connection.php');

// Include the header file
include('./includes/header.php');

// Start the session
session_start();

$user_id = $_GET['user_id'] ?? null;



// Fetch user details from the database based on user type
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update the profile if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $country = $_POST['country'] ?? $user['country'];
    $contact_number = $_POST['contact_number'] ?? $user['contact_number'];
    $slmc_registration_number = $_POST['slmc_registration_number'] ?? $user['slmc_registration_number'];
    $caretaker_registration_number = $_POST['caretaker_registration_number'] ?? $user['caretaker_registration_number'];
    $age = $_POST['age'] ?? $user['age'];
    $experience_years = $_POST['experience_years'] ?? $user['experience_years'];
    $blood_group = $_POST['blood_group'] ?? $user['blood_group'];
    $allergies = $_POST['allergies'] ?? $user['allergies'];

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to update user profile based on user type
    if ($user_type == 'patient') {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, country = ?, blood_group = ?, allergies = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $email, $hashed_password, $country, $blood_group, $allergies, $user_id);
    } elseif ($user_type == 'general_doctor' || $user_type == 'special_doctor') {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, contact_number = ?, slmc_registration_number = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $hashed_password, $contact_number, $slmc_registration_number, $user_id);
    } elseif ($user_type == 'caretaker') {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, caretaker_registration_number = ?, age = ?, experience_years = ? WHERE id = ?");
        $stmt->bind_param("ssssiii", $name, $email, $hashed_password, $caretaker_registration_number, $age, $experience_years, $user_id);
    }

    // Execute the update query
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Error updating profile: " . $stmt->error;
    }
}

// Delete the user account if requested
if (isset($_POST['delete_account'])) {
    // Prepare SQL statement to delete the account
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    // Execute the deletion
    if ($stmt->execute()) {
        // Destroy the session and log the user out
        session_destroy();
        header("Location: user_login.php");
        exit();
    } else {
        $error_message = "Error deleting account: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="./css/profile.css">
</head>
<body>
    <div class="form-container">
        <h1>My Account</h1>

        <!-- Display success or error messages -->
        <?php if (isset($success_message)) { echo "<p class='success'>$success_message</p>"; } ?>
        <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

        <form action="my_account.php" method="POST">
            <!-- Common fields for all users -->
            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= $user['name'] ?>" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Specific fields for patients -->
            <?php if ($user_type == 'patient'): ?>
                <div class="field">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?= $user['country'] ?>">
                </div>
                <div class="field">
                    <label for="blood_group">Blood Group</label>
                    <input type="text" id="blood_group" name="blood_group" value="<?= $user['blood_group'] ?>">
                </div>
                <div class="field">
                    <label for="allergies">Allergies</label>
                    <textarea id="allergies" name="allergies"><?= $user['allergies'] ?></textarea>
                </div>
            <?php elseif ($user_type == 'general_doctor' || $user_type == 'special_doctor'): ?>
                <div class="field">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= $user['contact_number'] ?>">
                </div>
                <div class="field">
                    <label for="slmc_registration_number">SLMC Registration Number</label>
                    <input type="text" id="slmc_registration_number" name="slmc_registration_number" value="<?= $user['slmc_registration_number'] ?>">
                </div>
            <?php elseif ($user_type == 'caretaker'): ?>
                <div class="field">
                    <label for="caretaker_registration_number">Caretaker Registration Number</label>
                    <input type="text" id="caretaker_registration_number" name="caretaker_registration_number" value="<?= $user['caretaker_registration_number'] ?>">
                </div>
                <div class="field">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?= $user['age'] ?>">
                </div>
                <div class="field">
                    <label for="experience_years">Experience in Years</label>
                    <input type="number" id="experience_years" name="experience_years" value="<?= $user['experience_years'] ?>">
                </div>
            <?php endif; ?>

            <button type="submit">Update Profile</button>
        </form>

        <form action="my_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
            <button type="submit" name="delete_account">Delete Account</button>
        </form>
    </div>

</body>
</html>