<?php
// controllers/MyAppointmentsController.php

class MyAppointmentsController {
    public function index() {
        // Ensure the user is logged in and is a patient
        require_once 'includes/SessionManager.php';
        SessionManager::requireLogin('Patient'); // Assuming 'Patient' is the role name

        $userId = SessionManager::getUserId();

        // Load the model
        require_once 'models/MyAppointmentsModel.php';
        $model = new MyAppointmentsModel();

        // Get appointments for the patient
        $appointments = $model->getAppointmentsByPatientId($userId);

        // Make $appointments available in the view
        include 'views/my_appointments.php';
    }
}
?>
