<?php
namespace App\Controllers;

use App\Models\TransportationAssistance;
use App\Helpers\SessionHelper;

class AgentTransportationController {
    private $model;
    private $session;

    public function __construct() {
        global $db;
        $this->model = new TransportationAssistance($db);
        $this->session = SessionHelper::getInstance();
    }

    public function index() {
        $this->session = SessionHelper::getInstance();
        $this->model = new TransportationAssistance($GLOBALS['db']);
    
        $pendingRequests = $this->model->getByStatus('Pending');
        $acceptedRequests = $this->model->getByStatus('Booked');     // or "Accepted" if you use that word
        $rejectedRequests = $this->model->getByStatus('Canceled');   // or "Rejected"
    
        require_once ROOT_PATH . '/app/views/transportation/agent/index.php';
    }
    

    public function view($id) {
        $request = $this->model->getById($id);
        require_once ROOT_PATH . '/app/views/transportation/agent/respond.php';
    }

    public function respond($id) {
        $providerId = $this->session->getUserId();

        // 🧠 Map frontend values to DB ENUMs
        $frontendStatus = $_POST['status'] ?? '';
        $status = match ($frontendStatus) {
            'Accepted' => 'Booked',
            'Rejected' => 'Canceled',
            default => 'Pending'
        };

        $this->model->respondToRequest($id, $status, $providerId);

        header("Location: /Medceylon/agent/transport-requests");
        exit();
    }
}
