<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedCeylon</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/auth.css">
</head>
<body class="login-page">
    <div class="form-container">
        <form action="<?php echo $basePath; ?>/login" method="POST">
            <h1>Login to MedCeylon</h1>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['registration_success'])): ?>
                <div class="success">Registration successful! Please login.</div>
                <?php unset($_SESSION['registration_success']); ?>
            <?php endif; ?>

            <!-- User Type -->
            <div class="field">
                <label for="user_type">User Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="patient">Patient</option>
                    <option value="general_doctor">General Doctor</option>
                    <option value="special_doctor">Special Doctor</option>
                    <option value="caretaker">Caretaker</option>
                    <option value="admin">Admin</option>
                    <option value="hospital">Hospital Admin</option>
                    <option value="travel_agent">Travel Agent</option> 
                </select>
            </div>

            <!-- Email -->
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Password -->
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>

            <div class="links">
    <div><a href="<?php echo $basePath; ?>/register">Don't have an account? Register</a></div>
    <div><a href="<?php echo $basePath; ?>/forgot-password">Forgot Password?</a></div>
</div>
        </form>
    </div>

    <script>
        document.getElementById('user_type').addEventListener('change', function () {
            const email = document.getElementById('email');
            const password = document.getElementById('password');

            if (this.value === 'travel_agent') {
                email.value = 'travelagent@example.com';
                password.value = 'agent123';
                email.readOnly = true;
                password.readOnly = true;
            } else {
                email.value = '';
                password.value = '';
                email.readOnly = false;
                password.readOnly = false;
            }
        });
    </script>
</body>
</html>
