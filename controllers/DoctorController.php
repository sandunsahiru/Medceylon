<?php
// controllers/DoctorController.php

class DoctorController {
    public function index() {
        // Load data from models
        $doctorModel = new DoctorModel();
        $doctors = $doctorModel->getAllDoctors();

        // Check for search query
        $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

        if ($searchQuery) {
            // Filter doctors by search query
            $doctors = array_filter($doctors, function($doctor) use ($searchQuery) {
                return stripos($doctor['name'], $searchQuery) !== false;
            });
        }

        // Pass data to the view
        require_once 'views/doctors.php';
    }
}
?>
