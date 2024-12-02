<?php
$page = "bookings";
include "./includes/header.php";
?>

<body>
    <link rel="stylesheet" href="./css/bookings.css">
    <?php include "./includes/navbar.php" ?>

    <div class="main-content">
        <header>
            <h1>User Management</h1>
        </header>

        <div class="container">
            <div class="search-and-actions">
                <form method="GET">
                    <input type="text" placeholder="Search here...">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="info-card">
                <div class="info-header">
                    <div class="header-details">
                        <div class="org-name">Patient: Eren Yaeger</div>
                        <div class="time-ago">Online Consultation</div>
                    </div>
                </div>
                <div class="info-body">
                    <div class="event-details">
                        <span class="event-label">Doctor</span>
                        <span class="event-value">Erwin Smith</span>
                    </div>
                    <div class="amount-details">
                        <span class="amount-label">Plan Type</span>
                        <span class="amount-value">Gold Plan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>