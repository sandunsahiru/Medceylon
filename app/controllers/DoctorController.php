<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\MedicalSession;
use App\Models\Specialization;

class DoctorController extends BaseController
{
    private $doctorModel;
    private $appointmentModel;
    private $medicalSessionModel;
    private $specializationModel;
    private $patientModel;

    public function __construct()
    {
        parent::__construct();
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
        $this->medicalSessionModel = new MedicalSession();
        $this->patientModel = new \App\Models\Patient();
        // $this->specializationModel = new Specialization();
    }

    public function dashboard()
    {
        try {
            // Get user_id from session
            $userId = $this->validateDoctorSession();
            error_log("Loading dashboard for user ID: " . $userId);

            // Get doctor ID from the doctors table (IMPORTANT STEP)
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                error_log("No doctor record found for user ID: " . $userId);
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];
            error_log("Found doctor_id: " . $doctorId . " for user_id: " . $userId);

            // Get dashboard statistics
            $stats = $this->doctorModel->getDoctorDashboardStats($doctorId);
            error_log("Dashboard stats: " . print_r($stats, true));

            // Get recent appointments using the new method (includes all statuses and dates)
            $appointments = $this->appointmentModel->getRecentAppointments($doctorId);
            error_log("Found " . count($appointments) . " recent appointments for dashboard");

            $data = [
                'stats' => $stats,
                'appointments' => $appointments,
                'basePath' => $this->basePath,
                'page_title' => 'Doctor Dashboard',
                'current_page' => 'dashboard'
            ];

            echo $this->view('doctor/dashboard', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Dashboard error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->handleError($e);
        }
    }

    public function appointments()
    {
        try {
            // Get doctor_id from the users table and doctors table
            $userId = $this->validateDoctorSession();

            // Get doctor ID from the doctors table
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];

            // Handle POST requests for appointment updates
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleAppointmentActions($_POST);
                header("Location: " . $this->basePath . "/doctor/appointments");
                exit();
            }

            // Get appointments using the doctor_id
            $appointments = $this->appointmentModel->getDoctorAppointments($doctorId);
            $availability = $this->doctorModel->getDoctorAvailability($doctorId);

            $data = [
                'appointments' => $appointments,
                'availability' => $availability,
                'basePath' => $this->basePath,
                'page_title' => 'Appointments',
                'current_page' => 'appointments',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];

            echo $this->view('doctor/appointments', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    public function profile()
    {
        try {
            // Get user_id from session
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                header('Location: ' . $this->basePath . '/auth/login');
                exit();
            }

            // Handle profile update
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($_POST['csrf_token']) || !$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                    throw new \Exception('Invalid security token');
                }

                // Validate required fields
                if (empty($_POST['hospital_id']) || empty($_POST['experience'])) {
                    throw new \Exception('Please fill in all required fields');
                }

                $profileData = [
                    'qualifications' => $_POST['qualifications'] ?? '',
                    'experience' => intval($_POST['experience'] ?? 0),
                    'description' => $_POST['description'] ?? '',
                    'hospital_id' => intval($_POST['hospital_id'] ?? 0)
                ];

                $this->doctorModel->updateProfile($userId, $profileData);
                $this->session->setFlash('success', 'Profile updated successfully!');
                header("Location: " . $this->basePath . "/doctor/profile");
                exit();
            }

            // Get profile data using user_id
            $profile = $this->doctorModel->getDoctorProfile($userId);
            if (!$profile) {
                throw new \Exception('Failed to load doctor profile');
            }

            // Get hospitals list
            $hospitals = $this->doctorModel->getHospitals();
            if (!$hospitals) {
                throw new \Exception('Failed to load hospitals');
            }

            $data = [
                'profile' => $profile,
                'hospitals' => $hospitals,
                'basePath' => $this->basePath,
                'page_title' => 'Doctor Profile',
                'current_page' => 'profile',
                'csrfToken' => $_SESSION['csrf_token'] ?? '',
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ];

