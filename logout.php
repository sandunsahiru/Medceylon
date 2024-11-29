<?php
require_once 'sessionmanager.php';
$sessionManager = SessionManager::getInstance();

// Destroy the session
$sessionManager->logout();

// Redirect to index page
header("Location: index.php");
exit();