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
    <?php if (isset($extraCss)): ?>
        <?php foreach($extraCss as $css): ?>
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
            <a href="<?php echo $basePath; ?>hospital/partner-hospitals">Our Hospitals</a>
            <?php if ($this->session->isLoggedIn()): ?>
                <a href="<?php echo $basePath; ?>/patient/book-appointment">Book Appointments</a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>/login" onclick="alert('Please login to book appointments');">Book Appointments</a>
            <?php endif; ?>
            <a href="<?php echo $basePath; ?>/pricing">Pricing</a>
        </nav>
        <div class="header-buttons">
            <?php if ($this->session->isLoggedIn()): ?>
                <div class="dropdown">
                    <button href="<?php echo $basePath; ?>/about-us" class="btn profile">Profile</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const profileButton = document.querySelector('.btn.profile');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        let isDropdownOpen = false;

    if (profileButton && dropdownMenu) {
        profileButton.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            dropdownMenu.classList.toggle('show', isDropdownOpen);
        });

        document.addEventListener('click', function (e) {
            if (!profileButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                isDropdownOpen = false;
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
                isDropdownOpen = false;
            }
        });

        dropdownMenu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
});
</script>
