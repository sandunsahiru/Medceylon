<?php
// app/config/database.php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'medceylon';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check for connection error
if ($db->connect_error) {
    // If connection fails, throw an error message
    die("Connection failed: " . $db->connect_error);
}

// Define debug mode if not already defined
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

return $db;  // Return the mysqli connection object if successful