            echo $this->view('doctor/profile', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in doctor profile: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            $this->handleError($e);
        }
    }


    public function availability()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $availability = json_decode($_POST['availability'], true);
                $this->doctorModel->updateAvailability($doctorId, $availability);
                header("Location: " . $this->basePath . "/doctor/availability?success=1");
                exit();
            }

            $currentAvailability = $this->doctorModel->getDoctorAvailability($doctorId);

            $data = [
                'availability' => $currentAvailability,
                'basePath' => $this->basePath,
                'page_title' => 'Set Availability',
                'current_page' => 'availability'
            ];

            echo $this->view('doctor/availability', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function getTimeSlots()
    {
        try {
            header('Content-Type: application/json');

            // Validate inputs
            $doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
            $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

            if (!$doctorId) {
                throw new \Exception('Doctor ID is required');
            }

            // Get available slots for the specialist
            $slots = $this->doctorModel->getAvailableTimeSlots($doctorId, $date);

            echo json_encode([
                'success' => true,
                'slots' => $slots
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getTimeSlots: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }


    // New method for all-doctors page
    public function allDoctors()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            // Get specialists list
            $result = $this->doctorModel->getAllSpecialists();

            // Get statistics
            $stats = $this->doctorModel->getSpecialistStats();

            // Get current doctor's patients for booking form
            $patients = $this->doctorModel->getDoctorPatients($doctorId);

            $data = [
                'doctors' => $result,
                'stats' => $stats,
                'patients' => $patients,
                'doctorId' => $doctorId,
                'basePath' => $this->basePath,
                'page_title' => 'Specialist Doctors',
                'current_page' => 'doctors',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];

            echo $this->view('doctor/all-doctors', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    // Method to get doctor profile for modal
    public function getDocProfile()
    {
        try {
            // Set JSON header early to prevent any HTML output
            header('Content-Type: application/json');

            if (!isset($_GET['doctor_id'])) {
                throw new \Exception('Doctor ID is required');
            }

            $doctorId = (int) $_GET['doctor_id'];
            if ($doctorId <= 0) {
                throw new \Exception('Invalid doctor ID');
            }

            // Updated query with error handling
            $query = "SELECT 
            d.doctor_id,
            d.qualifications,
            d.years_of_experience,
            d.profile_description,
            u.first_name,
            u.last_name,
            u.email,
            u.phone_number,
            h.name as hospital_name,
            GROUP_CONCAT(DISTINCT s.name) as specializations
            FROM doctors d
            JOIN users u ON d.user_id = u.user_id
            LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
            LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
            LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
            WHERE d.doctor_id = ? 
            AND d.is_active = 1
            GROUP BY d.doctor_id, u.first_name, u.last_name, u.email, 
                     u.phone_number, d.qualifications, d.years_of_experience, 
                     h.name, d.profile_description";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new \Exception($this->db->error);
            }

            $stmt->bind_param("i", $doctorId);
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $result = $stmt->get_result();
            $profile = $result->fetch_assoc();

            if (!$profile) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Doctor profile not found'
                ]);
                exit();
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'first_name' => $profile['first_name'],
                    'last_name' => $profile['last_name'],
                    'qualifications' => $profile['qualifications'] ?? '',
                    'years_of_experience' => $profile['years_of_experience'] ?? 0,
                    'hospital_name' => $profile['hospital_name'] ?? '',
                    'email' => $profile['email'] ?? '',
                    'phone_number' => $profile['phone_number'] ?? '',
                    'profile_description' => $profile['profile_description'] ?? '',
                    'specializations' => $profile['specializations'] ?? ''
                ]
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getDocProfile: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }
    // Method to process specialist booking
    public function processBooking()
    {
        try {
            // Get the referring doctor's ID (logged in general doctor)
            $referringDoctorId = $this->validateDoctorSession();

            error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            error_log("POST Data: " . print_r($_POST, true));

            $required = ['specialist_id', 'patient_id', 'consultation_type', 'preferred_date', 'appointment_time'];
            foreach ($required as $field) {
                error_log("Checking field $field: " . (isset($_POST[$field]) ? $_POST[$field] : 'not set'));
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Use specialist_id as doctor_id and referringDoctorId as the actual referring doctor
            $bookingData = [
                'doctor_id' => $_POST['specialist_id'],  // Specialist doctor who will handle the appointment
                'patient_id' => $_POST['patient_id'],
                'consultation_type' => $_POST['consultation_type'],
                'preferred_date' => $_POST['preferred_date'],
                'appointment_time' => $_POST['appointment_time'],
                'reason_for_visit' => $_POST['medical_history'] ?? '',
                'medical_history' => '',
                'referring_doctor_id' => $referringDoctorId,  // General doctor who is making the referral
                'notes' => "Referred by Doctor ID: " . $referringDoctorId,
                'appointment_status' => 'Asked'
            ];

            error_log("Booking Data: " . print_r($bookingData, true));

            $appointmentId = $this->appointmentModel->bookAppointment($bookingData);

            header('Content-Type: application/json');
            if ($appointmentId) {
                error_log("Appointment booked successfully with ID: " . $appointmentId);
                echo json_encode(['success' => true]);
            } else {
                throw new \Exception("Failed to book appointment");
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in processBooking: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit();
        }
    }


    public function patients()
    {
        try {
            // Get doctor_id from the users table and doctors table
            $userId = $this->validateDoctorSession();

            // Get doctor ID from the doctors table
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];

            // Get basic statistics
            $stats = $this->doctorModel->getDoctorDashboardStats($doctorId);

            // Get patients list with their appointment history and medical reports
            $patientsQuery = "SELECT DISTINCT
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.gender,
                COUNT(DISTINCT a.appointment_id) as total_visits,
                MAX(a.appointment_date) as last_visit,
                COUNT(DISTINCT mr.report_id) as total_reports
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                LEFT JOIN medical_reports mr ON u.user_id = mr.patient_id
                WHERE a.doctor_id = ?
                GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.gender
                ORDER BY last_visit DESC";

            $stmt = $this->db->prepare($patientsQuery);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $patients = $stmt->get_result();

            $data = [
                'stats' => [
                    'total_patients' => $stats['total_patients'],
                    'completed_visits' => $stats['completed_visits'],
                    'upcoming_appointments' => $stats['upcoming_appointments']
                ],
                'patients' => $patients,
                'doctorId' => $doctorId,
                'basePath' => $this->basePath,
                'page_title' => 'My Patients',
                'current_page' => 'patients',
                'csrfToken' => $_SESSION['csrf_token'] ?? ''
            ];

            echo $this->view('doctor/patients', $data);
            exit();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function getPatientMedicalReports()
    {
        try {
            $patientId = $_GET['patient_id'] ?? null;

            if (!$patientId) {
                throw new \Exception("Patient ID is required");
            }

            // Get current doctor's ID
            $userId = $this->validateDoctorSession();
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $doctor = $stmt->get_result()->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            // Get medical reports
            $query = "SELECT 
            mr.*,
            u.first_name,
            u.last_name
            FROM appointments a
            JOIN users u ON a.patient_id = u.user_id
            LEFT JOIN medical_reports mr ON u.user_id = mr.patient_id
            WHERE a.patient_id = ? 
            AND a.doctor_id = ?
            AND mr.report_id IS NOT NULL
            ORDER BY mr.upload_date DESC";

            error_log("Executing query for patient ID: " . $patientId . " and doctor ID: " . $doctor['doctor_id']);

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctor['doctor_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            error_log("Found " . $result->num_rows . " medical reports");

            $reports = [];
            while ($row = $result->fetch_assoc()) {
                $reports[] = [
                    'report_id' => $row['report_id'],
                    'report_name' => $row['report_name'] ?? 'Medical Report',
                    'report_type' => $row['report_type'] ?? 'General',
                    'description' => $row['description'],
                    'upload_date' => date('F j, Y', strtotime($row['upload_date'])),
                    'file_path' => 'uploads/medical-reports/' . basename($row['file_path']), // Modify the file path
                    'patient_name' => $row['first_name'] . ' ' . $row['last_name']
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($reports);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getPatientMedicalReports: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // Add these patient-related methods as well
    public function getPatientAppointments()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            $patientId = $_GET['patient_id'] ?? 0;

            if (!$patientId) {
                throw new \Exception('Invalid patient ID');
            }

            $appointments = $this->appointmentModel->getPatientAppointments($patientId, $doctorId);

            header('Content-Type: application/json');
            echo json_encode($appointments);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }
    public function getAppointmentDetails()
    {
        try {
            // Get appointment_id from GET parameters
            $appointmentId = $_GET['appointment_id'] ?? null;

            if (!$appointmentId) {
                throw new \Exception("Appointment ID is required");
            }

            // Get current doctor's ID
            $userId = $this->validateDoctorSession();
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $doctor = $stmt->get_result()->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            // Get appointment details with validation
            $query = "SELECT 
            a.*, 
            u.first_name,
            u.last_name,
            u.email,
            u.phone_number
            FROM appointments a
            JOIN users u ON a.patient_id = u.user_id
            WHERE a.appointment_id = ? 
            AND a.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctor['doctor_id']);
            $stmt->execute();
            $details = $stmt->get_result()->fetch_assoc();

            if (!$details) {
                throw new \Exception("Appointment not found");
            }

            // Format date and time for display
            $details['appointment_date'] = date('Y-m-d', strtotime($details['appointment_date']));
            $details['appointment_time'] = date('g:i A', strtotime($details['appointment_time']));

            header('Content-Type: application/json');
            echo json_encode($details);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    public function getPatientHistory()
    {
        try {
            $doctorId = $this->validateDoctorSession();
            $patientId = $_GET['patient_id'] ?? 0;

            if (!$patientId) {
                throw new \Exception('Invalid patient ID');
            }

            $query = "SELECT 
                a.appointment_date,
                h.diagnosis,
                h.treatment_plan
                FROM appointments a
                LEFT JOIN healthrecords h ON a.appointment_id = h.appointment_id
                WHERE a.patient_id = ? AND a.doctor_id = ?
                ORDER BY a.appointment_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $records = [];
            while ($row = $result->fetch_assoc()) {
                $records[] = [
                    'appointment_date' => date('d/m/Y', strtotime($row['appointment_date'])),
                    'diagnosis' => $row['diagnosis'],
                    'treatment_plan' => $row['treatment_plan']
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($records);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    private function handleAppointmentActions($postData)
    {
        $action = $postData['action'] ?? '';
        $doctorId = $_SESSION['user_id'];

        switch ($action) {
            case 'add_availability':
                $this->doctorModel->addAvailability(
                    $doctorId,
                    $postData['day_of_week'],
                    $postData['start_time'],
                    $postData['end_time']
                );
                break;

            case 'update_appointment':
                if (!empty($postData['appointment_id']) && !empty($postData['status'])) {
                    $appointmentId = $postData['appointment_id'];
                    $newStatus = $postData['status'];

                    // Additional validation could be added here
                    $validStatuses = ['Scheduled', 'Completed', 'Canceled', 'Rescheduled'];
                    if (in_array($newStatus, $validStatuses)) {
                        // Assuming you'll add this method to the Appointment model
                        $this->appointmentModel->updateStatus($appointmentId, $newStatus);
                    }
                }
                break;
        }
    }

    private function updateDoctorProfile($doctorId, $postData)
    {
        $profileData = [
            'qualifications' => $postData['qualifications'] ?? '',
            'experience' => intval($postData['experience'] ?? 0),
            'description' => $postData['description'] ?? '',
            'hospital_id' => intval($postData['hospital_id'] ?? 0),
            'specializations' => $postData['specializations'] ?? []
        ];

        $this->doctorModel->updateProfile($doctorId, $profileData);
    }

    private function validateDoctorSession()
    {
        $doctorId = $_SESSION['user_id'] ?? null;
        if (!$doctorId) {
            header('Location: ' . $this->basePath . '/auth/login');
            exit();
        }
        return $doctorId;
    }

    private function handleError(\Exception $e)
    {
        error_log("Doctor Controller Error: " . $e->getMessage());

        // Check if database connection is available
        if (!$this->db || !$this->db->ping()) {
            error_log("Database connection lost");
            $this->reconnectDatabase();
        }

        // For AJAX requests
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => 'Database error occurred. Please try again later.',
                'debug' => DEBUG_MODE ? $e->getMessage() : null
            ]);
            exit();
        }

        $message = $this->getFormattedErrorMessage($e);

        $data = [
            'message' => $message,
            'basePath' => $this->basePath,
            'page_title' => 'Error',
            'current_page' => 'error'
        ];

        try {
            echo $this->view('errors/error', $data);
        } catch (\Exception $viewError) {
            // Fallback if view fails
            echo "<h1>Error</h1>";
            echo "<p>" . htmlspecialchars($message) . "</p>";
            echo "<a href='{$this->basePath}/doctor/dashboard'>Back to Dashboard</a>";
        }
        exit();
    }

    private function getFormattedErrorMessage(\Exception $e)
    {
        if ($e instanceof \mysqli_sql_exception) {
            // Check specific MySQL error codes
            switch ($e->getCode()) {
                case 2002: // Connection refused
                    return "Unable to connect to database. Please try again later.";
                case 2006: // Server gone away
                    return "Database connection was lost. Please refresh the page.";
                case 1045: // Access denied
                    return "Database authentication error. Please contact support.";
                default:
                    return "A database error occurred. Please try again later.";
            }
        } elseif ($e instanceof \PDOException) {
            return "Database connection error. Please try again later.";
        } else {
            return DEBUG_MODE ? $e->getMessage() : "An unexpected error occurred. Please try again later.";
        }
    }

    private function reconnectDatabase()
    {
        try {
            if ($this->db) {
                $this->db->close();
            }
            $this->db = new \mysqli('localhost', 'root', '', 'medceylon');
            if ($this->db->connect_error) {
                throw new \Exception("Failed to reconnect to database");
            }
        } catch (\Exception $e) {
            error_log("Database reconnection failed: " . $e->getMessage());
        }
    }

    public function getPatients()
    {
        try {
            // Get current doctor's ID from session
            $userId = $this->validateDoctorSession();

            // Get doctor ID from the doctors table
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];
            error_log("Fetching patients for doctor ID: " . $doctorId); // Debug log

            // Get patients list
            $patients = $this->doctorModel->getDoctorPatients($doctorId);
            error_log("Retrieved " . count($patients) . " patients");

            header('Content-Type: application/json');
            echo json_encode($patients);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getPatients: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }


    /**
     * View patient session from appointment
     * 
     * @param int $appointmentId The appointment ID
     * @return void
     */
    public function viewAppointment($appointmentId)
    {
        try {
            $userId = $this->validateDoctorSession();

            // Get doctor ID
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                error_log("No doctor record found for user ID: " . $userId);
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];

            // Check if appointment exists and belongs to this doctor
            $query = "SELECT a.*, u.first_name, u.last_name, a.session_id
                 FROM appointments a
                 JOIN users u ON a.patient_id = u.user_id
                 WHERE a.appointment_id = ? AND a.doctor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);
            $stmt->execute();
            $appointment = $stmt->get_result()->fetch_assoc();

            if (!$appointment) {
                $this->session->setFlash('error', 'Appointment not found or does not belong to you');
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Get or create session
            $sessionId = $appointment['session_id'];
            if (!$sessionId) {
                // If no session exists, create one
                $sessionData = [
                    'patient_id' => $appointment['patient_id'],
                    'status' => 'Active',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $sessionId = $this->medicalSessionModel->create($sessionData);
                if (!$sessionId) {
                    $this->session->setFlash('error', 'Failed to create medical session');
                    header('Location: ' . $this->url('doctor/dashboard'));
                    exit();
                }

                // Update the appointment with the session ID
                $this->appointmentModel->updateSessionId($appointmentId, $sessionId);
            }

            // Redirect to the patient session page
            header('Location: ' . $this->url('doctor/patient-session/' . $sessionId));
            exit();
        } catch (\Exception $e) {
            error_log("Error in viewAppointment: " . $e->getMessage());
            $this->session->setFlash('error', 'Error viewing appointment: ' . $e->getMessage());
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
    }

    

    /**
     * Send medical request
     */
    public function sendMedicalRequest()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Check CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $sessionId = $_POST['session_id'];
            $patientId = $_POST['patient_id'];
            $requestType = $_POST['request_type'];
            $requestDetails = $_POST['request_details'];

            // Verify the session exists
            $session = $this->medicalSessionModel->getById($sessionId);
            if (!$session || $session['patient_id'] != $patientId) {
                throw new \Exception("Invalid session");
            }

            // Get current doctor's ID
            $userId = $this->validateDoctorSession();
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];

            // Create medical request
            $requestData = [
                'session_id' => $sessionId,
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'request_type' => $requestType,
                'request_details' => $requestDetails,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insert into medical_requests table
            $fields = implode(', ', array_keys($requestData));
            $placeholders = implode(', ', array_fill(0, count($requestData), '?'));

            $query = "INSERT INTO medical_requests ({$fields}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($query);
            $types = str_repeat('s', count($requestData));
            $stmt->bind_param($types, ...array_values($requestData));

            if (!$stmt->execute()) {
                throw new \Exception("Failed to create medical request: " . $stmt->error);
            }

            $this->session->setFlash('success', 'Medical request sent successfully');
            header('Location: ' . $this->url('doctor/patient-session/' . $sessionId));
            exit();
        } catch (\Exception $e) {
            error_log("Error in sendMedicalRequest: " . $e->getMessage());
            $this->session->setFlash('error', 'Error sending medical request: ' . $e->getMessage());

            // Redirect back to the session page
            if (isset($_POST['session_id'])) {
                header('Location: ' . $this->url('doctor/patient-session/' . $_POST['session_id']));
            } else {
                header('Location: ' . $this->url('doctor/dashboard'));
            }
            exit();
        }
    }

   

    /**
     * Add specialist notes
     */
    public function addSpecialistNotes()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Check CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $sessionId = $_POST['session_id'];
            $notes = $_POST['specialist_notes'];

            // Save notes
            $query = "UPDATE medical_sessions SET specialist_notes = ? WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $notes, $sessionId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception("Failed to save specialist notes");
            }

            $this->session->setFlash('success', 'Specialist notes saved successfully');
            header('Location: ' . $this->url('doctor/patient-session/' . $sessionId));
            exit();
        } catch (\Exception $e) {
            error_log("Error in addSpecialistNotes: " . $e->getMessage());
            $this->session->setFlash('error', 'Error saving specialist notes: ' . $e->getMessage());

            // Redirect back to the session page
            if (isset($_POST['session_id'])) {
                header('Location: ' . $this->url('doctor/patient-session/' . $_POST['session_id']));
            } else {
                header('Location: ' . $this->url('doctor/dashboard'));
            }
            exit();
        }
    }

    /**
     * Save session notes (session.php form action route)
     */
    public function sessionSaveNotes()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Check CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $sessionId = $_POST['session_id'];
            $doctorId = $_POST['doctor_id'];
            $notes = $_POST['doctor_notes'];

            // Save notes
            $query = "UPDATE medical_sessions SET general_doctor_notes = ? WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $notes, $sessionId);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new \Exception("Failed to save notes");
            }

            $this->session->setFlash('success', 'Medical notes saved successfully');
            header('Location: ' . $this->url('doctor/patient-session/' . $sessionId));
            exit();
        } catch (\Exception $e) {
            error_log("Error in sessionSaveNotes: " . $e->getMessage());
            $this->session->setFlash('error', 'Error saving notes: ' . $e->getMessage());

            // Redirect back to the session page
            if (isset($_POST['session_id'])) {
                header('Location: ' . $this->url('doctor/patient-session/' . $_POST['session_id']));
            } else {
                header('Location: ' . $this->url('doctor/dashboard'));
            }
            exit();
        }
    }

    /**
     * Complete session from the session view
     */
    public function sessionComplete()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Check CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $sessionId = $_POST['session_id'];

            // Update session status to completed
            $query = "UPDATE medical_sessions SET status = 'Completed', updated_at = ? WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $updatedAt = date('Y-m-d H:i:s');
            $stmt->bind_param("si", $updatedAt, $sessionId);

            if (!$stmt->execute()) {
                throw new \Exception("Failed to complete session: " . $stmt->error);
            }

            $this->session->setFlash('success', 'Medical session marked as completed');
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in sessionComplete: " . $e->getMessage());
            $this->session->setFlash('error', 'Error completing session: ' . $e->getMessage());
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
    }

    /**
     * Helper method to get medical requests for a session
     */
    private function getMedicalRequests($sessionId)
    {
        try {
            $query = "SELECT mr.*, 
                  CONCAT(d.first_name, ' ', d.last_name) as doctor_name
                  FROM medical_requests mr
                  JOIN users d ON mr.doctor_id = d.user_id
                  WHERE mr.session_id = ?
                  ORDER BY mr.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();

            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }

            return $requests;
        } catch (\Exception $e) {
            error_log("Error getting medical requests: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Create a new session from an appointment
     * 
     * @param int $appointmentId The appointment ID
     * @return void
     */
    public function createSessionFromAppointment($appointmentId)
    {
        try {
            $userId = $this->validateDoctorSession();

            // Get doctor ID
            $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                error_log("No doctor record found for user ID: " . $userId);
                throw new \Exception("Doctor not found");
            }

            $doctorId = $doctor['doctor_id'];

            // Check if appointment exists and belongs to this doctor
            $query = "SELECT a.*, u.first_name, u.last_name, a.session_id
                 FROM appointments a
                 JOIN users u ON a.patient_id = u.user_id
                 WHERE a.appointment_id = ? AND a.doctor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);
            $stmt->execute();
            $appointment = $stmt->get_result()->fetch_assoc();

            if (!$appointment) {
                $this->session->setFlash('error', 'Appointment not found or does not belong to you');
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Check if a session already exists
            if (!empty($appointment['session_id'])) {
                // If session exists, just redirect to it
                header('Location: ' . $this->url('doctor/patient-session/' . $appointment['session_id']));
                exit();
            }

            // Create a new session
            $sessionData = [
                'patient_id' => $appointment['patient_id'],
                'status' => 'Active',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $sessionId = $this->medicalSessionModel->create($sessionData);
            if (!$sessionId) {
                $this->session->setFlash('error', 'Failed to create medical session');
                header('Location: ' . $this->url('doctor/dashboard'));
                exit();
            }

            // Update the appointment with the session ID
            $this->appointmentModel->updateSessionId($appointmentId, $sessionId);

            // Redirect to the new session
            $this->session->setFlash('success', 'Medical session created successfully');
            header('Location: ' . $this->url('doctor/patient-session/' . $sessionId));
            exit();
        } catch (\Exception $e) {
            error_log("Error in createSessionFromAppointment: " . $e->getMessage());
            $this->session->setFlash('error', 'Error creating session: ' . $e->getMessage());
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
    }


/**
 * Save session notes
 */
public function saveSessionNotes()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
        
        // Check CSRF token
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $sessionId = $_POST['session_id'];
        $doctorId = $_POST['doctor_id'];
        $notes = $_POST['doctor_notes'];
        
        // Save notes
        $query = "UPDATE medical_sessions SET general_doctor_notes = ? WHERE session_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $notes, $sessionId);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new \Exception("Failed to save notes");
        }
        
        $this->session->setFlash('success', 'Medical notes saved successfully');
        header('Location: ' . $this->url('doctor/session/' . $sessionId));
        exit();
    } catch (\Exception $e) {
        error_log("Error in saveSessionNotes: " . $e->getMessage());
        $this->session->setFlash('error', 'Error saving notes: ' . $e->getMessage());
        
        // Redirect back to the session page
        if (isset($_POST['session_id'])) {
            header('Location: ' . $this->url('doctor/session/' . $_POST['session_id']));
        } else {
            header('Location: ' . $this->url('doctor/dashboard'));
        }
        exit();
    }
}

private function getSessionAppointmentData($sessionId, $doctorId) 
{
    try {
        $query = "SELECT * FROM appointments 
                 WHERE session_id = ? AND doctor_id = ?
                 ORDER BY appointment_date DESC
                 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sessionId, $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return [];
    } catch (\Exception $e) {
        error_log("Error in getSessionAppointmentData: " . $e->getMessage());
        return [];
    }
}


/**
 * View a patient's medical session
 * 
 * @param int $sessionId The session ID
 * @return void
 */
public function session($sessionId)
{
    try {
        error_log("Loading session with ID: " . $sessionId);
        
        // Ensure sessionId is a valid integer
        $sessionId = (int)$sessionId;
        if ($sessionId <= 0) {
            error_log("Invalid session ID: " . $sessionId);
            throw new \Exception("Invalid session ID");
        }
        
        $userId = $this->validateDoctorSession();
        
        // Get doctor ID
        $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        
        if (!$doctor) {
            error_log("No doctor record found for user ID: " . $userId);
            throw new \Exception("Doctor not found");
        }
        
        $doctorId = $doctor['doctor_id'];
        error_log("Found doctor_id: " . $doctorId . " for user_id: " . $userId);
        
        // Get doctor info using direct query instead of method call
        $query = "SELECT 
                d.doctor_id,
                d.qualifications,
                d.years_of_experience,
                d.profile_description,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                h.name as hospital_name,
                h.hospital_id
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                WHERE d.doctor_id = ? AND d.is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $doctorInfo = $stmt->get_result()->fetch_assoc();
        
        // Check if session exists
        $session = $this->medicalSessionModel->getById($sessionId);
        if (!$session) {
            error_log("Medical session not found: " . $sessionId);
            $this->session->setFlash('error', 'Medical session not found');
            header('Location: ' . $this->basePath . '/doctor/dashboard');
            exit();
        }
        
        // Get patient information
        $patientId = $session['patient_id'];
        error_log("Session associated with patient ID: " . $patientId);
        
        // Get patient details using direct query
        $query = "SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.gender,
                u.date_of_birth as dob
                FROM users u
                WHERE u.user_id = ? AND u.is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("Patient not found for ID: " . $patientId);
            $this->session->setFlash('error', 'Patient not found');
            header('Location: ' . $this->basePath . '/doctor/dashboard');
            exit();
        }
        
        $patientInfo = $result->fetch_assoc();
        
        // Get all specializations for the referral dropdown
        $query = "SELECT specialization_id, name FROM specializations WHERE is_active = 1 ORDER BY name";
        $result = $this->db->query($query);
        
        if (!$result) {
            error_log("Error fetching specializations: " . $this->db->error);
            throw new \Exception("Failed to load specializations");
        }
        
        $specializations = [];
        while ($row = $result->fetch_assoc()) {
            $specializations[] = $row;
        }
        
        // Get session data for the view
        $sessionData = $this->prepareSessionData($session, $patientId);
        error_log("Prepared session data with " . ($sessionData['generalDoctorBooked'] ? "general doctor" : "no general doctor") . 
                 " and " . ($sessionData['specialistBooked'] ? "specialist" : "no specialist"));
        
        // Get appointment data
        $appointmentData = $this->getSessionAppointmentData($sessionId, $doctorId);
        
        // Get medical records
        $medicalRecords = $this->patientModel->getMedicalReports($patientId);
        $medicalRecordsArray = [];
        if ($medicalRecords && $medicalRecords instanceof \mysqli_result) {
            while ($row = $medicalRecords->fetch_assoc()) {
                $medicalRecordsArray[] = $row;
            }
        }
        error_log("Retrieved " . count($medicalRecordsArray) . " medical records for patient");
        $medicalRecords = $medicalRecordsArray;
        
        // Check if current doctor is the general doctor for this session
        $isGeneralDoctor = false;
        if ($sessionData['generalDoctorBooked'] && isset($sessionData['generalDoctor'])) {
            $isGeneralDoctor = ($sessionData['generalDoctor']['id'] == $doctorId);
        }
        
        // Check if current doctor is the specialist doctor for this session
        $isSpecialistDoctor = false;
        if ($sessionData['specialistBooked'] && isset($sessionData['specialist'])) {
            $isSpecialistDoctor = ($sessionData['specialist']['id'] == $doctorId);
        }
        
        error_log("Doctor role in session: " . 
                 ($isGeneralDoctor ? "General Doctor" : 
                 ($isSpecialistDoctor ? "Specialist" : "Neither general nor specialist")));
        
        // Prepare data for the view
        $data = [
            'patientInfo' => $patientInfo,
            'sessionData' => $sessionData,
            'appointmentData' => $appointmentData,
            'medicalRecords' => $medicalRecords,
            'specializations' => $specializations,
            'isGeneralDoctor' => $isGeneralDoctor,
            'isSpecialistDoctor' => $isSpecialistDoctor,
            'doctorInfo' => $doctorInfo,
            'basePath' => $this->basePath,
            'page_title' => 'Patient Session',
            'current_page' => 'patients'
        ];
        
        // Render the view
        error_log("Rendering session view for session ID: " . $sessionId);
        echo $this->view('doctor/session', $data);
        exit();
    } catch (\Exception $e) {
        error_log("Error in session method: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        $this->session->setFlash('error', 'Error loading patient session: ' . $e->getMessage());
        header('Location: ' . $this->basePath . '/doctor/dashboard');
        exit();
    }
}

/**
* Create a new session from an appointment
* 
* @param int $appointmentId The appointment ID
* @return void
*/
public function createSession($appointmentId = null)
{
   try {
       error_log("createSession method called with appointmentId: " . ($appointmentId ? $appointmentId : "null"));
       
       // If no appointmentId was passed, try to get it from the URL
       if ($appointmentId === null || empty($appointmentId)) {
           $uri = $_SERVER['REQUEST_URI'];
           error_log("Extracting appointment ID from URI: " . $uri);
           
           if (preg_match('/\/doctor\/session\/create\/(\d+)/', $uri, $matches)) {
               $appointmentId = (int)$matches[1];
               error_log("Extracted appointment ID: " . $appointmentId);
           } else {
               error_log("Failed to extract appointment ID from URL: " . $uri);
               throw new \Exception("Invalid appointment ID URL format");
           }
       }
       
       // Ensure appointmentId is a valid integer
       $appointmentId = (int)$appointmentId;
       if ($appointmentId <= 0) {
           error_log("Invalid appointment ID value: " . $appointmentId);
           throw new \Exception("Invalid appointment ID");
       }

       $userId = $this->validateDoctorSession();
       
       // Get doctor ID
       $query = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
       $stmt = $this->db->prepare($query);
       $stmt->bind_param("i", $userId);
       $stmt->execute();
       $result = $stmt->get_result();
       $doctor = $result->fetch_assoc();
       
       if (!$doctor) {
           error_log("No doctor record found for user ID: " . $userId);
           throw new \Exception("Doctor not found");
       }
       
       $doctorId = $doctor['doctor_id'];
       error_log("Found doctor_id: " . $doctorId . " for user_id: " . $userId);
       
       // Check if appointment exists and belongs to this doctor
       $query = "SELECT a.*, u.first_name, u.last_name, a.session_id
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.appointment_id = ? AND a.doctor_id = ?";
       $stmt = $this->db->prepare($query);
       $stmt->bind_param("ii", $appointmentId, $doctorId);
       $stmt->execute();
       $result = $stmt->get_result();
       
       if ($result->num_rows === 0) {
           error_log("Appointment not found or does not belong to doctor_id: " . $doctorId);
           $this->session->setFlash('error', 'Appointment not found or does not belong to you');
           header('Location: ' . $this->basePath . '/doctor/dashboard');
           exit();
       }
       
       $appointment = $result->fetch_assoc();
       error_log("Found appointment with ID: " . $appointmentId);
       
       // Check if a session already exists
       if (!empty($appointment['session_id'])) {
           error_log("Session already exists with ID: " . $appointment['session_id']);
           // If session exists, just redirect to it
           $sessionUrl = $this->basePath . '/doctor/session/' . $appointment['session_id'];
           error_log("Redirecting to existing session: " . $sessionUrl);
           header('Location: ' . $sessionUrl);
           exit();
       }
       
       // Create a new session
       $sessionData = [
           'patient_id' => $appointment['patient_id'],
           'status' => 'Active',
           'created_at' => date('Y-m-d H:i:s')
       ];
       
       error_log("Creating new medical session for patient_id: " . $appointment['patient_id']);
       $sessionId = $this->medicalSessionModel->create($sessionData);
       
       if (!$sessionId) {
           error_log("Failed to create medical session");
           $this->session->setFlash('error', 'Failed to create medical session');
           header('Location: ' . $this->basePath . '/doctor/dashboard');
           exit();
       }
       
       error_log("Created session with ID: " . $sessionId);
       
       // Update the appointment with the session ID
       $updateQuery = "UPDATE appointments SET session_id = ? WHERE appointment_id = ?";
       $updateStmt = $this->db->prepare($updateQuery);
       $updateStmt->bind_param("ii", $sessionId, $appointmentId);
       
       if (!$updateStmt->execute()) {
           error_log("Failed to update appointment with session ID: " . $this->db->error);
           throw new \Exception("Failed to update appointment with session ID");
       }
       
       error_log("Updated appointment " . $appointmentId . " with session_id " . $sessionId);
       
       // Redirect to the new session
       $redirectUrl = $this->basePath . '/doctor/session/' . $sessionId;
       error_log("Redirecting to: " . $redirectUrl);
       
       $this->session->setFlash('success', 'Medical session created successfully');
       header("Location: " . $redirectUrl);
       exit();
   } catch (\Exception $e) {
       error_log("Error in createSession: " . $e->getMessage() . "\n" . $e->getTraceAsString());
       $this->session->setFlash('error', 'Error creating session: ' . $e->getMessage());
       header('Location: ' . $this->basePath . '/doctor/dashboard');
       exit();
   }
}

/**
 * Prepare session data for view
 * 
 * @param array $session Session data from database
 * @param int $patientId Patient ID
 * @return array Formatted session data
 */
private function prepareSessionData($session, $patientId) 
{
    try {
        // Default structure with placeholders
        $sessionData = [
            'id' => $session['session_id'],
            'status' => $session['status'],
            'treatment_plan_id' => $session['treatment_plan_id'] ?? null,
            'generalDoctorBooked' => false,
            'specialistBooked' => false,
            'treatmentPlanCreated' => false,
            'transportBooked' => false,
            'travelPlanSelected' => false,
            'generalDoctor' => null,
            'specialist' => null,
            'general_doctor_notes' => $session['general_doctor_notes'] ?? null,
            'referral_reason' => $session['referral_reason'] ?? null,
            'specialist_notes' => $session['specialist_notes'] ?? null
        ];
        
        // If treatment plan exists, set flag and get details
        if ($session['treatment_plan_id']) {
            $sessionData['treatmentPlanCreated'] = true;
            
            // Get treatment plan details
            $query = "SELECT * FROM treatment_plans WHERE plan_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $session['treatment_plan_id']);
            $stmt->execute();
            $treatmentPlan = $stmt->get_result()->fetch_assoc();
            
            if ($treatmentPlan) {
                $sessionData['diagnosis'] = $treatmentPlan['diagnosis'] ?? null;
                $sessionData['treatment_description'] = $treatmentPlan['treatment_description'] ?? null;
                $sessionData['medications'] = $treatmentPlan['medications'] ?? null;
                $sessionData['travelRestrictions'] = $treatmentPlan['travel_restrictions'] ?? null;
                $sessionData['estimatedBudget'] = $treatmentPlan['estimated_budget'] ?? null;
                $sessionData['treatment_duration'] = $treatmentPlan['treatment_duration'] ?? null;
                $sessionData['follow_up'] = $treatmentPlan['follow_up'] ?? null;
                $sessionData['specialist_notes'] = $treatmentPlan['specialist_notes'] ?? null;
                
                // Get treatment plan creator
                $query = "SELECT 
                          CONCAT(u.first_name, ' ', u.last_name) as doctor_name
                          FROM treatment_plans tp
                          JOIN doctors d ON tp.doctor_id = d.doctor_id
                          JOIN users u ON d.user_id = u.user_id
                          WHERE tp.plan_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("i", $session['treatment_plan_id']);
                $stmt->execute();
                $doctor = $stmt->get_result()->fetch_assoc();
                
                if ($doctor) {
                    $sessionData['treatmentPlanCreator'] = $doctor['doctor_name'];
                }
            }
        }
        
        // Get appointments for this session
        $query = "SELECT 
                 a.*,
                 d.doctor_id,
                 u.first_name as doctor_first_name,
                 u.last_name as doctor_last_name,
                 s.name as specialization
                 FROM appointments a
                 JOIN doctors d ON a.doctor_id = d.doctor_id
                 JOIN users u ON d.user_id = u.user_id
                 LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                 LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                 WHERE a.session_id = ?
                 ORDER BY a.appointment_date ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $session['session_id']);
        $stmt->execute();
        $appointments = $stmt->get_result();
        
        while ($appointment = $appointments->fetch_assoc()) {
            // Check if general doctor or specialist based on specialization
            $isGeneral = ($appointment['specialization'] == 'General Medicine' || 
                          $appointment['specialization'] == 'General Practitioner' ||
                          !$appointment['specialization']);
            
            if ($isGeneral && !$sessionData['generalDoctorBooked']) {
                $sessionData['generalDoctorBooked'] = true;
                $sessionData['generalDoctor'] = [
                    'id' => $appointment['doctor_id'],
                    'name' => $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'],
                    'specialty' => $appointment['specialization'] ?? 'General Practitioner',
                    'appointmentDate' => $appointment['appointment_date'] . ' ' . $appointment['appointment_time'],
                    'appointmentMode' => $appointment['consultation_type'],
                    'meetLink' => $appointment['meet_link'] ?? ''
                ];
            } elseif (!$isGeneral) {
                $sessionData['specialistBooked'] = true;
                
                // Get hospital name
                $query = "SELECT h.name as hospital_name
                         FROM doctors d
                         LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                         WHERE d.doctor_id = ?";
                $stmt2 = $this->db->prepare($query);
                $stmt2->bind_param("i", $appointment['doctor_id']);
                $stmt2->execute();
                $hospital = $stmt2->get_result()->fetch_assoc();
                
                $sessionData['specialist'] = [
                    'id' => $appointment['doctor_id'],
                    'name' => $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'],
                    'specialty' => $appointment['specialization'] ?? 'Specialist',
                    'hospital' => $hospital['hospital_name'] ?? 'General Hospital',
                    'appointmentDate' => $appointment['appointment_date'],
                    'appointmentTime' => $appointment['appointment_time'],
                    'appointmentMode' => $appointment['consultation_type'],
                    'meetLink' => $appointment['meet_link'] ?? ''
                ];
            }
        }
        
        return $sessionData;
    } catch (\Exception $e) {
        error_log("Error in prepareSessionData: " . $e->getMessage());
        return [
            'id' => $session['session_id'],
            'status' => $session['status'],
            'generalDoctorBooked' => false,
            'specialistBooked' => false,
            'treatmentPlanCreated' => false,
            'transportBooked' => false,
            'travelPlanSelected' => false
        ];
    }
}
/**
 * Get recent appointments with specialist data for dashboard
 * Updated method to include more information about specialist appointments
 * 
 * @param int $doctorId The doctor ID
 * @param int $limit Maximum number of appointments to return
 * @return array Array of appointment data
 */
public function getRecentAppointmentsWithSpecialists($doctorId, $limit = 10)
{
    try {
        error_log("Getting recent appointments with specialists for doctor ID: " . $doctorId);

        $query = "SELECT a.*, 
                p.first_name AS patient_first_name, 
                p.last_name AS patient_last_name,
                p.email, 
                p.phone_number,
                a.session_id,
                ms.general_doctor_notes,
                ms.specialist_notes,
                ms.status AS session_status,
                
                -- Include specialist appointment info if available
                sa.appointment_id AS specialist_appointment_id,
                sa.appointment_date AS specialist_date,
                sa.appointment_time AS specialist_time,
                sa.consultation_type AS specialist_mode,
                sa.doctor_id AS specialist_id,
                
                -- Specialist doctor details
                su.first_name AS specialist_first_name,
                su.last_name AS specialist_last_name,
                s.name AS specialist_specialty,
                h.name AS specialist_hospital
                
                FROM appointments a
                JOIN users p ON a.patient_id = p.user_id
                LEFT JOIN medical_sessions ms ON a.session_id = ms.session_id
                
                -- Left join to find specialist appointment for the same session
                LEFT JOIN appointments sa ON (
                    a.session_id = sa.session_id 
                    AND sa.doctor_id != a.doctor_id
                    AND sa.appointment_id != a.appointment_id
                )
                
                -- Join specialist doctor info if available
                LEFT JOIN doctors sd ON sa.doctor_id = sd.doctor_id
                LEFT JOIN users su ON sd.user_id = su.user_id
                LEFT JOIN doctorspecializations ds ON sd.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                LEFT JOIN hospitals h ON sd.hospital_id = h.hospital_id
                
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC
                LIMIT ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Query preparation failed: " . $this->db->error);
            throw new \Exception("Failed to prepare query: " . $this->db->error);
        }

        $stmt->bind_param("ii", $doctorId, $limit);
        if (!$stmt->execute()) {
            error_log("Query execution failed: " . $stmt->error);
            throw new \Exception("Failed to execute query: " . $stmt->error);
        }

        $result = $stmt->get_result();
        error_log("Found " . $result->num_rows . " recent appointments");

        // Convert to array for easier handling in the view
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            // Check if specialist is booked
            $specialistBooked = !empty($row['specialist_id']);
            
            // Create formatted specialist name if available
            $specialistName = null;
            if ($specialistBooked) {
                $specialistName = $row['specialist_first_name'] . ' ' . $row['specialist_last_name'];
            }
            
            // Add the processed data to the appointment
            $row['specialist_booked'] = $specialistBooked;
            $row['specialist_name'] = $specialistName;
            
            // Add treatment plan status (simplified - you might need more checks for real data)
            $row['treatment_plan_created'] = !empty($row['treatment_plan_id']);
            
            $appointments[] = $row;
        }

        return $appointments;
    } catch (\Exception $e) {
        error_log("Error getting recent appointments with specialists: " . $e->getMessage());
        throw $e;
    }
}
/**
 * Save specialist notes
 */
