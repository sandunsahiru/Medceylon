<?php
// controllers/AddDestinationToPlanController.php

class AddDestinationToPlan extends Controller{
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new AddDestinationToPlanModel($db);
    }

    // Display the modal with destination details
    public function displayAddToPlanModal($destinationId) {
        $destination = $this->model->getDestinationDetails($destinationId);
        require 'views/addToPlanModal.php'; // Pass $destination to the view
    }

    // Handle form submission
    public function handleAddToPlanForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destinationId = $_POST['destinationId']; // Hidden input in the form
            $startDate = $_POST['startDate'];
            $endDate = $_POST['endDate'];

            // Add destination to the travel plan
            if ($this->model->addDestination($destinationId, $startDate, $endDate)) {
                // Redirect to success page or show a success message
                header('Location: ' . URLROOT . '/plans/success');
            } else {
                // Handle error
                die("Failed to add destination to the travel plan.");
            }
        }
    }
}
?>
