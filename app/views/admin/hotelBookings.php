<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/hotelbooking.css">

    <div class="main-content">
        <header>
            <h1>Hotel Bookings</h1>
        </header>
        <div class="section">
            <h3 style="text-align: center;">Hotel Booking Requests</h3>

            <?php if (!empty($hotelBooking)): ?>
                <?php foreach ($hotelBooking as $hotelBookings): ?>
                    <div class="request-card">
                        <div class="request-details">
                            <div class="left-details">
                                <p>Patient:</p>
                                <p>Hospital:</p>
                                <p>Check-in: </p>
                                <p>Check-out: </p>
                            </div>
                            <div class="right-details">
                                <p>image</p>
                                <p>Hotel: </p>
                                <p>Status: </p>
                            </div>
                        </div>
                        <div class="request-status">
                            <button>Approve</button>
                            <button>Reject</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No Pending Bookings</p>
            <?php endif; ?>
        </div>


    </div>

</body>