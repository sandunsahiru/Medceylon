<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Travel Agent Dashboard</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/agent-transport.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0fdf5;
            color: #1f2d2b;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #299D97;
            color: white;
            padding-top: 40px;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.05);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #e9fef5;
        }

        .tab-link {
            display: block;
            padding: 15px 30px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        .tab-link:hover,
        .tab-link.active {
            background-color: #247c78;
        }

        .main-content {
            flex: 1;
            padding: 40px;
        }

        h2 {
            font-size: 22px;
            margin-bottom: 18px;
            color: #299D97;
            border-left: 5px solid #299D97;
            padding-left: 14px;
            font-weight: 600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0, 128, 96, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #d6eee2;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #e4f9f8;
            font-weight: 600;
            color: #299D97;
        }

        tr:hover {
            background-color: #f1fffd;
        }

        .btn {
            background-color: #299D97;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #247c78;
        }

        .empty {
            font-style: italic;
            color: #7c8b8f;
        }

        .status-accepted {
            color: #299D97;
            font-weight: 600;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: 600;
        }

        .logout-float-btn {
            position: fixed;
            top: 20px;
            right: 30px;
            background-color: #299D97;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(41, 157, 151, 0.2);
            z-index: 1000;
        }

        .logout-float-btn:hover {
            background-color: #247c78;
        }
    </style>
</head>
<body>

<a href="/Medceylon/logout" class="logout-float-btn">Logout</a>

<div class="dashboard">
    <div class="sidebar">
        <h2>Transport Agent Dashboard</h2>
        <div class="tab-link active" onclick="showTab('pending')">Pending</div>
        <div class="tab-link" onclick="showTab('accepted')">Accepted</div>
        <div class="tab-link" onclick="showTab('rejected')">Rejected</div>
    </div>

    <div class="main-content">
        <!-- Pending Tab -->
        <div id="pending" class="tab-content active">
            <h2>Pending Transportation Requests</h2>
            <?php if (empty($pendingRequests)): ?>
                <p class="empty">No pending requests.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th><th>Patient Name</th><th>Email</th><th>Phone</th>
                            <th>Pickup</th><th>Dropoff</th><th>Date</th><th>Time</th><th>Vehicle</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingRequests as $index => $req): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= htmlspecialchars($req['phone_number']) ?></td>
                                <td><?= htmlspecialchars($req['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($req['dropoff_location']) ?></td>
                                <td><?= htmlspecialchars($req['date']) ?></td>
                                <td><?= htmlspecialchars($req['time']) ?></td>
                                <td><?= htmlspecialchars($req['transport_type']) ?></td>
                                <td><a href="/Medceylon/agent/transport/view/<?= $req['transport_request_id'] ?>" class="btn">Respond</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Accepted Tab -->
        <div id="accepted" class="tab-content">
            <h2>Accepted Transportation Requests</h2>
            <?php if (empty($acceptedRequests)): ?>
                <p class="empty">No accepted requests.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th><th>Patient Name</th><th>Email</th><th>Phone</th>
                            <th>Pickup</th><th>Dropoff</th><th>Date</th><th>Time</th><th>Vehicle</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($acceptedRequests as $index => $req): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= htmlspecialchars($req['phone_number']) ?></td>
                                <td><?= htmlspecialchars($req['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($req['dropoff_location']) ?></td>
                                <td><?= htmlspecialchars($req['date']) ?></td>
                                <td><?= htmlspecialchars($req['time']) ?></td>
                                <td><?= htmlspecialchars($req['transport_type']) ?></td>
                                <td class="status-accepted">Accepted</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Rejected Tab -->
        <div id="rejected" class="tab-content">
            <h2>Rejected Transportation Requests</h2>
            <?php if (empty($rejectedRequests)): ?>
                <p class="empty">No rejected requests.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th><th>Patient Name</th><th>Email</th><th>Phone</th>
                            <th>Pickup</th><th>Dropoff</th><th>Date</th><th>Time</th><th>Vehicle</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rejectedRequests as $index => $req): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($req['patient_name']) ?></td>
                                <td><?= htmlspecialchars($req['email']) ?></td>
                                <td><?= htmlspecialchars($req['phone_number']) ?></td>
                                <td><?= htmlspecialchars($req['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($req['dropoff_location']) ?></td>
                                <td><?= htmlspecialchars($req['date']) ?></td>
                                <td><?= htmlspecialchars($req['time']) ?></td>
                                <td><?= htmlspecialchars($req['transport_type']) ?></td>
                                <td class="status-rejected">Rejected</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function showTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        document.querySelector(`.tab-link[onclick*="${tabId}"]`).classList.add('active');
    }
</script>

</body>
</html>
