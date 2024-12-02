<?php
session_start();

class SessionManager {
    private static $instance = null;
    
    private function __construct() {
        // Private constructor to prevent direct creation
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new SessionManager();
        }
        return self::$instance;
    }
    
    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    // Get current user ID
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    // Get user role
    public function getUserRole() {
        return $_SESSION['role_id'] ?? null;
    }
    
    // Set user session data
    public function setUserSession($userId, $username, $roleId) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['role_id'] = $roleId;
    }
    
    // Clear user session
    public function clearSession() {
        session_unset();
        session_destroy();
    }
    
    // Get username
    public function getUsername() {
        return $_SESSION['username'] ?? null;
    }
    
    // Logout function
    public function logout() {
        // First check if there's an active session
        if ($this->isLoggedIn()) {
            // Clear all session variables
            $this->clearSession();
            
            // Start a new session to ensure clean state
            session_start();
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Set a logout message if needed
            $_SESSION['logout_message'] = "You have been successfully logged out.";
            
            return true;
        }
        return false;
    }
}