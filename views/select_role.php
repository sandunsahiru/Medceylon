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
        <form method="POST" action="index.php">
            <button type="submit" name="role" value="GeneralDoctor">General Doctor</button>
            <button type="submit" name="role" value="Doctor">Specialist Doctor</button>
            <button type="submit" name="role" value="Patient">Patient</button>
        </form>
    </div>
</body>
</html>
