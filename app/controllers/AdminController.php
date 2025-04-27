<?php
namespace App\Controllers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\User;

class AdminController extends BaseController
{
    private $adminModel;
    private $userModel;
    private $appointmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel = new Admin();
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();

    }

    public function dashboard()
    {
        try {
            $patients_count = $this->adminModel->getPatientsCount();
            $doctors_count = $this->adminModel->getDoctorsCount();
            $hospitals_count = $this->adminModel->getHospitalsCount();
            $appointments = $this->adminModel->getUpcomingAppointments();
            $booking_count = $this->adminModel->getHotelBookingsCount();

            $data = [
                'patients_count' => $patients_count,
                'doctors_count' => $doctors_count,
                'hospitals_count' => $hospitals_count,
                'appointments' => $appointments,
                'booking_count' => $booking_count,
                'basePath' => $this->basePath
            ];

            echo $this->view('admin/dashboard', $data);
        } catch (\Exception $e) {
            error_log("Error in dashboard method: " . $e->getMessage());
            echo $this->view('admin/error', ['message' => 'An error occurred while loading the dashboard.']);
        }
    }

    public function userManagement()
    {
        try {
            $doctors = $this->adminModel->getDoctors();
            $patients = $this->adminModel->getPatients();
            $hospitals = $this->adminModel->getHospitals();

            $data = [
                'admin' => $this->adminModel,
                'doctors' => $doctors,
                'patients' => $patients,
                'hospitals' => $hospitals,
                'basePath' => $this->basePath
            ];


            echo $this->view('admin/user-management', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

    }


    public function editProfile()
    {
        try {
            $user_id = $_GET['user_id'] ?? null;
            $user = $this->adminModel->getUserById($user_id);
            $data = [
                'user' => $user,
                'basePath' => $this->basePath,
            ];

            echo $this->view('admin/editProfile', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

    }

    public function updateProfile()
    {
        try {

            if (isset($_POST['delete_user'])) {
                $user_id = $_POST['user_id'] ?? null;
                $this->adminModel->deleteUser($user_id);
                header("Location: " . $this->basePath . "/admin/user-management?success=User deleted successfully.");
                exit;
            }
            $user_id = $_POST['user_id'] ?? null;
            $first_name = $_POST['first_name'] ?? null;
            $last_name = $_POST['last_name'] ?? null;
            $email = $_POST['email'] ?? null;
            $phone_number = $_POST['phone_number'] ?? null;
            $address = $_POST['address'] ?? null;
            $city_id = isset($_POST['city']) ? (int) $_POST['city'] : null;

            // Validate input data
            if (empty($user_id) || empty($first_name) || empty($last_name) || empty($email)) {
                throw new \Exception("Required fields are missing.");
            }

            // Update the user profile in the database
            $this->adminModel->updateUserProfile($user_id, $first_name, $last_name, $email, $phone_number, $address, $city_id);

            // Redirect to the user management page or show a success message
            header("Location: " . $this->basePath . "/admin/user-management?success=Profile updated successfully.");
        } catch (\Exception $e) {
            error_log("Error in updateProfile method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => 'Failed to update profile.',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function addUser()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userData = [
                'user_type' => $_POST['user_type'] ?? '',
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'country' => $_POST['country'] ?? null,
                'contact_number' => $_POST['contact_number'] ?? null,
                'slmc_registration_number' => $_POST['slmc_registration_number'] ?? null,
                'age' => $_POST['age'] ?? null,
                'experience_years' => $_POST['experience_years'] ?? null,
                'formAction' => $this->basePath . '/admin/adduser'
            ];

            // 🔒 BACKEND VALIDATION
            if (!preg_match("/^[a-zA-Z\s]+$/", $userData['name'])) {
                $error = "Name can only contain letters and spaces.";
            } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $userData['password'])) {
                $error = "Password must be at least 8 characters with 1 uppercase & 1 digit.";
            } elseif ($userData['contact_number'] && !preg_match('/^\+?\d+$/', $userData['contact_number'])) {
                $error = "Invalid phone number.";
            } elseif ($userData['slmc_registration_number'] && !preg_match('/^SLMC\d+$/', $userData['slmc_registration_number'])) {
                $error = "SLMC number must start with 'SLMC' followed by digits.";
            } elseif ($userData['age'] && $userData['age'] < 18) {
                $error = "Age must be 18 or older.";
            } elseif ($userData['experience_years'] && $userData['experience_years'] < 0) {
                $error = "Experience must be a positive number.";
            }

            if (isset($error)) {
                echo $this->view('auth/register', [
                    'error' => $error,
                    'oldInput' => $userData,
                    'basePath' => $this->basePath
                ]);
                return;
            }

            // Register logic
            $result = $this->userModel->register($userData);

            if ($result['success']) {
                $_SESSION['registration_success'] = true;
                header("Location: {$this->basePath}/admin/user-management?success=User registered successfully.");
                exit();
            }

            echo $this->view('auth/register', [
                'error' => $result['error'],
                'oldInput' => $userData,
                'basePath' => $this->basePath,
                'formAction' => $this->basePath . '/admin/adduser'
            ]);
            return;
        }

        echo $this->view('auth/register', [
            'basePath' => $this->basePath,
            'formAction' => $this->basePath . '/admin/adduser'
        ]);
    }


    public function userProfiles($page)
    {
        try {
            $data = [];
            if ($page === 'doctors') {
                $data['profiles'] = $this->adminModel->getDoctors();
            } elseif ($page === 'patients') {
                $data['profiles'] = $this->adminModel->getPatients();
            } else {
                throw new \Exception("Invalid page type specified");
            }

            // Pass the page type to the view
            $data['page'] = $page;

            // Render the view
            echo $this->view('admin/user-profiles', $data);
        } catch (\Exception $e) {
            error_log("Error loading user profiles: " . $e->getMessage());
            echo $this->view('admin/error', ['message' => 'Failed to load profiles.']);
        }
    }


    public function appointments()
    {
        try {
            $appointments = $this->adminModel->getUpcomingAppointments();
            $data = [
                'appointments' => $appointments,
                'basePath' => $this->basePath
            ];
            echo $this->view('admin/appointments', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function getAppointments()
    {
        try {
            $appointments = $this->adminModel->getUpcomingAppointments();
            echo json_encode($appointments);
        } catch (\Exception $e) {
            error_log("Error in getAppointments method: " . $e->getMessage());
            echo json_encode(['error' => 'Failed to fetch appointments.']);
        }
    }

    public function bookings()
    {
        try {
            error_log("Entering dashboard method");
            $status = $_GET['status'] ?? 'Pending';

            $plans = $this->adminModel->getPatientPlan($status);
            
            $data = [
                'plans' => $plans,
                'basePath' => $this->basePath
            ];

            echo $this->view('admin/bookings', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in dashboard: " . $e->getMessage());
            throw $e;
        }

    }

    public function hotelBookings()
    {
        try {
            $status = $_GET['status'] ?? 'Pending';
            $hotelBooking = $this->adminModel->getStatusHotelBookings($status);
            $data = [
                'hotelBooking' => $hotelBooking,
                'basePath' => $this->basePath
            ];
            echo $this->view('admin/hotelBookings', $data);
        } catch (\Exception $e) {
            error_log("Error in hotelBooking: " . $e->getMessage());
            echo $this->view('admin/error', ['message' => 'An error occurred while loading the hotel booking page.']);
        }

    }

    public function confirmBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;

            if ($bookingId) {
                // Call your model method to confirm the booking
                $success = $this->adminModel->confirmBookingById($bookingId);

                // Return a JSON response to the frontend
                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false]);
                }
            } else {
                echo json_encode(['success' => false]);
            }

            exit(); // Make sure the script stops after sending the response
        }
    }

    public function rejectBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? null;

            if ($bookingId) {
                // Call your model method to reject the booking
                $success = $this->adminModel->rejectBookingById($bookingId);

                // Return a JSON response to the frontend
                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false]);
                }
            } else {
                echo json_encode(['success' => false]);
            }

            exit(); // Make sure the script stops after sending the response
        }
    }

}
?>