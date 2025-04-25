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

        select, button {
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

        #vehicle-selection {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
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
        <label for="status">Status</label>
        <select name="status" id="status" required onchange="toggleVehicleDropdown()">
            <option value="">-- Select Response --</option>
            <option value="Accepted">Accept</option>
            <option value="Rejected">Reject</option>
        </select>

        <?php if (isset($availableVehicles)): ?>
        <div id="vehicle-selection">
            <label for="vehicle_id">Select Vehicle</label>
            <select name="vehicle_id" id="vehicle_id">
                <option value="">-- Choose a Vehicle --</option>
                <?php foreach ($availableVehicles as $v): ?>
                    <option value="<?= $v['vehicle_id'] ?>">
                        <?= htmlspecialchars($v['vehicle_number']) ?> - <?= htmlspecialchars($v['driver_name']) ?> (<?= $v['contact_number'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <button type="submit" class="submit-btn">Submit Response</button>
    </form>
</div>

<script>
    function toggleVehicleDropdown() {
        const status = document.getElementById('status').value;
        const vehicleDropdown = document.getElementById('vehicle-selection');
        vehicleDropdown.style.display = status === 'Accepted' ? 'block' : 'none';
    }
</script>

</body>
</html>
