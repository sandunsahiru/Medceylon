<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    private $userModel;
    protected $basePath = '/Medceylon';

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userData = [
                'user_type' => $_POST['user_type'] ?? '',
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'country' => $_POST['country'] ?? null,
                'contact_number' => $_POST['contact_number'] ?? null,
                'slmc_registration_number' => $_POST['slmc_registration_number'] ?? null,
                'age' => $_POST['age'] ?? null,
                'experience_years' => $_POST['experience_years'] ?? null
            ];

            // 🔒 BACKEND VALIDATION
            if (!preg_match("/^[a-zA-Z\s]+$/", $userData['name'])) {
                $error = "Name can only contain letters and spaces.";
            } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $userData['password'])) {
                $error = "Password must be at least 8 characters with 1 uppercase & 1 digit.";
            } elseif ($userData['contact_number'] && !preg_match('/^\+?\d+$/', $userData['contact_number'])) {
                $error = "Invalid phone number.";
            } elseif ($userData['slmc_registration_number'] && !preg_match('/^SLMC\d+$/', $userData['slmc_registration_number'])) {
                $error = "SLMC number must start with 'SLMC' followed by digits.";
            } elseif ($userData['age'] && $userData['age'] < 18) {
                $error = "Age must be 18 or older.";
            } elseif ($userData['experience_years'] && $userData['experience_years'] < 0) {
                $error = "Experience must be a positive number.";
            }

            if (isset($error)) {
                echo $this->view('auth/register', [
                    'error' => $error,
                    'oldInput' => $userData,
                    'basePath' => $this->basePath
                ]);
                return;
            }

            // Register logic
            $result = $this->userModel->register($userData);

            if ($result['success']) {
                $_SESSION['registration_success'] = true;
                header("Location: {$this->basePath}/login");
                exit();
            }

            echo $this->view('auth/register', [
                'error' => $result['error'],
                'oldInput' => $userData,
                'basePath' => $this->basePath,
                'formAction' => $this->basePath . '/register'
            ]);
            return;
        }

        echo $this->view('auth/register', [
            'basePath' => $this->basePath,
            'formAction' => $this->basePath . '/register'
        ]);
    }


    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $userType = $_POST['user_type'] ?? '';

            if ($userType === 'travel_agent') {
                if ($email === 'travelagent@example.com' && $password === 'agent123') {
                    $_SESSION['user_id'] = 999;
                    $_SESSION['name'] = 'Travel Agent';
                    $_SESSION['role_id'] = 4;
                    header("Location: {$this->basePath}/agent/transport-requests");
                    exit();
                } else {
                    echo $this->view('auth/login', [
                        'error' => 'Invalid Travel Agent credentials.',
                        'basePath' => $this->basePath
                    ]);
                    return;
                }
            }

            $result = $this->userModel->authenticate($email, $password, $userType);

            if ($result['success']) {
                $_SESSION['user_id'] = $result['user']['user_id'];
                $_SESSION['name'] = $result['user']['name'];
                $_SESSION['role_id'] = $result['user']['role_id'];

                $this->redirectBasedOnRole($result['user']['role_id']);
            } else {
                echo $this->view('auth/login', [
                    'error' => $result['error'],
                    'basePath' => $this->basePath
                ]);
                return;
            }
        }

        echo $this->view('auth/login', ['basePath' => $this->basePath]);
    }

    public function logout()
    {
        session_destroy();
        header("Location: {$this->basePath}/login");
        exit();
    }

    public function forgotPassword()
    {
        // keep as-is
    }

    public function resetPassword()
    {
        // keep as-is
    }

    private function redirectBasedOnRole($roleId)
    {
        $redirects = [
            1 => $this->basePath . '/home',
            2 => $this->basePath . '/doctor/dashboard',
            3 => $this->basePath . '/vpdoctor/dashboard',
            4 => $this->basePath . '/agent/transport-requests',
            5 => $this->basePath . '/admin/dashboard',
            6 => $this->basePath . '/hospital/dashboard',
            7 => $this->basePath . '/caregiver/dashboard'
        ];

        header("Location: " . ($redirects[$roleId] ?? $this->basePath . '/home'));
        exit();
    }

    private function mapUserTypeToRoleId($type)
    {
        return match ($type) {
            'patient' => 1,
            'general_doctor' => 2,
            'special_doctor' => 3,
            'caretaker' => 4,
            'admin' => 5,
            'hospital' => 6,
            default => 1
        };
    }
}
