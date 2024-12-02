<<<<<<< HEAD
<?php
// controllers/DestinationController.php
//require_once 'models/Destination.php';

class Destination extends Controller
{
    private $destinationModel;

    public function __construct()
    {
        $this->destinationModel = $this->model('DestinationModel');
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
=======
<?php
// controllers/DestinationController.php
//require_once 'models/Destination.php';

class Destination extends Controller
{
    private $destinationModel;

    public function __construct()
    {
        $this->destinationModel = $this->model('DestinationModel');
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
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
