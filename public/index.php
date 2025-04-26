<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', dirname(__DIR__));


// Require dependencies
require_once ROOT_PATH . '/vendor/autoload.php';

// Check and load configurations
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

    // Public routes (no authentication required)
    $router->get('/', 'HomeController', 'index');
    $router->get('/index.php', 'HomeController', 'index');
    $router->get('/about-us', 'HomeController', 'aboutUs');
    $router->get('/partner-hospitals', 'HomeController', 'partnerHospitals');
    $router->get('/pricing', 'HomeController', 'pricing');

    // Auth routes
    $router->get('/login', 'AuthController', 'login');
    $router->post('/login', 'AuthController', 'login');
    $router->get('/register', 'AuthController', 'register');
    $router->post('/register', 'AuthController', 'register');
    $router->get('/logout', 'AuthController', 'logout');

    // Protected routes (require authentication)
    $router->get('/home', 'HomeController', 'home', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/ratedoctor', 'HomeController', 'rateDoctor');

    // Patient Chat Routes
    $router->get('/patient/chat', 'ChatController', 'index', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\AuthMiddleware::class);


    // Doctor Chat Routes
    $router->get('/doctor/chat', 'ChatController', 'index', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // VP Doctor Chat Routes
    $router->get('/vpdoctor/chat', 'ChatController', 'index', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    // Admin routes
    $router->get('/admin/dashboard', 'AdminController', 'dashboard', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/user-management', 'AdminController', 'userManagement', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/appointments', 'AdminController', 'appointments', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/bookings', 'AdminController', 'bookings', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/editProfile', 'AdminController', 'editProfile', \App\Core\Middleware\AdminMiddleware::class);
    $router->post('/admin/updateProfile', 'AdminController', 'updateProfile', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/hotelBookings', 'AdminController', 'hotelBookings', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/adduser', 'AdminController', 'addUser', \App\Core\Middleware\AdminMiddleware::class);
    $router->post('/admin/adduser', 'AdminController', 'addUser', \App\Core\Middleware\AdminMiddleware::class);
    $router->post('/admin/confirm-booking', 'AdminController', 'confirmBooking', \App\Core\Middleware\AdminMiddleware::class);
    $router->post('/admin/reject-booking', 'AdminController', 'rejectBooking', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/pending-bookings', 'AdminController', 'pendingBookings', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/ongoing-bookings', 'AdminController', 'ongoingBookings', \App\Core\Middleware\AdminMiddleware::class);
    $router->get('/admin/cancelled-bookings', 'AdminController', 'cancelledBookings', \App\Core\Middleware\AdminMiddleware::class);
    

    // Doctor Dashboard Routes
    $router->get('/doctor/dashboard', 'DoctorController', 'dashboard', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/appointments', 'DoctorController', 'appointments', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/patients', 'DoctorController', 'patients', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/profile', 'DoctorController', 'profile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/all-doctors', 'DoctorController', 'allDoctors', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/availability', 'DoctorController', 'availability', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Doctor POST routes for forms and actions
    $router->post('/doctor/appointments/update', 'DoctorController', 'handleAppointmentActions', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/profile/update', 'DoctorController', 'updateProfile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/availability/update', 'DoctorController', 'updateAvailability', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Doctor API routes
    // Doctor API routes
    $router->get('/doctor/getTimeSlots', 'DoctorController', 'getTimeSlots', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getDocProfile', 'DoctorController', 'getDocProfile', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getPatients', 'DoctorController', 'getPatients', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-patient-appointments', 'DoctorController', 'getPatientAppointments', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/get-patient-history', 'DoctorController', 'getPatientHistory', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/getPatientMedicalReports', 'DoctorController', 'getPatientMedicalReports', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/processBooking', 'DoctorController', 'processBooking', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/process-booking', 'DoctorController', 'processBooking', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // View existing session
    $router->get('/doctor/session/create/([0-9]+)', 'DoctorController', 'createSession', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/session/([0-9]+)', 'DoctorController', 'session', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->get('/doctor/view-appointment/([0-9]+)', 'DoctorController', 'createSession', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Save session notes
    $router->post('/doctor/session/save-notes', 'DoctorController', 'saveSessionNotes', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Refer to specialist
    $router->post('/doctor/refer-to-specialist', 'DoctorController@referToSpecialist', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Treatment plan actions
    $router->post('/doctor/create-treatment-plan', 'DoctorController@createTreatmentPlan', \App\Core\Middleware\DoctorAuthMiddleware::class);
    $router->post('/doctor/update-treatment-plan', 'DoctorController@updateTreatmentPlan', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Complete session
    $router->post('/doctor/session/complete', 'DoctorController@completeSession', \App\Core\Middleware\DoctorAuthMiddleware::class);

    $router->get('/doctor/patient-session/([0-9]+)', 'DoctorController', 'session', \App\Core\Middleware\DoctorAuthMiddleware::class);

    // Specialist Doctor Routes
    $router->get('/vpdoctor/dashboard', 'VPDoctorController', 'dashboard', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/appointments', 'VPDoctorController', 'appointments', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/patients', 'VPDoctorController', 'patients', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/profile', 'VPDoctorController', 'profile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    // Specialist Doctor API Routes
    $router->post('/vpdoctor/update-appointment-status', 'VPDoctorController', 'updateAppointmentStatus', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/manage-availability', 'VPDoctorController', 'manageAvailability', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/get-patient-details', 'VPDoctorController', 'getPatientDetails', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/getPatientMedicalReports', 'VPDoctorController', 'getPatientMedicalReports', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->get('/vpdoctor/get-appointment-details', 'VPDoctorController', 'getAppointmentDetails', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    // Specialist Profile Routes
    $router->post('/vpdoctor/update-profile', 'VPDoctorController', 'updateProfile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/update-specializations', 'VPDoctorController', 'updateSpecializations', \App\Core\Middleware\VPDoctorAuthMiddleware::class);
    $router->post('/vpdoctor/profile', 'VPDoctorController', 'profile', \App\Core\Middleware\VPDoctorAuthMiddleware::class);

    $router->get('/hospital/partner-hospitals', 'HospitalController', 'hospitals');

    // Hospital Dashboard Routes
    $router->get('/hospital/dashboard', 'HospitalController', 'dashboard', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('hospital/partials/header', 'HospitalController', 'hospitalName', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/treatment-requests', 'HospitalController', 'treatmentRequests', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/doctors', 'HospitalController', 'doctors', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/departments', 'HospitalController', 'departments', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/patients', 'HospitalController', 'patients', \App\Core\Middleware\HospitalAuthMiddleware::class);

    // Hospital API Routes
    $router->get('/hospital/get-request-details', 'HospitalController', 'getRequestDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/process-response', 'HospitalController', 'processResponse', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-department-details', 'HospitalController', 'getDepartmentDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-department', 'HospitalController', 'saveDepartment', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/add-department', 'HospitalController', 'addDepartment', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/delete-department', 'HospitalController', 'deleteDepartment', \App\Core\Middleware\HospitalAuthMiddleware::class);

    // Doctor Management Routes
    $router->get('/hospital/get-doctor-details', 'HospitalController', 'getDoctorDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-doctor', 'HospitalController', 'saveDoctor', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-doctor-schedule', 'HospitalController', 'getDoctorSchedule', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/save-doctor-schedule', 'HospitalController', 'saveDoctorSchedule', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/toggle-doctor-status', 'HospitalController', 'toggleDoctorStatus', \App\Core\Middleware\HospitalAuthMiddleware::class);

    // Patient Management Routes
    $router->get('/hospital/get-patient-details', 'HospitalController', 'getPatientDetails', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->get('/hospital/get-medical-history', 'HospitalController', 'getMedicalHistory', \App\Core\Middleware\HospitalAuthMiddleware::class);

    // Treatment Request Management Routes
    $router->post('/hospital/approve-request', 'HospitalController', 'approveRequest', \App\Core\Middleware\HospitalAuthMiddleware::class);
    $router->post('/hospital/reject-request', 'HospitalController', 'rejectRequest', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/hospital/complete-request', 'HospitalController', 'completeRequest', \App\Core\Middleware\AuthMiddleware::class);

    // Patient routes (existing)
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
    $router->get('/patient/paymentPlan', 'PatientController', 'paymentPlan', \App\Core\Middleware\AuthMiddleware::class);

    // Caregiver routes
    $router->get('/caregiver/dashboard', 'CaregiverController', 'dashboard', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/caregiver/patients', 'CaregiverController', 'patients', \App\Core\Middleware\AuthMiddleware::class);


    //travel routes
    $router->get('/travelplan/destinations', 'TravelPlanController', 'destinations', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/add-destination', 'TravelPlanController', 'addDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/edit-plan', 'TravelPlanController', 'editDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/delete-destination', 'TravelPlanController', 'deleteDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-plans', 'TravelPlanController', 'TravelPlans', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-preferences', 'TravelPlanController', 'travelPreferences', \App\Core\Middleware\AuthMiddleware::class);

    $router->get('/forgot-password', 'ForgotPasswordController', 'showForm');
    $router->post('/forgot-password', 'ForgotPasswordController', 'handleForm');
    $router->get('/reset-password', 'ForgotPasswordController', 'showResetForm');
    $router->post('/reset-password', 'ForgotPasswordController', 'handleReset');

    $router->get('/patient/transport', 'TransportationRequestController', 'index', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/transport/create', 'TransportationRequestController', 'create', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/save', 'TransportationRequestController', 'save', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/transport/edit/{id}', 'TransportationRequestController', 'edit', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/update/{id}', 'TransportationRequestController', 'update', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/delete/{id}', 'TransportationRequestController', 'delete', \App\Core\Middleware\AuthMiddleware::class);
    


    // Transportation Module — Travel Agent
    $router->get('/agent/transport-requests', 'AgentTransportationController', 'index', \App\Core\Middleware\TravelAgentMiddleware::class);
    $router->get('/agent/transport/view/{id}', 'AgentTransportationController', 'view', \App\Core\Middleware\TravelAgentMiddleware::class);
    $router->post('/agent/transport/respond/{id}', 'AgentTransportationController', 'respond', \App\Core\Middleware\TravelAgentMiddleware::class);

    $router->get('/caregivers', 'CaregiverMessageController', 'list');
    $router->get('/caregiver/profile/{id}', 'CaregiverMessageController', 'viewProfile');
    $router->post('/caregiver/send-message/{id}', 'CaregiverMessageController', 'sendMessage');
    $router->get('/caregiver/chat/{id}', 'CaregiverMessageController', 'viewChat');
    $router->get('/caregiver/dashboard', 'CaregiverMessageController', 'dashboard', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/caregivers', 'CaregiverMessageController', 'list');
    $router->post('/caregiver/request/{id}', 'CaregiverRequestController', 'sendRequest', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/caregiver/requests', 'CaregiverRequestController', 'viewRequests', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/caregiver/respond/{id}', 'CaregiverRequestController', 'respond', \App\Core\Middleware\AuthMiddleware::class);

    $router->get('/debug/test-meet', 'DebugController', 'testMeet');
    $router->get('/debug/check-calendar', 'DebugController', 'checkCalendarAccess');

    // TEMP TEST (Remove later)
    $router->get('/agent/test', 'AgentTransportationController', 'index');
    // Add these debug routes
    $router->get('/debug/test-meet', 'DebugController', 'testMeet');
    $router->get('/debug/check-calendar', 'DebugController', 'checkCalendarAccess');
    $router->get('/debug/test-basic-event', 'DebugController', 'testBasicEvent');
    $router->get('/debug/server-info', 'DebugController', 'serverInfo');
    $router->get('/debug/test-service-account', 'DebugController', 'testServiceAccount');
    $router->get('/debug/test-basic-calendar-event', 'DebugController', 'testBasicCalendarEvent');
    $router->get('/debug/test-conference-event', 'DebugController', 'testConferenceEvent');
    $router->get('/debug/test-auth', 'DebugController', 'testAuth');

    // Set 404 handler
    $router->setNotFound(function () {
        header("HTTP/1.0 404 Not Found");
        require ROOT_PATH . '/app/views/errors/404.php';
        exit();
    });

    // Get current URI and dispatch
    $uri = $_SERVER['REQUEST_URI'];
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