public function saveSpecialistNotes()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
        
        // Check CSRF token
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $sessionId = $_POST['session_id'];
        $notes = $_POST['specialist_notes'];
        
        // Save notes to medical_sessions table
        $query = "UPDATE medical_sessions SET specialist_notes = ? WHERE session_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $notes, $sessionId);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new \Exception("Failed to save specialist notes");
        }
        
        $this->session->setFlash('success', 'Specialist notes saved successfully');
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    } catch (\Exception $e) {
        error_log("Error in saveSpecialistNotes: " . $e->getMessage());
        $this->session->setFlash('error', 'Error saving specialist notes: ' . $e->getMessage());
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    }
}

/**
 * Request medical tests
 */
public function requestMedicalTests()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
        
        // Check CSRF token
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $sessionId = $_POST['session_id'];
        $patientId = $_POST['patient_id'];
        $testType = $_POST['test_type'];
        $testDescription = $_POST['test_description'];
        $requiresFasting = $_POST['requires_fasting'] === 'yes';
        $urgency = $_POST['urgency'];
        
        // Get current doctor's ID
        $userId = $this->validateDoctorSession();
        $doctorId = $this->doctorModel->getDoctorIdByUserId($userId);
        
        if (!$doctorId) {
            throw new \Exception("Doctor not found");
        }
        
        // Insert into medical_tests table
        $query = "INSERT INTO medical_tests (
                    session_id, 
                    patient_id, 
                    doctor_id, 
                    test_type, 
                    test_description, 
                    requires_fasting, 
                    urgency,
                    status,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        
        $stmt = $this->db->prepare($query);
        $fastingInt = $requiresFasting ? 1 : 0;
        $stmt->bind_param("iiisssi", $sessionId, $patientId, $doctorId, $testType, $testDescription, $fastingInt, $urgency);
        
        if (!$stmt->execute()) {
            throw new \Exception("Failed to request medical test: " . $stmt->error);
        }
        
        $this->session->setFlash('success', 'Medical test requested successfully');
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    } catch (\Exception $e) {
        error_log("Error in requestMedicalTests: " . $e->getMessage());
        $this->session->setFlash('error', 'Error requesting medical test: ' . $e->getMessage());
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    }
}

