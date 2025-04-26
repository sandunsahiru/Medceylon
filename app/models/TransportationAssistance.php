<?php
namespace App\Models;

use mysqli;

class TransportationAssistance {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllByPatient($patientId) {
        $stmt = $this->db->prepare(
            "SELECT t.*, 
                    v.vehicle_number, 
                    v.driver_name, 
                    v.contact_number 
             FROM transportationassistance t
             LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
             WHERE t.patient_id = ?"
        );
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM transportationassistance WHERE transport_request_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO transportationassistance 
        (patient_id, transport_type, pickup_location, dropoff_location, date, time, fare, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    
        $stmt->bind_param(
            "isssssd",
            $data['patient_id'],
            $data['transport_type'],
            $data['pickup_location'],
            $data['dropoff_location'],
            $data['date'],
            $data['time'],
            $data['fare']
        );
    
        $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE transportationassistance 
            SET transport_type = ?, pickup_location = ?, dropoff_location = ?, date = ?, time = ?, fare = ?, last_updated = NOW() 
            WHERE transport_request_id = ? AND status = 'Pending'");
        
        $stmt->bind_param(
            "ssssssi",
            $data['transport_type'],
            $data['pickup_location'],
            $data['dropoff_location'],
            $data['date'],
            $data['time'],
            $data['fare'],
            $id
        );

        return $stmt->execute();
    }

    public function delete($id, $patientId) {
        $stmt = $this->db->prepare("DELETE FROM transportationassistance WHERE transport_request_id = ? AND patient_id = ? AND status = 'Pending'");
        $stmt->bind_param("ii", $id, $patientId);
        return $stmt->execute();
    }

    public function getAllPending() {
        $sql = "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) AS patient_name, u.email, u.phone_number 
                FROM transportationassistance t
                INNER JOIN users u ON t.patient_id = u.user_id
                WHERE t.status = 'Pending'";
        
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function respondToRequest($id, $status, $providerId) {
        $stmt = $this->db->prepare("UPDATE transportationassistance SET status = ?, transport_provider_id = ?, last_updated = NOW() WHERE transport_request_id = ?");
        $stmt->bind_param("sii", $status, $providerId, $id);
        return $stmt->execute();
    } 
    
    public function getByStatus($status) {
        $sql = "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) AS patient_name, u.email, u.phone_number 
                FROM transportationassistance t
                INNER JOIN users u ON t.patient_id = u.user_id
                WHERE t.status = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function assignVehicleToRequest($requestId, $vehicleId) {
        $stmt = $this->db->prepare("UPDATE transportationassistance SET vehicle_id = ? WHERE transport_request_id = ?");
        $stmt->bind_param("ii", $vehicleId, $requestId);
        return $stmt->execute();
    }

    public function markRequestCompleted($id) {
        $stmt = $this->db->prepare("UPDATE transportationassistance SET status = 'Completed', last_updated = NOW() WHERE transport_request_id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }   
    
    public function getAllByPatientWithVehicle($patientId) {
        $stmt = $this->db->prepare(
            "SELECT t.*, v.vehicle_number 
             FROM transportationassistance t
             LEFT JOIN vehicles v ON t.vehicle_id = v.vehicle_id
             WHERE t.patient_id = ?"
        );
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    public function respondToRequestWithVehicle($id, $data) {
        $stmt = $this->db->prepare("UPDATE transportationassistance 
            SET status = ?, transport_provider_id = ?, vehicle_id = ?, 
                external_vehicle_number = ?, external_driver_name = ?, external_driver_contact = ?, 
                last_updated = NOW() 
            WHERE transport_request_id = ?");
    
        $stmt->bind_param(
            "siisssi",
            $data['status'],
            $data['provider_id'],
            $data['vehicle_id'],
            $data['external_vehicle_number'],
            $data['external_driver_name'],
            $data['external_driver_contact'],
            $id
        );
    
        return $stmt->execute();
    }
    
    
    
}
