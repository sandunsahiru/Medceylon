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
            $doctorId = $this->validateDoctorSession();

            // Handle POST requests for appointment updates
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleAppointmentActions($_POST);
                // Redirect to prevent form resubmission
                header("Location: " . $this->basePath . "/doctor/appointments");
                exit();
            }

            $appointments = $this->doctorModel->getAppointmentHistory($doctorId);
            $availability = $this->doctorModel->getDoctorAvailability($doctorId);

            $data = [
                'appointments' => $appointments,
                'availability' => $availability,
                'basePath' => $this->basePath,
                'page_title' => 'Appointments',
                'current_page' => 'appointments'
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
            $doctorId = $this->validateDoctorSession();
            $date = $_GET['date'] ?? date('Y-m-d');

            $slots = $this->doctorModel->getAvailableTimeSlots($doctorId, $date);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'slots' => $slots]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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
    public function getDoctorProfile()
    {
        try {
            $doctorId = $_GET['doctor_id'] ?? 0;

            if (!$doctorId) {
                throw new \Exception('Invalid doctor ID');
            }

            $profile = $this->doctorModel->getDoctorProfile($doctorId);

            header('Content-Type: application/json');
            echo json_encode($profile);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    // Method to process specialist booking
    public function processBooking()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            $required = ['specialist_id', 'patient_id', 'consultation_type', 'preferred_date'];
            foreach ($required as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            $bookingData = [
                'specialist_id' => $_POST['specialist_id'],
                'patient_id' => $_POST['patient_id'],
                'consultation_type' => $_POST['consultation_type'],
                'preferred_date' => $_POST['preferred_date'],
                'medical_history' => $_POST['medical_history'] ?? '',
                'referring_doctor_id' => $doctorId,
                'notes' => "Referred by Doctor ID: " . $doctorId,
                'appointment_status' => 'Asked'
            ];

            $appointmentId = $this->appointmentModel->bookAppointment($bookingData);

            if ($appointmentId) {
                header("Location: " . $this->basePath . "/doctor/all-doctors?booking=success");
            } else {
                throw new \Exception("Failed to book appointment");
            }
            exit();
        } catch (\Exception $e) {
            header("Location: " . $this->basePath . "/doctor/all-doctors?booking=error&message=" . urlencode($e->getMessage()));
            exit();
        }
    }
    public function patients()
    {
        try {
            $doctorId = $this->validateDoctorSession();

            // Get basic statistics
            $stats = $this->doctorModel->getDoctorDashboardStats($doctorId);

            // Get patients list with their appointment history
            $patientsQuery = "SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.gender,
                COUNT(a.appointment_id) as total_visits,
                MAX(a.appointment_date) as last_visit
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ?
                GROUP BY u.user_id
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
}
