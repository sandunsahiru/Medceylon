
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'MedCeylon'; ?></title>
    <base href="/Medceylon/">
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
            <a href="<?php echo $this->session->isLoggedIn() ? '/home' : '/'; ?>">MedCeylon</a>
        </div>
        <nav class="nav-links">
            <a href="<?php echo $this->session->isLoggedIn() ? '/home' : '/'; ?>">Home</a>
            <a href="/about-us">About Us</a>
            <a href="/partner-hospitals">Our Hospitals</a>
            <?php if ($this->session->isLoggedIn()): ?>
                <a href="<?php echo $basePath; ?>/patient/book-appointment">Book Appointments</a>
            <?php else: ?>
                <a href="/login" onclick="alert('Please login to book appointments');">Book Appointments</a>
            <?php endif; ?>
            <a href="/pricing">Pricing</a>
        </nav>
        <div class="header-buttons">
            <?php if ($this->session->isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="btn profile">Profile</button>
                    <div class="dropdown-menu">
                        <a href="<?php echo $basePath; ?>/patient/book-appointment">My Appointments</a>
                        <a href="<?php echo $basePath; ?>/patient/profile">My Account</a>
                    </div>
                </div>
                <a href="/logout" class="btn logout">Logout</a>
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
        profileButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            if(isDropdownOpen) {
                dropdownMenu.classList.add('show');
            } else {
                dropdownMenu.classList.remove('show');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                isDropdownOpen = false;
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isDropdownOpen) {
                dropdownMenu.classList.remove('show');
                isDropdownOpen = false;
            }
        });

        // Prevent closing when clicking inside dropdown
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>