<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\VPDoctor;

class VPDoctorController extends BaseController
{
    private $vpDoctorModel;

    public function __construct()
    {
        parent::__construct();
        $this->vpDoctorModel = new VPDoctor();
    }

    private function validateDoctorSession()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $roleId = $_SESSION['role_id'] ?? null;

        if (!$userId || $roleId !== 3) {
            header('Location: ' . $this->basePath . '/login');
            exit();
        }
        return $userId;
    }

    public function dashboard()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            $stats = $this->vpDoctorModel->getDashboardStats($doctorId);
            $requests = $this->vpDoctorModel->getNewAppointmentRequests($doctorId);
            $scheduled = $this->vpDoctorModel->getScheduledAppointments($doctorId);

            $data = [
                'stats' => $stats,
                'requests' => $requests,
                'scheduled' => $scheduled,
                'basePath' => $this->basePath,
                'page_title' => 'Specialist Dashboard',
                'current_page' => 'dashboard'
            ];

            echo $this->view('vpdoctor/dashboard', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

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

            $appointmentId = $_POST['appointment_id'] ?? null;
            $status = $_POST['status'] ?? null;
            $newDate = $_POST['new_date'] ?? null;
            $newTime = $_POST['new_time'] ?? null;

            if (!$appointmentId || !$status) {
                throw new \Exception('Missing required parameters');
            }

            $result = $this->vpDoctorModel->updateAppointmentStatus(
                $appointmentId,
                $status,
                $newDate,
                $newTime
            );

            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }

    public function appointments()
    {
        try {
            $doctorId = $this->validateDoctorSession();

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
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];

            echo $this->view('vpdoctor/appointments', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

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
                if (empty($_POST['hospital_id']) || empty($_POST['experience']) || 
                    empty($_POST['qualifications']) || empty($_POST['specializations'])) {
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