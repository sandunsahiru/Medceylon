<?php
namespace App\Controllers;

use App\Models\Admin;
use App\Models\Appointment;

class AdminController extends BaseController
{
    private $adminModel;
    private $appointmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel = new Admin();
        $this->appointmentModel = new Appointment();

    }

    public function dashboard()
    {
        try {
            $patients_count = $this->adminModel->getPatientsCount();
            $doctors_count = $this->adminModel->getDoctorsCount();
            $hospitals_count = $this->adminModel->getHospitalsCount();

            $data = [
                'patients_count' => $patients_count,
                'doctors_count' => $doctors_count,
                'hospitals_count' => $hospitals_count,
                'basePath' => $this->basePath
            ];

            echo $this->view('admin/dashboard', $data);
        } catch (\Exception $e) {
            error_log("Error in dashboard method: " . $e->getMessage());
            echo $this->view('admin/error', ['message' => 'An error occurred while loading the dashboard.']);
        }
    }

    public function userManagement()
    {
        try {
            $doctors = $this->adminModel->getDoctors();
            $patients = $this->adminModel->getPatients();
            $hospitals = $this->adminModel->getHospitals();

            $data = [
                'admin' => $this->adminModel,
                'doctors' => $doctors,
                'patients' => $patients,
                'hospitals' => $hospitals,
                'basePath' => $this->basePath
            ];


            echo $this->view('admin/user-management', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

    }


    public function editProfile($user_id=null, $page=null)
    {
        try {
            if ($page === 'doctors') {
                $doctor = $this->adminModel->getDoctorById($user_id);
                $data = ['doctor' => $doctor];
            } elseif ($page === 'patients') {
                $patient = $this->adminModel->getPatientById($user_id);
                $data = ['patient' => $patient];
            } else {
                $hospital = $this->adminModel->getHospitalById($user_id);
                $data = ['hospital' => $hospital];
            }            

            echo $this->view('admin/editProfile', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

    }

    public function userProfiles($page)
    {
        try {
            $data = [];
            if ($page === 'doctors') {
                $data['profiles'] = $this->adminModel->getDoctors();
            } elseif ($page === 'patients') {
                $data['profiles'] = $this->adminModel->getPatients();
            } else {
                throw new \Exception("Invalid page type specified");
            }

            // Pass the page type to the view
            $data['page'] = $page;

            // Render the view
            echo $this->view('admin/user-profiles', $data);
        } catch (\Exception $e) {
            error_log("Error loading user profiles: " . $e->getMessage());
            echo $this->view('admin/error', ['message' => 'Failed to load profiles.']);
        }
    }


    public function appointments()
    {
        try {
            $appointments = $this->adminModel->getUpcomingAppointments();
            $data = [
                'appointments' => $appointments,
                'basePath' => $this->basePath
            ];
            echo $this->view('admin/appointments', $data);
        } catch (\Exception $e) {
            error_log("Error in userManagement method: " . $e->getMessage());
            echo $this->view('admin/error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function getAppointments(){
        try {
            $appointments = $this->adminModel->getUpcomingAppointments();
            echo json_encode($appointments);
        } catch (\Exception $e) {
            error_log("Error in getAppointments method: " . $e->getMessage());
            echo json_encode(['error' => 'Failed to fetch appointments.']);
        }
    }

    public function bookings()
    {
        try {
            error_log("Entering dashboard method");
            $patientId = $this->session->getUserId();
            $appointments = $this->appointmentModel->getPatientAppointments($patientId);

            error_log("Patient ID: " . $patientId);
            error_log("Appointments: " . print_r($appointments, true));

            $data = [
                'appointments' => $appointments,
                'basePath' => $this->basePath
            ];

            echo $this->view('admin/bookings', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in dashboard: " . $e->getMessage());
            throw $e;
        }

    }

}
?>