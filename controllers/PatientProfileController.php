<?php
// controllers/PatientProfileController.php

class PatientProfileController {
    private $patientProfileModel;

    public function __construct() {
        require_once 'models/PatientProfileModel.php';
        $this->patientProfileModel = new PatientProfileModel();
    }

    public function index() {
        require_once 'includes/SessionManager.php';
        $patientId = SessionManager::getUserId();

        // Fetch patient profile
        $patientProfile = $this->patientProfileModel->getPatientProfile($patientId);

        // Load the profile view
        require 'views/patient_profile.php';
    }

    public function update() {
        require_once 'includes/SessionManager.php';
        $patientId = SessionManager::getUserId();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Gather data from the form
            $profileData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number'],
                'address_line1' => $_POST['address_line1'],
                'address_line2' => $_POST['address_line2'],
                'city_id' => $_POST['city_id']
            ];

            // Update the profile
            if ($this->patientProfileModel->updatePatientProfile($patientId, $profileData)) {
                // Redirect back to the profile page with success message
                header('Location: index.php?page=profile&status=success');
                exit();
            } else {
                header('Location: index.php?page=profile&status=error');
                exit();
            }
        }
    }
}
?>
