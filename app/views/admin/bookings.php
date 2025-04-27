<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <?php $page = 'bookings'; require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/bookings.css">

    <div class="main-content">
        <header>
            <h1>Patient Plans</h1>
        </header>
        <div class="section">
            <div class="toolbar">
                <div class="search-box">
                    <input type="text" id="search" placeholder="Search">
                </div>
                <div class="user-filters">
                    <button onclick="navigate('silver')">Silver </button>
                    <button onclick="navigate('gold')">Gold</button>
                    <button onclick="navigate('platinum')">Platinum</button>
                </div>
                <div>
                    <!-- <span>All users <strong>44</strong></span> -->
                    <button class="add-user-btn" onclick="window.location.href='<?= $basePath ?>/admin/adduser'">+
                        Add a hotel</button>
                </div>
            </div>


            <?php if (!empty($plans)): ?>
                <?php foreach ($plans as $plan): ?>
                    <div class="request-card">
                        <div class="request-details">
                            <div class="left-details">
                                <p><b>Patient:</b>
                                    <?php echo htmlspecialchars($plan['first_name']) . ' ' . htmlspecialchars($plan['last_name'] ?? '') ?>
                                </p>
                                <p><b>Plan Type:</b> <?php echo htmlspecialchars($plan['plan_name']) ?></p>
                                <p><b>Check-in:</b> <?php echo htmlspecialchars($plan['check_in_date'] ?? '') ?></p>
                                <p><b>Check-out:</b> <?php echo htmlspecialchars($plan['check_out_date'] ?? '') ?></p>
                            </div>
                            <div class="right-details">
                                <!-- buttons etc -->
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No Patient Plans</p>
            <?php endif; ?>


            <div id="bookingModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <div id="bookingDetails"></div>
                </div>
            </div>

        </div>



    </div>

</body>

<script>
    function navigate(status) {
        window.location.href = "<?= $basePath ?>/admin/Bookings?status=" + status;
    }

    function openModal(booking) {
        document.getElementById('bookingDetails').innerHTML = `
        <p><b>Room Type:</b> ${booking.room_type} ${booking.last_name ?? ''}</p>
        <p><b>Hotel:</b> ${booking.name}</p>
        <p><b>Contact:</b> ${booking.contact_info}</p>
        <p><b>Rooms Available:</b> ${booking.room_availability} Rooms</p>
        <div style="display:flex; justify-content:flex-end">
        <button onclick="confirmBooking(${booking.booking_id})">Confirm Booking</button></div>
    `;
        document.getElementById('bookingModal').style.display = 'flex';
    }

    function rejectModal(booking) {
        if (confirm("Are you sure you want to reject this booking?")) {
            // Create the AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo $basePath; ?>/admin/reject-booking', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Send the booking_id to the server
            xhr.send('booking_id=' + booking.booking_id);

            // Handle the server response
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    // Check if the booking was rejected successfully
                    if (response.success) {
                        alert('Booking rejected!');
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert('Failed to reject the booking!');
                    }
                } else {
                    alert('Something went wrong!');
                }
            };
        }
    }

    function closeModal() {
        document.getElementById('bookingModal').style.display = 'none';
    }

    function confirmBooking(bookingId) {
        // Create the AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo $basePath; ?>/admin/confirm-booking', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Send the booking_id to the server
        xhr.send('booking_id=' + bookingId);

        // Handle the server response
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);

                // Check if the booking was confirmed successfully
                if (response.success) {
                    alert('Booking confirmed!');
                    document.getElementById('bookingModal').style.display = 'none'; // Close modal
                    location.reload(); // Optionally reload the page to reflect changes
                } else {
                    alert('Failed to confirm the booking!');
                }
            } else {
                alert('Something went wrong!');
            }
        };
    }

</script>