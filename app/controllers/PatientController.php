<?php

namespace App\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\HealthRecord;
use App\Models\Patient;

class PatientController extends BaseController
{
    private $appointmentModel;
    private $doctorModel;
    private $healthRecordModel;
    private $patientModel;

    public function __construct()
    {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
        $this->healthRecordModel = new HealthRecord();
        $this->patientModel = new Patient();
    }

    public function dashboard()
    {
        $patientId = $this->session->getUserId('user_id');
        $appointments = $this->appointmentModel->getPatientAppointments($patientId);
        $this->view('patient/dashboard', ['appointments' => $appointments]);
    }

    public function bookAppointment() {
        try {
            error_log("Starting bookAppointment");
            $doctors = $this->doctorModel->getAvailableDoctors();
            error_log("Doctors result: " . print_r($doctors, true));
            
            if (!$doctors || $doctors->num_rows === 0) {
                error_log("No doctors found");
                $this->session->setFlash('error', 'No doctors available');
            }
            
            $this->view('patient/book-appointment', [
                'doctors' => $doctors,
                'error' => $this->session->getFlash('error'),
                'success' => $this->session->getFlash('success')
            ]);
        } catch (\Exception $e) {
            error_log("Error in bookAppointment: " . $e->getMessage());
            $this->session->setFlash('error', $e->getMessage());
            header('Location: ' . $this->url('patient/dashboard'));
        }
    }

    public function processAppointment() {
        try {
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }
            
            $appointmentData = [
                'patient_id' => $this->session->getUserId(),
                'doctor_id' => $_POST['doctor_id'],
                'date' => $_POST['appointment_date'],
                'time' => date('H:i:s', strtotime($_POST['time_slot'])),
                'consultation_type' => $_POST['consultation_type'],
                'reason' => $_POST['reason'],
                'medical_history' => $_POST['medical_history'] ?? null,
                'documents' => $_FILES['documents'] ?? []
            ];
            
            $this->appointmentModel->bookAppointment($appointmentData);
            $this->session->setFlash('success', 'Appointment booked successfully!');
            header('Location: ' . $this->url('patient/dashboard'));
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error booking appointment: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/book-appointment'));
        }
    }
    public function getAppointmentDetails()
    {
        $appointmentId = $_GET['id'] ?? 0;
        $details = $this->appointmentModel->getAppointmentDetails($appointmentId);
        header('Content-Type: application/json');
        echo json_encode($details);
    }

    public function medicalHistory()
    {
        $patientId = $this->session->getUserId('user_id');
        $records = $this->healthRecordModel->getPatientRecords($patientId);
        $this->view('patient/medical-history', ['records' => $records]);
    }

    public function profile()
    {
        $userId = $this->session->getUserId('user_id');
        $profile = $this->patientModel->getProfile($userId);
        $cities = $this->patientModel->getCities();
        $countries = $this->patientModel->getCountries();

        $this->view('patient/profile', [
            'user' => $profile,
            'cities' => $cities,
            'countries' => $countries
        ]);
    }

    public function updateProfile()
    {
        try {
            $userId = $this->session->getUserId('user_id');
            $this->patientModel->updateProfile($userId, $_POST);
            $this->session->setFlash('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error updating profile: ' . $e->getMessage());
        }
        header('Location: ' . $this->url('patient/profile'));
        exit;
    }

    public function deleteProfile()
    {
        try {
            $userId = $this->session->getUserId('user_id');
            $this->patientModel->deleteAccount($userId);
            session_destroy();
            header('Location: ' . $this->url('login') . '?message=Account+deactivated+successfully');
        } catch (\Exception $e) {
            $this->session->setFlash('error', 'Error deactivating account: ' . $e->getMessage());
            header('Location: ' . $this->url('patient/profile'));
        }
        exit;
    }

    public function getTimeSlots()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            exit;
        }

        $slots = $this->doctorModel->getAvailableTimeSlots(
            $_POST['doctor_id'],
            $_POST['date']
        );

        header('Content-Type: application/json');
        echo json_encode($slots);
    }
}
