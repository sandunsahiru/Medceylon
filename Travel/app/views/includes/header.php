<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon</title>
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/styles.css">
</head>
<body>
    <header class="header">
        <div class="logo">MedCeylon</div>
        <nav class="nav-links">
            <a href="home.php">Home</a>
            <a href="about-us.php">About Us</a>
            <a href="hospitals.php">Our Hospitals</a>
            <a href="#">Book Appointments</a> <!-- No link added yet -->
            <a href="pricing.php">Pricing</a>
        </nav>
        <div class="header-buttons">
            <div class="dropdown">
                <button class="btn profile">Profile</button>
                <div class="dropdown-menu">
                    <a href="#">My Appointments</a>
                    <a href="my_account.php">My Account</a>
                </div>
            </div>
            <button class="btn logout">Log Out</button>
        </div>
    </header>
</body>
</html>
