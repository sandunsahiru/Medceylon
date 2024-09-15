<?php
// controllers/SpecialistDashboardController.php

class SpecialistDashboardController {
    public function index() {
        // Ensure the user is logged in and is a specialist doctor
        require_once 'includes/SessionManager.php';
        SessionManager::requireLogin('Doctor'); // Assuming 'Doctor' is the role name

        $userId = SessionManager::getUserId();

        // Load data from models
        require_once 'models/SpecialistAppointmentModel.php';
        $appointmentModel = new SpecialistAppointmentModel();

        // Fetch summary data and appointments from the database
        $summaryData = $appointmentModel->getSummaryData($userId);
        $appointments = $appointmentModel->getNewAppointments($userId);

        // Pass data to the view
        require_once 'views/specialist_dashboard.php';
    }
}
?>
