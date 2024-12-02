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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportation Assistance</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }
        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
        }
        .form-container input:focus, .form-container select:focus, .form-container button:focus {
            border-color: #299d97;
            outline: none;
        }
        .form-container input[type="text"], .form-container input[type="date"], .form-container input[type="time"] {
            background: #fafafa;
        }
        .form-container select {
            background: #fafafa;
        }
        .form-container button {
            background-color: #299d97;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-container button:hover {
            background-color: #257f6e;
        }
        .autocomplete-results {
            background: #fff;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 10;
            width: 100%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: -10px;
        }
        .autocomplete-results div {
            padding: 12px;
            cursor: pointer;
            font-size: 14px;
        }
        .autocomplete-results div:hover {
            background: #f4f4f4;
        }
        .btn-link {
            background-color: transparent;
            border: none;
            color: #299d97;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            padding: 10px;
            cursor: pointer;
        }
        .btn-link:hover {
            color: #257f6e;
            text-decoration: underline;
        }
        footer {
            margin-top: auto;
            background-color: #299d97;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .form-section {
            display: flex;
            flex-direction: column;
        }
        .form-section label {
            font-size: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-section input, .form-section select {
            width: 100%;
            max-width: 500px;
            margin: 0 auto 20px;
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

        // Set the minimum date for the date input field to today's date
        window.onload = function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date').setAttribute('min', today);
        }

        // Close autocomplete results when clicking outside
        document.addEventListener('click', function(event) {
            const resultsContainers = document.querySelectorAll('.autocomplete-results');
            resultsContainers.forEach(container => {
                if (!container.contains(event.target) && event.target !== container.previousElementSibling) {
                    container.innerHTML = '';
                }
            });
        });
    </script>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Book Your Transportation Assistance</h2>
        <form method="POST">
            <div class="form-section">
                <label for="pickup_location">Pickup Location</label>
                <input type="text" id="pickup_location" name="pickup_location" placeholder="Enter pickup location" oninput="fetchLocations(this.value, 'pickup_location')" required>
                <div class="autocomplete-results" id="pickup_location-results"></div>
            </div>

            <div class="form-section">
                <label for="dropoff_location">Dropoff Location</label>
                <input type="text" id="dropoff_location" name="dropoff_location" placeholder="Enter dropoff location" oninput="fetchLocations(this.value, 'dropoff_location')" required>
                <div class="autocomplete-results" id="dropoff_location-results"></div>
            </div>

            <div class="form-section">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>
            </div>

            <div class="form-section">
                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
            </div>

            <div class="form-section">
                <label for="transport_type">Transport Type</label>
                <select id="transport_type" name="transport_type" required>
                    <option value="Car">Car</option>
                    <option value="Ambulance">Ambulance</option>
                    <option value="Wheelchair Accessible Van">Wheelchair Accessible Van</option>
                </select>
            </div>

            <button type="submit" name="book_transport">Book Now</button>
        </form>
    </div>

    <div class="form-container">
        <h2>View Your Bookings</h2>
        <form action="view_bookings.php">
            <button type="submit" class="btn-link">View Bookings</button>
        </form>
    </div>
</div>

<footer>
    <?php include('footer.php'); // Include the footer ?>
</footer>

</body>
</html>
