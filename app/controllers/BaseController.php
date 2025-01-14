<?php
namespace App\Controllers;

class BaseController {
    protected $db;
    protected $session;
    protected $config;
    protected $basePath;
    
    public function __construct() {
        global $db;
        $this->db = $db;
        $this->session = \App\Helpers\SessionHelper::getInstance();
        $this->config = require ROOT_PATH . '/app/config/app.php';
        $this->basePath = $this->config['base_url'];
    }
    
    protected function view($view, $data = []) {
        $data['basePath'] = $this->basePath;
        extract($data);
        require_once ROOT_PATH . "/app/views/{$view}.php";
    }

    protected function asset($path) {
        return $this->basePath . '/public/assets/' . ltrim($path, '/');
    }

    protected function url($path) {
        return $this->basePath . '/' . ltrim($path, '/');
    }
}