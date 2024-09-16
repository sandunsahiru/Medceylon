<!-- views/templates/navbar.php -->
<?php
require_once 'includes/SessionManager.php';
SessionManager::startSession();
?>

<header class="main-header">
    <div class="logo">
        <a href="index.php"><img src="assets/images/logo.png" alt="MedCeylon Logo"></a>
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php?page=home">Home</a></li>
            <li><a href="index.php?page=about">About Us</a></li>
            <li><a href="index.php?page=hospitals">Our Hospitals</a></li>
            <li><a href="index.php?page=faqs">FAQs</a></li>
            <?php
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                $userRole = SessionManager::getUserRole();
                if ($userRole === 'Patient') {
                    echo '<li><a href="index.php?page=profile">My Profile</a></li>';
                    echo '<li><a href="index.php?page=appointments">My Appointments</a></li>';
                    echo '<li><a href="index.php?page=accommodation">Accommodation</a></li>';
                } elseif ($userRole === 'Doctor') {
                    echo '<li><a href="index.php?page=doctor_dashboard">Dashboard</a></li>';
                    echo '<li><a href="index.php?page=patients">My Patients</a></li>';
                } elseif ($userRole === 'Hotel') {
                    echo '<li><a href="index.php?page=hotel_dashboard">Dashboard</a></li>';
                    echo '<li><a href="index.php?page=bookings">Bookings</a></li>';
                }
                echo '<li><a href="index.php?page=logout">Logout</a></li>';
            } else {
                echo '<li><a href="index.php?page=login">Sign In</a></li>';
                echo '<li><a href="index.php?page=register">Sign Up</a></li>';
            }
            ?>
        </ul>
    </nav>
</header>

