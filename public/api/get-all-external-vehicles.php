<?php
require_once '../../../app/Core/Database.php'; // This stays here inside API file, NOT in respond.php

use App\Core\Database;

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->connect();

    $query = "SELECT external_vehicle_number, status FROM transportationassistance WHERE external_vehicle_number IS NOT NULL";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
