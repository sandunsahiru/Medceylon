
<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/accommodation.css">

<?php if ($this->session->hasFlash('error')): ?>
    <div class="flash-message error">
        <?= $this->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<?php if (!empty($details)): ?>
    <?php $firstItem = $details[0]; ?>
    <div class="accommodation-details">
        <div class="details-header">
            <h2><?= htmlspecialchars($firstItem['name']) ?></h2>
            <a href="<?= $basePath ?>/accommodation/accommodations" class="back-btn">Back to List</a>
        </div>
        
        <div class="details-content">
            <div class="details-left">
                <img src="<?= $basePath ?>/public/assets/<?= htmlspecialchars($firstItem['image_path'] ?? 'default.jpg') ?>" 
                     alt="<?= htmlspecialchars($firstItem['name']) ?>" class="details-image">
                
                <div class="details-info">
                    <p class="details-address">
                        <?= htmlspecialchars($firstItem['address_line1']) ?><br>
                        <?= htmlspecialchars($firstItem['address_line2']) ?><br>
                        <?= htmlspecialchars($firstItem['city_name']) ?>
                    </p>
                    <p class="details-contact"><strong>Contact:</strong> <?= htmlspecialchars($firstItem['contact_info']) ?></p>
                </div>
            </div>
            
            <div class="details-right">
                <h3>Available Room Types</h3>
                <div class="room-types-list">
                    <?php 
                    $roomTypes = [];
                    foreach ($details as $room) {
                        if (!isset($roomTypes[$room['room_type']])) {
                            $roomTypes[$room['room_type']] = [
                                'price' => $room['price_per_night'],
                                'description' => $room['description'] ?? ''
                            ];
                        }
                    }
                    
                    foreach ($roomTypes as $type => $info): ?>
                        <div class="room-type-item">
                            <h4><?= htmlspecialchars($type) ?></h4>
                            <p><?= htmlspecialchars($info['description']) ?></p>
                            <p class="price">LKR <?= htmlspecialchars($info['price']) ?> per night</p>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (!empty($firstItem['services'])): ?>
                    <div class="services-section">
                        <h3>Services Available</h3>
                        <p><?= htmlspecialchars($firstItem['services']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="booking-action">
            <button id="bookNowBtn" class="book-now-button" 
                    data-id="<?= htmlspecialchars($firstItem['provider_id']) ?>"
                    data-name="<?= htmlspecialchars($firstItem['name']) ?>">
                Book Now
            </button>
        </div>
    </div>
    
    <!-- Keep the booking modal from the original page -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <!-- Booking form content (same as in accommodation-providers.php) -->
            <!-- ... -->
        </div>
    </div>
    
<?php else: ?>
    <div class="no-details">
        <p>No details available for this accommodation.</p>
        <a href="<?= $basePath ?>/accommodation/accommodations" class="back-btn">Back to List</a>
    </div>
<?php endif; ?>

<script>
    const basePath = '<?php echo $basePath; ?>';
    const isLoggedIn = <?= $this->session->isLoggedIn() ? 'true' : 'false' ?>;
    
    // Store accommodation details for use in booking
    const accommodationDetails = <?= json_encode($details) ?>;
</script>

<script src="<?php echo $basePath; ?>/public/assets/js/accommodation-details.js"></script>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>