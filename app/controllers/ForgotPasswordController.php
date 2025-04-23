<?php

namespace App\Controllers;

use App\Models\User;

class ForgotPasswordController extends BaseController
{
    protected $basePath = '/Medceylon';

    public function showForm()
    {
        echo $this->view('auth/forgot_password', ['basePath' => $this->basePath]);
    }

    public function handleForm()
    {
        $email = $_POST['email'] ?? '';
        if (!$email) {
            echo $this->view('auth/forgot_password', [
                'error' => 'Email is required.',
                'basePath' => $this->basePath
            ]);
            return;
        }

        $token = bin2hex(random_bytes(16));
        $_SESSION['reset_tokens'][$email] = $token;

        $resetLink = "http://localhost:8080{$this->basePath}/reset-password?email=$email&token=$token";

        // For demo purposes: show the reset link on screen
        echo "<div style='padding:30px'><strong>Reset Link:</strong> <a href='$resetLink'>$resetLink</a></div>";
    }

    public function showResetForm()
    {
        $email = $_GET['email'] ?? '';
        $token = $_GET['token'] ?? '';

        if (!isset($_SESSION['reset_tokens'][$email]) || $_SESSION['reset_tokens'][$email] !== $token) {
            echo "Invalid or expired reset link.";
            return;
        }

        echo $this->view('auth/reset_password', [
            'email' => $email,
            'token' => $token,
            'basePath' => $this->basePath
        ]);
    }

    public function handleReset()
    {
        $email = $_POST['email'] ?? '';
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if (!isset($_SESSION['reset_tokens'][$email]) || $_SESSION['reset_tokens'][$email] !== $token) {
            echo "Invalid or expired reset link.";
            return;
        }

        $userModel = new User();
        $success = $userModel->updatePassword($email, $newPassword);

        if ($success) {
            unset($_SESSION['reset_tokens'][$email]);

            // ✅ Now show a styled success view instead of raw echo
            echo $this->view('auth/password_reset_success', [
                'basePath' => $this->basePath
            ]);
        } else {
            echo "Something went wrong. Try again.";
        }
    }
}
