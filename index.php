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
    // Add more cases for other pages like doctors, patients, etc.
    default:
        // Handle 404
        echo 'Page not found';
        break;
}
?>
