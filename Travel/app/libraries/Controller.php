<<<<<<< HEAD
<?php
    class Controller {
        // Load model
        public function model($model) {
            require_once '../app/models/' . $model . '.php';
            return new $model();
        }

        // Load view
        public function view($view, $data = []) {
            if(file_exists('../app/views/' . $view . '.php')) {
                extract($data);
                require_once '../app/views/' . $view . '.php';
            } else {
                die('Corresponding view does not exist');
            }
        }
=======
<?php
    class Controller {
        // Load model
        public function model($model) {
            require_once '../app/models/' . $model . '.php';
            return new $model();
        }

        // Load view
        public function view($view, $data = []) {
            if(file_exists('../app/views/' . $view . '.php')) {
                extract($data);
                require_once '../app/views/' . $view . '.php';
            } else {
                die('Corresponding view does not exist');
            }
        }
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
    }