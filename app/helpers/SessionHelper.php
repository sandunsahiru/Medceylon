<?php
namespace App\Helpers;

class SessionHelper {
    private static $instance = null;

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SessionHelper();
        }
        return self::$instance;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getUserRole() {
        return $_SESSION['role_id'] ?? null;
    }

    // New method for doctor role check
    public function isDoctor() {
        return $this->isLoggedIn() && $this->getUserRole() === 2;
    }

    // New method for patient role check
    public function isPatient() {
        return $this->isLoggedIn() && $this->getUserRole() === 1;
    }

    // New method for admin role check
    public function isAdmin() {
        return $this->isLoggedIn() && $this->getUserRole() === 3;
    }

    // Enhanced user session setting
    public function setUserSession($userId, $username, $roleId, $additionalData = []) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role_id'] = $roleId;
        
        // Store additional user data if provided
        if (!empty($additionalData)) {
            foreach ($additionalData as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }
    }

    public function getDoctorInfo() {
        if (!$this->isDoctor()) {
            return null;
        }

        return [
            'id' => $this->getUserId(),
            'username' => $this->getUsername(),
            'first_name' => $_SESSION['first_name'] ?? null,
            'last_name' => $_SESSION['last_name'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'hospital_id' => $_SESSION['hospital_id'] ?? null
        ];
    }

    public function clearSession() {
        session_unset();
        session_destroy();
    }

    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }

    public function logout() {
        if ($this->isLoggedIn()) {
            $this->clearSession();
            session_start();
            session_regenerate_id(true);
            $_SESSION['flash_messages']['success'] = "You have been successfully logged out.";
            return true;
        }
        return false;
    }

    public function setFlash($key, $message) {
        $_SESSION['flash_messages'][$key] = $message;
    }

    public function getFlash($key) {
        if (isset($_SESSION['flash_messages'][$key])) {
            $message = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $message;
        }
        return null;
    }

    public function hasFlash($key) {
        return isset($_SESSION['flash_messages'][$key]);
    }

    public function getCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // New method for getting all user data
    public function getUserData() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userData = [];
        $userKeys = ['user_id', 'username', 'role_id', 'first_name', 'last_name', 'email'];
        
        foreach ($userKeys as $key) {
            if (isset($_SESSION[$key])) {
                $userData[$key] = $_SESSION[$key];
            }
        }

        return $userData;
    }
}