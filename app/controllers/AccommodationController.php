<?php

namespace App\Controllers;

use App\Models\Accommodation;


class AccommodationController extends BaseController
{
    private $accommodationModel;

    public function __construct()
    {
        parent::__construct();
        $this->accommodationModel = new Accommodation();
    }

    public function accommodations()
    {
        try{
            error_log("Accommodations method invoked");
            $accommodations = $this->accommodationModel->getAllAccommodations();

            if  (!$accommodations || count($accommodations) === 0) {
                error_log("No accommodations found");
                $this->session->setFlash('error', 'No accommodations available');
            }

            $provinces = $this->accommodationModel->getAllProvinces();
            
            $data = [
                'provinces' => $provinces,
                'accommodations' => $accommodations,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('/accommodation/accommodation-providers', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in accommodations: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }

    public function processBooking() {
        header('Content-Type: application/json');
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid security token']);
                exit();
            }
            
            // Check if user is logged in
            if (!$this->session->isLoggedIn()) {
                echo json_encode(['success' => false, 'error' => 'You must be logged in to book accommodation']);
                exit();
            }
            
            // Validate required fields
            $requiredFields = ['patient_id', 'accommodation_provider_id', 'check_in_date', 'check_out_date', 'accommodation_type'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'error' => 'Please fill all required fields']);
                    exit();
                }
            }
            
            // Validate dates
            $checkIn = new \DateTime($_POST['check_in_date']);
            $checkOut = new \DateTime($_POST['check_out_date']);
            $today = new \DateTime();
            
            if ($checkIn < $today) {
                echo json_encode(['success' => false, 'error' => 'Check-in date cannot be in the past']);
                exit();
            }
            
            if ($checkOut <= $checkIn) {
                echo json_encode(['success' => false, 'error' => 'Check-out date must be after check-in date']);
                exit();
            }
            
            // Prepare data for insertion
            $bookingData = [
                'patient_id' => $_POST['patient_id'],
                'accommodation_provider_id' => $_POST['accommodation_provider_id'],
                'check_in_date' => $_POST['check_in_date'],
                'check_out_date' => $_POST['check_out_date'],
                'accommodation_type' => $_POST['accommodation_type'],
                'special_requests' => $_POST['special_requests'] ?? '',
                'status' => 'pending',
                'last_updated' => date('Y-m-d H:i:s')
            ];
        
            $bookingId = $this->accommodationModel->createBooking(
                $_POST['patient_id'],
                $_POST['accommodation_provider_id'],
                $_POST['check_in_date'],
                $_POST['check_out_date'],
                $_POST['accommodation_type'],
                $_POST['special_requests'] ?? ''
            );
            
            if ($bookingId) {
                echo json_encode(['success' => true, 'message' => 'Booking created successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create booking']);
            }
            exit();
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            exit();
        }
    }

    public function getBookingDetails() {
        $userId = $this->session->getUserId();
        try {
            $bookings = $this->accommodationModel->getAllBookings($userId);

            if (!$bookings || count($bookings) === 0) {
                error_log("No bookings found for user ID: $userId");
                $this->session->setFlash('error', 'No bookings found');
            }

            $data = [
                'bookings' => $bookings,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];

            echo $this->view('/accommodation/get-booking-details', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getBookingDetails: " . $e->getMessage());
            $this->session->setFlash('error', 'Failed to retrieve booking details');
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }
    
}

