<?php


class HospitalController extends Controller {
    private $hospitalModel;

    public function __construct() {
        $this->hospitalModel = $this->model('Hospital');
    }

    public function Requests(){
        $requests = $this->hospitalModel->getAllRequests();
        $this->view('hospital-dashboard',['requests' => $requests]);
    }

    public function LatestRequests(){
        $latestRequests = $this->hospitalModel->getLatestRequests();
        $this->view('hospital-dashboard',['latestRequests' => $LatestRequests]);
    }

    public function Departments(){
        $departments = $this->hospitalModel->getDepartments();
        $this->view('departments',['departments' => $departments]);
    }

    public function Doctors(){
        $doctors = $this->hospitalModel->getDoctors();
        $this->view('doctors',['doctors' => $doctors]);
    }

    public function Patients(){
        $patients = $this->hospitalModel->getPatients();
        $this->view('patients',['patients' => $patients]);
    }

}