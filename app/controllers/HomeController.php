<?php
namespace App\Controllers;

use App\Models\Patient;

class HomeController extends BaseController
{
    private $patientModel;
    public function __construct()
    {
        parent::__construct();
        $this->patientModel = new Patient();
    }

    public function index()
    {
        try {
            // If user is logged in, redirect to appropriate dashboard based on role
            if ($this->session->isLoggedIn()) {
                $roleId = $this->session->getUserRole();
                return $this->redirectBasedOnRole($roleId);
            }

            // If not logged in, show the public landing page
            error_log("Loading public index page");
            echo $this->view('home/index', [
                'basePath' => $this->basePath,
                'title' => 'Welcome to MedCeylon'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function home()
    {
        try {
            // Ensure user is authenticated
            if (!$this->session->isLoggedIn()) {
                header("Location: " . $this->basePath . "/");
                exit();
            }

            $patientId = $this->session->getUserId();
            $roleId = $this->session->getUserRole();
            $paymentPlan = $this->patientModel->getPatientPaymentPlan($patientId);

            // Prepare data for view
            $data = [
                'user' => [
                    'name' => $this->session->getUsername(),
                    'role_id' => $roleId
                ],
                'paymentPlan' => $paymentPlan,
                'basePath' => $this->basePath,
                'title' => 'MedCeylon - Dashboard'
            ];

            echo $this->view('home/home', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in home: " . $e->getMessage());
            throw $e;
        }
    }

    private function redirectBasedOnRole($roleId)
    {
        $redirects = [
            1 => '/patient/dashboard',
            2 => '/doctor/dashboard',
            4 => '/admin/dashboard',
            5 => '/caregiver/dashboard',
            6 => '/travel-partner/dashboard'
        ];

        $redirect = isset($redirects[$roleId]) ? $redirects[$roleId] : '/home';
        header("Location: " . $this->basePath . $redirect);
        exit();
    }

    public function rateDoctor()
    {
        try {
            echo $this->view('home/rateyourdoctor', [
                'basePath' => $this->basePath,
                'title' => 'Welcome to MedCeylon'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function contactUs()
    {
        try {
            echo $this->view('home/contact-us', [
                'basePath' => $this->basePath,
                'title' => 'Welcome to MedCeylon'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function legalAgreements()
    {
        try {
            echo $this->view('home/legal-agreements', [
                'basePath' => $this->basePath,
                'title' => 'Legal Agreements'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function faq()
    {
        try {
            echo $this->view('home/faq', [
                'basePath' => $this->basePath,
                'title' => 'Frequently Asked Questions'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function visaGuidance()
    {
        try {

            echo $this->view('home/visa_guidance', [
                'basePath' => $this->basePath,
                'title' => 'Sri Lanka Visa Information'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function aboutUs()
    {
        try {
            echo $this->view('home/about-us', [
                'basePath' => $this->basePath,
                'title' => 'About Us - MedCeylon'
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }

    public function partnerHospitals()
    {
        try {
            $partner_hospitals = [
                [
                    "name" => "Durdans Hospital",
                    "image" => "./assets/images/durdans_hospital.jpg",
                    "description" => "Located in Colombo, known for advanced cardiology care."
                ],
                [
                    "name" => "Asiri Medical Hospital",
                    "image" => "./assets/images/asiri_medical_hospital.jpg",
                    "description" => "Based in Colombo, specializing in multi-specialty treatments."
                ],
                [
                    "name" => "Nawaloka Hospital",
                    "image" => "./assets/images/nawaloka_hospital.jpg",
                    "description" => "A leading hospital in Colombo offering state-of-the-art facilities."
                ],
                [
                    "name" => "Lanka Hospitals",
                    "image" => "./assets/images/lanka_hospitals.jpg",
                    "description" => "Situated in Colombo, well-known for international patient care."
                ],
                [
                    "name" => "Golden Key Hospital",
                    "image" => "./assets/images/golden_key_hospital.jpg",
                    "description" => "Located in Rajagiriya, specializes in eye care and ENT services."
                ],
                [
                    "name" => "Central Hospital",
                    "image" => "./assets/images/central_hospital.jpg",
                    "description" => "A top-notch hospital in Kandy, offering diverse medical services."
                ]
            ];

            echo $this->view('home/partnerHospitals', [
                'basePath' => $this->basePath,
                'title' => 'Our Partner Hospitals',
                'partner_hospitals' => $partner_hospitals
            ]);
            exit();
        } catch (\Exception $e) {
            error_log("Error in index: " . $e->getMessage());
            throw $e;
        }
    }
}