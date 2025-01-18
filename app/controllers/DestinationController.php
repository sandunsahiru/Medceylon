<?php
// controllers/DestinationController.php
//require_once 'models/Destination.php';

class DestinationController extends Controller
{
    private $destinationModel;

    public function __construct()
    {
        $this->destinationModel = $this->model('Destination');
    }

    public function destinations()
    {
        // Retrieve all destinations from the model
        $destinations = $this->destinationModel->getAllDestinations();
        
        // Include the view and pass the data
        $this->view('destinations', ['destinations' => $destinations]);
    }

    public function addDestinationToPlan()
    {

    }
}
