<?php
// Include the database connection
include('db_connection.php');

// Start the session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $role_id = match($_POST['user_type']) {
        'patient' => 1,
        'general_doctor', 'special_doctor' => 2,
        'caretaker' => 5,
        'admin' => 4,
        default => 1
    };
    
    // Split name into first and last name
    $name_parts = explode(' ', $_POST['name'], 2);
    $first_name = $name_parts[0];
    $last_name = $name_parts[1] ?? '';
    $email = $_POST['email'];
    
    // Add fixed email and password for admin
    if ($_POST['user_type'] == 'admin') {
        $email = "admin@example.com";
        $password_hash = password_hash("admin123", PASSWORD_DEFAULT);
    } else {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Initialize specific fields
    $nationality = $phone_number = $license_number = null;
    $age = $experience_years = null;

    // Handle fields specific to user types
    if ($_POST['user_type'] == 'patient') {
        $nationality = $_POST['country'];
    } elseif ($_POST['user_type'] == 'general_doctor' || $_POST['user_type'] == 'special_doctor') {
        $phone_number = $_POST['contact_number'];
        $license_number = $_POST['slmc_registration_number'];
    } elseif ($_POST['user_type'] == 'caretaker') {
        $age = $_POST['age'];
        $experience_years = $_POST['experience_years'];
    }

    // Create username from name
    $username = strtolower(str_replace(' ', '_', $_POST['name']));
    
    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into users table first
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, nationality, phone_number, role_id, age) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
        $stmt->bind_param("sssssssii", $username, $email, $password_hash, $first_name, $last_name, $nationality, $phone_number, $role_id, $age);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert additional data based on user type
            if ($_POST['user_type'] == 'general_doctor' || $_POST['user_type'] == 'special_doctor') {
                $stmt = $conn->prepare("INSERT INTO doctors (user_id, license_number, years_of_experience) VALUES (?, ?, NULL)");
                $stmt->bind_param("is", $user_id, $license_number);
                $stmt->execute();
            } elseif ($_POST['user_type'] == 'caretaker') {
                $stmt = $conn->prepare("INSERT INTO caretakers (user_id, registration_number, experience_years) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $user_id, $_POST['caretaker_registration_number'], $experience_years);
                $stmt->execute();
            }

            // Commit transaction
            $conn->commit();
            
            // Set success message in session
            $_SESSION['registration_success'] = true;
            
            header("Location: user_login.php");
            exit();
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = "Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="form-container">
        <form action="register.php" method="POST" id="registrationForm">
            <h1>Welcome to MedCeylon</h1>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- User Type Selection -->
            <label for="user_type">Select User Type</label>
            <select name="user_type" id="user_type" onchange="toggleFields()" required>
                <option value="" disabled selected>Select User Type</option>
                <option value="patient">Patient</option>
                <option value="general_doctor">General Doctor</option>
                <option value="special_doctor">Special Doctor</option>
                <option value="caretaker">Caretaker</option>

            </select>

            <!-- Common Fields -->
            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Patient Fields -->
            <div id="patient_fields" class="user-type-fields" style="display:none;">
                <div class="field">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country">
                </div>
            </div>

            <!-- Doctor Fields -->
            <div id="doctor_fields" class="user-type-fields" style="display:none;">
                <div class="field">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number">
                </div>
                <div class="field">
                    <label for="slmc_registration_number">SLMC Registration Number</label>
                    <input type="text" id="slmc_registration_number" name="slmc_registration_number">
                </div>
            </div>

            <!-- Caretaker Fields -->
            <div id="caretaker_fields" class="user-type-fields" style="display:none;">
                <div class="field">
                    <label for="caretaker_registration_number">Caretaker Registration Number</label>
                    <input type="text" id="caretaker_registration_number" name="caretaker_registration_number">
                </div>
                <div class="field">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age">
                </div>
                <div class="field">
                    <label for="experience_years">Experience in Years</label>
                    <input type="number" id="experience_years" name="experience_years">
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" id="registerButton">Register</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            const userType = document.getElementById('user_type').value;
            const userTypeFields = document.querySelectorAll('.user-type-fields');
            
            // Hide all specific fields
            userTypeFields.forEach(field => field.style.display = 'none');
            
            // Show relevant fields based on user type
            switch(userType) {
                case 'patient':
                    document.getElementById('patient_fields').style.display = 'block';
                    break;
                case 'general_doctor':
                case 'special_doctor':
                    document.getElementById('doctor_fields').style.display = 'block';
                    break;
                case 'caretaker':
                    document.getElementById('caretaker_fields').style.display = 'block';
                    break;
            }

            // Handle email field for admin
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            if (userType === 'admin') {
                emailField.value = 'admin@example.com';
                emailField.readOnly = true;
                passwordField.value = 'admin123';
                passwordField.readOnly = true;
            } else {
                emailField.readOnly = false;
                emailField.value = '';
                passwordField.readOnly = false;
                passwordField.value = '';
            }
        }
    </script>
</body>
</html>