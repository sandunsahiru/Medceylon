<?php
// index.php

// Autoload controllers, models, and includes
spl_autoload_register(function ($class_name) {
    $paths = ['controllers/', 'models/', 'includes/'];
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Start session
require_once 'includes/SessionManager.php';
SessionManager::startSession();

// Check if a role selection has been made
if (isset($_POST['role'])) {
    // Simulate login by setting session variables
    $selectedRole = $_POST['role'];
    switch ($selectedRole) {
        case 'GeneralDoctor':
            // Set session variables for General Doctor
            SessionManager::setSampleSessionData(1, 'GeneralDoctor'); // Replace 1 with a valid user_id
            break;
        case 'Doctor':
            // Set session variables for Specialist Doctor
            SessionManager::setSampleSessionData(2, 'Doctor'); // Replace 2 with a valid user_id
            break;
        case 'Patient':
            // Set session variables for Patient
            SessionManager::setSampleSessionData(3, 'Patient'); // Replace 3 with a valid user_id
            break;
        default:
            // Invalid role selected
            echo 'Invalid role selected.';
            exit();
    }
    // Redirect to the dashboard
    header('Location: index.php?page=dashboard');
    exit();
}

// Get current page
$page = isset($_GET['page']) ? $_GET['page'] : 'select_role';

// Get user role
$userRole = SessionManager::getUserRole();

// Basic routing
switch ($page) {
    case 'select_role':
        // Display the role selection page
        require_once 'views/select_role.php';
        break;

    case 'dashboard':
        if ($userRole === 'Doctor') {
            $controller = new SpecialistDashboardController();
            $controller->index();
        } elseif ($userRole === 'GeneralDoctor') {
            $controller = new DashboardController();
            $controller->index();
        } elseif ($userRole === 'Patient') {
            $controller = new PatientDashboardController();
            $controller->index();
        } else {
            // Redirect to role selection page
            header('Location: index.php?page=select_role');
            exit();
        }
        break;

    case 'appointments':
        if ($userRole === 'Doctor' || $userRole === 'GeneralDoctor') {
            $controller = new AppointmentController();
            $controller->index();
        } else {
            echo 'Unauthorized access';
        }
        break;

    case 'doctors':
        if ($userRole === 'GeneralDoctor' || $userRole === 'Patient' || $userRole === 'Doctor') {
            $controller = new DoctorController();
            $controller->index();
        } else {
            echo 'Unauthorized access';
        }
        break;

    case 'patients':
        if ($userRole === 'Doctor' || $userRole === 'GeneralDoctor') {
            $controller = new PatientController();
            $controller->index();
        } else {
            echo 'Unauthorized access';
        }
        break;

    case 'chat':
        if ($userRole) { // Allow any logged-in user to access chat
            $controller = new ChatController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->sendMessage();
            } elseif (isset($_GET['action'])) {
                if ($_GET['action'] === 'fetchMessages') {
                    $controller->fetchMessages();
                } elseif ($_GET['action'] === 'startConversation') {
                    $controller->startConversation();
                }
            } else {
                $controller->index();
            }
        } else {
            echo 'Unauthorized access';
        }
        break;
    case 'book_appointment':
        if ($userRole === 'Patient') {
            $controller = new BookAppointmentController();
            $controller->index();
        } else {
            echo 'Unauthorized access';
        }
        break;
    case 'my_appointments':
        if ($userRole === 'Patient') {
            $controller = new MyAppointmentsController();
            $controller->index();
        } else {
            echo 'Unauthorized access';
        }
        break;
    case 'profile':
        if ($userRole === 'Patient') {
            $controller = new PatientProfileController();
            if (isset($_GET['action']) && $_GET['action'] === 'update') {
                $controller->update();
            } else {
                $controller->index();
            }
        } else {
            echo 'Unauthorized access';
        }
        break;


        // Add more cases for other pages like help, etc.

    default:
        // Handle 404
        echo 'Page not found';
        break;
}