/**
 * Cancel treatment
 */
public function cancelTreatment()
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $this->url('doctor/dashboard'));
            exit();
        }
        
        // Check CSRF token
        if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
            throw new \Exception("Invalid CSRF token");
        }
        
        $sessionId = $_POST['session_id'];
        $cancelReason = $_POST['cancel_reason'] ?? 'Treatment no longer required';
        
        // Update session status to canceled
        $query = "UPDATE medical_sessions SET status = 'Canceled', updated_at = ?, cancel_reason = ? WHERE session_id = ?";
        $stmt = $this->db->prepare($query);
        $updatedAt = date('Y-m-d H:i:s');
        $stmt->bind_param("ssi", $updatedAt, $cancelReason, $sessionId);
        
        if (!$stmt->execute()) {
            throw new \Exception("Failed to cancel treatment: " . $stmt->error);
        }
        
        // Also update appointment statuses
        $query = "UPDATE appointments SET appointment_status = 'Canceled' WHERE session_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sessionId);
        $stmt->execute();
        
        $this->session->setFlash('success', 'Treatment has been canceled successfully');
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    } catch (\Exception $e) {
        error_log("Error in cancelTreatment: " . $e->getMessage());
        $this->session->setFlash('error', 'Error canceling treatment: ' . $e->getMessage());
        header('Location: ' . $this->url('doctor/dashboard'));
        exit();
    }
}


}
