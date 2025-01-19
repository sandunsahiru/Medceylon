<?php
namespace App\Core\Middleware;

class AdminMiddleware {
    public function handle() {
        $session = \App\Helpers\SessionHelper::getInstance();
        if (!$session->isLoggedIn() || $session->getUserRole() !== 4) {
            header('Location: /Medceylon/login');
            return false;
        }
        return true;
    }
}
?>