<?php
// includes/SessionManager.php

class SessionManager {
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function setSampleSessionData($userId, $role) {
        self::startSession();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;
        $_SESSION['logged_in'] = true;
    }

    public static function getUserRole() {
        self::startSession();
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    }

    public static function requireLogin($requiredRole = null) {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            header('Location: index.php?page=select_role');
            exit();
        }

        if ($requiredRole !== null && $_SESSION['user_role'] !== $requiredRole) {
            echo 'Unauthorized access';
            exit();
        }
    }

    public static function getUserId() {
        self::startSession();
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
}
?>
