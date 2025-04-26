<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthRecord;
use App\Models\Patient;
use App\Models\MedicalSession;

class PatientController extends BaseController
{
    private $appointmentModel;
    private $doctorModel;
    private $healthRecordModel;
    private $patientModel;
    private $medicalSessionModel;

    public function __construct()
    {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
        $this->healthRecordModel = new HealthRecord();
        $this->patientModel = new Patient();
        $this->medicalSessionModel = new MedicalSession();
    }

    public function dashboard()
    {
        try {
            error_log("Entering dashboard method");
            $patientId = $this->session->getUserId();
            $appointments = $this->appointmentModel->getPatientAppointments($patientId);

            error_log("Patient ID: " . $patientId);
            error_log("Appointments: " . print_r($appointments, true));
            $data = [
                'appointments' => $appointments,
                'activeMedicalSession' => $activeMedicalSession,
                'sessionData' => $sessionData,
                'basePath' => $this->basePath
            ];

            echo $this->view('patient/dashboard', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in dashboard: " . $e->getMessage());
            throw $e;
        }
    }

    public function bookAppointment()
    {
        try {
            error_log("Starting bookAppointment");
            $doctors = $this->doctorModel->getAvailableDoctors();

            if (!$doctors || $doctors->num_rows === 0) {
                error_log("No doctors found");
                $this->session->setFlash('error', 'No doctors available');
            }
            $data = [
                'doctors' => $doctors,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath,
                'startMedicalSession' => $startMedicalSession
            ];

            echo $this->view('patient/book-appointment', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in bookAppointment: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }

    public function processAppointment()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $appointmentData = [
                'patient_id' => $this->session->getUserId(),
                'doctor_id' => $_POST['doctor_id'],
                'preferred_date' => $_POST['appointment_date'],  // Changed from date to preferred_date
                'appointment_time' => $_POST['time_slot'],       // Changed from time to appointment_time
                'consultation_type' => $_POST['consultation_type'],
                'reason_for_visit' => $_POST['reason'],          // Changed from reason to reason_for_visit
                'medical_history' => $_POST['medical_history'] ?? null,
                'documents' => $_FILES['documents'] ?? []
            ];

            $this->appointmentModel->bookAppointment($appointmentData);
            $this->session->setFlash('success', 'Appointment booked successfully!');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error booking appointment: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/book-appointment'));
            exit();
        }
    }

    public function getAppointmentDetails()
    {
        try {
            $appointmentId = $_GET['id'] ?? 0;
            $details = $this->appointmentModel->getAppointmentDetails($appointmentId);
            
            // Get the full appointment record to access meet_link
            $appointment = $this->appointmentModel->getById($appointmentId);
            
            // Add meet_link to the details if it exists
            if ($appointment && !empty($appointment['meet_link'])) {
                $details['appointment']['meet_link'] = $appointment['meet_link'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($details);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getAppointmentDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    public function profile()
    {
        try {
            $userId = $this->session->getUserId();
            $profile = $this->patientModel->getProfile($userId);
            $cities = $this->patientModel->getCities();
            $countries = $this->patientModel->getCountries();

            $data = [
                'user' => $profile,
                'cities' => $cities,
                'countries' => $countries,
                'basePath' => $this->basePath
            ];

            echo $this->view('patient/profile', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in profile: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfile()
    {
        try {
            $userId = $this->session->getUserId();
            $this->patientModel->updateProfile($userId, $_POST);
            $this->session->setFlash('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error updating profile: ' . $e->getMessage());
        }
        header('Location: ' . $this->url('patient/profile'));
        exit();
    }

    public function deleteProfile()
    {
        try {
            $userId = $this->session->getUserId();
            $this->patientModel->deleteAccount($userId);
            session_destroy();
            header('Location: ' . $this->url('login') . '?message=Account+deactivated+successfully');
            exit();
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error deactivating account: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/profile'));
            exit();
        }
    }

    public function getTimeSlots()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request method']);
                exit();
            }

            if (!isset($_POST['doctor_id']) || !isset($_POST['date'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required parameters']);
                exit();
            }

            $doctorId = $_POST['doctor_id'];
            $date = $_POST['date'];

            // Input validation
            if (!is_numeric($doctorId) || empty($date) || !strtotime($date)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid parameters']);
                exit();
            }

            // Get available time slots from the model
            $slots = $this->doctorModel->getAvailableTimeSlots($doctorId, $date);

            // Ensure we have an array, even if empty
            if (!is_array($slots)) {
                $slots = [];
            }

            header('Content-Type: application/json');
            echo json_encode($slots);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getTimeSlots: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Server error']);
            exit();
        }
    }

    public function medicalHistory()
    {
        try {
            $patientId = $this->session->getUserId();
            $records = $this->healthRecordModel->getPatientRecords($patientId);
            $reports = $this->patientModel->getMedicalReports($patientId);
            $data = [
                'records' => $records,
                'reports' => $reports,
                'basePath' => $this->basePath
            ];
            echo $this->view('patient/medical-history', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in medicalHistory: " . $e->getMessage());
            throw $e;
        }
    }

    public function uploadMedicalReport()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $patientId = $this->session->getUserId();
            $reportName = $_POST['report_name'];
            $reportType = $_POST['report_type'];
            $description = $_POST['description'] ?? '';

            // Validate file upload
            if (!isset($_FILES['report_file']) || $_FILES['report_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("Error uploading file");
            }

            $file = $_FILES['report_file'];
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                throw new \Exception("Invalid file type");
            }

            if ($file['size'] > $maxSize) {
                throw new \Exception("File size exceeds limit");
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = ROOT_PATH . '/public/uploads/medical-reports/' . $filename;

            // Create directory if it doesn't exist
            if (!is_dir(dirname($uploadPath))) {
                mkdir(dirname($uploadPath), 0777, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new \Exception("Failed to save file");
            }

            // Save to database
            $reportData = [
                'patient_id' => $patientId,
                'report_name' => $reportName,
                'report_type' => $reportType,
                'description' => $description,
                'file_path' => $filename
            ];

            $this->patientModel->saveMedicalReport($reportData);
            $this->session->setFlash('success', 'Medical report uploaded successfully!');
            header('Location: ' . $this->url('patient/medical-history'));
            exit();

        } catch (\Exception $e) {
            error_log("Error uploading medical report: " . $e->getMessage());
            $this->session->setFlash('error', 'Error uploading report: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/medical-history'));
            exit();
        }
    }

    public function deleteMedicalReport()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $reportId = $_POST['report_id'] ?? 0;
            $patientId = $this->session->getUserId();

            // Get report details to delete file
            $report = $this->patientModel->getMedicalReport($reportId);
            if (!$report || $report['patient_id'] != $patientId) {
                throw new \Exception("Invalid report");
            }

            // Delete file
            $filePath = ROOT_PATH . '/public/uploads/medical-reports/' . $report['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $this->patientModel->deleteMedicalReport($reportId, $patientId);

            echo json_encode(['success' => true]);
            exit();

        } catch (\Exception $e) {
            error_log("Error deleting medical report: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }

    public function paymentPlan()
    {
        try {
            $patientId = $this->session->getUserId();
            // $paymentPlans = $this->patientModel->getPaymentPlans($patientId);

            $data = [
                // 'paymentPlans' => $paymentPlans,
                'basePath' => $this->basePath
            ];

            echo $this->view('patient/paymentPlan', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in paymentPlan: " . $e->getMessage());
            throw $e;
        }
    }
}