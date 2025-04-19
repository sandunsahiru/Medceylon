<?php

namespace App\Controllers;

use App\Models\Hospital;

class HospitalController extends BaseController
{
    private $hospitalModel;

    public function __construct()
    {
        parent::__construct();
        $this->hospitalModel = new Hospital();

        $publicMethods = ['hospitals'];

        $currentMethod = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];

        if (!in_array($currentMethod, $publicMethods)) {
            $this->validateAccess();
        }
        
    }

    private function validateAccess()
    {
        if (!$this->session->isLoggedIn()) {
            header('Location: ' . $this->url('login'));
            exit();
        }

        if ($this->session->getUserRole() !== 6) { // 6 is hospital admin role
            header('Location: ' . $this->url('unauthorized'));
            exit();
        }
    }

    // Dashboard Methods
    public function dashboard()
    {
        try {
            error_log("Entering hospital dashboard method");
            $userId = $this->session->getUserId();
            
            $totalData = $this->hospitalModel->getRequestStatistics();
            $requests = $this->hospitalModel->getLatestRequests(5);
            
            $data = [
                'pageTitle' => 'Hospital Dashboard',
                'currentPage' => 'dashboard',
                'totalData' => $totalData,
                'requests' => $requests,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            
            echo $this->view('hospital/dashboard', $data);
        } catch (\Exception $e) {
            error_log("Error in hospital dashboard: " . $e->getMessage());
            $this->session->setFlash('error', 'An error occurred while loading the dashboard');
            throw $e;
        }
    }

    public function hospitals()
    {
        try {
            error_log("Starting hospitals view");
            $hospitals = $this->hospitalModel->getAllHospitals();

            if  (!$hospitals || count($hospitals) === 0) {
                error_log("No hospitals found");
                $this->session->setFlash('error', 'No hospitals available');
            }

            $data = [
                'pageTitle' => 'Hospitals',
                'currentPage' => 'partner-hospitals',
                'hospitals' => $hospitals,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            echo $this->view('hospital/partner-hospitals', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in hospitals: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
        
    }

    // Department Methods
    public function departments()
    {
        try {
            $departments = $this->hospitalModel->getAllDepartments();
            $doctors = $this->hospitalModel->getAllDoctors(); // For head doctor selection
            
            $data = [
                'pageTitle' => 'Departments',
                'currentPage' => 'departments',
                'departments' => $departments,
                'doctors' => $doctors,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            
            echo $this->view('hospital/departments', $data);
        } catch (\Exception $e) {
            error_log("Error in departments: " . $e->getMessage());
            $this->session->setFlash('error', 'An error occurred while loading departments');
            throw $e;
        }
    }

    public function saveDepartment()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $departmentData = [
                'department_name' => $_POST['department_name'],
                'description' => $_POST['description'],
                'head_doctor' => $_POST['head_doctor'],
                'updated_by' => $this->session->getUserId()
            ];

            if (!empty($_POST['department_id'])) {
                $this->hospitalModel->updateDepartment($_POST['department_id'], $departmentData);
                $message = 'Department updated successfully';
            } else {
                $this->hospitalModel->createDepartment($departmentData);
                $message = 'Department created successfully';
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            error_log("Error in saveDepartment: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDepartmentDetails()
    {
        try {
            $departmentId = $_GET['id'] ?? 0;
            $details = $this->hospitalModel->getDepartmentDetails($departmentId);
            
            header('Content-Type: application/json');
            echo json_encode($details);
        } catch (\Exception $e) {
            error_log("Error in getDepartmentDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteDepartment()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $departmentId = $_POST['department_id'];
            $this->hospitalModel->deleteDepartment($departmentId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
        } catch (\Exception $e) {
            error_log("Error in deleteDepartment: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Doctor Methods
    public function doctors()
    {
        try {
            $doctors = $this->hospitalModel->getAllDoctors();
            $departments = $this->hospitalModel->getAllDepartments();
            
            $data = [
                'pageTitle' => 'Doctors',
                'currentPage' => 'doctors',
                'doctors' => $doctors,
                'departments' => $departments,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            
            echo $this->view('hospital/doctors', $data);
        } catch (\Exception $e) {
            error_log("Error in doctors: " . $e->getMessage());
            $this->session->setFlash('error', 'An error occurred while loading doctors');
            throw $e;
        }
    }

    public function saveDoctor()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $doctorData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'specialization' => $_POST['specialization'],
                'license_number' => $_POST['license_number'],
                'department_id' => $_POST['department_id'],
                'updated_by' => $this->session->getUserId()
            ];

            if (!empty($_POST['doctor_id'])) {
                $this->hospitalModel->updateDoctor($_POST['doctor_id'], $doctorData);
                $message = 'Doctor updated successfully';
            } else {
                $this->hospitalModel->createDoctor($doctorData);
                $message = 'Doctor created successfully';
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            error_log("Error in saveDoctor: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDoctorDetails()
    {
        try {
            $doctorId = $_GET['id'] ?? 0;
            $details = $this->hospitalModel->getDoctorDetails($doctorId);
            
            header('Content-Type: application/json');
            echo json_encode($details);
        } catch (\Exception $e) {
            error_log("Error in getDoctorDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function saveDoctorSchedule()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $doctorId = $_POST['doctor_id'];
            $scheduleData = $_POST['schedule'];
            
            $this->hospitalModel->updateDoctorSchedule($doctorId, $scheduleData);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Schedule updated successfully']);
        } catch (\Exception $e) {
            error_log("Error in saveDoctorSchedule: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getDoctorSchedule()
    {
        try {
            $doctorId = $_GET['id'] ?? 0;
            $schedule = $this->hospitalModel->getDoctorSchedule($doctorId);
            
            header('Content-Type: application/json');
            echo json_encode($schedule);
        } catch (\Exception $e) {
            error_log("Error in getDoctorSchedule: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function toggleDoctorStatus()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $doctorId = $_POST['doctor_id'];
            $this->hospitalModel->toggleDoctorStatus($doctorId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            error_log("Error in toggleDoctorStatus: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Patient Methods
    public function patients()
    {
        try {
            $patients = $this->hospitalModel->getAllPatients();
            
            $data = [
                'pageTitle' => 'Patients',
                'currentPage' => 'patients',
                'patients' => $patients,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            
            echo $this->view('hospital/patients', $data);
        } catch (\Exception $e) {
            error_log("Error in patients: " . $e->getMessage());
            $this->session->setFlash('error', 'An error occurred while loading patients');
            throw $e;
        }
    }

    public function getPatientDetails()
    {
        try {
            $patientId = $_GET['id'] ?? 0;
            $details = $this->hospitalModel->getPatientDetails($patientId);
            
            header('Content-Type: application/json');
            echo json_encode($details);
        } catch (\Exception $e) {
            error_log("Error in getPatientDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getMedicalHistory()
    {
        try {
            $patientId = $_GET['id'] ?? 0;
            $history = $this->hospitalModel->getPatientMedicalHistory($patientId);
            
            header('Content-Type: application/json');
            echo json_encode($history);
        } catch (\Exception $e) {
            error_log("Error in getMedicalHistory: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Treatment Request Methods
    public function treatmentRequests()
    {
        try {
            $requests = $this->hospitalModel->getAllTreatmentRequests();
            
            $data = [
                'pageTitle' => 'Treatment Requests',
                'currentPage' => 'treatment-requests',
                'requests' => $requests,
                'basePath' => $this->basePath,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];
            
            echo $this->view('hospital/treatment-requests', $data);
        } catch (\Exception $e) {
            error_log("Error in treatmentRequests: " . $e->getMessage());
            $this->session->setFlash('error', 'An error occurred while loading treatment requests');
            throw $e;
        }
    }

    public function processResponse()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $requestId = $_POST['request_id'];
            $responseData = [
                'estimated_cost' => $_POST['estimated_cost'],
                'response_message' => $_POST['response_message'],
                'additional_requirements' => $_POST['additional_requirements'],
                'updated_by' => $this->session->getUserId()
            ];

            $this->hospitalModel->updateRequest($requestId, $responseData);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            error_log("Error in processResponse: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getRequestDetails()
    {
        try {
            $requestId = $_GET['id'] ?? 0;
            $details = $this->hospitalModel->getRequestDetails($requestId);
            
            header('Content-Type: application/json');
            echo json_encode($details);
        } catch (\Exception $e) {
            error_log("Error in getRequestDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function approveRequest()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $requestId = $_POST['request_id'];
            $this->hospitalModel->updateRequestStatus($requestId, 'Approved', $this->session->getUserId());
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            error_log("Error in approveRequest: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}