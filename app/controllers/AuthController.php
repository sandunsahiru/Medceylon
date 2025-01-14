<?php
namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController {
   private $userModel;
   protected $basePath = '/Medceylon';
   
   public function __construct() {
       parent::__construct();
       $this->userModel = new User();
   }
   
   public function register() {
       if ($this->session->isLoggedIn()) {
           header("Location: {$this->basePath}/home");
           exit();
       }

       if ($_SERVER["REQUEST_METHOD"] == "POST") {
           $userData = [
               'name' => $_POST['name'],
               'email' => $_POST['email'],
               'password' => $_POST['password'],
               'user_type' => $_POST['user_type'],
               'country' => $_POST['country'] ?? null,
               'contact_number' => $_POST['contact_number'] ?? null,
               'slmc_registration_number' => $_POST['slmc_registration_number'] ?? null,
               'age' => $_POST['age'] ?? null,
               'experience_years' => $_POST['experience_years'] ?? null
           ];

           $result = $this->userModel->register($userData);
           
           if ($result['success']) {
               $_SESSION['registration_success'] = true;
               header("Location: {$this->basePath}/login");
               exit();
           }
           
           return $this->view('auth/register', [
               'error' => $result['error'],
               'oldInput' => $userData
           ]);
       }

       return $this->view('auth/register');
   }

   public function login() {
       if ($this->session->isLoggedIn()) {
           header("Location: {$this->basePath}/home");
           exit();
       }

       if ($_SERVER["REQUEST_METHOD"] == "POST") {
           $email = $_POST['email'];
           $password = $_POST['password'];
           $userType = $_POST['user_type'];

           $result = $this->userModel->authenticate($email, $password, $userType);
           
           if ($result['success']) {
               $this->session->setUserSession(
                   $result['user']['user_id'],
                   $result['user']['name'],
                   $result['user']['role_id']
               );
               
               $this->redirectBasedOnRole($result['user']['role_id']);
           }
           
           return $this->view('auth/login', ['error' => $result['error']]);
       }

       return $this->view('auth/login');
   }

   public function logout() {
       $this->session->logout();
       header("Location: {$this->basePath}/login");
       exit();
   }

   private function redirectBasedOnRole($roleId) {
       $redirects = [
           1 => $this->basePath . '/patient/dashboard',
           2 => $this->basePath . '/doctor/dashboard',
           4 => $this->basePath . '/admin/dashboard', 
           5 => $this->basePath . '/caregiver/dashboard',
           6 => $this->basePath . '/travel-partner/dashboard'
       ];
       
       header("Location: " . ($redirects[$roleId] ?? $this->basePath . '/home'));
       exit();
   }
}