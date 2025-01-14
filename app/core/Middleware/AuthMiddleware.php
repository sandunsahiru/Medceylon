<?php
namespace App\Core\Middleware;

class AuthMiddleware {
    public function handle() {
        $session = \App\Helpers\SessionHelper::getInstance();
        $config = require ROOT_PATH . '/app/config/app.php';
        
        if (!$session->isLoggedIn()) {
            header('Location: ' . $config['base_url'] . '/login');
            return false;
        }
        return true;
    }
}

?>