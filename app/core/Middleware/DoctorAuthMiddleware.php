<?php

namespace App\Core\Middleware;

class DoctorAuthMiddleware {
    public function handle() {
        $session = \App\Helpers\SessionHelper::getInstance();
        $config = require ROOT_PATH . '/app/config/app.php';
        
        if (!$session->isLoggedIn()) {
            header('Location: ' . $config['base_url'] . '/auth/login');
            exit();
        }

        // Check if user is a doctor (assuming role_id 2 is for doctors)
        if ($session->getUserRole() !== 2) {
            header('Location: ' . $config['base_url'] . '/auth/unauthorized');
            exit();
        }

        return true;
    }
}