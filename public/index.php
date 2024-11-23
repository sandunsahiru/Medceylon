<?php
// Define the base path of the application
define('BASEPATH', dirname(dirname(__FILE__)));

// Load Config
require_once '../app/config/config.php';

// Autoload Libraries
require_once '../app/libraries/Core.php';
require_once '../app/libraries/Controller.php';
require_once '../app/libraries/Database.php';

// Initialize Core Library
$init = new Core();