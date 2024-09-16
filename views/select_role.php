<!-- views/select_role.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Role</title>
    <style>
        /* Basic styling for the role selection page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
        }
        .role-selection {
            text-align: center;
        }
        .role-selection h1 {
            margin-bottom: 30px;
        }
        .role-selection button {
            padding: 15px 30px;
            margin: 10px;
            font-size: 18px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #00857c;
            color: #fff;
        }
        .role-selection button:hover {
            background-color: #006b64;
        }
    </style>
</head>
<body>
    <div class="role-selection">
        <h1>Select Your Role</h1>
        <!-- General Doctor Form -->
        <form method="POST" action="index.php">
            <input type="hidden" name="role" value="GeneralDoctor">
            <button type="submit">General Doctor</button>
        </form>
        <!-- Specialist Doctor Form -->
        <form method="POST" action="index.php">
            <input type="hidden" name="role" value="Doctor">
            <button type="submit">Specialist Doctor</button>
        </form>
        <!-- Patient Form -->
        <form method="POST" action="index.php">
            <input type="hidden" name="role" value="Patient">
            <button type="submit">Patient</button>
        </form>
        <!-- Accommodation as Patient Form -->
        <form method="POST" action="index.php">
            <input type="hidden" name="role" value="Patient">
            <input type="hidden" name="redirect" value="accommodation">
            <button type="submit">Accommodation as Patient</button>
        </form>
    </div>
</body>
</html>
