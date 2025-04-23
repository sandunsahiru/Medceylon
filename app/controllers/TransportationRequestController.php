<?php
namespace App\Controllers;

use App\Models\TransportationAssistance;
use App\Helpers\SessionHelper;
use App\Helpers\DistanceHelper;

class TransportationRequestController {
    private $model;
    private $session;

    public function __construct() {
        global $db;
        $this->model = new TransportationAssistance($db);
        $this->session = SessionHelper::getInstance();
    }

    public function index() {
        $requests = $this->model->getAllByPatient($this->session->getUserId());
        require_once ROOT_PATH . '/app/views/transportation/patient/index.php';
    }

    public function create() {
        require_once ROOT_PATH . '/app/views/transportation/patient/create.php';
    }

    public function save() {
        $pickup = $_POST['pickup_location'];
        $dropoff = $_POST['dropoff_location'];
        $type = $_POST['transport_type'];

        $pickupData = DistanceHelper::getCoordinates($pickup);
        $dropoffData = DistanceHelper::getCoordinates($dropoff);

        $distanceKm = DistanceHelper::calculateDistanceInKm(
            $pickupData['lat'], $pickupData['lon'],
            $dropoffData['lat'], $dropoffData['lon']
        );

        $fare = DistanceHelper::calculateFare($distanceKm, $type);

        $this->model->create([
            'patient_id' => $this->session->getUserId(),
            'transport_type' => $type,
            'pickup_location' => $pickup,
            'dropoff_location' => $dropoff,
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'fare' => $fare
        ]);

        header("Location: /Medceylon/patient/transport");
    }

    public function edit($id) {
        $request = $this->model->getById($id);
        if ($request['status'] !== 'Pending') {
            die("You can't edit this request.");
        }
        require_once ROOT_PATH . '/app/views/transportation/patient/edit.php';
    }

    public function update($id) {
        $pickup = $_POST['pickup_location'];
        $dropoff = $_POST['dropoff_location'];
        $type = $_POST['transport_type'];

        $pickupData = DistanceHelper::getCoordinates($pickup);
        $dropoffData = DistanceHelper::getCoordinates($dropoff);

        $distanceKm = DistanceHelper::calculateDistanceInKm(
            $pickupData['lat'], $pickupData['lon'],
            $dropoffData['lat'], $dropoffData['lon']
        );

        $fare = DistanceHelper::calculateFare($distanceKm, $type);

        $this->model->update($id, [
            'pickup_location' => $pickup,
            'dropoff_location' => $dropoff,
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'transport_type' => $type,
            'fare' => $fare
        ]);

        header("Location: /Medceylon/patient/transport");
    }

    public function delete($id) {
        $this->model->delete($id, $this->session->getUserId());
        header("Location: /Medceylon/patient/transport");
    }
}
