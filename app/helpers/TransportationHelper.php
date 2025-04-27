<?php
// app/Helpers/TransportationHelper.php

namespace App\Helpers;

use App\Core\Database;
use PDO;
use DateTime;

class TransportationHelper
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function assignAvailableVehicleAndBookTransportation($transport_request_id, $durationHours)
    {
        // Step 1: Find the transport request
        $stmt = $this->db->prepare("SELECT * FROM transportationassistance WHERE transport_request_id = ?");
        $stmt->execute([$transport_request_id]);
        $transportRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transportRequest) {
            return false; // Transport request not found
        }

        // Step 2: Calculate fare
        $ratePerHour = 1000; // Example: 1000 LKR per hour. Change this as needed
        $fare = $durationHours * $ratePerHour;

        // Step 3: Find an available vehicle
        $vehicleStmt = $this->db->prepare("SELECT * FROM vehicles WHERE status = 'available' LIMIT 1");
        $vehicleStmt->execute();
        $vehicle = $vehicleStmt->fetch(PDO::FETCH_ASSOC);

        if ($vehicle) {
            $vehicle_id = $vehicle['vehicle_id'];

            // Step 4: Update transportationassistance
            $updateStmt = $this->db->prepare("
                UPDATE transportationassistance
                SET fare = ?, vehicle_id = ?, status = 'Booked', last_updated = NOW()
                WHERE transport_request_id = ?
            ");
            $updateStmt->execute([$fare, $vehicle_id, $transport_request_id]);

            // Step 5: Update vehicle status to booked
            $vehicleUpdateStmt = $this->db->prepare("
                UPDATE vehicles
                SET status = 'booked'
                WHERE vehicle_id = ?
            ");
            $vehicleUpdateStmt->execute([$vehicle_id]);

            return true;
        } else {
            // No vehicles available, fallback logic (optional)
            return false;
        }
    }

    /**
     * Releases the vehicle after the trip date
     */
    public function releaseVehicleAfterTrip($transport_request_id)
    {
        // Step 1: Get transport request
        $stmt = $this->db->prepare("SELECT * FROM transportationassistance WHERE transport_request_id = ?");
        $stmt->execute([$transport_request_id]);
        $transportRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transportRequest) {
            return false; // Not found
        }

        $tripDate = $transportRequest['date'];
        $tripTime = $transportRequest['time'];
        $vehicle_id = $transportRequest['vehicle_id'];

        if (!$vehicle_id) {
            return false; // No assigned vehicle
        }

        // Step 2: Check if current datetime > trip datetime
        $now = new DateTime();
        $tripDateTime = new DateTime($tripDate . ' ' . $tripTime);

        if ($now > $tripDateTime) {
            // Step 3: Set vehicle available again
            $vehicleUpdateStmt = $this->db->prepare("
                UPDATE vehicles
                SET status = 'available'
                WHERE vehicle_id = ?
            ");
            $vehicleUpdateStmt->execute([$vehicle_id]);

            // Step 4: Optionally update trip status to Completed
            $tripUpdateStmt = $this->db->prepare("
                UPDATE transportationassistance
                SET status = 'Completed', last_updated = NOW()
                WHERE transport_request_id = ?
            ");
            $tripUpdateStmt->execute([$transport_request_id]);

            return true;
        } else {
            // Trip still not completed
            return false;
        }
    }
}
