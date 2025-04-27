<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Transportation Requests</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/header.css">
    <link rel="stylesheet" href="/Medceylon/public/assets/css/patient-transport.css?v=3">
    <style>
        .dashboard-widgets {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary-cards {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .card {
            background-color: #e4f9f8;
            padding: 12px 20px;
            border-radius: 8px;
            color: #299D97;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(41, 157, 151, 0.1);
        }

        .chart-card {
            flex: 1;
            max-width: 240px;
        }
    </style>
</head>
<body>
<div class="page-wrapper">

    <?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

    <div class="container">


<!-- VEHICLE AVAILABILITY WIDGET -->
<div class="vehicle-widget-box">
    <div class="vehicle-widget-inner stacked-layout">

        <!-- Summary cards stacked vertically -->
        <div class="summary-cards-vertical">
            <div class="card">🚑 Ambulance: <?= $counts['Ambulance'] ?? 0 ?></div>
            <div class="card">🚗 Car: <?= $counts['Car'] ?? 0 ?></div>
            <div class="card">🚐 Van: <?= $counts['Van'] ?? 0 ?></div>
        </div>

        <!-- Title + Button + Chart -->
        <div class="right-panel">
        <h2>Need a ride? Book it here!</h2>
<div class="header-actions">
    <a href="/Medceylon/patient/transport/create" class="btn action-btn">+ New Request</a>
    <a href="/Medceylon/patient/transport/report" class="btn action-btn" target="_blank">📄 Download Ride History (PDF)</a>
</div>


    <div class="chart-box">
        <canvas id="vehiclePieChart"></canvas>
    </div>
</div>


    </div>
</div>




        <?php if (empty($requests)): ?>
            <p class="empty">You have not made any requests yet.</p>
        <?php else: ?>
            <table>
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
                        <th>Vehicle No</th>
                        <th>Driver</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $index => $req): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($req['pickup_location']) ?></td>
                            <td><?= htmlspecialchars($req['dropoff_location']) ?></td>
                            <td><?= htmlspecialchars($req['date']) ?></td>
                            <td><?= htmlspecialchars($req['time']) ?></td>
                            <td><?= htmlspecialchars($req['transport_type']) ?></td>
                            <td><?= number_format($req['fare'] ?? 0, 2) ?></td>
                            <td><?= htmlspecialchars($req['status']) ?></td>
                            <td><?= htmlspecialchars($req['vehicle_number'] ?? $req['external_vehicle_number'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($req['driver_name'] ?? $req['external_driver_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($req['contact_number'] ?? $req['external_driver_contact'] ?? '-') ?></td>
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

<!-- CHART SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('vehiclePieChart');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Ambulance', 'Car', 'Van'],
        datasets: [{
            label: 'Available Vehicles',
            data: [<?= $counts['Ambulance'] ?? 0 ?>, <?= $counts['Car'] ?? 0 ?>, <?= $counts['Van'] ?? 0 ?>],
            backgroundColor: ['#4dc9c0', '#36a2eb', '#ffce56'],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
</body>
</html>
