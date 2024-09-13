<?php
// controllers/AppointmentController.php

class AppointmentController {
    public function index() {
        // Load data from models
        $appointmentModel = new AppointmentModel();
        $appointments = $appointmentModel->getAllAppointments();

        // Check for search query
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

        if ($searchQuery) {
            // Filter appointments by search query
            $appointments = array_filter($appointments, function($appointment) use ($searchQuery) {
                return stripos($appointment['name'], $searchQuery) !== false;
            });
        }

        // Pass data to the view
        require_once 'views/appointments.php';
    }
}
?>
