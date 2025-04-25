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
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}

return $db;