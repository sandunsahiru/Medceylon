<?php
include('includes/config.php');
include('header.php');

// Handle booking deletion
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status']; // Get the booking status

    // Check if the status is pending before deletion
    if ($status === 'Pending') {
        $query = "DELETE FROM transportationassistance WHERE transport_request_id = '$booking_id'";
        mysqli_query($conn, $query);
    } else {
        echo "<script>alert('Booking is confirmed. Contact the MedCeylon Team.');</script>";
    }
}

// Handle booking update
if (isset($_POST['update_booking'])) {
    $booking_id = $_POST['booking_id'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $transport_type = $_POST['transport_type'];
    $status = $_POST['status']; // Get the booking status

    // Check if the status is pending before updating
    if ($status === 'Pending') {
        $query = "UPDATE transportationassistance 
                  SET pickup_location = '$pickup_location', 
                      dropoff_location = '$dropoff_location', 
                      date = '$date', 
                      time = '$time', 
                      transport_type = '$transport_type' 
                  WHERE transport_request_id = '$booking_id'";
        mysqli_query($conn, $query);
    } else {
        echo "<script>alert('Booking is confirmed. Contact the MedCeylon Team.');</script>";
    }
}

// Fetch bookings for display
$bookings = mysqli_query($conn, "SELECT * FROM transportationassistance WHERE patient_id = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings</title>
    <style>
        /* General styles for body */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures that the body takes the full height of the screen */
            background-color: #f4f7fc;
        }

        /* Main container for content */
        .container {
            flex: 1; /* This allows the container to take up available space */
            width: 90%;
            margin: 50px auto;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            font-size: 36px;
            color: #333;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 16px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #299d97;
            color: #ffffff;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        tr:hover {
            background-color: #e2e2e2;
        }

        input[type="text"], input[type="date"], input[type="time"], select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #fafafa;
            margin-bottom: 15px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="date"]:focus, input[type="time"]:focus, select:focus {
            outline: none;
            border: 2px solid #299d97;
        }

        button {
            background-color: #299d97;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #257f6e;
            transform: scale(1.05);
        }

        button:disabled {
            background-color: #d0d0d0;
            cursor: not-allowed;
        }

        .form-actions {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        /* Footer styles */
        footer {
            background-color: #299d97;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 16px;
            width: 100%;
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Your Transport Bookings</h2>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
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
                    <tr>
                        <td><?= $row['transport_request_id'] ?></td>
                        <td><input type="text" name="pickup_location" value="<?= $row['pickup_location'] ?>" required <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>></td>
                        <td><input type="text" name="dropoff_location" value="<?= $row['dropoff_location'] ?>" required <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>></td>
                        <td><input type="date" name="date" value="<?= $row['date'] ?>" required <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>></td>
                        <td><input type="time" name="time" value="<?= $row['time'] ?>" required <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>></td>
                        <td>
                            <select name="transport_type" required <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>>
                                <option value="Car" <?= $row['transport_type'] == 'Car' ? 'selected' : '' ?>>Car</option>
                                <option value="Ambulance" <?= $row['transport_type'] == 'Ambulance' ? 'selected' : '' ?>>Ambulance</option>
                                <option value="Wheelchair Accessible Van" <?= $row['transport_type'] == 'Wheelchair Accessible Van' ? 'selected' : '' ?>>Wheelchair Accessible Van</option>
                            </select>
                        </td>
                        <td><?= $row['status'] ?></td>
                        <td class="form-actions">
                            <input type="hidden" name="booking_id" value="<?= $row['transport_request_id'] ?>">
                            <input type="hidden" name="status" value="<?= $row['status'] ?>">
                            <button type="submit" name="update_booking" <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>>Update</button>
                            <button type="submit" name="delete_booking" onclick="return confirm('Are you sure you want to delete this booking?')" <?= $row['status'] !== 'Pending' ? 'disabled' : '' ?>>Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>
</div>

<footer>
    <?php include('footer.php'); // Include the footer ?>
</footer>

</body>
</html>
