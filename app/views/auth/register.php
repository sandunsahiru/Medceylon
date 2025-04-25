<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MedCeylon</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/auth.css">
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/register.css">
</head>
<body class="login-page">

<div class="form-container">
    <form action="<?= $basePath ?>/register" method="POST" onsubmit="return validateForm()">
        <h1>Join MedCeylon</h1>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>

        <!-- User Type FIRST -->
        <div class="field">
            <label>User Type</label>
            <select name="user_type" id="user_type" onchange="toggleFields()" required>
                <option value="">Select</option>
                <option value="patient">Patient</option>
                <option value="general_doctor">General Doctor</option>
                <option value="special_doctor">Special Doctor</option>
                <option value="caregiver">
            </select>
        </div>

        <div class="field">
            <label>Full Name</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="field">
            <label>Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div id="patient_fields" class="user-type-fields" style="display:none">
            <div class="field">
                <label>Country</label>
                <select name="country" id="country" disabled>
                    <option value="">-- Select --</option>
                    <option value="LK">Sri Lanka</option>
                    <option value="US">United States</option>
                    <option value="GB">UK</option>
                    <option value="CA">Canada</option>
                </select>
            </div>
        </div>

        <div id="doctor_fields" class="user-type-fields" style="display:none">
            <div class="field">
                <label>Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" disabled>
            </div>
            <div class="field">
                <label>SLMC Registration Number</label>
                <input type="text" name="slmc_registration_number" id="slmc_registration_number" disabled>
            </div>
        </div>

        <div id="caretaker_fields" class="user-type-fields" style="display:none">
            <div class="field">
                <label>Age</label>
                <input type="number" name="age" id="age" disabled>
            </div>
            <div class="field">
                <label>Experience (Years)</label>
                <input type="number" name="experience_years" id="experience_years" disabled>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="margin-bottom: 10px;">Register</button>
        <p class="login-link" style="color: #299d97;">
    Already have an account? <a href="<?= $basePath ?>/login" style="color: #299d97;">Login here</a>
</p>
    </form>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('user_type').value;

    document.querySelectorAll('.user-type-fields').forEach(section => {
        section.style.display = 'none';
        section.querySelectorAll('input, select').forEach(el => el.disabled = true);
    });

    let showSectionId = null;
    if (type === 'patient') showSectionId = 'patient_fields';
    else if (type === 'general_doctor' || type === 'special_doctor') showSectionId = 'doctor_fields';
    else if (type === 'caretaker') showSectionId = 'caretaker_fields';

    if (showSectionId) {
        const section = document.getElementById(showSectionId);
        section.style.display = 'block';
        section.querySelectorAll('input, select').forEach(el => el.disabled = false);
    }
}

function validateForm() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const contact = document.getElementById('contact_number');
    const slmc = document.getElementById('slmc_registration_number');
    const age = document.getElementById('age');
    const experience = document.getElementById('experience_years');

    const namePattern = /^[A-Za-z\s]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    const contactPattern = /^\+?\d+$/;
    const slmcPattern = /^SLMC\d+$/;

    if (!namePattern.test(name)) {
        alert("Full name can only contain letters and spaces.");
        return false;
    }
    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return false;
    }
    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters, include 1 uppercase letter and 1 number.");
        return false;
    }
    if (contact && !contact.disabled && !contactPattern.test(contact.value)) {
        alert("Contact number must be valid (digits only, optional +).");
        return false;
    }
    if (slmc && !slmc.disabled && !slmcPattern.test(slmc.value)) {
        alert("SLMC number must start with 'SLMC' followed by numbers.");
        return false;
    }
    if (age && !age.disabled && parseInt(age.value) < 18) {
        alert("Age must be at least 18.");
        return false;
    }
    if (experience && !experience.disabled && parseInt(experience.value) < 0) {
        alert("Experience must be a positive number.");
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', toggleFields);
</script>
</body>
</html>
