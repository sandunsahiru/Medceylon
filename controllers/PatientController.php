<?php
// controllers/PatientController.php

class PatientController {
    public function index() {
        // Load data from models
        $patientModel = new PatientModel();
        $patients = $patientModel->getAllPatients();

        // Check for search query
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

        if ($searchQuery) {
            // Filter patients by search query
            $patients = array_filter($patients, function($patient) use ($searchQuery) {
                return stripos($patient['name'], $searchQuery) !== false;
            });
        }

        // Pass data to the view
        require_once 'views/patients.php';
    }
}
?>
