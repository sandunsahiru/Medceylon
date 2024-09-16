<?php
// controllers/AccommodationController.php

class AccommodationController {
    private $accommodationModel;

    public function __construct() {
        require_once 'models/AccommodationModel.php';
        $this->accommodationModel = new AccommodationModel();
    }

    public function index() {
        require_once 'includes/SessionManager.php';
        SessionManager::requireLogin('Patient');

        $patientId = SessionManager::getUserId();

        // Get the city of the patient's hospital
        $cityData = $this->accommodationModel->getHospitalCityByPatient($patientId);

        if ($cityData) {
            $cityId = $cityData['city_id'];
            $cityName = $cityData['city_name'];

            // Fetch hotels in the city
            $hotels = $this->accommodationModel->getHotelsByCity($cityId);

            // For each hotel, get available rooms
            foreach ($hotels as &$hotel) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $checkInDate = $_POST['check_in_date'];
                    $checkOutDate = $_POST['check_out_date'];
                    $guests = $_POST['guests'];

                    $hotel['rooms'] = $this->accommodationModel->getAvailableRooms(
                        $hotel['hotel_id'],
                        $checkInDate,
                        $checkOutDate,
                        $guests
                    );
                } else {
                    // Get all rooms without availability check
                    $hotel['rooms'] = $this->accommodationModel->getHotelRooms($hotel['hotel_id']);
                }
            }

            // Pass data to the view
            require 'views/accommodation.php';
        } else {
            $errorMessage = "No associated hospital city found.";
            require 'views/error.php';
        }
    }

    public function book() {
        // Booking logic remains the same
    }
}
?>
