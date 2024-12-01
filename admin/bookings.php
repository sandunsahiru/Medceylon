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
                        <div class="org-name">Rotaract Club - University of Colombo School of Computing</div>
                        <div class="time-ago">3 days ago</div>
                    </div>
                </div>
                <div class="info-body">
                    <div class="event-details">
                        <span class="event-label">Event Name</span>
                        <span class="event-value">Fitness Challenge 2.0</span>
                    </div>
                    <div class="amount-details">
                        <span class="amount-label">Amount</span>
                        <span class="amount-value">Rs. 120,000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>