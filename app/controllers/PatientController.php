<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthRecord;
use App\Models\Patient;
use App\Models\MedicalSession;
use App\Models\Hospital;

class PatientController extends BaseController
{
    private $appointmentModel;
    private $doctorModel;
    private $healthRecordModel;
    private $patientModel;
    private $medicalSessionModel;
    private $hospitalModel;
    private array $treatmentPlanFields;

    public function __construct()
    {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
        $this->healthRecordModel = new HealthRecord();
        $this->patientModel = new Patient();
        $this->medicalSessionModel = new MedicalSession();
        $this->hospitalModel = new Hospital();
        $this->treatmentPlanFields = [
            'treatment_description', 'diagnosis', 'medications',
            'treatment_duration', 'follow_up', 'travel_restrictions',
            'estimated_budget', 'specialist_notes', 'estimated_duration',
            'vehicle_type'
        ];

    }

    public function dashboard()
{
    try {
        $patientId = $this->session->getUserId();
        $appointments = $this->appointmentModel->getPatientAppointments($patientId);
        
        // Get active medical session if exists
        $activeMedicalSession = false;
        $sessionData = null;
        
        try {
            // Check if there's an active medical session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                $activeMedicalSession = true;
                // Set session data based on what we have in the database
                $sessionData = $this->prepareSessionData($activeSession, $patientId);
            }
        } catch (\Exception $e) {
            error_log("Error getting active medical session: " . $e->getMessage());
            // Continue even if there's an error with session data
        }
        
        // Check if tab parameter exists in URL
        $currentTab = $_GET['tab'] ?? 'general-doctor';
        
        $data = [
            'appointments' => $appointments,
            'activeMedicalSession' => $activeMedicalSession,
            'sessionData' => $sessionData,
            'currentTab' => $currentTab,
            'basePath' => $this->basePath
        ];

        echo $this->view('patient/dashboard', $data);
        exit();
    } catch (\Exception $e) {
        error_log("Error in dashboard: " . $e->getMessage());
        $this->session->setFlash('error', 'Error loading dashboard: ' . $e->getMessage());
        header('Location: ' . $this->url('home'));
        exit();
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
            
            // Check if we need to filter for just general doctors (for new medical session)
            $startMedicalSession = isset($_GET['start_session']) && $_GET['start_session'] == 1;
            
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

    /**
     * Start medical session with an existing appointment
     */
    public function startSessionWithAppointment()
    {
        try {
            $appointmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $patientId = $this->session->getUserId();
            
            if (!$appointmentId) {
                $this->session->setFlash('error', 'Invalid appointment ID');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Verify the appointment belongs to this patient
            $appointment = $this->appointmentModel->getById($appointmentId);
            if (!$appointment || $appointment['patient_id'] != $patientId) {
                $this->session->setFlash('error', 'Invalid appointment');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Check if there's already an active session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                $this->session->setFlash('info', 'You already have an active medical session');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Create a new session
            $sessionData = [
                'patient_id' => $patientId,
                'status' => 'Active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $sessionId = $this->medicalSessionModel->create($sessionData);
            if (!$sessionId) {
                $this->session->setFlash('error', 'Failed to create medical session');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Update the appointment with the session ID
            $this->appointmentModel->updateSessionId($appointmentId, $sessionId);
            
            $this->session->setFlash('success', 'Medical session started successfully');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
            
        } catch (\Exception $e) {
            error_log("Error in startSessionWithAppointment: " . $e->getMessage());
            $this->session->setFlash('error', 'Error starting medical session: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }

    /**
     * Display the start medical session form
     */
    public function startMedicalSession()
    {
        try {
            $patientId = $this->session->getUserId();
            
            // Check if there's already an active session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                $this->session->setFlash('info', 'You already have an active medical session');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Get the general doctor appointment
            $generalAppointment = null;
            $appointmentId = null;
            
            // Get patient's appointments
            $appointments = $this->appointmentModel->getPatientAppointments($patientId);
            if ($appointments && $appointments->num_rows > 0) {
                while ($appointment = $appointments->fetch_assoc()) {
                    // Check if this is a general doctor appointment
                    $isGeneralDoctor = (
                        (!isset($appointment['specialization']) || 
                        $appointment['specialization'] == 'General Medicine' ||
                        $appointment['specialization'] == 'General Practitioner')
                    );
                    
                    if ($isGeneralDoctor && $appointment['appointment_status'] != 'Canceled') {
                        $generalAppointment = $appointment;
                        $appointmentId = $appointment['appointment_id'];
                        break;
                    }
                }
            }
            
            if (!$generalAppointment) {
                $this->session->setFlash('error', 'No general doctor appointment found. Please book an appointment first.');
                header('Location: ' . $this->url('patient/book-appointment'));
                exit();
            }
            
            $data = [
                'appointmentId' => $appointmentId,
                'appointment' => $generalAppointment,
                'basePath' => $this->basePath
            ];
            
            echo $this->view('patient/start-medical-session', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in startMedicalSession: " . $e->getMessage());
            $this->session->setFlash('error', 'Error starting medical session: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }

    /**
     * Process the form submission to confirm and start a medical session
     */
    public function confirmMedicalSession()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }

            // Check CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $patientId = $this->session->getUserId();
            $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
            
            if (!$appointmentId) {
                throw new \Exception("Invalid appointment ID");
            }
            
            // Verify the appointment belongs to this patient
            $appointment = $this->appointmentModel->getById($appointmentId);
            if (!$appointment || $appointment['patient_id'] != $patientId) {
                throw new \Exception("Invalid appointment");
            }
            
            // Check if there's already an active session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                $this->session->setFlash('info', 'You already have an active medical session');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Create a new session
            $sessionData = [
                'patient_id' => $patientId,
                'status' => 'Active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $sessionId = $this->medicalSessionModel->create($sessionData);
            if (!$sessionId) {
                throw new \Exception("Failed to create medical session");
            }
            
            // Update the appointment with the session ID
            $this->appointmentModel->updateSessionId($appointmentId, $sessionId);
            
            $this->session->setFlash('success', 'Medical session started successfully');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
            
        } catch (\Exception $e) {
            error_log("Error in confirmMedicalSession: " . $e->getMessage());
            $this->session->setFlash('error', 'Error starting medical session: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }

    public function processAppointment() 
    {
        try {
            error_log("Raw POST data: " . print_r($_POST, true));
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $appointmentData = [
                'patient_id' => $this->session->getUserId(),
                'doctor_id' => $_POST['doctor_id'],
                'preferred_date' => $_POST['preferred_date'],      // FIXED: Updated to match form field name
                'appointment_time' => $_POST['appointment_time'],  // FIXED: Updated to match form field name
                'consultation_type' => $_POST['consultation_type'],
                'reason_for_visit' => $_POST['reason_for_visit'],  // FIXED: Updated to match form field name
                'medical_history' => $_POST['medical_history'] ?? null,
                'documents' => $_FILES['documents'] ?? []
            ];
            
            error_log("Processed appointment data: " . print_r($appointmentData, true));
            
            $appointmentId = $this->appointmentModel->bookAppointment($appointmentData);
            
            // Get the appointment details to check if a Google Meet link was created
            $appointmentDetails = $this->appointmentModel->getById($appointmentId);
            
            // Check if this is a general doctor booking for a new medical session
            if (isset($_POST['start_medical_session']) && $_POST['start_medical_session'] == 1) {
                // Create a new medical session
                $this->startNewMedicalSession($appointmentId);
            }
            
            // Prepare success message
            $successMessage = 'Appointment booked successfully!';
            
            // Add Meet link info if it's an online appointment with a meet link
            if ($appointmentDetails && 
                $appointmentDetails['consultation_type'] === 'Online' && 
                !empty($appointmentDetails['meet_link'])) {
                $successMessage .= ' Your Google Meet link is: ' . $appointmentDetails['meet_link'];
            }
            
            $this->session->setFlash('success', $successMessage);
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in processAppointment: " . $e->getMessage());
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

    /**
     * Start a new medical session
     * 
     * @param int $appointmentId
     * @return int|false
     */
    private function startNewMedicalSession($appointmentId)
    {
        try {
            $patientId = $this->session->getUserId();
            
            // Check if there's already an active session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                error_log("Patient already has an active medical session");
                return false;
            }
            
            // Create a new medical session
            $sessionData = [
                'patient_id' => $patientId,
                'status' => 'Active',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $sessionId = $this->medicalSessionModel->create($sessionData);
            if (!$sessionId) {
                error_log("Failed to create medical session");
                return false;
            }

            // Update the appointment with session information
            // This requires adding a session_id column to your appointments table
            // and a method to update it in your Appointment model
            
            // For now, log this to implement later
            error_log("Medical session created with ID: " . $sessionId . " for appointment ID: " . $appointmentId);
            
            return $sessionId;
        } catch (\Exception $e) {
            error_log("Error creating medical session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Book a general doctor to start a medical session
     */
    public function bookGeneralDoctor()
    {
        try {
            $patientId = $this->session->getUserId();
            
            // Check if patient already has an active session
            $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
            if ($activeSession) {
                $this->session->setFlash('error', 'You already have an active medical session');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Redirect to the regular booking page but with a flag to start a session
            header('Location: ' . $this->url('patient/book-appointment') . '?start_session=1');
            exit();
        } catch (\Exception $e) {
            error_log("Error in bookGeneralDoctor: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }
    
    /**
     * Join a Google Meet appointment
     */
    public function joinMeetAppointment()
    {
        try {
            $appointmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $patientId = $this->session->getUserId();
            
            if (!$appointmentId) {
                $this->session->setFlash('error', 'Invalid appointment ID');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Verify the appointment belongs to this patient
            $appointment = $this->appointmentModel->getById($appointmentId);
            if (!$appointment || $appointment['patient_id'] != $patientId) {
                $this->session->setFlash('error', 'Invalid appointment');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Check if the appointment has a Meet link
            if (empty($appointment['meet_link'])) {
                $this->session->setFlash('error', 'This appointment does not have a Google Meet link');
                header('Location: ' . $this->url('patient/dashboard'));
                exit();
            }
            
            // Redirect to the Meet link
            header('Location: ' . $appointment['meet_link']);
            exit();
        } catch (\Exception $e) {
            error_log("Error joining Meet appointment: " . $e->getMessage());
            $this->session->setFlash('error', 'Error joining meeting: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
    }
    
 /**
 * Update the prepareSessionData method to include travel plan data
 * 
 * @param array $activeSession
 * @param int $patientId
 * @return array
 */
private function prepareSessionData($activeSession, $patientId)
{
    // Get the existing session data structure
    $sessionData = [
        'id' => $activeSession['session_id'],
        'status' => $activeSession['status'],
        'generalDoctorBooked' => false,
        'specialistBooked' => false,
        'treatmentPlanCreated' => false,
        'transportBooked' => false,
        'travelPlanSelected' => false,
        'generalDoctor' => null,
        'specialist' => null,
        'treatmentPlan' => [
            'diagnosis' => null,
            'treatment_description' => null,
            'medications' => null,
            'treatment_duration' => null,
            'follow_up' => null,
            'travel_restrictions' => 'None', // Default value
            'estimated_budget' => 0,         // Default value
            'estimated_duration' => 0,       // Default value
            'vehicle_type' => null,
            'arrival_deadline' => null,
            'specialist_notes' => null
        ],
        'travelPlan' => null  // Add this line to include travel plan data
    ];
    
    // Fill treatment plan data
    try {
        $query = "SELECT * FROM treatment_plans WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $plan = $result->fetch_assoc();
            $sessionData['treatmentPlanCreated'] = true;
            error_log("Treatment plan found for patient ID: $patientId");
            
            // Map the fields we want to display
            $treatmentPlanFields = [
                'treatment_description', 'diagnosis', 'medications',
                'treatment_duration', 'follow_up', 'travel_restrictions',
                'estimated_budget', 'specialist_notes', 'vehicle_type',
                'estimated_duration', 'arrival_deadline'
            ];
            
            foreach ($treatmentPlanFields as $field) {
                if (isset($plan[$field]) && $plan[$field] !== null) {
                    $sessionData['treatmentPlan'][$field] = $plan[$field];
                }
            }
        }
    } catch (\Exception $e) {
        error_log("Error getting treatment plan: " . $e->getMessage());
    }
    
    // Get most recent appointment for the patient to use as general doctor booking
    try {
        $patientAppointments = $this->appointmentModel->getPatientAppointments($patientId, 5);
        if ($patientAppointments && $patientAppointments->num_rows > 0) {
            $sessionData['generalDoctorBooked'] = true;
            
            // Get the first appointment
            $appointment = $patientAppointments->fetch_assoc();
            
            $sessionData['generalDoctor'] = [
                'id' => $appointment['doctor_id'],
                'name' => $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'],
                'specialty' => $appointment['specialization'] ?? 'General Practitioner',
                'appointmentDate' => $appointment['appointment_date'] . ' ' . $appointment['appointment_time'],
                'appointmentMode' => $appointment['consultation_type'],
                'meetLink' => $appointment['meet_link'] ?? '',
                'hospital' => $appointment['hospital_name'] ?? 'General Hospital'
            ];
            
            // Check if we have other appointments to use as the specialist
            if ($patientAppointments->num_rows > 1) {
                $specialistAppointment = $patientAppointments->fetch_assoc();
                $sessionData['specialistBooked'] = true;
                $sessionData['specialist'] = [
                    'id' => $specialistAppointment['doctor_id'],
                    'name' => $specialistAppointment['doctor_first_name'] . ' ' . $specialistAppointment['doctor_last_name'],
                    'specialty' => $specialistAppointment['specialization'] ?? 'Specialist',
                    'hospital' => $specialistAppointment['hospital_name'] ?? 'General Hospital',
                    'appointmentDate' => $specialistAppointment['appointment_date'] . ' ' . $specialistAppointment['appointment_time'],
                    'appointmentMode' => $specialistAppointment['consultation_type'],
                    'meetLink' => $specialistAppointment['meet_link'] ?? ''
                ];
            } else {
                // Add a placeholder for waiting for specialist state
                $sessionData['waitingForSpecialist'] = true;
            }
        }
    } catch (\Exception $e) {
        error_log("Error getting patient appointments for session data: " . $e->getMessage());
    }
    
    // Check for transportation assistance bookings
    try {
        $query = "SELECT COUNT(*) as count FROM transportationassistance 
                WHERE patient_id = ? AND status != 'Canceled'";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $transportResult = $stmt->get_result()->fetch_assoc();
        $sessionData['transportBooked'] = ($transportResult['count'] > 0);
    } catch (\Exception $e) {
        error_log("Error checking transportation bookings: " . $e->getMessage());
        $sessionData['transportBooked'] = false;
    }

    // Check for travel plans and get the travel plan data 
    try {
        // Check if any travel plans exist first
        $query = "SELECT COUNT(*) as count FROM travel_plans WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $travelResult = $stmt->get_result()->fetch_assoc();
        $sessionData['travelPlanSelected'] = ($travelResult['count'] > 0);
        
        // If travel plans exist, get the details
        if ($sessionData['travelPlanSelected']) {
            $travelPlanData = $this->getTravelPlanData();
            $sessionData['travelPlan'] = $travelPlanData;
        }
    } catch (\Exception $e) {
        error_log("Error checking travel plans: " . $e->getMessage());
        $sessionData['travelPlanSelected'] = false;
    }
    
    // Get patient name for summary
    try {
        $query = "SELECT CONCAT(first_name, ' ', last_name) as full_name FROM users WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $patientResult = $stmt->get_result()->fetch_assoc();
        if ($patientResult) {
            $sessionData['patientName'] = $patientResult['full_name'];
        }
    } catch (\Exception $e) {
        error_log("Error getting patient name: " . $e->getMessage());
    }
    
    return $sessionData;
}


/**
 * Get travel plan data for the patient's dashboard
 * 
 * @return array Travel plan data
 */
public function getTravelPlanData()
{
    try {
        $patientId = $this->session->getUserId();
        
        // Get the patient's most recent trip
        $trip = $this->patientModel->getLatestTrip($patientId);
        
        if (!$trip) {
            // No trip found
            return [
                'hasTrip' => false
            ];
        }
        
        // Get all destinations in this trip
        $tripId = $trip['trip_id'];
        $destinations = $this->patientModel->getTripDestinations($tripId);
        
        if (!$destinations || count($destinations) === 0) {
            // Trip exists but no destinations
            return [
                'hasTrip' => true,
                'trip' => $trip,
                'hasDestinations' => false
            ];
        }
        
        // Format the data for display
        $travelPlanData = [
            'hasTrip' => true,
            'trip' => $trip,
            'hasDestinations' => true,
            'destinations' => $destinations,
            'totalDuration' => $trip['total_duration_hours'],
            'startDate' => $trip['start_date'],
            'endDate' => $trip['end_date'],
            'travelDays' => $trip['travel_days']
        ];
        
        return $travelPlanData;
    } catch (\Exception $e) {
        error_log("Error getting travel plan data: " . $e->getMessage());
        return [
            'hasTrip' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Navigate to a specific treatment session tab
 * 
 * @param string $tab The tab identifier
 * @return void
 */
public function navigateSessionTab($tab = 'general-doctor')
{
    try {
        $validTabs = [
            'general-doctor', 'specialist', 'treatment-plan', 
            'hotel-transport', 'travel-plan', 'summary'
        ];
        
        if (!in_array($tab, $validTabs)) {
            $tab = 'general-doctor';
        }
        
        header('Location: ' . $this->url('patient/dashboard') . '?tab=' . $tab);
        exit();
    } catch (\Exception $e) {
        error_log("Error in navigateSessionTab: " . $e->getMessage());
        header('Location: ' . $this->url('patient/dashboard'));
        exit();
    }
}

/**
 * Book hotel and transport
 * 
 * @return void
 */
public function bookHotelTransport()
{
    try {
        $patientId = $this->session->getUserId();
        $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
        
        if (!$activeSession) {
            $this->session->setFlash('error', 'No active medical session found');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
        
        // Redirect to accommodation providers page
        header('Location: ' . $this->url('accommodation/accommodation-providers'));
        exit();
    } catch (\Exception $e) {
        error_log("Error in bookHotelTransport: " . $e->getMessage());
        $this->session->setFlash('error', 'Error accessing hotel and transport booking');
        header('Location: ' . $this->url('patient/dashboard'));
        exit();
    }
}

/**
 * Select travel plan
 * 
 * @return void
 */
public function selectTravelPlan()
{
    try {
        $patientId = $this->session->getUserId();
        $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
        
        if (!$activeSession) {
            $this->session->setFlash('error', 'No active medical session found');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
        
        // Redirect to transport page
        header('Location: ' . $this->url('patient/transport'));
        exit();
    } catch (\Exception $e) {
        error_log("Error in selectTravelPlan: " . $e->getMessage());
        $this->session->setFlash('error', 'Error accessing travel plan selection');
        header('Location: ' . $this->url('patient/dashboard'));
        exit();
    }
}

/**
 * View medical session summary
 * 
 * @return void
 */
public function sessionSummary()
{
    try {
        $patientId = $this->session->getUserId();
        $activeSession = $this->medicalSessionModel->getActiveSessionByPatient($patientId);
        
        if (!$activeSession) {
            $this->session->setFlash('error', 'No active medical session found');
            header('Location: ' . $this->url('patient/dashboard'));
            exit();
        }
        
        // Prepare complete session data for summary
        $sessionData = $this->prepareSessionData($activeSession, $patientId);
        
        // Store session data in session for access in the view
        $this->session->set('medical_session_data', $sessionData);
        
        // Navigate to summary tab
        header('Location: ' . $this->url('patient/dashboard') . '?tab=summary');
        exit();
    } catch (\Exception $e) {
        error_log("Error in sessionSummary: " . $e->getMessage());
        $this->session->setFlash('error', 'Error accessing session summary');
        header('Location: ' . $this->url('patient/dashboard'));
        exit();
    }
}

    /**
 * Create a Google Meet link for an appointment
 * Helper method to standardize the process of creating meet links
 * 
 * @param int $appointmentId The ID of the appointment
 * @param int $patientId The ID of the patient
 * @param int $doctorId The ID of the doctor
 * @param string $date The appointment date
 * @param string $time The appointment time
 * @param string $reason The reason for the appointment
 * @return string|false The Meet link URL or false on failure
 */
private function createMeetLinkForAppointment($appointmentId, $patientId, $doctorId, $date, $time, $reason) {
    try {
        error_log("Starting createMeetLinkForAppointment for appointment ID: " . $appointmentId);
        error_log("Date: {$date}, Time: {$time}");
        
        // Get doctor and patient details
        $query = "SELECT 
                CONCAT(d_user.first_name, ' ', d_user.last_name) AS doctor_name,
                CONCAT(p_user.first_name, ' ', p_user.last_name) AS patient_name,
                d_user.email AS doctor_email,
                p_user.email AS patient_email
                FROM doctors d 
                JOIN users d_user ON d.user_id = d_user.user_id
                JOIN users p_user ON p_user.user_id = ?
                WHERE d.doctor_id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $patientId, $doctorId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            error_log("Failed to get doctor and patient details for Meet link creation");
            error_log("Patient ID: " . $patientId . ", Doctor ID: " . $doctorId);
            return false;
        }

        error_log("Doctor and patient details retrieved: " . json_encode($result));

        // Standardize the date format (ensure it's YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $originalDate = $date;
            $date = date('Y-m-d', strtotime($date));
            error_log("Standardized date format from: {$originalDate} to: {$date}");
        }
        
        // Standardize the time format (ensure it's HH:MM:SS)
        $originalTime = $time; 
        // Check if time is in 12-hour format (with AM/PM)
        if (stripos($time, 'am') !== false || stripos($time, 'pm') !== false) {
            $time = date('H:i:s', strtotime($time));
            error_log("Converted 12-hour time to 24-hour: {$originalTime} to {$time}");
        } 
        // Check if time is missing seconds
        else if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            $time = $time . ':00';
            error_log("Added seconds to time: {$originalTime} to {$time}");
        }
        // If time format is HH:MM without leading zeros
        else if (preg_match('/^\d{1}:\d{2}(:\d{2})?$/', $time)) {
            $timeParts = explode(':', $time);
            $time = sprintf('%02d:%02d:%02d', 
                $timeParts[0], 
                $timeParts[1], 
                isset($timeParts[2]) ? $timeParts[2] : 0
            );
            error_log("Added leading zeros to time: {$originalTime} to {$time}");
        }
        // If it's not in HH:MM:SS format, try to convert it
        else if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            $time = date('H:i:s', strtotime($time));
            error_log("Converted time to standard format: {$originalTime} to {$time}");
        }
        
        // Combine date and time
        $startDateTime = $date . ' ' . $time;
        error_log("Combined formatted start date/time: {$startDateTime}");
        
        // Validate the combined datetime
        $startDateObj = \DateTime::createFromFormat('Y-m-d H:i:s', $startDateTime);
        if (!$startDateObj) {
            error_log("CRITICAL ERROR: Invalid datetime format after standardization");
            $errors = \DateTime::getLastErrors();
            error_log("DateTime parse errors: " . print_r($errors, true));
            
            // Try one more time with a different approach
            try {
                $startDateObj = new \DateTime($startDateTime);
                $startDateTime = $startDateObj->format('Y-m-d H:i:s');
                error_log("Recovery attempt successful, new datetime: {$startDateTime}");
            } catch (\Exception $dateEx) {
                error_log("Recovery attempt failed: " . $dateEx->getMessage());
                return false;
            }
        } else {
            // Reformat to ensure correct format
            $startDateTime = $startDateObj->format('Y-m-d H:i:s');
        }
        
        // Default appointment duration is 30 minutes
        $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + 30 * 60);
        error_log("Calculated end date/time: {$endDateTime}");

        // Create event summary and description
        $summary = "Medical Appointment: Dr. " . $result['doctor_name'] . " with " . $result['patient_name'];
        $description = "Appointment ID: " . $appointmentId . "\nReason for visit: " . $reason;

        // Attendee emails
        $attendees = [
            $result['doctor_email'],
            $result['patient_email']
        ];

        error_log("Creating Google Meet with attendees: " . json_encode($attendees));

        // Create Google Meet event
        $googleMeetService = new \App\Services\GoogleMeetService();
        $eventData = $googleMeetService->createMeetEvent(
            $summary,
            $description,
            $startDateTime,
            $endDateTime,
            $attendees
        );

        if ($eventData && isset($eventData['meet_link'])) {
            error_log("Meet link created successfully: " . $eventData['meet_link']);
            
            // Update the appointment with the Meet link
            $appointmentModel = new \App\Models\Appointment();
            $updateResult = $appointmentModel->updateMeetLink($appointmentId, $eventData['meet_link']);
            error_log("Appointment update result: " . ($updateResult ? "Success" : "Failed"));
            
            return $eventData['meet_link'];
        } else {
            error_log("No meet link returned from GoogleMeetService");
            return false;
        }
    } catch (\Exception $e) {
        error_log("Error in createMeetLinkForAppointment: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}


    
    /**
 * Process an appointment booking with Google Meet link generation
 * Handles form submission, creates appointment and generates Meet link for online consultations
 * 
 * @return void
 */
public function processAppointmentWithMeetLink() {
    // Set JSON content type immediately
    header('Content-Type: application/json');
    
    // Buffer all output to prevent warnings from being directly sent to the client
    ob_start();
    
    try {
        if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            ob_end_clean();
            echo json_encode([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ]);
            exit();
        }
        
        error_log("### PROCESSING APPOINTMENT WITH MEET LINK ###");
        error_log("Raw POST data: " . print_r($_POST, true));
        
        $appointmentData = [
            'patient_id' => $this->session->getUserId(),
            'doctor_id' => $_POST['doctor_id'],
            'preferred_date' => $_POST['preferred_date'],
            'appointment_time' => $_POST['appointment_time'],
            'consultation_type' => $_POST['consultation_type'],
            'reason_for_visit' => $_POST['reason_for_visit'],
            'medical_history' => $_POST['medical_history'] ?? null,
            'documents' => $_FILES['documents'] ?? []
        ];
        
        error_log("Processed appointment data: " . print_r($appointmentData, true));
        
        // Start by creating the appointment without the Meet link
        $appointmentModel = new \App\Models\Appointment();
        $appointmentId = $appointmentModel->bookAppointment($appointmentData);
        
        error_log("Appointment created with ID: " . $appointmentId);
        
        // Flag to track if Meet link generation was attempted
        $meetLinkAttempted = false;
        $meetLinkSuccess = false;
        $meetLink = null;
        
        // Try to generate Meet link only for online appointments
        if ($appointmentData['consultation_type'] === 'Online') {
            $meetLinkAttempted = true;
            
            try {
                error_log("Attempting to create Google Meet link for online appointment");
                
                // Get the appointment details
                $appointment = $appointmentModel->getById($appointmentId);
                
                if ($appointment) {
                    // Log the retrieved appointment details
                    error_log("Retrieved appointment details: " . print_r($appointment, true));
                    
                    // Extract appointment details directly for meet link creation
                    $patientId = $appointment['patient_id'];
                    $doctorId = $appointment['doctor_id'];
                    $date = $appointment['appointment_date'];
                    $time = $appointment['appointment_time'];
                    $reason = $appointment['reason_for_visit'];
                    
                    error_log("Creating Meet link with: Date={$date}, Time={$time}, PatientID={$patientId}, DoctorID={$doctorId}");
                    
                    // Directly instantiate the GoogleMeetService
                    try {
                        $googleMeetService = new \App\Services\GoogleMeetService();
                        
                        // Try to format the date and time first
                        $startDateObj = new \DateTime($date . ' ' . $time);
                        $startDateTime = $startDateObj->format('Y-m-d H:i:s');
                        $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + 30 * 60);
                        
                        error_log("Formatted start time: {$startDateTime}, Formatted end time: {$endDateTime}");
                        
                        // Get doctor and patient details for attendees
                        $query = "SELECT 
                                CONCAT(d_user.first_name, ' ', d_user.last_name) AS doctor_name,
                                CONCAT(p_user.first_name, ' ', p_user.last_name) AS patient_name,
                                d_user.email AS doctor_email,
                                p_user.email AS patient_email
                                FROM doctors d 
                                JOIN users d_user ON d.user_id = d_user.user_id
                                JOIN users p_user ON p_user.user_id = ?
                                WHERE d.doctor_id = ?";
                
                        $stmt = $this->db->prepare($query);
                        $stmt->bind_param("ii", $patientId, $doctorId);
                        $stmt->execute();
                        $result = $stmt->get_result()->fetch_assoc();
                        
                        if (!$result) {
                            error_log("Failed to get doctor and patient details");
                            throw new \Exception("Could not retrieve doctor and patient information");
                        }
                        
                        // Create event title and description
                        $summary = "Medical Appointment: Dr. {$result['doctor_name']} with {$result['patient_name']}";
                        $description = "Appointment ID: {$appointmentId}\nReason for visit: {$reason}";
                        
                        // Attendee emails
                        $attendees = [
                            $result['doctor_email'],
                            $result['patient_email']
                        ];
                        
                        // Create the Google Meet event
                        $eventData = $googleMeetService->createMeetEvent(
                            $summary,
                            $description,
                            $startDateTime,
                            $endDateTime,
                            $attendees
                        );
                        
                        if ($eventData && isset($eventData['meet_link'])) {
                            $meetLink = $eventData['meet_link'];
                            $meetLinkSuccess = true;
                            
                            // Update the appointment with the Meet link
                            $updateResult = $appointmentModel->updateMeetLink($appointmentId, $meetLink);
                            error_log("Meet link created successfully: {$meetLink}, Update result: " . ($updateResult ? "Success" : "Failed"));
                        } else {
                            error_log("No meet link returned from GoogleMeetService");
                        }
                    } catch (\Exception $meetServiceEx) {
                        error_log("Error with GoogleMeetService: " . $meetServiceEx->getMessage());
                        error_log("GoogleMeetService error trace: " . $meetServiceEx->getTraceAsString());
                    }
                } else {
                    error_log("Could not retrieve appointment details for Meet link creation");
                }
            } catch (\Exception $e) {
                error_log("Error creating Meet link (appointment will still be created): " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                // Don't rethrow - we want to continue even if Meet link fails
            }
        }
        
        // Get the full appointment details including the Meet link if it was created
        $appointmentDetails = $appointmentModel->getById($appointmentId);
        
        // Check if this is a general doctor booking for a new medical session
        if (isset($_POST['start_medical_session']) && $_POST['start_medical_session'] == 1) {
            // Create a new medical session
            $this->startNewMedicalSession($appointmentId);
        }
        
        // Prepare response data
        $response = [
            'success' => true,
            'appointment_id' => $appointmentId,
            'message' => 'Appointment booked successfully!'
        ];
        
        // Add Meet link info if it's an online appointment
        if ($appointmentData['consultation_type'] === 'Online') {
            // First check if the link was saved in the appointment details
            if ($appointmentDetails && !empty($appointmentDetails['meet_link'])) {
                $response['meet_link'] = $appointmentDetails['meet_link'];
                $response['message'] .= ' Your Google Meet link is: ' . $appointmentDetails['meet_link'];
            } 
            // Then check if we just created a link but it wasn't saved yet
            else if ($meetLinkSuccess && $meetLink) {
                $response['meet_link'] = $meetLink;
                $response['message'] .= ' Your Google Meet link is: ' . $meetLink;
            } 
            // If link creation was attempted but failed, note this in the response
            else if ($meetLinkAttempted) {
                $response['meet_link_failed'] = true;
                $response['message'] .= ' However, we could not generate a Google Meet link at this time. Please check back later.';
            }
        }
        
        // Clear any buffered output before sending JSON response
        ob_end_clean();
        
        echo json_encode($response);
        exit();
        
    } catch (\Exception $e) {
        // Clear any buffered output
        ob_end_clean();
        
        error_log("Error in processAppointmentWithMeetLink: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error booking appointment: ' . $e->getMessage()
        ]);
        exit();
    }
}
    
    public function paymentPlan()
    {
        try {
            $paymentPlans = $this->patientModel->showPaymentPlans();

            $data = [
                'paymentPlans' => $paymentPlans,
                'basePath' => $this->basePath
            ];
            
            echo $this->view('patient/paymentPlan', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in paymentPlan: " . $e->getMessage());
            throw $e;
        }
    }


    public function choosePlan()
    {
        try {
            $patientId = $this->session->getUserId(); // get logged in patient id
            $planId = $_POST['plan_id'] ?? null;

            if (!$planId) {
                echo json_encode(['success' => false, 'message' => 'No plan selected']);
                return;
            }

            $this->patientModel->assignPaymentPlan($patientId, $planId);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            error_log("Error in assignPaymentPlan: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Something went wrong']);
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
}