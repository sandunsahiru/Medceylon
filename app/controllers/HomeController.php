<?php
namespace App\Controllers;

class HomeController extends BaseController {
    
    public function index() {
        // If user is logged in, redirect to home
        if ($this->session->isLoggedIn()) {
            return $this->home();
        }
        
        return $this->view('home/index');
    }
    
    public function home() {
        // If user is not logged in, redirect to index
        if (!$this->session->isLoggedIn()) {
            header("Location: /");
            exit();
        }

        $data = [
            'user' => [
                'name' => $this->session->getUsername(),
                'role_id' => $this->session->getUserRole()
            ]
        ];
        
        return $this->view('home/home', $data);
    }
}