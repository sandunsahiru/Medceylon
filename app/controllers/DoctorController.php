<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Doctor;
use App\Models\Appointment;

class DoctorController extends BaseController
{
    private $doctorModel;
    private $appointmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->doctorModel = new Doctor();
        $this->appointmentModel = new Appointment();
    }

    public function dashboard()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            // Get dashboard statistics
            $stats = $this->doctorModel->getDoctorDashboardStats($doctorId);
            $upcomingAppointments = $this->appointmentModel->getUpcomingAppointments($doctorId);

            $data = [
                'stats' => $stats,
                'appointments' => $upcomingAppointments,
                'basePath' => $this->basePath,
                'page_title' => 'Doctor Dashboard',
                'current_page' => 'dashboard'
            ];

            echo $this->view('doctor/dashboard', $data);
            exit();
        } catch (\Exception $e) {
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

            $doctorId = (int)$_GET['doctor_id'];
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
}
