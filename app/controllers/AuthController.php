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

            // Hardcoded Travel Agent credentials
            if ($userType === 'travel_agent') {
                if ($email === 'travelagent@example.com' && $password === 'agent123') {
                    $this->session->setUserSession(999, 'Travel Agent', 4);
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
                $this->session->setUserSession(
                    $result['user']['user_id'],
                    $result['user']['name'],
                    $result['user']['role_id']
                );

                $this->redirectBasedOnRole($result['user']['role_id']);
            }

            echo $this->view('auth/login', [
                'error' => $result['error'],
                'basePath' => $this->basePath
            ]);
            return;
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';

            if (empty($email)) {
                echo $this->view('auth/forgot-password', ['error' => 'Email is required.']);
                return;
            }

            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            if ($this->userModel->storeResetToken($email, $token, $expiry)) {
                $resetLink = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "{$this->basePath}/reset-password?token=$token";

                echo $this->view('auth/forgot-password', [
                    'message' => "Password reset link: <a href=\"$resetLink\">Reset Password</a>"
                ]);
                return;
            } else {
                echo $this->view('auth/forgot-password', ['error' => 'Email not found.']);
                return;
            }
        }

        echo $this->view('auth/forgot-password');
    }

    public function resetPassword()
    {
        $token = $_GET['token'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($newPassword)) {
                echo $this->view('auth/reset-password', ['error' => 'Password is required.', 'token' => $token]);
                return;
            }

            $result = $this->userModel->resetPasswordWithToken($token, $newPassword);

            if ($result['success']) {
                echo $this->view('auth/reset-password', ['message' => 'Password successfully updated!']);
                return;
            } else {
                echo $this->view('auth/reset-password', ['error' => $result['error'], 'token' => $token]);
                return;
            }
        }

        echo $this->view('auth/reset-password', ['token' => $token]);
    }

    private function redirectBasedOnRole($roleId)
    {
        $redirects = [
            1 => $this->basePath . '/home',
            2 => $this->basePath . '/doctor/dashboard',
            3 => $this->basePath . '/vpdoctor/dashboard',
            4 => $this->basePath . '/admin/dashboard',
            5 => $this->basePath . '/agent/transport-requests',
            6 => $this->basePath . '/hospital/dashboard'
        ];

        header("Location: " . ($redirects[$roleId] ?? $this->basePath . '/home'));
        exit();
    }
}
