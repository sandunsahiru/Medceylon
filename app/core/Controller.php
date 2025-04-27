<?php
namespace App\Core;

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);
        require_once ROOT_PATH . "/app/views/" . $view . ".php";
    }

    public function redirect($path)
    {
        header("Location: " . BASE_URL . $path);
        exit();
    }
}
