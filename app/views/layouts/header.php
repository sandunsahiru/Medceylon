<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'MedCeylon'; ?></title>
    <base href="<?php echo $basePath; ?>/">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/header.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="<?php echo $basePath . ($this->session->isLoggedIn() ? '/home' : '/'); ?>">MedCeylon</a>
        </div>
        <nav class="nav-links">
            <a href="<?php echo $basePath . ($this->session->isLoggedIn() ? '/home' : '/'); ?>">Home</a>
            <a href="<?php echo $basePath; ?>/about-us">About Us</a>
            <a href="<?php echo $basePath; ?>/partner-hospitals">Our Hospitals</a>
            <?php if ($this->session->isLoggedIn()): ?>
                <a href="<?php echo $basePath; ?>/patient/book-appointment">Book Appointments</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>/login" onclick="alert('Please login to book appointments');">Book
                    Appointments</a>
            <?php endif; ?>
            <a href="<?php echo $basePath; ?>/patient/paymentPlan/">Pricing</a>
        </nav>
        <div class="header-buttons">
            <?php if ($this->session->isLoggedIn()): ?>
                <div class="dropdown">

                    <button onclick="toggleDropdown(event)" class="btn profile">Profile</i></button>
                    <div class="dropdown-menu">
                        <a href="<?php echo $basePath; ?>/patient/book-appointment">My Appointments</a>
                        <a href="<?php echo $basePath; ?>/patient/profile">My Account</a>
                    </div>
                </div>

                <a href="<?php echo $basePath; ?>/logout" class="btn logout">Logout</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>/register" class="btn">Sign Up</a>
                <a href="<?php echo $basePath; ?>/login" class="btn">Sign In</a>
            <?php endif; ?>
        </div>
    </header>

    <script>
        // Function to toggle dropdown visibility
        function toggleDropdown(event) {
            event.stopPropagation();  // Prevent the click event from propagating to the window
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.classList.toggle('show');
        }

        // Close dropdown if clicked outside
        window.addEventListener('click', function (event) {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const dropdownButton = document.querySelector('.profile');

            // Only close the dropdown if clicked outside both the dropdown and button
            if (!dropdownMenu.contains(event.target) && !dropdownButton.contains(event.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Ensure dropdown closes when focus is lost (for accessibility)
        document.querySelector('.profile').addEventListener('blur', function () {
            document.querySelector('.dropdown-menu').classList.remove('show');
        });
    </script>