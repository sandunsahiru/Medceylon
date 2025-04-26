<?php
namespace App\Helpers;

class DistanceHelper {
    public static function getCoordinates($place) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($place);

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: MedCeylonApp/1.0\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        return $data[0] ?? null;
    }

    public static function calculateDistanceInKm($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // km
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat/2)**2 + cos($lat1)*cos($lat2)*sin($dlon/2)**2;
        $c = 2 * asin(sqrt($a));
        return $earthRadius * $c;
    }

    public static function calculateFare($distanceKm, $type) {
        $baseFare = [
            'Ambulance' => 150,
            'Car' => 100,
            'Premium Car' => 200,
            'Van' => 120
        ];

        $ratePerKm = [
            'Ambulance' => 100,
            'Car' => 60,
            'Premium Car' => 120,
            'Van' => 80
        ];

        return $baseFare[$type] + ($distanceKm * $ratePerKm[$type]);
    }
}
