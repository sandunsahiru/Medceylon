<?php

namespace App\Controllers;

use App\Models\TravelPlan;


class TravelPlanController extends BaseController
{
    private $travelPlanModel;
    //private $destinationModel;
    //private $database;

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
            error_log("Starting destinations view");
            $travelPlans = $this->travelPlanModel->getAllDestinations();
            
            if  (!$travelPlans || count($travelPlans) === 0) {
                error_log("No destinations found");
                $this->session->setFlash('error', 'No destinations available');
            }
            
            $data = [
                'destinations' => $travelPlans,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success'),
                'basePath' => $this->basePath
            ];
            
            echo $this->view('/travelplan/destinations', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in destinations: " . $e->getMessage());
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
            
            $travelPlanData = [
                'user_id' => $this->session->getUserId(),
                'destination_id' => filter_var($_POST['destination_id'], FILTER_SANITIZE_NUMBER_INT),
                'start_date' => filter_var($_POST['check_in'], FILTER_SANITIZE_STRING),
                'end_date' => filter_var($_POST['check_out'], FILTER_SANITIZE_STRING)
            ];
            
            $this->travelPlanModel->addTravelPlan($travelPlanData);
            $this->session->setFlash('success', 'Destination added to travel plan successfully!');
            header('Location: ' . $this->url('travelplan/dashboard'));
            exit();
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error adding destination: ' . $e->getMessage());
            header('Location: ' . $this->url('error/404'));
            exit();
        }
    }

    public function TravelPlans() {

        $userId = $this->session->getUserId();
        $travelPlans = $this->travelPlanModel->getAllTravelPlans($userId);
        $this->view('travelplan/travel-plans',['travelPlans' => $travelPlans]);
        try {
            error_log("Starting travel plans view");
            $travelPlans = $this->travelPlanModel->getAllTravelPlans();
            
            if  (!$travelPlans || count($travelPlans) === 0) {
                error_log("No travel plans found");
                $this->session->setFlash('error', 'No travel plans available');
            }
            
            $data = [
                'travel plans' => $travelPlans,
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

    public function displayAddToPlanModal()
    {
        try {
            $destinationId = $_GET['id'] ?? 0;
            $details = $this->destinationModel->getDestinationDetails($destinationId);
            header('Content-Type: application/json');
            echo json_encode($details);
            exit();
        } catch (\Exception $e) {
            error_log("Error in displayAddToPlanModal: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
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

            error_log("Editing travel plan - ID: $travel_id, Start: $startDate, End: $endDate");

            if ($this->travelPlanModel->editTravelPlan($travel_id, $startDate, $endDate)) {
                $this->session->setFlash('success', 'Travel plan updated successfully!');
                $this->session->setFlash('success', 'Travel plan updated successfully!');
            } else {
                throw new \Exception('Failed to update travel plan');
            }

            header('Location: ' . $this->url('travelplan/dashboard'));
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

            header('Location: ' . $this->url('travelplan/dashboard'));
            exit();
        } catch (\Exception $e) {
            error_log("Error in deleteDestination: " . $e->getMessage());
            $this->session->setFlash('error', 'Error deleting travel plan: ' . $e->getMessage());
            header('Location: ' . $this->url('error/404'));
                throw new \Exception('Failed to delete travel plan');
            

            header('Location: ' . $this->url('travelplan/dashboard'));
            exit();
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

}