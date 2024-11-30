<?php
// Include database connection
include('includes/config.php');

// Start the session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Map user type to role_id
    $role_id = match($user_type) {
        'patient' => 1,
        'general_doctor', 'special_doctor' => 2,
        'caretaker' => 5,
        'admin' => 4,
        'support_agent' => 5,
        default => 1
    };

    // Check if the user is an admin with fixed credentials
    if ($user_type === 'admin' && $email === 'admin@example.com' && $password === 'admin123') {
        $_SESSION['user_id'] = 3; // From the sample data
        $_SESSION['name'] = 'Admin User';
        $_SESSION['role_id'] = 4;
        $_SESSION['user_type'] = 'admin';
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Prepare SQL statement to fetch user details
        $stmt = $conn->prepare("
            SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as name, 
                   u.email, u.password_hash, u.role_id,
                   r.role_name
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE u.email = ? AND u.role_id = ? AND u.is_active = 1
        ");
        
        $stmt->bind_param("si", $email, $role_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['user_type'] = $user['role_name'];

                // Update last login timestamp
                $update_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
                $update_stmt->bind_param("i", $user['user_id']);
                $update_stmt->execute();

                // Redirect based on role
                switch($user['role_id']) {
                    case 1: // Patient
                        header("Location: patient_dashboard.php");
                        break;
                    case 2: // Doctor
                        header("Location: doctor_dashboard.php");
                        break;
                    case 4: // Admin
                        header("Location: admin_dashboard.php");
                        break;
                    case 5: // Support/Caretaker
                        header("Location: support_dashboard.php");
                        break;
                    default:
                        header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email and type.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MedCeylon</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        .field input, .field select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .field input:focus, .field select:focus {
            border-color: #4e73df;
            outline: none;
            background-color: #fff;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #299d97;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1e7f76;
        }

        .error {
            color: #e74a3b;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }

        .links {
            margin-top: 20px;
            text-align: center;
        }

        .links a {
            color: #299d97;
            text-decoration: none;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h1>Login to MedCeylon</h1>

            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="field">
                <label for="user_type">User Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="" disabled selected>Select your role</option>
                    <option value="patient">Patient</option>
                    <option value="general_doctor">General Doctor</option>
                    <option value="special_doctor">Special Doctor</option>
                    <option value="caretaker">Caretaker</option>
                    <option value="admin">Admin</option>
                    <option value="support_agent">Support Agent</option>
                </select>
            </div>

            <button type="submit">Login</button>

            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

            <div class="links">
                <a href="register.php">Don't have an account? Register</a><br>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('user_type').addEventListener('change', function() {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            
            if (this.value === 'admin') {
                email.value = 'admin@example.com';
                password.value = 'admin123';
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