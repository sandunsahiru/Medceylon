<?php
// No database connection needed here.

// $occupiedVehicles, $availableVehicles and $request are already passed from controller

// Handle form submit inside view only for user-side quick check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vehicle_id']) && $_POST['vehicle_id'] === 'manual') {
        $externalVehicleNumber = strtolower(trim($_POST['external_vehicle_number'] ?? ''));

        if (in_array($externalVehicleNumber, $occupiedVehicles)) {
            $error = "❌ External vehicle is already occupied. Please use another one.";
        }
    }

    if (!isset($error)) {
        // Proceed to update database, etc.
        // Example: processAgentResponse($_POST);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond to Transportation Request</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/respond-request.css">
    <style>
        .container {
            max-width: 600px;
            margin: 100px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(41, 157, 151, 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        h2 {
            text-align: center;
            color: #299D97;
            margin-bottom: 30px;
        }
        .request-details p {
            margin: 12px 0;
            font-size: 15px;
        }
        label {
            font-weight: 500;
            margin-top: 20px;
            display: block;
        }
        select, button, input {
            padding: 10px;
            font-size: 14px;
            margin-top: 8px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button.submit-btn {
            background-color: #299D97;
            color: white;
            font-weight: 500;
            border: none;
            cursor: pointer;
            margin-top: 24px;
        }
        button.submit-btn:hover {
            background-color: #247c78;
        }
        #vehicle-selection, #external-vehicle-details {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color: red; font-weight: bold; text-align: center;"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <h2>Respond to Transportation Request</h2>

    <div class="request-details">
        <p><strong>Pickup:</strong> <?= htmlspecialchars($request['pickup_location']) ?></p>
        <p><strong>Drop-off:</strong> <?= htmlspecialchars($request['dropoff_location']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($request['date']) ?></p>
        <p><strong>Time:</strong> <?= htmlspecialchars($request['time']) ?></p>
        <p><strong>Transport Type:</strong> <?= htmlspecialchars($request['transport_type']) ?></p>
        <p><strong>Fare:</strong> Rs. <?= isset($request['fare']) ? number_format($request['fare'], 2) : '0.00' ?></p>
    </div>

    <form method="POST" action="/Medceylon/agent/transport/respond/<?= $request['transport_request_id'] ?>">

        <label>Status</label>
        <select name="status" id="status" required onchange="toggleVehicleSection()">
            <option value="Accepted">Accept</option>
            <option value="Rejected">Reject</option>
        </select>

        <div id="vehicle-selection">
            <?php if (!empty($availableVehicles)): ?>
                <label>Select Available Vehicle</label>
                <select name="vehicle_id" id="vehicle_id" onchange="toggleExternalVehicleFields()">
                    <option value="">-- Select a vehicle --</option>
                    <?php foreach ($availableVehicles as $vehicle): ?>
                        <option value="<?= $vehicle['vehicle_id'] ?>">
                            <?= $vehicle['vehicle_number'] ?> (<?= $vehicle['vehicle_type'] ?>)
                        </option>
                    <?php endforeach; ?>
                    <option value="manual">Other (3rd Party Vehicle)</option>
                </select>
            <?php else: ?>
                <p>No internal vehicles available. Please enter 3rd-party vehicle details below.</p>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('external-vehicle-details').style.display = 'block';
                    });
                </script>
            <?php endif; ?>

            <div id="external-vehicle-details">
                <label>Vehicle Number (External)</label>
                <input type="text" id="external_vehicle_number" name="external_vehicle_number" value="<?= isset($_POST['external_vehicle_number']) ? htmlspecialchars($_POST['external_vehicle_number']) : '' ?>" placeholder="E.g. WP-1234" />

                <label>Driver Name</label>
                <input type="text" name="external_driver_name" value="<?= isset($_POST['external_driver_name']) ? htmlspecialchars($_POST['external_driver_name']) : '' ?>" placeholder="E.g. John Perera" />

                <label>Contact Number</label>
                <input type="text" name="external_driver_contact" value="<?= isset($_POST['external_driver_contact']) ? htmlspecialchars($_POST['external_driver_contact']) : '' ?>" placeholder="E.g. 0777123456" />
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Response</button>
    </form>
</div>

<script>
function toggleVehicleSection() {
    const status = document.getElementById('status').value;
    const vehicleSection = document.getElementById('vehicle-selection');
    const externalDetails = document.getElementById('external-vehicle-details');

    if (status === 'Accepted') {
        vehicleSection.style.display = 'block';
    } else {
        vehicleSection.style.display = 'none';
        externalDetails.style.display = 'none';
    }
}

function toggleExternalVehicleFields() {
    const vehicleSelected = document.getElementById('vehicle_id').value;
    const externalDetails = document.getElementById('external-vehicle-details');

    if (vehicleSelected === 'manual') {
        externalDetails.style.display = 'block';
    } else {
        externalDetails.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleVehicleSection();
    toggleExternalVehicleFields();
});
</script>

</body>
</html>
