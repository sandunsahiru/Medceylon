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
    error_log("Attempting to load view: " . $view);
    
    // Extract data to make variables available in view
    extract($data);
    
    // Set the base path for the view
    $basePath = $this->basePath;
    
    // Include the view file
    $viewFile = ROOT_PATH . '/app/views/' . $view . '.php';
    error_log("Full view path: " . $viewFile);
    error_log("View file exists: " . (file_exists($viewFile) ? 'Yes' : 'No'));
    
    if (!file_exists($viewFile)) {
        error_log("View file not found: " . $viewFile);
        throw new \Exception("View {$view} not found at {$viewFile}");
    }
    
    ob_start();
    require $viewFile;
    $content = ob_get_clean();
    
    if (empty($content)) {
        error_log("View rendered empty content");
    }
    
    return $content;
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
