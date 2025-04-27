<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transportation Ride Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            width: 140px;
            opacity: 0.8;
        }

        h1 {
            text-align: center;
            color: #299D97;
            margin-bottom: 5px;
        }

        p.subhead {
            text-align: center;
            font-style: italic;
            margin-bottom: 30px;
        }

        .meta {
            margin-bottom: 30px;
            font-size: 14px;
        }

        .meta strong {
            color: #299D97;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #e4f9f8;
            color: #299D97;
        }

        .footer {
            margin-top: 60px;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 16px;
            background-color: #299D97;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
        }

        .btn:hover {
            background-color: #247c78;
        }
    </style>
</head>
<body>

<a href="#" class="btn no-print" onclick="window.print()">🖨️ Print or Save as PDF</a>

<div class="logo">
    <img src="/Medceylon/public/assets/images/logo.png" alt="MedCeylon Logo"> <!-- Change this path if needed -->
</div>

<h1>Transportation Ride Report</h1>
<p class="subhead">Generated for insurance claim purposes</p>

<div class="meta">
    <p><strong>Patient:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
    <p><strong>Report Issued:</strong> <?= date('F j, Y') ?></p>
    <p><strong>Reference ID:</strong> MED-RPT-<?= date('Ymd') ?>-<?= $_SESSION['user_id'] ?></p>
</div>

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
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rides as $index => $ride): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($ride['pickup_location']) ?></td>
                <td><?= htmlspecialchars($ride['dropoff_location']) ?></td>
                <td><?= $ride['date'] ?></td>
                <td><?= $ride['time'] ?></td>
                <td><?= $ride['vehicle_number'] ?? '-' ?></td>
                <td><?= number_format($ride['fare'], 2) ?></td>
                <td><?= $ride['status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="footer">
    <p>Signature: ___________________________</p>
</div>

</body>
</html>
