<?php
    class Pages extends Controller{
        
        public function __construct() {
            $this->pageModel = $this->model('M_Pages');            
        }

        public function index() {
            $this->view('index');
            
        }

        public function about() {
            $this->view('about-us');
        }
    }

?>