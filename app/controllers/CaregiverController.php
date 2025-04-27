<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CaregiverRequest;
use App\Helpers\SessionHelper;
use App\Models\CaregiverRating;

class CaregiverController {
    private $userModel;
    private $requestModel;
    private $session;

    public function __construct() {
        global $db;
        $this->userModel = new UserModel($db);
        $this->requestModel = new CaregiverRequest($db);
        $this->session = SessionHelper::getInstance();
    }

    // 🟢 Patient views all caregivers
    public function list() {
        $filter = $_GET['filter'] ?? null;
        $sort = $_GET['sort'] ?? null;
        $patientId = $this->session->getUserId();
    
        $caregivers = $this->userModel->getAllCaregivers($filter, $sort);
    
        // Mark whether already requested
        foreach ($caregivers as &$caregiver) {
            $caregiver['already_requested'] = $this->requestModel->hasPendingRequest($patientId, $caregiver['user_id']);
        }
    
        require_once ROOT_PATH . '/app/views/caregiver/list.php';
    }

    // 🟢 Patient sends caregiver request
    public function request($caregiverId) {
        $patientId = $this->session->getUserId(); // 🧠 Get logged-in patient
        $this->requestModel->sendRequest($patientId, $caregiverId); // 🧠 Correct order!
        header("Location: /Medceylon/caregivers");
        exit();
    }

    // 🟢 Caregiver views their own dashboard
    public function dashboard() {
        $caregiverId = $this->session->getUserId();
        $requests = $this->requestModel->getRequestsForCaregiver($caregiverId);
        require_once ROOT_PATH . '/app/views/caregiver/dashboard.php';
    }

    // 🟢 Caregiver accepts/rejects requests
    public function respond($requestId, $action) {
        if (!in_array($action, ['Accepted', 'Rejected'])) {
            die('Invalid action');
        }
        $this->requestModel->respondToRequest($requestId, $action);
        header("Location: /Medceylon/caregiver/dashboard");
        exit();
    }

    // 🟢 View popup of caregiver profile
    public function profile($caregiverId) {
        $caregiver = $this->userModel->getUserById($caregiverId);
        $reviews = (new CaregiverRating($GLOBALS['db']))->getReviews($caregiverId);
        require_once ROOT_PATH . '/app/views/caregiver/profile-popup.php';
    }
}
