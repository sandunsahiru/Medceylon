<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/accommodation.css">

<?php if ($this->session->hasFlash('error')): ?>
    <div class="flash-message error">
        <?= $this->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<?php if ($this->session->hasFlash('success')): ?>
    <div class="flash-message success">
        <?= $this->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<h2>Browse Accommodations</h2>

<div class="filter-bar">
    <form method="get" action="">
        <div class="filter-item">
            <label for="province">Select Your Current Location</label>
            <select id="province" name="province_id">
                <option value="">Select Province</option>
                <?php foreach ($provinces as $province): ?>
                    <option value="<?= $province['province_id'] ?>" 
                        <?= ($province['province_id'] == ($selected_province ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($province['province_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-item">
            <label for="budget">Budget Category:</label>
            <select id="budget" name="budget">
                <option value="">Select Budget</option>
                <option value="Affordable" <?= (($selected_budget ?? '') == 'Affordable') ? 'selected' : '' ?>>Low</option>
                <option value="Luxury" <?= (($selected_budget ?? '') == 'Luxury') ? 'selected' : '' ?>>Medium</option>
            </select>
        </div>

        <div class="filter-item">
            <button type="submit">Filter</button>
        </div>
    </form>
</div>

<div class="accommodations-wrapper">
    <?php if (!empty($accommodations)): ?> 
        <?php foreach ($accommodations as $acc): ?>
            <div class="accommodation">
                <div class="accommodation-image">
                    <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($acc['image_path'] ?? 'default.jpg') ?>" 
                         alt="<?= htmlspecialchars($acc['name'] ?? 'Accommodation') ?>">
                </div>
                <div class="accommodation-info">
                    <h3 class="accommodation-name"><?= htmlspecialchars($acc['name']) ?></h3>
                    <p class="accommodation-address">
                        <?= htmlspecialchars($acc['address_line1']) ?><br>
                        <?= htmlspecialchars($acc['address_line2']) ?>
                    </p>
                    <p class="accommodation-contact"><strong>Contact:</strong> <?= htmlspecialchars($acc['contact_info']) ?></p>
                    <button class="view-details-button" 
                            data-id="<?php echo $acc['provider_id']; ?>">View Details<i class="ri-eye-line"></i></button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No accommodations found. Try changing the filters.</p>
    <?php endif; ?>
</div>
<hr class="divider">

<div class="booking-status-section">
    <h3>Your Bookings</h3>
    <?php if (!empty($bookings)): ?>
        <table class="booking-table">
            <thead>
                <tr>
                    <th>Accommodation</th>
                    <th>Room Type</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking['accommodation_name']) ?></td>
                        <td><?= htmlspecialchars($booking['room_type']) ?></td>
                        <td><?= date('M d, Y', strtotime($booking['check_in_date'])) ?></td>
                        <td><?= date('M d, Y', strtotime($booking['check_out_date'])) ?></td>
                        <td>LKR <?= number_format($booking['total_price'], 2) ?></td>
                        <td class = "bookingStatus"><?= htmlspecialchars($booking['status']) ?><td>
                        <td><?php if ($booking['status'] === 'Pending'): ?>
                            <button class="delete-booking" data-booking-id="<?= $booking['booking_id'] ?>">
                                Cancel Booking</button>
                            <?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        
                                 
    <?php else: ?>
        <p>You don't have any bookings yet.</p>
    <?php endif; ?>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="detailsName"></h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-left">
                <img id="detailsImage" src="" alt="Accommodation Image" class="modal-image">
                <p id="detailsAddress"></p>
                <p id="detailsContact"></p>
            </div>
            <div class="modal-right">
                <h4>Available Room Types</h4>
                <div id="roomTypesList"></div>
                <div id="detailsServices"></div>
            </div>
        </div>
        <button id="bookNowBtn" class="book-now-button">Book Now</button>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="bookingModalTitle">Book Accommodation</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="bookingForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="accommodation_provider_id" id="accommodationProviderId">
            <input type="hidden" name="patient_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="accommodation_type" id="accommodationType">
            <input type="hidden" name="room_type" id="roomType">
            <input type="hidden" name="total_price" id="totalPriceInput">
            
            <div class="form-group">
                <label for="accommodationName">Accommodation</label>
                <input type="text" id="accommodationName" readonly>
            </div>
            
            <div class="form-group">
                <label for="checkInDate">Check-in Date</label>
                <input type="date" id="checkInDate" name="check_in_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label for="checkOutDate">Check-out Date</label>
                <input type="date" id="checkOutDate" name="check_out_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            
            <div class="form-group">
                <label for="specialRequests">Special Requests</label>
                <textarea id="specialRequests" name="special_requests" rows="3"></textarea>
            </div>
            
            <div class="booking-summary">
                <h3>Booking Summary</h3>
                <div class="summary-item">
                    <span>Price per night:</span>
                    <span>LKR <span id="pricePerNight">0.00</span></span>
                </div>
                <div class="summary-item">
                    <span>Total nights:</span>
                    <span><span id="totalNights">1</span> night(s)</span>
                </div>
                <div class="summary-item total">
                    <span>Total price:</span>
                    <span>LKR <span id="totalPrice">0.00</span></span>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-check-line"></i>
                    Confirm Booking
                </button>
                <button type="button" class="close-btn">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const basePath = "http://localhost/Medceylon";
    const isLoggedIn = <?= $this->session->isLoggedIn() ? 'true' : 'false' ?>;
</script>

<script src="<?php echo $basePath; ?>/public/assets/js/accommodation.js"></script>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>