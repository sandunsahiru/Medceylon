<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Transportation Requests</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/header.css">
    <link rel="stylesheet" href="/Medceylon/public/assets/css/patient-transport.css?v=2">
</head>
<body>
<div class="page-wrapper">

    <?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>


    <div class="container">

        <!-- Header + Button Combined in One Line -->
        <div class="page-header-combined">
            <h2>Your Transportation Requests</h2>
            <a href="/Medceylon/patient/transport/create" class="new-request-btn">+ New Request</a>
        </div>

        <a href="/Medceylon/patient/transport/report" class="btn" target="_blank">📄 Download Ride History (PDF)</a>

        <?php if (empty($requests)): ?>
            <p class="empty">You have not made any requests yet.</p>
        <?php else: ?>
            <table>
            <thead>
            <thead>
    <tr>
        <th>#</th>
        <th>Pickup</th>
        <th>Dropoff</th>
        <th>Date</th>
        <th>Time</th>
        <th>Vehicle</th>
        <th>Fare (Rs.)</th>
        <th>Status</th>
        <th>Vehicle No</th>   <!-- ✅ now matches below -->
        <th>Driver</th>
        <th>Contact</th>
        <th>Actions</th>
    </tr>
</thead>

</thead>
                <tbody>
                    <?php foreach ($requests as $index => $req): ?>
                        <tr>
                        <td>
                            <?= $index + 1 ?>
                            </td>
                            <td><?= htmlspecialchars($req['pickup_location']) ?></td>
                            <td><?= htmlspecialchars($req['dropoff_location']) ?></td>
                            <td><?= htmlspecialchars($req['date']) ?></td>
                            <td><?= htmlspecialchars($req['time']) ?></td>
                            <td><?= htmlspecialchars($req['transport_type']) ?></td>
                            <td><?= number_format($req['fare'] ?? 0, 2) ?></td>
                            <td><?= htmlspecialchars($req['status']) ?></td>
                            <td><?= htmlspecialchars($req['vehicle_number'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($req['driver_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($req['contact_number'] ?? '-') ?></td>
            

                            <td>
                                <?php if ($req['status'] === 'Pending'): ?>
                                    <div class="request-actions">
                                        <a href="/Medceylon/patient/transport/edit/<?= $req['transport_request_id'] ?>" class="btn secondary-btn">Edit</a>
                                        <form method="POST" action="/Medceylon/patient/transport/delete/<?= $req['transport_request_id'] ?>" class="inline-form">
                                            <button type="submit" class="btn danger-btn">Delete</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="locked">Locked</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>

</div>
</body>
</html>
