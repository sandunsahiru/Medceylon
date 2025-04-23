<?php
namespace App\Core\Middleware;

use App\Helpers\SessionHelper;

class TravelAgentMiddleware {
    public function handle() {
        $session = SessionHelper::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 4) {
            header("Location: /Medceylon/login");
            exit();
        }
        return true;
    }
}

