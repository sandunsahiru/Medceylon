<?php
include './includes/db_connection.php'; // Include the database connection file

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $user_type = $_POST['user_type'];
    $firstname = $_POST['firstname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $password = $_POST['password'];  // Consider hashing the password for security
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Validate Date of Birth (DOB)
    $current_date = date("Y-m-d");
    if (strtotime($dob) > strtotime($current_date)) {
        // Date of birth cannot be a future date
        $error_message = "Date of Birth cannot be in the future.";
        header("Location: register.php?error=" . urlencode($error_message));
        exit($error_message);
    }

    // Map user_type to role_id
    switch ($user_type) {
        case 'patient':
            $role_id = 1;
            break;
        case 'general_doctor':
            $role_id = 2;
            break;
        case 'special_doctor':
            $role_id = 3;
            break;
        case 'caretaker':
            $role_id = 4;
            break;
        case 'support_agent':
            $role_id = 5;
            break;
        default:
            $role_id = 1; // Default to Patient if no valid type
            break;
    }

    // Check if the username already exists in the database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($username_count);
    $stmt->fetch();
    $stmt->close();  // Close statement to avoid commands out of sync

    if ($username_count > 0) {
        // Username already exists, show an error
        $error_message = "Username already exists. Please choose a different one.";
        header("Location: registration_form.php?error=" . urlencode($error_message));
        exit($error_message);
    }

    // Insert user into the `users` table
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, date_of_birth, gender, phone_number, email, username, password_hash, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $firstname, $lastname, $dob, $gender, $contact_number, $email, $username, $password_hash, $role_id);

    if ($stmt->execute()) {
        // Get the last inserted user_id
        $user_id = $conn->insert_id;
        $stmt->close();  // Close statement after user insertion

        // Now, let's proceed with user-type specific data after inserting the user

        if ($user_type == 'patient') {
            // Get the country name from the form
            $country_name = $_POST['country'];

            // Prepare a statement to get the country_code from the countries table
            $stmt_country = $conn->prepare("SELECT country_code FROM countries WHERE country_name = ?");
            $stmt_country->bind_param("s", $country_name);
            $stmt_country->execute();
            $stmt_country->bind_result($country_code);
            $stmt_country->fetch();
            $stmt_country->close(); // Close the country statement

            // If country_code is found, update the users table
            if ($country_code) {
                // Insert the country code into the user table
                $stmt_update = $conn->prepare("UPDATE users SET nationality = ? WHERE user_id = ?");
                $stmt_update->bind_param("si", $country_code, $user_id);
                $stmt_update->execute();
                $stmt_update->close();
            } else {
                echo "Country not found in the database.";
            }

        } elseif ($user_type == 'general_doctor' || $user_type == 'special_doctor') {
            // Insert doctor-specific details

            $slmc_registration_number = $_POST['slmc_registration_number'];

            $stmt_doctor = $conn->prepare("INSERT INTO doctors (user_id, license_number) VALUES (?, ?)");
            $stmt_doctor->bind_param("is", $user_id, $slmc_registration_number);
            $stmt_doctor->execute();
            $stmt_doctor->close();

        } elseif ($user_type == 'caretaker') {
            // Insert caretaker-specific details
            $caretaker_registration_number = $_POST['caretaker_registration_number'];
            $age = $_POST['age'];
            $experience_years = $_POST['experience_years'];

            // Close the previous user statement
            $stmt->close();

            $stmt_caretaker = $conn->prepare("INSERT INTO caretakers (user_id, caretaker_registration_number, age, experience_years) VALUES (?, ?, ?, ?)");
            $stmt_caretaker->bind_param("isii", $user_id, $caretaker_registration_number, $age, $experience_years);
            $stmt_caretaker->execute();
            $stmt_caretaker->close();
        }

        // Redirect to success page or display success message
        header('Location: index.php'); // Redirect to a success page
        exit;
    } else {
        // Handle error
        $error_message = "Error: " . $stmt->error;
        echo $error_message;
    }

    // Free result and close the statement at the end
    $stmt->free_result();
    $stmt->close();
}

// Close the connection
$conn->close();
?>