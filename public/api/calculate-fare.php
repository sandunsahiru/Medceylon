<?php
require_once '../../app/helpers/DistanceHelper.php';

use App\Helpers\DistanceHelper;

$pickup = $_GET['pickup'] ?? '';
$dropoff = $_GET['dropoff'] ?? '';
$type = $_GET['type'] ?? 'Car';

$pickupData = DistanceHelper::getCoordinates($pickup);
$dropoffData = DistanceHelper::getCoordinates($dropoff);

if ($pickupData && $dropoffData) {
    $distance = DistanceHelper::calculateDistanceInKm(
        $pickupData['lat'], $pickupData['lon'],
        $dropoffData['lat'], $dropoffData['lon']
    );
    $fare = DistanceHelper::calculateFare($distance, $type);
    echo json_encode(['fare' => $fare]);
} else {
    echo json_encode(['fare' => 0]);
}
