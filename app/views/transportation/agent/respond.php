<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond to Transportation Request</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/respond-request.css">
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
        <p><strong>Fare:</strong> Rs. <?= isset($request['cost']) ? number_format($request['cost'], 2) : '0.00' ?></p>
    </div>

    <form method="POST" action="/Medceylon/agent/transport/respond/<?= $request['transport_request_id'] ?>">
        <label>Status</label>
        <select name="status" required>
            <option value="Accepted">Accept</option>
            <option value="Rejected">Reject</option>
        </select>

        <button type="submit" class="submit-btn">Submit Response</button>
    </form>
</div>

</body>
</html>
