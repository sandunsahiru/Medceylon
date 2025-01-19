<?php

namespace App\Core\Middleware;

class VPDoctorAuthMiddleware
{
    public function handle()
    {
        // Check if session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit();
        }

        // Check if user has role ID for specialist doctor (role_id = 3)
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== 3) {
            // If not a specialist doctor, redirect based on role
            switch ($_SESSION['role_id']) {
                case 2: // General Doctor
                    header('Location: /doctor/dashboard');
                    break;
                case 4: // Patient
                    header('Location: /patient/dashboard');
                    break;
                case 5: // Admin
                    header('Location: /admin/dashboard');
                    break;
                default:
                    // Invalid or unknown role, log them out
                    session_destroy();
                    header('Location: /auth/login?error=invalid_role');
                    break;
            }
            exit();
        }
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Check if user account is active
        if (isset($_SESSION['is_active']) && !$_SESSION['is_active']) {
            session_destroy();
            header('Location: /auth/login?error=account_inactive');
            exit();
        }

        // Verify CSRF token for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                header('HTTP/1.1 403 Forbidden');
                exit('Invalid CSRF token');
            }
        }

        // Update last activity timestamp
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Check session timeout
     * @return bool
     */
    private function checkSessionTimeout()
    {
        $timeout = 30 * 60; // 30 minutes
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            session_destroy();
            header('Location: /auth/login?error=session_timeout');
            exit();
        }
        return true;
    }

    /**
     * Verify if user has necessary permissions
     * @return bool
     */
    private function verifyPermissions()
    {
        // Add additional permission checks here if needed
        return true;
    }
}