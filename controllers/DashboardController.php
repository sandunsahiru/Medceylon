<?php
// controllers/DashboardController.php

class DashboardController {
    public function index() {
        // Load data from models
        $appointmentModel = new AppointmentModel();
        $summaryData = $appointmentModel->getSummaryData();
        $appointments = $appointmentModel->getNewAppointments();

        // Pass data to the view
        require_once 'views/dashboard.php';
    }
}
?>
