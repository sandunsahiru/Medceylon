<?php
namespace App\Controllers;

use App\Models\TransportationAssistance;
use App\Models\Vehicle;
use App\Helpers\SessionHelper;

class AgentTransportationController {
    private $model;
    private $vehicleModel;
    private $session;

    public function __construct() {
        global $db;
        $this->model = new TransportationAssistance($db);
        $this->vehicleModel = new Vehicle($db); 
        $this->session = SessionHelper::getInstance();
    }

    public function index() {
        $pendingRequests = $this->model->getByStatus('Pending');
        $acceptedRequests = $this->model->getByStatus('Booked');
        $rejectedRequests = $this->model->getByStatus('Canceled');

        require_once ROOT_PATH . '/app/views/transportation/agent/index.php';
    }

    public function view($id) {
        $request = $this->model->getById($id);
        $availableVehicles = $this->vehicleModel->getAvailableByType($request['transport_type']);
        require_once ROOT_PATH . '/app/views/transportation/agent/respond.php';
    }

    public function respond($id) {
        $providerId = $this->session->getUserId();
    
        $frontendStatus = $_POST['status'] ?? '';
        $status = match ($frontendStatus) {
            'Accepted' => 'Booked',
            'Rejected' => 'Canceled',
            default => 'Pending'
        };
    
        $vehicleId = $_POST['vehicle_id'] ?? null;
        $externalVehicle = $_POST['external_vehicle_number'] ?? null;
        $driverName = $_POST['external_driver_name'] ?? null;
        $driverContact = $_POST['external_driver_contact'] ?? null;
    
        $this->model->respondToRequestWithVehicle($id, [
            'status' => $status,
            'provider_id' => $providerId,
            'vehicle_id' => ($vehicleId !== 'manual') ? $vehicleId : null,
            'external_vehicle_number' => $externalVehicle,
            'external_driver_name' => $driverName,
            'external_driver_contact' => $driverContact
        ]);
    
        header("Location: /Medceylon/agent/transport-requests");
        exit();
    }
    

    public function complete($id) {
        // 1. Get request to find the vehicle
        $request = $this->model->getById($id);
    
        if (!$request || $request['status'] !== 'Booked') {
            die("Invalid request or already completed.");
        }
    
        // 2. Update status to Completed
        $this->model->markRequestCompleted($id);
    
        // 3. Free the assigned vehicle
        if ($request['vehicle_id']) {
            $this->vehicleModel->freeVehicle($request['vehicle_id']);
        }
    
        header("Location: /Medceylon/agent/transport-requests");
        exit();
    }
    
}
