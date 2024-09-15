<?php
// controllers/PatientDashboardController.php

class PatientDashboardController {
    public function index() {
        // Ensure the user is logged in and is a patient
        require_once 'includes/SessionManager.php';
        SessionManager::requireLogin('Patient'); // Assuming 'Patient' is the role name

        $userId = SessionManager::getUserId();

        // Load data from models
        require_once 'models/PatientModel.php';
        $patientModel = new PatientModel();

        // Fetch data for the dashboard
        $appointments = $patientModel->getUpcomingAppointments($userId);
        $notifications = $patientModel->getNotifications($userId);

        // Pass data to the view
        require_once 'views/patient_dashboard.php';
    }
}
?>
