<?php

namespace App\Controllers;

class BaseController
{
    protected $db;
    protected $session;
    protected $config;
    protected $basePath;

    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->session = \App\Helpers\SessionHelper::getInstance();
        $this->config = require ROOT_PATH . '/app/config/app.php';
        $this->basePath = $this->config['base_url'];
    }

    public function view($view, $data = [])
    {
        // Extract data to make variables available in view
        extract($data);

        // Set the base path for the view
        $basePath = $this->basePath;

        // Debug information
        error_log("Loading view: " . $view);
        error_log("View file path: " . ROOT_PATH . '/app/views/' . $view . '.php');

        // Include the view file
        $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            return ob_get_clean();
        }

        throw new \Exception("View {$view} not found");
    }

    protected function asset($path)
    {
        return $this->basePath . '/public/assets/' . ltrim($path, '/');
    }

    protected function url($path)
    {
        return $this->basePath . '/' . ltrim($path, '/');
    }
}
