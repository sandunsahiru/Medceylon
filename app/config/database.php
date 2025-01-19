<?php
// app/config/database.php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'medceylon';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
define('DEBUG_MODE', true); // Set to false in production
// Make the database connection available globally
return $db;