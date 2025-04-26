<?php
namespace App\Controllers;

use App\Helpers\SessionHelper;
use App\Models\CaregiverRequest;
use App\Models\UserModel;

class CaregiverDashboardController {
    private $session;
    private $requestModel;
    private $userModel;

    public function __construct() {
        global $db;
        $this->session = SessionHelper::getInstance();
        $this->requestModel = new CaregiverRequest($db);
        $this->userModel = new UserModel($db);
    }

    public function index() {
        $caregiverId = $this->session->getUserId();
    
        $pendingRequests = $this->requestModel->getRequestsByStatus($caregiverId, 'Pending');
        $acceptedRequests = $this->requestModel->getRequestsByStatus($caregiverId, 'Accepted');
    
        $averageRating = $this->requestModel->getAverageRating($caregiverId); // ⭐
    
        require_once ROOT_PATH . '/app/views/caregiver/dashboard.php';
    }
    

    public function respond($requestId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status']; // 'Accepted' or 'Rejected'
            $this->requestModel->respondToRequest($requestId, $status);
            header("Location: /Medceylon/caregiver/dashboard");
            exit();
        }
    }
}
