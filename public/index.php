<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));

// Autoload (Composer)
require_once ROOT_PATH . '/vendor/autoload.php';

// Load configs
if (!file_exists(ROOT_PATH . '/app/config/database.php')) {
    die('Database configuration file not found');
}
if (!file_exists(ROOT_PATH . '/app/config/app.php')) {
    die('Application configuration file not found');
}

require_once ROOT_PATH . '/app/config/database.php';
$config = require_once ROOT_PATH . '/app/config/app.php';

try {
    $router = new \App\Core\Router();

    // 🟢 Public Routes
    $router->get('/', 'HomeController', 'index');
    $router->get('/index.php', 'HomeController', 'index');
    $router->get('/about-us', 'HomeController', 'aboutUs');
    $router->get('/partner-hospitals', 'HomeController', 'partnerHospitals');
    $router->get('/pricing', 'HomeController', 'pricing');

    // 🔐 Auth Routes
    $router->get('/login', 'AuthController', 'login');
    $router->post('/login', 'AuthController', 'login');
    $router->get('/register', 'AuthController', 'register');
    $router->post('/register', 'AuthController', 'register');
    $router->get('/logout', 'AuthController', 'logout');
    $router->get('/forgot-password', 'AuthController', 'forgotPassword');
    $router->post('/forgot-password', 'AuthController', 'forgotPassword');
    $router->get('/reset-password', 'AuthController', 'resetPassword');
    $router->post('/reset-password', 'AuthController', 'resetPassword');


$router->get('/forgot-password', 'ForgotPasswordController', 'showForm');
$router->post('/forgot-password', 'ForgotPasswordController', 'handleForm');
$router->get('/reset-password', 'ForgotPasswordController', 'showResetForm');
$router->post('/reset-password', 'ForgotPasswordController', 'handleReset');

    // 👤 Home Dashboard (Authenticated)
    $router->get('/home', 'HomeController', 'home', \App\Core\Middleware\AuthMiddleware::class);

    // 💬 Patient Chat
    $router->get('/patient/chat', 'ChatController', 'index', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\AuthMiddleware::class);

    // 💬 Doctor Chat
    $router->get('/doctor/chat', 'ChatController', 'index', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // 💬 Specialist (VP) Doctor Chat
    $router->get('/vpdoctor/chat', 'ChatController', 'index', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    // Admin routes
    $router->get('/admin/dashboard', 'AdminController', 'dashboard', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/overview', 'AdminController', 'overview', \App\Core\Middleware\AdminMiddleware::class);

    // 👨‍⚕️ Doctor Dashboard
    $router->get('/doctor/dashboard', 'DoctorController', 'dashboard', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/appointments', 'DoctorController', 'appointments', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/patients', 'DoctorController', 'patients', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/profile', 'DoctorController', 'profile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/all-doctors', 'DoctorController', 'allDoctors', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/availability', 'DoctorController', 'availability', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/appointments/update', 'DoctorController', 'handleAppointmentActions', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/profile/update', 'DoctorController', 'updateProfile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/availability/update', 'DoctorController', 'updateAvailability', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getTimeSlots', 'DoctorController', 'getTimeSlots', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getDocProfile', 'DoctorController', 'getDocProfile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getPatients', 'DoctorController', 'getPatients', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-patient-appointments', 'DoctorController', 'getPatientAppointments', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-patient-history', 'DoctorController', 'getPatientHistory', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getPatientMedicalReports', 'DoctorController', 'getPatientMedicalReports', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/processBooking', 'DoctorController', 'processBooking', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/process-booking', 'DoctorController', 'processBooking', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // 👨‍⚕️ Specialist (VP) Doctor Dashboard
    $router->get('/vpdoctor/dashboard', 'VPDoctorController', 'dashboard', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/appointments', 'VPDoctorController', 'appointments', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/patients', 'VPDoctorController', 'patients', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/profile', 'VPDoctorController', 'profile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/update-appointment-status', 'VPDoctorController', 'updateAppointmentStatus', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/manage-availability', 'VPDoctorController', 'manageAvailability', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/get-patient-details', 'VPDoctorController', 'getPatientDetails', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/getPatientMedicalReports', 'VPDoctorController', 'getPatientMedicalReports', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/update-profile', 'VPDoctorController', 'updateProfile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/update-specializations', 'VPDoctorController', 'updateSpecializations', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/profile', 'VPDoctorController', 'profile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    // 🏥 Hospital Dashboard
    $router->get('/hospital/dashboard', 'HospitalController', 'dashboard', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/treatment-requests', 'HospitalController', 'treatmentRequests', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/doctors', 'HospitalController', 'doctors', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/departments', 'HospitalController', 'departments', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/patients', 'HospitalController', 'patients', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-request-details', 'HospitalController', 'getRequestDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/process-response', 'HospitalController', 'processResponse', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-department-details', 'HospitalController', 'getDepartmentDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-department', 'HospitalController', 'saveDepartment', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/delete-department', 'HospitalController', 'deleteDepartment', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-doctor-details', 'HospitalController', 'getDoctorDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-doctor', 'HospitalController', 'saveDoctor', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-doctor-schedule', 'HospitalController', 'getDoctorSchedule', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-doctor-schedule', 'HospitalController', 'saveDoctorSchedule', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/toggle-doctor-status', 'HospitalController', 'toggleDoctorStatus', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-patient-details', 'HospitalController', 'getPatientDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-medical-history', 'HospitalController', 'getMedicalHistory', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/approve-request', 'HospitalController', 'approveRequest', \App\Core\Middleware\HospitalAuthMiddleware::class);

    // 👤 Patient Dashboard
    $router->get('/patient/dashboard', 'PatientController', 'dashboard', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/book-appointment', 'PatientController', 'bookAppointment', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/process-appointment', 'PatientController', 'processAppointment', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/medical-history', 'PatientController', 'medicalHistory', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/profile', 'PatientController', 'profile', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/update-profile', 'PatientController', 'updateProfile', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/delete-profile', 'PatientController', 'deleteProfile', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/get-time-slots', 'PatientController', 'getTimeSlots', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/get-appointment-details', 'PatientController', 'getAppointmentDetails', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/upload-medical-report', 'PatientController', 'uploadMedicalReport', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/delete-medical-report', 'PatientController', 'deleteMedicalReport', \App\Core\Middleware\AuthMiddleware::class);

    // 🧑‍⚕️ Caregiver Routes
    $router->get('/caregiver/dashboard', 'CaregiverController', 'dashboard', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/caregiver/patients', 'CaregiverController', 'patients', \App\Core\Middleware\AuthMiddleware::class);

    
    //travel routes
    $router->get('/travelplan/destinations', 'TravelPlanController', 'destinations', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/add-destination', 'TravelPlanController', 'addDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/edit-plan', 'TravelPlanController','editDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/delete-destination', 'TravelPlanController','deleteDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-plans', 'TravelPlanController','TravelPlans', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-preferences', 'TravelPlanController','travelPreferences', \App\Core\Middleware\AuthMiddleware::class);



    // Set 404 handler
    $router->setNotFound(function () {
        header("HTTP/1.0 404 Not Found");
        require ROOT_PATH . '/app/views/errors/404.php';
        exit();
    });

    // 🔁 Dispatch the Route
    $uri = $_SERVER['REQUEST_URI'];
    error_log("Dispatching URI: " . $uri); // Debug log
    $router->dispatch($uri);

} catch (\Exception $e) {
    error_log($e->getMessage());

    if (isset($config['debug']) && $config['debug'] === true) {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        require ROOT_PATH . '/app/views/errors/500.php';
    }
}
