<?php
// Include the header
include 'header.php';

// Simulated submitted requests for demonstration
$submitted_requests = [
    [
        'hospital' => 'Durdans Hospital',
        'stay_dates' => '2024-12-01 to 2024-12-05',
        'doctor' => 'Dr. Samantha Perera',
        'treatment' => 'Cardiology Consultation',
        'feedback' => 'Accepted',
        'cost' => '$2000'
    ],
    [
        'hospital' => 'Asiri Hospital',
        'stay_dates' => '2024-12-10 to 2024-12-12',
        'doctor' => 'Dr. Nuwan Jayasinghe',
        'treatment' => 'Orthopedic Surgery',
        'feedback' => 'Rejected',
        'cost' => 'N/A'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Hospitals</title>
    <link rel="stylesheet" href="./assets/css/hospital-requests.css">
</head>
<body>
    <div class="requests-container">
        <h1>Request to Hospitals</h1>
        
        <!-- Request Form -->
        <form class="request-form" method="POST" action="submit_request.php">
            <div class="form-group">
                <label for="hospital">Select Hospital</label>
                <select name="hospital" id="hospital" required>
                    <option value="Durdans Hospital">Durdans Hospital</option>
                    <option value="Asiri Hospital">Asiri Hospital</option>
                    <option value="Nawaloka Hospital">Nawaloka Hospital</option>
                    <option value="Lanka Hospitals">Lanka Hospitals</option>
                </select>
            </div>
            <div class="form-group">
                <label for="stay-dates">Stay Dates</label>
                <input type="text" name="stay_dates" id="stay-dates" placeholder="YYYY-MM-DD to YYYY-MM-DD" required>
            </div>
            <div class="form-group">
                <label for="doctor">Recommended Doctor</label>
                <input type="text" name="doctor" id="doctor" placeholder="Enter Doctor Name" required>
            </div>
            <div class="form-group">
                <label for="treatment">Treatment Name</label>
                <input type="text" name="treatment" id="treatment" placeholder="Enter Treatment Name" required>
            </div>
            <button type="submit" class="submit-btn">Submit Request</button>
        </form>

        <!-- Submitted Requests Section -->
        <div class="submitted-requests">
            <h2>Submitted Requests</h2>
            <?php if (!empty($submitted_requests)): ?>
                <div class="requests-list">
                    <?php foreach ($submitted_requests as $request): ?>
                        <div class="request-card">
                            <p><strong>Hospital:</strong> <?php echo $request['hospital']; ?></p>
                            <p><strong>Stay Dates:</strong> <?php echo $request['stay_dates']; ?></p>
                            <p><strong>Doctor:</strong> <?php echo $request['doctor']; ?></p>
                            <p><strong>Treatment:</strong> <?php echo $request['treatment']; ?></p>
                            <p><strong>Feedback:</strong> <?php echo $request['feedback']; ?></p>
                            <p><strong>Approximate Cost:</strong> <?php echo $request['cost']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No requests submitted yet.</p>
            <?php endif; ?>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
</body>
</html>
