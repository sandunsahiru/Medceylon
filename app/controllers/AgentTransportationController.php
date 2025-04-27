<?php
namespace App\Controllers;

use App\Models\TransportationAssistance;
use App\Models\Vehicle;
use App\Helpers\SessionHelper;

class AgentTransportationController {
    private $model;
    private $vehicleModel;
    private $session;
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
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
    
        // 🚀 NEW: Fetch occupied external vehicles correctly for MySQLi
        $occupiedVehicles = [];
        try {
            $stmt = $this->db->prepare("SELECT external_vehicle_number, status FROM transportationassistance WHERE external_vehicle_number IS NOT NULL");
            $stmt->execute();
            $result = $stmt->get_result(); 
            while ($vehicle = $result->fetch_assoc()) {
                if (strtolower($vehicle['status']) !== 'completed') {
                    $occupiedVehicles[] = strtolower(trim($vehicle['external_vehicle_number']));
                }
            }
        } catch (\Exception $e) {
            echo "Database error: " . $e->getMessage();
            exit;
        }
    
        require ROOT_PATH . '/app/views/transportation/agent/respond.php';
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

        // 🔥 Double security: Check if external vehicle is already occupied
        if ($vehicleId === 'manual' && $externalVehicle) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM transportationassistance WHERE external_vehicle_number = ? AND status != 'Completed'");
            if (!$stmt) {
                die("Database error: " . $this->db->error);
            }
            $stmt->bind_param("s", $externalVehicle);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $_SESSION['error'] = "❌ External vehicle '$externalVehicle' is already occupied. Please use another one.";
                
                // 👉 Instead of header redirect, just re-load view manually:
                $this->view($id);
                exit();
            }
            
        }

        // ✅ If all clear, proceed
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
        $request = $this->model->getById($id);
    
        if (!$request || $request['status'] !== 'Booked') {
            die("Invalid request or already completed.");
        }
    
        $this->model->markRequestCompleted($id);
    
        if ($request['vehicle_id']) {
            $this->vehicleModel->freeVehicle($request['vehicle_id']);
        }
    
        header("Location: /Medceylon/agent/transport-requests");
        exit();
    }
}
