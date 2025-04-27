<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VPDoctor;
use App\Models\MedicalSession;
use App\Models\Appointment;

class VPDoctorController extends BaseController
{
    private $vpDoctorModel;
    private $medicalSessionModel;
    private $appointmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->vpDoctorModel = new VPDoctor();
        $this->medicalSessionModel = new MedicalSession();
        $this->appointmentModel = new Appointment();
    }

    /**
     * Validate doctor session and return doctor_id
     */
    private function validateDoctorSession()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $roleId = $_SESSION['role_id'] ?? null;

            if (!$userId) {
                error_log("No user_id in session");
                header('Location: ' . $this->basePath . '/login');
                exit();
            }

            // Get doctor_id from the users table using user_id
            $doctorData = $this->vpDoctorModel->getDoctorIdFromUserId($userId);

            if (!$doctorData) {
                error_log("No doctor found for user_id: " . $userId);
                header('Location: ' . $this->basePath . '/login');
                exit();
            }

            return $doctorData['doctor_id'];
        } catch (\Exception $e) {
            error_log("Error in validateDoctorSession: " . $e->getMessage());
            header('Location: ' . $this->basePath . '/login');
            exit();
        }
    }

    /**
     * Dashboard page showing appointments and stats
     */
    public function dashboard()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            // Get dashboard stats
            $stats = $this->vpDoctorModel->getDashboardStats($doctorId);
            
            // Get appointments categorized into referral and regular
            $referralAppointments = $this->vpDoctorModel->getReferralAppointments($doctorId);
            $regularAppointments = $this->vpDoctorModel->getRegularAppointments($doctorId);

            $data = [
                'stats' => $stats,
                'referralAppointments' => $referralAppointments,
                'regularAppointments' => $regularAppointments,
                'basePath' => $this->basePath,
                'page_title' => 'Specialist Dashboard',
                'current_page' => 'dashboard',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];

            echo $this->view('vpdoctor/dashboard', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * View detailed patient session
     */
    public function session($sessionId)
    {
        try {
            $doctorId = $this->validateDoctorSession();
            
            // Ensure session ID is valid
            $sessionId = (int)$sessionId;
            if ($sessionId <= 0) {
                throw new \Exception("Invalid session ID");
            }
            
            // Check if session exists
            $session = $this->medicalSessionModel->getById($sessionId);
            if (!$session) {
                throw new \Exception("Medical session not found");
            }
            
            // Get patient information
            $patientId = $session['patient_id'];
            $patientInfo = $this->vpDoctorModel->getPatientBasicInfo($patientId);
            
            if (!$patientInfo) {
                throw new \Exception("Patient information not found");
            }
            
            // Get session data
            $sessionData = $this->medicalSessionModel->getFormattedSessionData($sessionId, $patientId);
            
            // Get appointment data
            $appointmentData = $this->appointmentModel->getSessionAppointmentData($sessionId, $doctorId);
            
            // Get medical records
            $medicalRecords = $this->vpDoctorModel->getPatientMedicalReports($patientId, $doctorId);
            
            // Check if current doctor is the specialist for this session
            $isSpecialist = false;
            if (isset($sessionData['specialist']) && $sessionData['specialist']) {
                $isSpecialist = ($sessionData['specialist']['id'] == $doctorId);
            }
            
            // Prepare data for the view
            $data = [
                'patientInfo' => $patientInfo,
                'sessionData' => $sessionData,
                'appointmentData' => $appointmentData,
                'medicalRecords' => $medicalRecords,
                'isSpecialist' => $isSpecialist,
                'basePath' => $this->basePath,
                'page_title' => 'Patient Session',
                'current_page' => 'patients',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];
            
            echo $this->view('vpdoctor/session', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in session method: " . $e->getMessage());
            $this->session->setFlash('error', 'Error loading patient session: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            exit();
        }
    }

    /**
     * Get appointment details via AJAX
     */
    public function getAppointmentDetails()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            $appointmentId = filter_input(INPUT_GET, 'appointment_id', FILTER_VALIDATE_INT);

            if (!$appointmentId) {
                throw new \Exception('Invalid appointment ID');
            }

            $details = $this->vpDoctorModel->getAppointmentDetails($appointmentId, $doctorId);

            if (!$details) {
                throw new \Exception('Appointment not found');
            }

            // Check if there's a session ID
            $sessionId = $details['session_id'] ?? null;
            
            // If there's a session ID, get session data
            $sessionData = null;
            if ($sessionId) {
                $sessionData = $this->medicalSessionModel->getById($sessionId);
                $details['session_data'] = $sessionData;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $details
            ]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }

    /**
     * Ajax endpoint to update appointment status
     */
    public function updateAppointmentStatus()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception('Invalid security token');
            }

            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

            if (!$appointmentId || !$status) {
                throw new \Exception('Missing required parameters');
            }

            // Get optional parameters
            $newDate = filter_input(INPUT_POST, 'new_date', FILTER_SANITIZE_STRING);
            $newTime = filter_input(INPUT_POST, 'new_time', FILTER_SANITIZE_STRING);

            // Validate status
            $validStatuses = ['Asked', 'Scheduled', 'Rescheduled', 'Completed', 'Canceled'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception('Invalid status value');
            }

            // Additional validation for rescheduling
            if ($status === 'Rescheduled' && (!$newDate || !$newTime)) {
                throw new \Exception('New date and time required for rescheduling');
            }

            // Update the appointment status
            $result = $this->vpDoctorModel->updateAppointmentStatus(
                $appointmentId,
                $status,
                $newDate,
                $newTime
            );

            if (!$result) {
                throw new \Exception('Failed to update appointment status');
            }

            // Return success response
            ob_clean(); // Clear any output buffers
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Appointment status updated successfully'
            ]);
            exit();
        } catch (\Exception $e) {
            // Log the error
            error_log("Error in updateAppointmentStatus: " . $e->getMessage());

            // Return error response
            ob_clean(); // Clear any output buffers
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }

    /**
     * Save specialist notes
     */
    public function saveSpecialistNotes()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Check CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            $notes = $_POST['specialist_notes'] ?? '';
            
            if (!$sessionId) {
                throw new \Exception('Invalid session ID');
            }
            
            // Save notes to medical_sessions table
            $result = $this->medicalSessionModel->updateSpecialistNotes($sessionId, $notes);
            
            if (!$result) {
                throw new \Exception("Failed to save specialist notes");
            }
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Specialist notes saved successfully'
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('success', 'Specialist notes saved successfully');
            
            // Redirect back to appropriate page
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header('Location: ' . $_POST['redirect_url']);
            } else {
                header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in saveSpecialistNotes: " . $e->getMessage());
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('error', 'Error saving specialist notes: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            exit();
        }
    }
    
    public function createTreatmentPlan()
{
    try {
        $doctorId = $this->validateDoctorSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('Invalid request method');
        }
        
        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
        $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
        
        if (!$sessionId || !$appointmentId) {
            throw new \Exception('Invalid session or appointment ID');
        }
        
        // Get required fields
        $travelRestrictions = $_POST['travel_restrictions'] ?? 'None';
        $vehicleType = $_POST['vehicle_type'] ?? 'Regular Vehicle';
        $arrivalDeadline = $_POST['arrival_deadline'] ?? null;
        $treatmentDescription = $_POST['treatment_description'] ?? '';
        $estimatedBudget = filter_input(INPUT_POST, 'estimated_budget', FILTER_VALIDATE_FLOAT);
        $estimatedDuration = filter_input(INPUT_POST, 'estimated_duration', FILTER_VALIDATE_INT);
        
        if (empty($treatmentDescription)) {
            throw new \Exception("Treatment description is required");
        }
        
        if (!$estimatedBudget || !$estimatedDuration) {
            throw new \Exception("Budget and duration are required");
        }
        
        $treatmentData = [
            'session_id' => $sessionId,
            'doctor_id' => $doctorId,
            'travel_restrictions' => $travelRestrictions,
            'vehicle_type' => $vehicleType,
            'arrival_deadline' => $arrivalDeadline,
            'treatment_description' => $treatmentDescription,
            'estimated_budget' => $estimatedBudget,
            'estimated_duration' => $estimatedDuration,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Create treatment plan
        $planId = $this->vpDoctorModel->createTreatmentPlan($treatmentData);
        
        if (!$planId) {
            throw new \Exception("Failed to create treatment plan");
        }
        
        // Update the medical session with the treatment plan ID
        $updateResult = $this->medicalSessionModel->update([
            'session_id' => $sessionId,
            'status' => 'Active',
            'treatment_plan_id' => $planId,
            'treatment_plan_created' => 1
        ]);
        
        if (!$updateResult) {
            throw new \Exception("Failed to update session with treatment plan");
        }
        
        // Update the appointment status
        $this->appointmentModel->updateWithTreatmentPlan($appointmentId, $planId);
        
        // Success response
        $this->session->setFlash('success', 'Treatment plan created successfully');
        header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
        exit();
        
    } catch (\Exception $e) {
        error_log("Error in createTreatmentPlan: " . $e->getMessage());
        
        // Error response
        $this->session->setFlash('error', 'Error creating treatment plan: ' . $e->getMessage());
        header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
        exit();
    }
}
    
    /**
     * Update treatment plan
     */
    public function updateTreatmentPlan()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Check CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            $planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
            
            if (!$sessionId) {
                // If plan_id is provided directly, use it
                if (!$planId) {
                    // Get session to retrieve the treatment plan ID
                    $session = $this->medicalSessionModel->getById($sessionId);
                    if (!$session || !$session['treatment_plan_id']) {
                        throw new \Exception("Treatment plan not found");
                    }
                    
                    $planId = $session['treatment_plan_id'];
                }
            }
            
            // Get required fields
            $travelRestrictions = $_POST['travel_restrictions'] ?? 'None';
            $vehicleType = $_POST['vehicle_type'] ?? 'Regular Vehicle';
            $arrivalDeadline = $_POST['arrival_deadline'] ?? null;
            $treatmentDescription = $_POST['treatment_description'] ?? '';
            $estimatedBudget = filter_input(INPUT_POST, 'estimated_budget', FILTER_VALIDATE_FLOAT);
            $estimatedDuration = filter_input(INPUT_POST, 'estimated_duration', FILTER_VALIDATE_INT);
            
            if (empty($treatmentDescription)) {
                throw new \Exception("Treatment description is required");
            }
            
            if (!$estimatedBudget || !$estimatedDuration) {
                throw new \Exception("Budget and duration are required");
            }
            
            // Update the treatment plan
            $updateData = [
                'plan_id' => $planId,
                'travel_restrictions' => $travelRestrictions,
                'vehicle_type' => $vehicleType,
                'arrival_deadline' => $arrivalDeadline,
                'treatment_description' => $treatmentDescription,
                'estimated_budget' => $estimatedBudget,
                'estimated_duration' => $estimatedDuration,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->vpDoctorModel->updateTreatmentPlan($updateData);
            
            if (!$result) {
                throw new \Exception("Failed to update treatment plan");
            }
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Treatment plan updated successfully'
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('success', 'Treatment plan updated successfully');
            
            // Redirect based on context
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header('Location: ' . $_POST['redirect_url']);
            } else {
                header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in updateTreatmentPlan: " . $e->getMessage());
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('error', 'Error updating treatment plan: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            exit();
        }
    }
    
    /**
     * Request medical tests
     */
    public function requestMedicalTests()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Check CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            $patientId = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
            
            if (!$sessionId || !$patientId) {
                throw new \Exception('Session ID and patient ID are required');
            }
            
            $testType = $_POST['test_type'] ?? '';
            $testDescription = $_POST['test_description'] ?? '';
            $requiresFasting = isset($_POST['requires_fasting']) && $_POST['requires_fasting'] === 'yes';
            $urgency = $_POST['urgency'] ?? 'routine';
            
            if (empty($testType) || empty($testDescription)) {
                throw new \Exception('Test type and description are required');
            }
            
            // Insert into medical_tests table
            $insertData = [
                'session_id' => $sessionId, 
                'patient_id' => $patientId, 
                'doctor_id' => $doctorId, 
                'appointment_id' => $appointmentId,
                'test_type' => $testType, 
                'test_description' => $testDescription, 
                'requires_fasting' => $requiresFasting ? 1 : 0, 
                'urgency' => $urgency,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $result = $this->vpDoctorModel->requestMedicalTest($insertData);
            
            if (!$result) {
                throw new \Exception("Failed to request medical test");
            }
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Medical test requested successfully'
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('success', 'Medical test requested successfully');
            
            // Redirect based on context
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header('Location: ' . $_POST['redirect_url']);
            } else {
                header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in requestMedicalTests: " . $e->getMessage());
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('error', 'Error requesting medical test: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            exit();
        }
    }
    
    /**
     * Cancel treatment
     */
    public function cancelTreatment()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Check CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $sessionId = filter_input(INPUT_POST, 'session_id', FILTER_VALIDATE_INT);
            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            
            if (!$sessionId) {
                throw new \Exception('Session ID is required');
            }
            
            $cancelReason = $_POST['cancel_reason'] ?? 'Treatment no longer required';
            
            if (empty($cancelReason)) {
                throw new \Exception('Cancellation reason is required');
            }
            
            // Update session status to canceled
            $result = $this->medicalSessionModel->cancelSession($sessionId, $cancelReason);
            
            if (!$result) {
                throw new \Exception("Failed to cancel treatment");
            }
            
            // Also update appointment status if appointment ID is provided
            if ($appointmentId) {
                $this->appointmentModel->updateStatus($appointmentId, 'Canceled');
            }
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Treatment has been canceled successfully'
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('success', 'Treatment has been canceled successfully');
            
            // Redirect based on context
            if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
                header('Location: ' . $_POST['redirect_url']);
            } else {
                header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in cancelTreatment: " . $e->getMessage());
            
            // Handle AJAX request
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
                exit();
            }
            
            // Handle regular form submission
            $this->session->setFlash('error', 'Error canceling treatment: ' . $e->getMessage());
            header('Location: ' . $this->basePath . '/vpdoctor/dashboard');
            exit();
        }
    }

    /**
     * Get patient medical reports
     */
    public function getPatientMedicalReports()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            $patientId = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);

            if (!$patientId) {
                throw new \Exception('Invalid patient ID');
            }

            $reports = $this->vpDoctorModel->getPatientMedicalReports($patientId, $doctorId);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $reports
            ]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }

    /**
     * Appointments management page
     */
    public function appointments()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            // Add debug information
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                $debug = $this->vpDoctorModel->debugAppointments($doctorId);
                error_log("Appointments Debug Info: " . json_encode($debug));
            }

            $newAppointments = $this->vpDoctorModel->getAppointmentsByStatus($doctorId, 'Asked');
            $scheduledAppointments = $this->vpDoctorModel->getAppointmentsByStatus($doctorId, 'Scheduled');
            $rescheduledAppointments = $this->vpDoctorModel->getAppointmentsByStatus($doctorId, 'Rescheduled');
            $availability = $this->vpDoctorModel->getDoctorAvailability($doctorId);

            $data = [
                'new_appointments' => $newAppointments,
                'scheduled_appointments' => $scheduledAppointments,
                'rescheduled_appointments' => $rescheduledAppointments,
                'availability' => $availability,
                'basePath' => $this->basePath,
                'page_title' => 'Appointments',
                'current_page' => 'appointments',
                'csrfToken' => $_SESSION['csrf_token'] ?? '',
                'debug_info' => defined('DEBUG_MODE') && DEBUG_MODE ? $debug : null
            ];

            echo $this->view('vpdoctor/appointments', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Manage doctor availability
     */
    public function manageAvailability()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception('Invalid security token');
            }

            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'add_availability':
                    $result = $this->vpDoctorModel->addAvailability(
                        $doctorId,
                        $_POST['day_of_week'],
                        $_POST['start_time'],
                        $_POST['end_time']
                    );
                    break;

                case 'edit_availability':
                    $result = $this->vpDoctorModel->editAvailability(
                        $_POST['availability_id'],
                        $doctorId,
                        $_POST['day_of_week'],
                        $_POST['start_time'],
                        $_POST['end_time']
                    );
                    break;

                case 'delete_availability':
                    $result = $this->vpDoctorModel->deleteAvailability(
                        $_POST['availability_id'],
                        $doctorId
                    );
                    break;

                default:
                    throw new \Exception('Invalid action');
            }

            if ($result) {
                $this->session->setFlash('success', 'Availability updated successfully');
            } else {
                $this->session->setFlash('error', 'Failed to update availability');
            }

            header('Location: ' . $this->basePath . '/vpdoctor/appointments');
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Patients listing page
     */
    public function patients()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            $stats = $this->vpDoctorModel->getPatientStats($doctorId);
            $patients = $this->vpDoctorModel->getDoctorPatients($doctorId);

            $data = [
                'stats' => $stats,
                'patients' => $patients,
                'basePath' => $this->basePath,
                'page_title' => 'Patients',
                'current_page' => 'patients',
                'doctorId' => $doctorId
            ];

            echo $this->view('vpdoctor/patients', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Get patient details via AJAX
     */
    public function getPatientDetails()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            $patientId = $_GET['patient_id'] ?? 0;

            if (!$patientId) {
                throw new \Exception('Invalid patient ID');
            }

            $details = $this->vpDoctorModel->getPatientDetails($patientId, $doctorId);

            header('Content-Type: application/json');
            echo json_encode($details);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    /**
     * Doctor profile page
     */
    public function profile()
    {
        try {
            $userId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                        throw new \Exception('Invalid security token');
                    }

                    // Validate required fields
                    if (
                        empty($_POST['hospital_id']) || empty($_POST['experience']) ||
                        empty($_POST['qualifications']) || empty($_POST['specializations'])
                    ) {
                        throw new \Exception('Please fill in all required fields');
                    }

                    $profileData = [
                        'qualifications' => $_POST['qualifications'],
                        'experience' => intval($_POST['experience']),
                        'description' => $_POST['description'] ?? '',
                        'hospital_id' => intval($_POST['hospital_id']),
                        'specializations' => $_POST['specializations'] ?? []
                    ];

                    // Get doctor profile to get doctor_id
                    $profile = $this->vpDoctorModel->getDoctorProfile($userId);
                    if (!$profile) {
                        throw new \Exception('Failed to load doctor profile');
                    }

                    // Update the profile
                    $this->vpDoctorModel->updateProfile($profile['doctor_id'], $profileData);

                    // Set success message
                    $_SESSION['success_message'] = "Profile updated successfully!";

                    // Redirect to refresh the page
                    header("Location: " . $this->basePath . "/vpdoctor/profile");
                    exit();
                } catch (\Exception $e) {
                    $_SESSION['error_message'] = $e->getMessage();
                    header("Location: " . $this->basePath . "/vpdoctor/profile");
                    exit();
                }
            }

            // GET request - display profile
            $profile = $this->vpDoctorModel->getDoctorProfile($userId);
            if (!$profile) {
                throw new \Exception('Failed to load doctor profile');
            }

            $hospitals = $this->vpDoctorModel->getHospitals();
            $specializations = $this->vpDoctorModel->getSpecializations();
            $doctor_specializations = $this->vpDoctorModel->getDoctorSpecializations($profile['doctor_id']);

            $data = [
                'profile' => $profile,
                'hospitals' => $hospitals,
                'specializations' => $specializations,
                'doctor_specializations' => $doctor_specializations,
                'basePath' => $this->basePath,
                'page_title' => 'Specialist Profile',
                'current_page' => 'profile',
                'csrfToken' => $_SESSION['csrf_token'] ?? '',
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];

            echo $this->view('vpdoctor/profile', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Appointment action (complete, cancel)
     */
    public function appointmentAction()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception('Invalid security token');
            }

            $appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
            $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

            if (!$appointmentId || !$action) {
                throw new \Exception('Missing required parameters');
            }

            $result = false;
            $message = '';

            switch ($action) {
                case 'complete':
                    $result = $this->vpDoctorModel->completeAppointment($appointmentId, $doctorId);
                    $message = 'Appointment completed successfully';
                    break;
                case 'cancel':
                    $reason = $_POST['reason'] ?? 'Canceled by specialist';
                    $result = $this->vpDoctorModel->cancelAppointment($appointmentId, $doctorId, $reason);
                    $message = 'Appointment canceled successfully';
                    break;
                default:
                    throw new \Exception('Invalid action');
            }

            if (!$result) {
                throw new \Exception('Failed to perform action');
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $message
            ]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }

    /**
     * Error handler
     */
    private function handleError(\Exception $e)
    {
        error_log("VPDoctor Controller Error: " . $e->getMessage());

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => 'An error occurred. Please try again later.',
                'debug' => DEBUG_MODE ? $e->getMessage() : null
            ]);
            exit();
        }

        $data = [
            'message' => DEBUG_MODE ? $e->getMessage() : 'An error occurred. Please try again later.',
            'basePath' => $this->basePath,
            'page_title' => 'Error',
            'current_page' => 'error'
        ];

        echo $this->view('errors/error', $data);
        exit();
    }
}