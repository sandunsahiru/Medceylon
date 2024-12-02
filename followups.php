<?php
include('header.php');

// Sample data (this should be fetched from a database in real use)
$appointments = [
    [
        'doctor_name' => 'Dr. Nirmala Wijesuriya',
        'specialty' => 'Orthopedic Surgeon',
        'date' => '2024-12-05',
        'time' => '10:00 AM',
        'frequency' => 'Every 3 months',
        'fee' => 'Rs. 5000',
        'message' => 'Dr. Nirmala is known for her expertise in sports injuries and joint replacements. She is following up on your recovery from recent surgery.'
    ],
    [
        'doctor_name' => 'Dr. Nirmala Wijesuriya',
        'specialty' => 'Orthopedic Surgeon',
        'date' => '2025-03-05',
        'time' => '10:00 AM',
        'frequency' => 'Every 3 months',
        'fee' => 'Rs. 5000',
        'message' => 'Dr. Nirmala will be checking on your recovery progress after the first follow-up.'
    ],
    [
        'doctor_name' => 'Dr. Nirmala Wijesuriya',
        'specialty' => 'Orthopedic Surgeon',
        'date' => '2025-06-05',
        'time' => '10:00 AM',
        'frequency' => 'Every 3 months',
        'fee' => 'Rs. 5000',
        'message' => 'Dr. Nirmala will assess the full recovery after 6 months of surgery.'
    ],
];
?>
<style>
    /* Next button in the top-right corner */
    .next-button {
        position: absolute;
        bottom: 250px;
        right: 20px;
        padding: 10px 20px;
        background-color: #299d97;
        color: white;
        font-size: 1rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .next-button:hover {
        background-color: #247f7a;
    }
</style>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-Up Appointments</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="./assets/css/follows.css"> <!-- Ensure the CSS file is in the same directory -->
</head>

<body>
    <!-- Next button -->
    <a href="rateyourdoctor.php">
        <button class="next-button">Next</button>
    </a>

    <!-- Main Content -->
    <div class="appointments-container">
        <h2>Follow-Up Appointments</h2>
        <p>Here are your upcoming follow-up appointments with Dr. Nirmala Wijesuriya to track your recovery progress:</p>

        <!-- Cards Section -->
        <div class="appointments-grid">
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-card">
                    <div class="appointment-details">
                        <h3><?php echo $appointment['doctor_name']; ?></h3>
                        <p><strong>Specialty:</strong> <?php echo $appointment['specialty']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($appointment['date'])); ?></p>
                        <p><strong>Time:</strong> <?php echo $appointment['time']; ?></p>
                        <p><strong>Frequency:</strong> <?php echo $appointment['frequency']; ?></p>
                        <p><strong>Consultation Fee:</strong> <?php echo $appointment['fee']; ?></p>
                        <p><strong>Message from the doctor:</strong> <?php echo $appointment['message']; ?></p>
                        <button class="get-link-btn" onclick="showLinkPopup()">Get Link</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal Popup for Google Meet Link -->
    <div id="linkPopup" class="popup">
        <div class="popup-content">
            <h4>Google Meet Link</h4>
            <p>Here is your Google Meet link for the appointment:</p>
            <a href="https://meet.google.com/fake-link" target="_blank">https://meet.google.com/fake-link</a>
            <button class="close-btn" onclick="closePopup()">Close</button>
        </div>
    </div>

    <?php
    include('footer.php');
    ?>

    <script>
        // Function to show the Google Meet link popup
        function showLinkPopup() {
            document.getElementById('linkPopup').style.display = 'flex';
        }

        // Function to close the popup
        function closePopup() {
            document.getElementById('linkPopup').style.display = 'none';
        }
    </script>