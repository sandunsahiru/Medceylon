<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/accommodation.css">

<h2>Your Accommodation Bookings</h2>
<div class="bookings-container">
<?php if (!empty($bookings)): ?> 
    <?php foreach ($bookings as $booking): ?>
        <div class="booking-item">
            <div class="left-info">
                <h2 class="accommodation-name"><?= htmlspecialchars($booking['accommodation_name']) ?></h2>
                <p class="location"><?= htmlspecialchars($booking['city_name']) ?></p>
                <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($booking['image_path'] ?? 'default.jpg') ?>" 
                 alt="<?= htmlspecialchars($booking['accommodation_name'] ?? 'Unknown') ?>" 
                 class="accommodation-image" />
            </div>
            
            <div class="right-info">
                <p><strong>Check-In:</strong> <?= htmlspecialchars($booking['check_in_date']) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($booking['check_out_date']) ?></p>
                <p><strong>Room Type:</strong> <?= htmlspecialchars(ucfirst($booking['accommodation_type'])) ?></p>
                <p><strong>Special Requests:</strong> <?= htmlspecialchars($booking['special_requests'] ?: 'None') ?></p>
                <p class="status <?= strtolower(htmlspecialchars($booking['status'])) ?>">
                    <?= htmlspecialchars(ucfirst($booking['status'])) ?>
                </p>
                <div class="action-buttons">
                    <button 
                        type="button" 
                        class="edit-booking-button" 
                        data-booking-id="<?= htmlspecialchars($booking['accommodation_request_id']) ?>"
                        data-provider-id="<?= htmlspecialchars($booking['accommodation_provider_id']) ?>"
                        data-name="<?= htmlspecialchars($booking['accommodation_name']) ?>"
                        data-checkin="<?= htmlspecialchars($booking['check_in_date']) ?>"
                        data-checkout="<?= htmlspecialchars($booking['check_out_date']) ?>"
                        data-type="<?= htmlspecialchars($booking['accommodation_type']) ?>"
                        data-requests="<?= htmlspecialchars($booking['special_requests']) ?>"
                    >Edit</button>
                    <button 
                        type="button" 
                        class="cancel-booking-button" 
                        data-booking-id="<?= htmlspecialchars($booking['accommodation_request_id']) ?>"
                    >Cancel</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No accommodation bookings available. <a href="<?php echo $basePath; ?>/accommodation/accommodation-providers">Browse accommodations</a> to make a booking.</p>
<?php endif; ?>
</div>


<script src="<?php echo $basePath; ?>/public/assets/js/bookings.js"></script>

<!-- footer -->
<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>