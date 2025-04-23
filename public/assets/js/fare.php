<?php
require_once __DIR__ . '/../../app/Helpers/DistanceHelper.php';

use App\Helpers\DistanceHelper;

header('Content-Type: application/json');

$pickup = $_GET['pickup'] ?? '';
$dropoff = $_GET['dropoff'] ?? '';
$type = $_GET['type'] ?? 'Car';

if (!$pickup || !$dropoff) {
    echo json_encode(['fare' => 0]);
    exit;
}

$start = DistanceHelper::getCoordinates($pickup);
$end = DistanceHelper::getCoordinates($dropoff);

if (!$start || !$end) {
    echo json_encode(['fare' => 0]);
    exit;
}

$distance = DistanceHelper::calculateDistanceInKm($start['lat'], $start['lon'], $end['lat'], $end['lon']);
$fare = DistanceHelper::calculateFare($distance, $type);

echo json_encode(['fare' => round($fare, 2)]);
