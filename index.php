<?php
// index.php

// Autoload controllers and models
spl_autoload_register(function ($class_name) {
    if (file_exists('controllers/' . $class_name . '.php')) {
        require_once 'controllers/' . $class_name . '.php';
    } elseif (file_exists('models/' . $class_name . '.php')) {
        require_once 'models/' . $class_name . '.php';
    }
});

// Start session
session_start();

// Basic routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

switch ($page) {
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
    case 'appointments':
        $controller = new AppointmentController();
        $controller->index();
        break;
    case 'doctors':
        $controller = new DoctorController();
        $controller->index();
        break;
    case 'patients':
        $controller = new PatientController();
        $controller->index();
        break;
    // Add more cases for other pages like help, etc.
    default:
        // Handle 404
        echo 'Page not found';
        break;
}
?>
