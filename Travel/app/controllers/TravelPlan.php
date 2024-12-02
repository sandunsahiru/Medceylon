<?php
// controllers/TravelPlanController.php

class TravelPlan extends Controller {
    private $travelPlanModel;

    public function __construct() {
        $this->travelPlanModel = $this->model('TravelPlanModel');
    }

    public function TravelPlans() {
        $travelPlans = $this->travelPlanModel->getAllTravelPlans();
        $this->view('travel-plans',['travelPlans' => $travelPlans]);
    }

    public function addDestination() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate input (optional)
            $destination_id = $_POST['destination_id'];
            $startDate = $_POST['check_in'];
            $endDate = $_POST['check_out'];
    
            // Insert into the travel_plans table
            $this->travelPlanModel->addTravelPlan($destination_id, $startDate, $endDate);
            
            // Redirect after successful submission
            header('Location: ' . URLROOT . '/destination/destinations');
            exit();
        }
    }

    public function editDestination(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    
            $travel_id = $_POST['travel_id'];  // Travel Plan ID
            $startDate = $_POST['check_in'];  // Start Date
            $endDate = $_POST['check_out'];  // End Date
    
            // Debugging: Log received data
            error_log("Received data: " . json_encode($_POST));
    
            // Update the travel plan
            if ($this->travelPlanModel->editTravelPlan($travel_id, $startDate, $endDate)) {
                header('Location: ' . URLROOT . '/travelplan/travelplans');
                exit();
            } else {
                die('Something went wrong.');
            }
        }
    }
    
    

    public function deleteDestination() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Fetch the travel_id from POST data
            $travel_id = $_POST['travel_id'];  // Ensure you use 'travel_id' here
    
            // Debug: Log received travel_id
            error_log("Travel ID received for deletion: " . $travel_id);
    
            if (!empty($travel_id)) {
                // Attempt to delete the travel plan
                if ($this->travelPlanModel->deleteTravelPlan($travel_id)) {
                    // Redirect after successful deletion
                    header('Location: ' . URLROOT . '/travelplan/travelplans');
                    exit();
                } else {
                    // Log error if deletion fails
                    die('Database deletion failed.');
                }
            } else {
                // Log error if travel_id is not provided
                die('Invalid travel ID.');
            }
        } else {
            // If the request method is not POST, redirect to the travel plans page
            header('Location: ' . URLROOT . '/travelplan/travelplans');
            exit();
        }
    }
    
    
}
?>
