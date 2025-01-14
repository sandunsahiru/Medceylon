<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MedCeylon</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/auth.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/register.css">
</head>
<body>
    <div class="form-container">
        <form action="<?php echo $basePath; ?>/register" method="POST" id="registrationForm">
            <h1>Welcome to MedCeylon</h1>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- User Type Selection -->
            <div class="field">
                <label for="user_type">Select User Type</label>
                <select name="user_type" id="user_type" onchange="toggleFields()" required>
                    <option value="" disabled selected>Select User Type</option>
                    <option value="patient">Patient</option>
                    <option value="general_doctor">General Doctor</option>
                    <option value="special_doctor">Special Doctor</option>
                    <option value="caretaker">Caretaker</option>
                </select>
            </div>

            <!-- Common Fields -->
            <div class="field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo $oldInput['name'] ?? ''; ?>" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $oldInput['email'] ?? ''; ?>" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Patient Fields -->
            <div id="patient_fields" class="user-type-fields">
                <div class="field">
                    <label for="country">Country</label>
                    <input type="text" id="country" name="country" value="<?php echo $oldInput['country'] ?? ''; ?>">
                </div>
            </div>

            <!-- Doctor Fields -->
            <div id="doctor_fields" class="user-type-fields">
                <div class="field">
                    <label for="contact_number">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo $oldInput['contact_number'] ?? ''; ?>">
                </div>
                <div class="field">
                    <label for="slmc_registration_number">SLMC Registration Number</label>
                    <input type="text" id="slmc_registration_number" name="slmc_registration_number" value="<?php echo $oldInput['slmc_registration_number'] ?? ''; ?>">
                </div>
            </div>

            <!-- Caretaker Fields -->
            <div id="caretaker_fields" class="user-type-fields">
                <div class="field">
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" value="<?php echo $oldInput['age'] ?? ''; ?>">
                </div>
                <div class="field">
                    <label for="experience_years">Experience in Years</label>
                    <input type="number" id="experience_years" name="experience_years" value="<?php echo $oldInput['experience_years'] ?? ''; ?>">
                </div>
            </div>

            <button type="submit">Register</button>

            <div class="links">
                <p>Already have an account? <a href="<?php echo $basePath; ?>/login">Login here</a></p>
            </div>
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
    }

    // Initialize fields on page load
    document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</body>
</html>