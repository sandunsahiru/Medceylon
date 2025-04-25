<?php
namespace App\Controllers;

class HomeController extends BaseController
{

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

            $roleId = $this->session->getUserRole();

            // Prepare data for view
            $data = [
                'user' => [
                    'name' => $this->session->getUsername(),
                    'role_id' => $roleId
                ],
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
}