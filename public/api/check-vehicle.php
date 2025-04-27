<?php
// public/api/check-vehicle.php

require_once '../../../app/config/database.php'; // Adjust if necessary

header('Content-Type: application/json');

if (!isset($_GET['vehicle_number'])) {
    echo json_encode(['error' => 'Vehicle number is required']);
    exit;
}

$vehicleNumber = trim($_GET['vehicle_number']);

try {
    $pdo = Database::connect();
    // First, check if there is a matching internal vehicle
    $stmtVehicle = $pdo->prepare("SELECT vehicle_id FROM vehicles WHERE vehicle_number = :vehicle_number");
    $stmtVehicle->execute(['vehicle_number' => $vehicleNumber]);
    $internalVehicleId = $stmtVehicle->fetchColumn();

    if ($internalVehicleId) {
        // If internal vehicle exists, check using vehicle_id
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM transportationassistance 
            WHERE vehicle_id = :vehicle_id
              AND status IN ('Pending', 'Booked')
        ");
        $stmt->execute(['vehicle_id' => $internalVehicleId]);
    } else {
        // Otherwise, it's an external vehicle
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM transportationassistance 
            WHERE external_vehicle_number = :vehicle_number
              AND status IN ('Pending', 'Booked')
        ");
        $stmt->execute(['vehicle_number' => $vehicleNumber]);
    }

    $count = $stmt->fetchColumn();

    echo json_encode(['in_use' => $count > 0]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?>
