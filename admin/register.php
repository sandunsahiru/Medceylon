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
        <form action="add_user.php" method="POST" id="registrationForm">
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
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" required>

                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>

            <div class="field" style="display:flex; gap:10px; align-items:center;">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required>


                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="field">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
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
            switch (userType) {
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