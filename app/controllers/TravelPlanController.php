<?php

namespace App\Controllers;

use App\Models\TravelPlan;
use App\Models\Accommodation;
use App\Helpers\DistanceHelper;


class TravelPlanController extends BaseController
{
    private $travelPlanModel;
    private $accommodationModel;

    public function __construct()
    {
        parent::__construct();
        $this->travelPlanModel = new TravelPlan();
        $this->accommodationModel = new Accommodation();
    }

    public function dashboard()
    {
        try {
            error_log("Entering travel plans dashboard");
            $userId = $this->session->getUserId();
            $travelPlans = $this->travelPlanModel->getAllTravelPlans($userId);
            
            error_log("User ID: " . $userId);
            error_log("Travel Plans: " . print_r($travelPlans, true));
            
            $data = [
                'travelPlans' => $travelPlans,
                'basePath' => $this->basePath
            ];
            
            echo $this->view('travelplan/travel-plans/', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in dashboard: " . $e->getMessage());
            throw $e;
        }
    }

    public function destinations()
{
    try {
        error_log("Executing action: destinations");

        // Get provinces and destination types
        $provinces = $this->travelPlanModel->getAllProvinces();
        $destinationTypes = $this->travelPlanModel->getDestinationTypes();

        // Get filters from query params
        $filters = [
            'province_id' => $_GET['province_id'] ?? null,
            'wheelchair' => $_GET['wheelchair'] ?? null,
            'type_id' => $_GET['type_id'] ?? null,
            'cost_category' => $_GET['cost_category'] ?? null
        ];

        error_log("Filters applied: " . print_r($filters, true));

        // Get destinations with filters
        $destinations = $this->travelPlanModel->getFilteredDestinations($filters);

        // Prepare view data
        $data = [
            'provinces' => $provinces,
            'destinationTypes' => $destinationTypes,
            'destinations' => $destinations,
            'error' => $this->session->getFlash('error'),
            'success' => $this->session->getFlash('success'),
            'basePath' => $this->basePath
        ];

        echo $this->view('/travelplan/destinations', $data);
        exit();

    } catch (\Exception $e) {
        error_log("Error in destinations(): " . $e->getMessage());
        $this->session->setFlash('error', "Something went wrong. Please try again.");
        header('Location: ' . $this->url('error/404'));
        exit();
    }
}

    public function provinces()
    {
        try {
            error_log("Provinces method invoked");
            $provinces = $this->travelPlanModel->getAllProvinces();

            if  (!$provinces || count($provinces) === 0) {
                error_log("No provinces found");
                $this->session->setFlash('error', 'No provinces available');
            }
            
            $data = [
                'provinces' => $provinces,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('/travelplan/destinations', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in provinces: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }


    public function addDestination()
    {
    
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $destination_id = filter_var($_POST['destination_id'], FILTER_SANITIZE_NUMBER_INT);
            $start_date = filter_var($_POST['check_in'], FILTER_SANITIZE_STRING);
            $end_date = filter_var($_POST['check_out'], FILTER_SANITIZE_STRING);
            
            if ($this->travelPlanModel->hasOverlappingPlan(
                $this->session->getUserId(),
                $start_date,
                $end_date
            )) {
                $this->session->setFlash('error', 'You already have a travel plan during these dates!');
                header('Location: ' . $this->url('travelplan/destinations'));
                exit();
            }

            $this->travelPlanModel->addTravelPlan(
                $this->session->getUserId(),
                $destination_id,
                $start_date,
                $end_date);

            $this->session->setFlash('success', 'Destination added to travel plan successfully!');
            header('Location: ' . $this->url('travelplan/destinations'));
            exit();
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error adding destination to Travel Plan: ' . $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }

    public function TravelPlans() {

        $userId = $this->session->getUserId();
        try {
            error_log("Starting travel plans view for user: $userId");
            $travelPlans = $this->travelPlanModel->getAllTravelPlans($userId);
            
            if  (!$travelPlans || count($travelPlans) === 0) {
                error_log("No travel plans found");
                $this->session->setFlash('error', 'No travel plans available');
            }
            
            $data = [
                'travelPlans' => $travelPlans,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('/travelplan/travel-plans', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in travel plans: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }

    public function editDestination()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $travel_id = filter_var($_POST['travel_id'], FILTER_SANITIZE_NUMBER_INT);
            $startDate = htmlspecialchars($_POST['check_in']);
            $endDate = htmlspecialchars($_POST['check_out']);

            if (empty($startDate) || empty($endDate)) {
                throw new \Exception("Dates cannot be empty");
            }

            if (!strtotime($startDate) || !strtotime($endDate)) {
                throw new \Exception("Invalid date format");
            }

            if ($endDate < $startDate) {
                throw new \Exception("Check-out date cannot be before check-in date");
            }

            if ($this->travelPlanModel->hasOverlappingPlan(
                $this->session->getUserId(),
                $startDate,
                $endDate,
                $travel_id
            )) {
                echo "<script>alert('Warning: You already have a travel plan overlapping with these dates!'); window.history.back();</script>";
                exit();
            }

            if ($this->travelPlanModel->editTravelPlan($travel_id, $startDate, $endDate)) {
                $this->session->setFlash('success', 'Travel plan updated successfully!');
            } else {
                throw new \Exception('Failed to update travel plan');
            }

            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();

        } catch (\Exception $e) {
            error_log("Error in editDestination: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        }
    }


            
    public function deleteDestination()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $travel_id = filter_var($_POST['travel_id'], FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($travel_id)) {
                throw new \Exception('Invalid travel plan ID');
            }

            error_log("Deleting travel plan ID: " . $travel_id);

            if ($this->travelPlanModel->deleteTravelPlan($travel_id)) {
                $this->session->setFlash('success', 'Travel plan deleted successfully!');
            } else {
                throw new \Exception('Failed to delete travel plan');
            }

            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in deleteDestination: " . $e->getMessage());
            $this->session->setFlash('error', 'Error deleting travel plan: ' . $e->getMessage());
            header('Location: ' . $this->url('error/404'));
                throw new \Exception('Failed to delete travel plan');
        } 
    }

    public function travelPreferences()
    {
        try {
            
            if (!$this->session->isLoggedIn()) {
                header("Location: " . $this->basePath . "/");
                exit();
            }

            $userId = $this->session->getUserId();
            
            $data = [
                
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('travelplan/travel-preferences', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in Travel Preferences From: " . $e->getMessage());
            throw $e;
        }
    }


   public function handleAddToPlanForm()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $planData = [
                'user_id' => $this->session->getUserId(),
                'destination_id' => filter_var($_POST['destinationId'], FILTER_SANITIZE_NUMBER_INT),
                'start_date' => filter_var($_POST['startDate'], FILTER_SANITIZE_STRING),
                'end_date' => filter_var($_POST['endDate'], FILTER_SANITIZE_STRING)
            ];

            if ($this->travelPlanModel->addTravelPlan($planData)) {
                $this->session->setFlash('success', 'Successfully added to travel plan!');
                header('Location: ' . $this->url('travelplan/dashboard'));
            } else {
                throw new \Exception('Failed to add destination to travel plan');
            }
            exit();
        } catch (\Exception $e) {
            error_log("Error in handleAddToPlanForm: " . $e->getMessage());
            $this->session->setFlash('error', 'Error adding to travel plan: ' . $e->getMessage());
            header('Location: ' . $this->url('destination/destinations'));
            exit();
        }
    }

    public function markCompleted()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $travel_id = filter_var($_POST['travel_id'], FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($travel_id)) {
                throw new \Exception('Invalid travel plan ID');
            }

            error_log("Marking travel plan as completed - ID: " . $travel_id);

            if ($this->travelPlanModel->markTravelPlanCompleted($travel_id)) {
                $this->session->setFlash('success', 'Travel plan marked as completed!');
            } else {
                throw new \Exception('Failed to update travel plan status');
            }

            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in markCompleted: " . $e->getMessage());
            $this->session->setFlash('error', 'Error updating travel plan: ' . $e->getMessage());
            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        }
    }

    public function addMemories()
    {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $travel_id = filter_var($_POST['travel_id'], FILTER_SANITIZE_NUMBER_INT);
            $note = filter_var($_POST['memory_note'], FILTER_SANITIZE_STRING);
            $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($travel_id)) {
                throw new \Exception('Invalid travel plan ID');
            }
            
            // Process photo uploads
            $photos = [];
            if (!empty($_FILES['memory_photos']['name'][0])) {
                $uploadDir = 'public/uploads/memories/';
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Process each uploaded file
                $fileCount = count($_FILES['memory_photos']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    // Check if upload is valid
                    if ($_FILES['memory_photos']['error'][$i] === UPLOAD_ERR_OK) {
                        $tempName = $_FILES['memory_photos']['tmp_name'][$i];
                        $originalName = $_FILES['memory_photos']['name'][$i];
                        
                        // Create unique filename
                        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                        $newFileName = uniqid('memory_') . '.' . $extension;
                        $destination = $uploadDir . $newFileName;
                        
                        // Move the uploaded file
                        if (move_uploaded_file($tempName, $destination)) {
                            $photos[] = $destination;
                        }
                    }
                }
            }
            
            // Save memories data
            if ($this->travelPlanModel->addTravelMemories($travel_id, $note, $rating, $photos)) {
                $this->session->setFlash('success', 'Memories added successfully!');
            } else {
                throw new \Exception('Failed to add memories');
            }

            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in addMemories: " . $e->getMessage());
            $this->session->setFlash('error', 'Error adding memories: ' . $e->getMessage());
            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();
        }
    }

    public function getMemories()
    {
        try {
            $travel_id = filter_var($_GET['travel_id'], FILTER_SANITIZE_NUMBER_INT);
            
            if (empty($travel_id)) {
                throw new \Exception('Invalid travel plan ID');
            }
            
            $memories = $this->travelPlanModel->getTravelMemories($travel_id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'memories' => $memories]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit();
        }
    }
    /**
     * Core calculation logic for travel plan dates
     */
    private function calculatePlanDates($accommodation, $destinations)
    {
        $averageSpeed = 40; // km/h
        $maxDailyHours = 8; // Maximum hours per day
        
        $plan = [];
        $currentDate = new \DateTime($accommodation['check_out']);
        $previousLocation = [
            'latitude' => $accommodation['latitude'],
            'longitude' => $accommodation['longitude']
        ];

        foreach ($destinations as $index => $destination) {
            // Calculate distance from previous location
            $distance = DistanceHelper::calculateDistanceInKm(
                $previousLocation['latitude'],
                $previousLocation['longitude'],
                $destination['latitude'],
                $destination['longitude']
            );

            // Calculate travel time (hours)
            $travelTime = $distance / $averageSpeed;
            
            // Total time needed (travel + visit)
            $totalTime = $travelTime + $destination['minimum_hours_spent'];
            
            // Determine if we need to move to next day
            $startDate = clone $currentDate;
            $endDate = clone $startDate;
            
            if ($totalTime > $maxDailyHours) {
                $endDate->modify('+1 day');
            }
            
            // Add to plan
            $plan[] = [
                'destination_id' => $destination['destination_id'],
                'destination_name' => $destination['destination_name'],
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'travel_time_hours' => round($travelTime, 2),
                'time_spent_hours' => $destination['minimum_hours_spent']
            ];
            
            // Update for next iteration
            $currentDate = $endDate;
            $previousLocation = [
                'latitude' => $destination['latitude'],
                'longitude' => $destination['longitude']
            ];
        }

        // Calculate total trip time
        $totalTripTime = array_reduce($plan, function($carry, $item) {
            return $carry + $item['travel_time_hours'] + $item['time_spent_hours'];
        }, 0);

        return [
            'items' => $plan,
            'total_trip_time_hours' => round($totalTripTime, 2)
        ];
    }

    
    public function calculateTravelDates()
    {
       
        if (ob_get_length()) ob_clean();
        
        try {
            
            header('Content-Type: application/json');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            
            
            $json = file_get_contents('php://input');
            if (empty($json)) {
                throw new \Exception("No input data received");
            }
            
            $data = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON: " . json_last_error_msg());
            }
            
            if (empty($data['destination_ids'])) {
                throw new \Exception("Please select at least one destination");
            }

            error_log("Starting calculation for user: " . $this->session->getUserId());
            
            
            $accommodationData = $this->travelPlanModel->getUserActiveAccommodation(
                $this->session->getUserId()
            );
            
            
            $accommodation = [
                'name' => 'Default Location',
                'check_out' => date('Y-m-d', strtotime('+1 day')),
                'latitude' => 6.927079,
                'longitude' => 79.861244
            ];
            
           
            if ($accommodationData) {
                $accommodation = [
                    'name' => $accommodationData['name'],
                    'check_out' => $accommodationData['check_out'],
                    'latitude' => $accommodationData['latitude'],
                    'longitude' => $accommodationData['longitude']
                ];
            }

            
            $destinations = [];
            foreach ($data['destination_ids'] as $destinationId) {
                $destination = $this->travelPlanModel->getDestinationById($destinationId);
                if (!$destination) {
                    throw new \Exception("Destination not found: $destinationId");
                }
                $destinations[] = $destination;
            }

           
            $plan = $this->calculatePlanDates($accommodation, $destinations);

            
            echo json_encode([
                'success' => true,
                'plan' => $plan,
                'accommodation' => $accommodation,
                'has_accommodation' => $accommodationData !== null
            ]);
            exit();

        } catch (\Exception $e) {
            
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => DEBUG_MODE ? $e->getTrace() : null
            ]);
            exit();
        }
    }

    
    public function savePlan()
{
    // Start with clean output
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    
    try {
        // Get raw input and log it
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }
        
        // Validate CSRF
        if (!$this->session->verifyCSRFToken($data['csrf_token'] ?? '')) {
            throw new \Exception("Invalid CSRF token");
        }

        // Validate items
        if (empty($data['items']) || !is_array($data['items'])) {
            throw new \Exception("No items provided");
        }
        
        // Process through model
        $tripId = $this->travelPlanModel->saveMultiDestinationPlan(
            $this->session->getUserId(), 
            $data
        );
        
        echo json_encode([
            'success' => true,
            'trip_id' => $tripId,
            'redirect' => $this->url('travelplan/view-trip/' . $tripId)
        ]);

    } catch (\Exception $e) {
        http_response_code(400);
        error_log("Controller error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

    public function viewMultiDestinationPlan()
    {
        try {
            $userId = $this->session->getUserId();
            $plan = $this->travelPlanModel->getMultiDestinationPlan($userId);
            
            $data = [
                'plan' => $plan,
                'basePath' => $this->basePath
            ];
            
            echo $this->view('travelplan/multi-destination-view', $data);
        } catch (\Exception $e) {
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('travelplan/destinations'));
        }
        exit();
    }

    public function viewTrip($tripId)
    {
        try {
            $userId = $this->session->getUserId();
            $tripDetails = $this->travelPlanModel->getTripDetails($tripId);
            
            // Verify the trip belongs to the current user
            if ($tripDetails['trip']['user_id'] != $userId) {
                throw new \Exception("You don't have permission to view this trip");
            }
            
            $data = [
                'trip' => $tripDetails['trip'],
                'destinations' => $tripDetails['destinations'],
                'basePath' => $this->basePath
            ];
            
            echo $this->view('travelplan/trip-view', $data);
            
        } catch (\Exception $e) {
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('travelplan/travel-plans'));
        }
        exit();
    }

    public function editDestinationDates()
{
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    try {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Invalid JSON: " . json_last_error_msg());
        }

        if (!$this->session->verifyCSRFToken($data['csrf_token'] ?? '')) {
            throw new \Exception("Invalid CSRF token");
        }

        if (empty($data['items']) || !is_array($data['items'])) {
            throw new \Exception("No items provided");
        }

        if (!isset($data['edited_index'])) {
            throw new \Exception("No edited index provided");
        }

        $items = $data['items'];
        $editedIndex = (int)$data['edited_index'];

        $plan = $this->yourModel->updateDestinationDates($items, $editedIndex);

        echo json_encode([
            'success' => true,
            'plan' => $plan
        ]);

    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}



}