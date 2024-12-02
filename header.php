<?php
require_once 'includes/sessionmanager.php';
$sessionManager = SessionManager::getInstance();

// Redirect to home.php if logged in and currently on index.php
if ($sessionManager->isLoggedIn() && basename($_SERVER['PHP_SELF']) === 'index.php') {
    header("Location: home.php");
    exit();
}

// Redirect to index.php if not logged in and trying to access home.php
if (!$sessionManager->isLoggedIn() && basename($_SERVER['PHP_SELF']) === 'home.php') {
    header("Location: index.php");
    exit();
}

// Handle Logout if logout button is clicked
if (isset($_GET['logout'])) {
    $sessionManager->logout();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon</title>
    <link rel="stylesheet" href="./assets/css/header.css">
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="<?php echo $sessionManager->isLoggedIn() ? 'home.php' : 'index.php'; ?>" style="color: white; text-decoration: none;">MedCeylon</a>
        </div>
        <nav class="nav-links">
            <a href="<?php echo $sessionManager->isLoggedIn() ? 'home.php' : 'index.php'; ?>">Home</a>
            <a href="about-us.php">About Us</a>
            <a href="partner-hospitals.php">Our Hospitals</a>
            <?php if ($sessionManager->isLoggedIn()): ?>
                <a href="book-appointment.php">Book Appointments</a>
            <?php else: ?>
                <a href="user_login.php" onclick="alert('Please login to book appointments');">Book Appointments</a>
            <?php endif; ?>
            <a href="pricing.php">Pricing</a>
        </nav>
        <div class="header-buttons">
            <?php if ($sessionManager->isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="btn profile">Profile</button>
                    <div class="dropdown-menu">
                        <a href="patient/book-appointment.php">My Appointments</a>
                        <a href="patient/profile.php">My Account</a>
                    </div>
                </div>
                <!-- Added logout link with a GET parameter for logout -->
                <a href="?logout=true" class="btn logout" style="text-decoration: none;">Logout</a>
            <?php else: ?>
                <a href="register.php" class="btn logout" style="text-decoration: none;">Sign Up</a>
                <a href="user_login.php" class="btn logout" style="text-decoration: none;">Sign In</a>
            <?php endif; ?>
        </div>
    </header>
</body>
</html>
