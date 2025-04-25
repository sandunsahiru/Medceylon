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

    // Public routes
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

    // Home dashboard
    $router->get('/home', 'HomeController', 'home', \App\Core\Middleware\AuthMiddleware::class);

    // Chat routes
    $router->get('/patient/chat', 'ChatController', 'index', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/send-message', 'ChatController', 'sendMessage', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/get-new-messages', 'ChatController', 'getNewMessages', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/archive-conversation', 'ChatController', 'archiveConversation', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/download-attachment', 'ChatController', 'downloadAttachment', \App\Core\Middleware\AuthMiddleware::class);

    // Caregiver routes
    $router->get('/caregivers', 'CaregiverMessageController', 'list');
    $router->get('/caregiver/profile/{id}', 'CaregiverMessageController', 'viewProfile');
    $router->post('/caregiver/send-message/{id}', 'CaregiverMessageController', 'sendMessage');
    $router->get('/caregiver/chat/{id}', 'CaregiverMessageController', 'viewChat');
    $router->get('/caregiver/dashboard', 'CaregiverMessageController', 'dashboard', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/caregiver/request/{id}', 'CaregiverRequestController', 'sendRequest', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/caregiver/requests', 'CaregiverRequestController', 'viewRequests', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/caregiver/respond/{id}', 'CaregiverRequestController', 'respond', \App\Core\Middleware\AuthMiddleware::class);

    // Caregiver rating
    $router->get('/caregiver/rate/{id}', 'CaregiverRatingController', 'showRatingForm', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/caregiver/save-rating/{id}', 'CaregiverRatingController', 'saveRating', \App\Core\Middleware\AuthMiddleware::class);

    // Travel routes
    $router->get('/travelplan/destinations', 'TravelPlanController', 'destinations', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/add-destination', 'TravelPlanController', 'addDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/edit-plan', 'TravelPlanController','editDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/travelplan/delete-destination', 'TravelPlanController','deleteDestination', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-plans', 'TravelPlanController','TravelPlans', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/travelplan/travel-preferences', 'TravelPlanController','travelPreferences', \App\Core\Middleware\AuthMiddleware::class);

    // Forgot password
    $router->get('/forgot-password', 'ForgotPasswordController', 'showForm');
    $router->post('/forgot-password', 'ForgotPasswordController', 'handleForm');
    $router->get('/reset-password', 'ForgotPasswordController', 'showResetForm');
    $router->post('/reset-password', 'ForgotPasswordController', 'handleReset');

    // Transportation
    $router->get('/patient/transport', 'TransportationRequestController', 'index', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/transport/create', 'TransportationRequestController', 'create', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/save', 'TransportationRequestController', 'save', \App\Core\Middleware\AuthMiddleware::class);
    $router->get('/patient/transport/edit/{id}', 'TransportationRequestController', 'edit', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/update/{id}', 'TransportationRequestController', 'update', \App\Core\Middleware\AuthMiddleware::class);
    $router->post('/patient/transport/delete/{id}', 'TransportationRequestController', 'delete', \App\Core\Middleware\AuthMiddleware::class);

    $router->get('/agent/transport-requests', 'AgentTransportationController', 'index', \App\Core\Middleware\TravelAgentMiddleware::class);
    $router->get('/agent/transport/view/{id}', 'AgentTransportationController', 'view', \App\Core\Middleware\TravelAgentMiddleware::class);
    $router->post('/agent/transport/respond/{id}', 'AgentTransportationController', 'respond', \App\Core\Middleware\TravelAgentMiddleware::class);

    $router->post('/agent/transport/complete/{id}', 'AgentTransportationController', 'complete');

    $router->get('/patient/transport/report', 'TransportationRequestController', 'downloadReport');

    $router->setNotFound(function () {
        header("HTTP/1.0 404 Not Found");
        require ROOT_PATH . '/app/views/errors/404.php';
        exit();
    });

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
