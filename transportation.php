<?php
include('includes/config.php');
include('header.php'); // Include the header

// Handle new booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_transport'])) {
    $patient_id = 1; // For simplicity, set patient_id statically
    $transport_type = $_POST['transport_type'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $query = "INSERT INTO transportationassistance (patient_id, transport_type, pickup_location, dropoff_location, date, time, status) 
              VALUES ('$patient_id', '$transport_type', '$pickup_location', '$dropoff_location', '$date', '$time', 'Pending')";
    mysqli_query($conn, $query);
}

// Handle booking deletion
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    $query = "DELETE FROM transportationassistance WHERE transport_request_id = '$booking_id'";
    mysqli_query($conn, $query);
}

// Handle booking update
if (isset($_POST['update_booking'])) {
    $booking_id = $_POST['booking_id'];
    $pickup_location = $_POST['pickup_location'];
    $dropoff_location = $_POST['dropoff_location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $transport_type = $_POST['transport_type'];

    $query = "UPDATE transportationassistance 
              SET pickup_location = '$pickup_location', 
                  dropoff_location = '$dropoff_location', 
                  date = '$date', 
                  time = '$time', 
                  transport_type = '$transport_type' 
              WHERE transport_request_id = '$booking_id'";
    mysqli_query($conn, $query);
}

// Fetch bookings for display
$bookings = mysqli_query($conn, "SELECT * FROM transportationassistance WHERE patient_id = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportation Assistance</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .form-container {
            margin: 20px;
            padding: 20px;
            border: 1px solid #299d97;
            border-radius: 5px;
            background-color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: auto;
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #299d97;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #299d97;
            color: #fff;
        }
        .autocomplete-results {
            border: 1px solid #299d97;
            background: #fff;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 10;
        }
        .autocomplete-results div {
            padding: 10px;
            cursor: pointer;
        }
        .autocomplete-results div:hover {
            background: #f0f0f0;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        button {
            background-color: #299d97;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #257f6e;
        }
    </style>
    <script>
        async function fetchLocations(query, target) {
            if (query.length < 3) return;
            const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${query}&format=json&addressdetails=1&limit=5`);
            const locations = await response.json();

            const resultsContainer = document.querySelector(`#${target}-results`);
            resultsContainer.innerHTML = '';

            locations.forEach(location => {
                const div = document.createElement('div');
                div.textContent = location.display_name;
                div.onclick = () => {
                    document.getElementById(target).value = location.display_name;
                    resultsContainer.innerHTML = '';
                };
                resultsContainer.appendChild(div);
            });
        }
    </script>
</head>
<body>
<div class="form-container">
    <h2>Book Transportation Assistance</h2>
    <form method="POST">
        <label for="pickup_location">Pickup Location:</label>
        <input type="text" id="pickup_location" name="pickup_location" oninput="fetchLocations(this.value, 'pickup_location')" required>
        <div class="autocomplete-results" id="pickup_location-results"></div>
        <br><br>

        <label for="dropoff_location">Dropoff Location:</label>
        <input type="text" id="dropoff_location" name="dropoff_location" oninput="fetchLocations(this.value, 'dropoff_location')" required>
        <div class="autocomplete-results" id="dropoff_location-results"></div>
        <br><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="time">Time:</label>
        <input type="time" id="time" name="time" required><br><br>

        <label for="transport_type">Transport Type:</label>
        <select id="transport_type" name="transport_type" required>
            <option value="Car">Car</option>
            <option value="Ambulance">Ambulance</option>
            <option value="Wheelchair Accessible Van">Wheelchair Accessible Van</option>
        </select><br><br>

        <button type="submit" name="book_transport">Book Now</button>
    </form>
</div>

<div class="form-container">
    <h2>Your Bookings</h2>
    <table>
        <thead>
            <tr>
                <th>Pickup</th>
                <th>Dropoff</th>
                <th>Date</th>
                <th>Time</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($bookings)): ?>
                <tr>
                    <form method="POST">
                        <td>
                            <input type="text" name="pickup_location" value="<?= $row['pickup_location'] ?>">
                        </td>
                        <td>
                            <input type="text" name="dropoff_location" value="<?= $row['dropoff_location'] ?>">
                        </td>
                        <td>
                            <input type="date" name="date" value="<?= $row['date'] ?>">
                        </td>
                        <td>
                            <input type="time" name="time" value="<?= $row['time'] ?>">
                        </td>
                        <td>
                            <select name="transport_type">
                                <option value="Car" <?= $row['transport_type'] == 'Car' ? 'selected' : '' ?>>Car</option>
                                <option value="Ambulance" <?= $row['transport_type'] == 'Ambulance' ? 'selected' : '' ?>>Ambulance</option>
                                <option value="Wheelchair Accessible Van" <?= $row['transport_type'] == 'Wheelchair Accessible Van' ? 'selected' : '' ?>>Wheelchair Accessible Van</option>
                            </select>
                        </td>
                        <td><?= $row['status'] ?></td>
                        <td class="actions">
                            <input type="hidden" name="booking_id" value="<?= $row['transport_request_id'] ?>">
                            <button type="submit" name="update_booking">Update</button>
                            <?php if (strtotime($row['last_updated']) >= strtotime('-5 minutes')): ?>
                                <button type="submit" name="delete_booking" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</button>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); // Include the footer ?>
</body>
</html>
