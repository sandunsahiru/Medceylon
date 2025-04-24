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
        try {
            error_log("Accommodations method invoked");
            $accommodations = $this->accommodationModel->getAllAccommodations();

            if (!$accommodations || count($accommodations) === 0) {
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

    public function getAccommodationDetails()
    {
        header('Content-Type: application/json');
        try {
            $providerId = $_GET['provider_id'] ?? null;
            error_log("Provider ID received: " . $providerId);
            if (!$providerId) {
                http_response_code(400);
                echo json_encode(['error' => 'Provider ID is required']);
                exit();
            }

            $accommodationDetails = $this->accommodationModel->getAllDetails($providerId);

            if (!$accommodationDetails || count($accommodationDetails) === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'No accommodation details found']);
                exit();
            }

            echo json_encode(['success' => true, 'data' => $accommodationDetails]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in getAccommodationDetails: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to retrieve accommodation details']);
            exit();
        }
    }
    public function processBooking()
    {
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
            $requiredFields = ['patient_id', 'provider_id', 'room_type', 'check_in_date', 'check_out_date', 'total_price'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['success' => false, 'error' => 'Please fill all required fields']);
                    exit();
                }
            }

            $patientId        = $_POST['patient_id'];
            $providerId       = $_POST['provider_id'];
            $roomType         = $_POST['room_type'];
            $checkInDate      = $_POST['check_in_date'];
            $checkOutDate     = $_POST['check_out_date'];
            $totalPrice       = $_POST['total_price'];
            $specialRequests  = $_POST['special_requests'] ?? '';

            $checkIn = new \DateTime($checkInDate);
            $checkOut = new \DateTime($checkOutDate);
            $today = new \DateTime();

            if ($checkIn < $today) {
                echo json_encode(['success' => false, 'error' => 'Check-in date cannot be in the past']);
                exit();
            }

            if ($checkOut <= $checkIn) {
                echo json_encode(['success' => false, 'error' => 'Check-out date must be after check-in date']);
                exit();
            }

            // Call the updated booking method (for room_bookings table)
            $bookingId = $this->accommodationModel->createBooking(
                $patientId,
                $providerId,
                $roomType,
                $checkInDate,
                $checkOutDate,
                $totalPrice,
                $specialRequests
            );

            if ($bookingId) {
                echo json_encode(['success' => true, 'message' => 'Booking created successfully', 'booking_id' => $bookingId]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to create booking']);
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in processBooking: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
            exit();
        }
    }


    public function getBookingDetails()
    {
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