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
            error_log("Error in rateDoctor: " . $e->getMessage());
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
            error_log("Error in contactUs: " . $e->getMessage());
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
            error_log("Error in legalAgreements: " . $e->getMessage());
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
            error_log("Error in faq: " . $e->getMessage());
            throw $e;
        }
    }

    public function visaGuidance()
    {
        try {
            // Debug info
            error_log("Starting visaGuidance method in HomeController");
            
            // Get database connection from parent class (BaseController)
            $db = $this->db;
            
            if (!$db) {
                // Try to create a new connection directly
                error_log("Database connection is null, trying direct connection");
                
                // Include database config
                require_once ROOT_PATH . '/app/config/database.php';
                
                // Create connection using globals from database.php
                if (isset($DB_HOST) && isset($DB_USER) && isset($DB_PASS) && isset($DB_NAME)) {
                    error_log("Creating direct database connection");
                    $db = new \mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
                    
                    if ($db->connect_error) {
                        error_log("Direct connection failed: " . $db->connect_error);
                    } else {
                        error_log("Direct connection successful");
                    }
                } else {
                    error_log("Database config variables not available");
                }
            }
            
            // If we still don't have a connection, show error
            if (!$db || ($db instanceof \mysqli && $db->connect_error)) {
                error_log("Unable to establish database connection for visa guidance");
                
                // First check if the error view exists
                $errorViewPath = ROOT_PATH . '/app/views/errors/database_error.php';
                if (!file_exists($errorViewPath)) {
                    error_log("Error view not found at: " . $errorViewPath);
                    
                    // Simple direct error output
                    echo '<h1>Database Error</h1>';
                    echo '<p>Unable to connect to the database. Please try again later.</p>';
                    echo '<p><a href="' . $this->basePath . '/">Return to Home</a></p>';
                    exit();
                }
                
                // Show error view
                echo $this->view('errors/database_error', [
                    'message' => 'Unable to connect to the database',
                    'basePath' => $this->basePath,
                    'title' => 'Database Error'
                ]);
                exit();
            }
            
            error_log("Database connection successful");
            
            // Initialize variables
            $countries = [];
            $country_details = null;
            
            // Process form submission if POST request
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['country_code'])) {
                $country_code = $_POST['country_code'];
                error_log("Processing form submission for country: " . $country_code);
                
                // Get country details
                $query = "SELECT country_code, country_name, visa_required, application_steps, embassy_link FROM countries WHERE country_code = ?";
                $stmt = $db->prepare($query);
                
                if (!$stmt) {
                    error_log("Prepare failed: " . $db->error);
                } else {
                    $stmt->bind_param("s", $country_code);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $country_details = $result->fetch_assoc();
                    $stmt->close();
                    
                    // Log the country details
                    if ($country_details) {
                        error_log("Found country details for: " . $country_details['country_name']);
                    } else {
                        error_log("No country details found for code: " . $country_code);
                    }
                }
            }
            
            // Get all countries for dropdown
            error_log("Fetching all countries for dropdown");
            $query = "SELECT country_code, country_name FROM countries ORDER BY country_name";
            $result = $db->query($query);
            
            if (!$result) {
                error_log("Error fetching countries: " . $db->error);
            } else {
                while ($row = $result->fetch_assoc()) {
                    $countries[] = $row;
                }
                error_log("Fetched " . count($countries) . " countries");
            }
            
            // Check if view file exists
            $viewPath = ROOT_PATH . '/app/views/home/visa_guidance.php';
            if (!file_exists($viewPath)) {
                error_log("View file not found at: " . $viewPath);
                echo '<h1>Error</h1>';
                echo '<p>View file not found: /app/views/home/visa_guidance.php</p>';
                exit();
            }
            
            error_log("View file exists, rendering visa_guidance view");
            
            // Pass data to view
            $data = [
                'countries' => $countries,
                'country_details' => $country_details,
                'basePath' => $this->basePath,
                'title' => 'Sri Lanka Visa Information'
            ];
            
            echo $this->view('home/visa_guidance', $data);
            exit();
        } catch (\Exception $e) {
            error_log("Error in visaGuidance: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Show simple error if the error view is not available
            if (!file_exists(ROOT_PATH . '/app/views/errors/database_error.php')) {
                echo '<h1>Application Error</h1>';
                echo '<p>An error occurred: ' . $e->getMessage() . '</p>';
                echo '<p><a href="' . $this->basePath . '/">Return to Home</a></p>';
                exit();
            }
            
            // Show error view
            echo $this->view('errors/database_error', [
                'message' => 'An error occurred while retrieving visa information',
                'basePath' => $this->basePath,
                'title' => 'Database Error'
            ]);
            exit();
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
            error_log("Error in aboutUs: " . $e->getMessage());
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
            error_log("Error in partnerHospitals: " . $e->getMessage());
            throw $e;
        }
    }
}