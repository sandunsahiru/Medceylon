<?php
// controllers/BookAppointmentController.php

class BookAppointmentController {
    public function index() {
        // Ensure the user is logged in and is a patient
        require_once 'includes/SessionManager.php';
        SessionManager::requireLogin('Patient'); // Assuming 'Patient' is the role name

        $userId = SessionManager::getUserId();

        // Load the model
        require_once 'models/BookAppointmentModel.php';
        $model = new BookAppointmentModel();

        // Get list of available doctors
        $doctors = $model->getAvailableDoctors();

        // If form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $doctorId = $_POST['doctor_id'];
            $appointmentDate = $_POST['appointment_date'];
            $appointmentTime = $_POST['appointment_time'];
            $reasonForVisit = $_POST['reason_for_visit'];

            // Validate inputs
            if (empty($doctorId) || empty($appointmentDate) || empty($appointmentTime)) {
                $error = 'Please fill in all required fields.';
            } else {
                // Book the appointment
                $appointmentId = $model->bookAppointment($userId, $doctorId, $appointmentDate, $appointmentTime, $reasonForVisit);
                if ($appointmentId) {
                    $success = 'Your appointment has been booked successfully!';
                } else {
                    $error = 'Failed to book appointment. Please try again.';
                }
            }
        }

        // Load the view
        require_once 'views/book_appointment.php';
    }
}
?>
