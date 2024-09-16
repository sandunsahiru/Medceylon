<!-- views/accommodation.php -->
<?php
$pageTitle = 'Accommodation Planning';
include 'templates/main_header.php';
include 'templates/navbar.php';
?>
<!-- Hero Section -->
<div class="hero-section">
    <h1>Find Accommodation Near Your Hospital</h1>
</div>

<div class="content">
    <!-- Display success or error message -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert success">Accommodation booked successfully.</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
        <div class="alert error">There was an error booking your accommodation.</div>
    <?php endif; ?>

    <!-- Accommodation Search Form -->
    <form action="index.php?page=accommodation" method="POST" class="accommodation-form">
        <div class="form-row">
            <div class="form-group">
                <label for="check_in_date" class="formbold-form-label">Check-In Date</label>
                <input type="date" id="check_in_date" name="check_in_date" class="formbold-form-input" required
                    value="<?php echo isset($checkInDate) ? htmlspecialchars($checkInDate) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="check_out_date" class="formbold-form-label">Check-Out Date</label>
                <input type="date" id="check_out_date" name="check_out_date" class="formbold-form-input" required
                    value="<?php echo isset($checkOutDate) ? htmlspecialchars($checkOutDate) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="guests" class="formbold-form-label">Number of Guests</label>
                <input type="number" id="guests" name="guests" class="formbold-form-input" value="<?php echo isset($guests) ? htmlspecialchars($guests) : '1'; ?>" min="1" required>
            </div>

            <button type="submit" class="btn">Search</button>
        </div>
    </form>

    <!-- Display Hotels and Available Rooms -->
    <?php if (!empty($hotels)): ?>
        <h2>Available Hotels in <?php echo htmlspecialchars($cityName); ?></h2>
    <?php else: ?>
        <h2>All Hotels</h2>
    <?php endif; ?>
    <div class="hotels-list">
        <?php foreach ($hotels as $hotel): ?>
            <div class="hotel-item card">
                <!-- Hotel Images Carousel -->
                <div class="hotel-images">
                    <?php
                    $images = [];
                    for ($i = 1; $i <= 4; $i++) {
                        if (!empty($hotel['image' . $i])) {
                            $images[] = $hotel['image' . $i];
                        }
                    }
                    ?>
                    <?php if (!empty($images)): ?>
                        <div class="carousel">
                            <?php foreach ($images as $image): ?>
                                <div class="carousel-item">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="Hotel Image">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <img src="assets/images/default-hotel.jpg" alt="Default Hotel Image">
                    <?php endif; ?>
                </div>

                <!-- Hotel Information -->
                <div class="hotel-info">
                    <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                    <p class="hotel-address"><?php echo htmlspecialchars($hotel['address_line1']); ?></p>
                    <p class="hotel-description"><?php echo htmlspecialchars($hotel['description']); ?></p>
                </div>

                <!-- Available Rooms -->
                <?php if (!empty($hotel['rooms'])): ?>
                    <div class="rooms-list">
                        <?php foreach ($hotel['rooms'] as $room): ?>
                            <div class="room-item">
                                <h4><?php echo htmlspecialchars($room['room_type']); ?></h4>
                                <p class="room-description"><?php echo htmlspecialchars($room['description']); ?></p>
                                <p class="room-price">Price per Night: $<?php echo htmlspecialchars($room['price_per_night']); ?></p>
                                <p class="room-capacity">Max Guests: <?php echo htmlspecialchars($room['max_guests']); ?></p>
                                <form action="index.php?page=accommodation&action=book" method="POST">
                                    <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                                    <input type="hidden" name="check_in_date" value="<?php echo htmlspecialchars($checkInDate ?? ''); ?>">
                                    <input type="hidden" name="check_out_date" value="<?php echo htmlspecialchars($checkOutDate ?? ''); ?>">
                                    <input type="hidden" name="guests" value="<?php echo htmlspecialchars($guests ?? '1'); ?>">
                                    <div class="form-group">
                                        <label for="special_requests_<?php echo htmlspecialchars($room['room_id']); ?>" class="formbold-form-label">Special Requests</label>
                                        <textarea id="special_requests_<?php echo htmlspecialchars($room['room_id']); ?>" name="special_requests" class="formbold-form-input"></textarea>
                                    </div>
                                    <button type="submit" class="btn">Book Now</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No rooms available for the selected dates.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/main_footer.php'; ?>
