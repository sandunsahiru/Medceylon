<?php
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // Validate inputs
    if (!empty($booking_id) && in_array($status, ['Pending', 'Booked', 'Completed', 'Canceled'])) {
        $query = "UPDATE transportationassistance SET status = '$status' WHERE transport_request_id = '$booking_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $success_message = "Booking status updated successfully.";
        } else {
            $error_message = "Error updating status: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Invalid booking ID or status.";
    }
}

// Fetch all bookings for display
$bookings = mysqli_query($conn, "SELECT * FROM transportationassistance");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Partner Dashboard</title>
    <link rel="stylesheet" href="./assets/css/travel_partner_dashboard.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .form-container { margin: 20px; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .actions { display: flex; gap: 10px; }
        .message { margin: 20px; padding: 10px; border: 1px solid green; color: green; background: #e6ffe6; }
        .error { margin: 20px; padding: 10px; border: 1px solid red; color: red; background: #ffe6e6; }
        
        /* Logout button styles */
        .logout-btn {
            position: absolute;
            top: 70px; /* Moved down */
            right: 100px; /* Moved to the left */
            padding: 10px 15px;
            background-color: #248c7f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background-color: #1d7364;
        }
    </style>
</head>
<body>
    <!-- Logout button -->
    <a href="user_login.php">
        <button class="logout-btn">Logout</button>
    </a>

<div class="form-container">
    <h2>Travel Partner Dashboard</h2>

    <!-- Display success or error message -->
    <?php if (!empty($success_message)): ?>
        <div class="message"><?= $success_message ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="error"><?= $error_message ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Patient ID</th>
                <th>Email</th> <!-- Added Email Column -->
                <th>Pickup Location</th>
                <th>Dropoff Location</th>
                <th>Date</th>
                <th>Time</th>
                <th>Transport Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($bookings)): ?>
                <?php
                // Fetch the email for the patient based on the patient_id
                $patient_id = $row['patient_id'];
                $email_query = "SELECT email FROM users WHERE user_id = '$patient_id'";
                $email_result = mysqli_query($conn, $email_query);
                $email_row = mysqli_fetch_assoc($email_result);
                $email = $email_row ? $email_row['email'] : 'N/A';  // If no email found, display 'N/A'
                ?>
                <tr>
                    <form method="POST">
                        <td><?= $row['transport_request_id'] ?></td>
                        <td><?= $row['patient_id'] ?></td>
                        <td><?= htmlspecialchars($email) ?></td> <!-- Display the email -->
                        <td><?= $row['pickup_location'] ?></td>
                        <td><?= $row['dropoff_location'] ?></td>
                        <td><?= $row['date'] ?></td>
                        <td><?= $row['time'] ?></td>
                        <td><?= $row['transport_type'] ?></td>
                        <td>
                            <select name="status">
                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Booked" <?= $row['status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                                <option value="Completed" <?= $row['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Canceled" <?= $row['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                            </select>
                        </td>
                        <td class="actions">
                            <input type="hidden" name="booking_id" value="<?= $row['transport_request_id'] ?>">
                            <button type="submit" name="update_status">Update Status</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
