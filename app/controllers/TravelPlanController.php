<?php

namespace App\Controllers;

use App\Models\TravelPlan;


class TravelPlanController extends BaseController
{
    private $travelPlanModel;

    public function __construct()
    {
        parent::__construct();
        $this->travelPlanModel = new TravelPlan();
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

            $province_id = filter_input(INPUT_GET, 'province_id', FILTER_SANITIZE_NUMBER_INT);
            $district_id = filter_input(INPUT_GET, 'district_id', FILTER_SANITIZE_NUMBER_INT);

            // Get filters from query params
            $filters = [
                'province_id' => $province_id,
                'district_id' => $district_id,
                'town_id' => filter_input(INPUT_GET, 'town_id', FILTER_SANITIZE_NUMBER_INT),
                'distance' => filter_input(INPUT_GET, 'distance', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'wheelchair' => isset($_GET['wheelchair']) && $_GET['wheelchair'] !== '' ? $_GET['wheelchair'] : null,
                'type_id' => filter_input(INPUT_GET, 'type_id', FILTER_SANITIZE_NUMBER_INT),
                'budget' => filter_input(INPUT_GET, 'budget', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
            ];

            error_log("Filters applied: " . print_r($filters, true));

            // Get dependent dropdown data
            $districts = $filters['province_id'] ? $this->travelPlanModel->getDistricts($filters['province_id']) : [];
            $towns = $filters['district_id'] ? $this->travelPlanModel->getTowns($filters['district_id']) : [];

            // Get destinations with filters
            $destinations = $this->travelPlanModel->getFilteredDestinations($filters);

            // Prepare view data
            $data = [
                'provinces' => $provinces,
                'destinationTypes' => $destinationTypes,
                'districts' => $districts,
                'towns' => $towns,
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

    public function districts()
    {
        try {
            if (!isset($_POST['province_id'])) {
                throw new \Exception("Province ID is required");
            }

            $province_id = filter_var($_POST['province_id'], FILTER_SANITIZE_NUMBER_INT);
            $districts = $this->travelPlanModel->getDistricts($province_id);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'districts' => $districts]);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

        public function towns()
    {
        try {
            if (!isset($_POST['district_id'])) {
                throw new \Exception("District ID is required");
            }

            $district_id = filter_var($_POST['district_id'], FILTER_SANITIZE_NUMBER_INT);
            $towns = $this->travelPlanModel->getTowns($district_id);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'towns' => $towns]);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
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
            $startDate = filter_var($_POST['check_in'], FILTER_SANITIZE_STRING);
            $endDate = filter_var($_POST['check_out'], FILTER_SANITIZE_STRING);

            if ($this->travelPlanModel->hasOverlappingPlan(
                $this->session->getUserId(),
                $startdate,
                $enddate
            )) {
                $this->session->setFlash('error', 'You already have a travel plan during these dates!');
                header('Location: ' . $this->url('travelplan/travel-plans'));
                exit();
            }

            error_log("Editing travel plan - ID: $travel_id, Start: $startDate, End: $endDate");

            if ($this->travelPlanModel->editTravelPlan($travel_id, $startDate, $endDate)) {
                $this->session->setFlash('success', 'Travel plan updated successfully!');
            } else {
                throw new \Exception('Failed to update travel plan');
            }

            header('Location: ' . $this->url('travelplan/travel-plans'));
            exit();

        } catch (\Exception $e) {
            error_log("Error in editDestination: " . $e->getMessage());
            $this->session->setFlash('error', 'Error updating travel plan: ' . $e->getMessage());
            header('Location: ' . $this->url('error/404'));
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

}