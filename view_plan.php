<?php
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Replace with actual authentication
$user_id = 1; 

// Fetch travel plans with their locations
$sql = "SELECT tp.id, tp.plan_name, tp.created_at,
        GROUP_CONCAT(DISTINCT tl.name SEPARATOR ', ') as locations
        FROM travel_plans tp
        LEFT JOIN plan_locations pl ON tp.id = pl.plan_id
        LEFT JOIN travel_locations tl ON pl.location_id = tl.id
        WHERE tp.user_id = $user_id
        GROUP BY tp.id
        ORDER BY tp.created_at DESC";

$result = mysqli_query($conn, $sql);
$plans = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Travel Plans</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">My Travel Plans</h1>

        <?php if (empty($plans)): ?>
            <div class="text-center bg-white p-6 rounded shadow">
                <p class="text-xl text-gray-600 mb-4">No travel plans yet.</p>
                <a href="add_plan.php" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Create Your First Plan
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($plans as $plan): ?>
                    <div class="bg-white p-4 rounded shadow">
                        <h2 class="text-2xl font-semibold"><?= htmlspecialchars($plan['plan_name']) ?></h2>
                        <p class="text-gray-600">Created on: <?= htmlspecialchars($plan['created_at']) ?></p>
                        <p class="mt-2">Locations: <?= htmlspecialchars($plan['locations']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="add_plan.php" class="bg-green-500 text-white px-6 py-3 rounded">
                Create New Plan
            </a>
        </div>
    </div>
</body>
</html>