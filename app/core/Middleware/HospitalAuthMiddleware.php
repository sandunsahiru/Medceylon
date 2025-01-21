<?php

namespace App\Core\Middleware;

class HospitalAuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
            header('Location: /Medceylon/login');
            return false;
        }

        // Check if user has hospital admin role (role_id = 6)
        if ($_SESSION['role_id'] !== 6) {
            header('Location: /Medceylon/unauthorized');
            return false;
        }

        return true;
    }
}