i<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medceylon";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="?page=overview">Overview</a></li>
                <li><a href="?page=appointments">Appointments</a></li>
                <li><a href="?page=transport">Transport</a></li>
                <li><a href="?page=travel">Travel</a></li>
                <li><a href="?page=caretakers">Caretakers</a></li>
                <li><a href="?page=payments">Payments</a></li>
            </ul>
        </div>
        <div class="main-content">
            <?php
            $page = $_GET['page'] ?? 'overview';

            if ($page == "overview") {
                $result = $conn->query("SELECT * FROM users");
                $count = $result->num_rows;
                echo "<h1>Overview</h1>";
                echo "<p>Total Registered Users: $count</p>";
                echo "<table><tr><th>ID</th><th>User Type</th><th>Name</th><th>Email</th><th>Actions</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['user_type']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>
                                <button>Edit</button>
                                <button>Delete</button>
                            </td>
                          </tr>";
                }
                echo "</table>";
            } elseif ($page == "appointments") {
                $result = $conn->query("SELECT * FROM appointments");
                echo "<h1>Appointments</h1>";
                echo "<table><tr><th>ID</th><th>Date</th><th>Time</th><th>Status</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['time']}</td>
                            <td>{$row['status']}</td>
                          </tr>";
                }
                echo "</table>";
            } elseif ($page == "transport") {
                $result = $conn->query("SELECT * FROM bookings");
                echo "<h1>Transport</h1>";
                echo "<table><tr><th>ID</th><th>From</th><th>To</th><th>Vehicle</th><th>Date</th><th>Time</th><th>Status</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['from_location']}</td>
                            <td>{$row['to_location']}</td>
                            <td>{$row['vehicle_type']}</td>
                            <td>{$row['travel_date']}</td>
                            <td>{$row['travel_time']}</td>
                            <td>{$row['status']}</td>
                          </tr>";
                }
                echo "</table>";
            } elseif ($page == "travel") {
                $result = $conn->query("SELECT * FROM sightseeing");
                echo "<h1>Travel</h1>";
                echo "<table><tr><th>ID</th><th>Location</th><th>Description</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['location']}</td>
                            <td>{$row['description']}</td>
                          </tr>";
                }
                echo "</table>";
            } elseif ($page == "caretakers") {
                $result = $conn->query("SELECT * FROM caregivers");
                echo "<h1>Caretakers</h1>";
                echo "<table><tr><th>ID</th><th>Name</th><th>Location</th><th>Experience</th><th>Night Shifts</th><th>Description</th><th>Email</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['location']}</td>
                            <td>{$row['experience_years']}</td>
                            <td>{$row['night_shifts']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['email']}</td>
                          </tr>";
                }
                echo "</table>";
            } elseif ($page == "payments") {
                $result = $conn->query("SELECT * FROM payments");
                echo "<h1>Payments</h1>";
                echo "<table><tr><th>ID</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['amount']}</td>
                            <td>{$row['status']}</td>
                            <td>{$row['date']}</td>
                          </tr>";
                }
                echo "</table>";
            }
            ?>
        </div>
    </div>
</body>
</html>
