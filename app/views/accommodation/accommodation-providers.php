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
                    <option value="<?= $province['province_id'] ?>"><?= htmlspecialchars($province['province_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-item" id="district-container" style="display: none;">
            <label for="district">District:</label>
            <select name="district_id" id="district">
                <option value="">Select District</option>
            </select>
        </div>

        <div class="filter-item" id="town-container" style="display: none;">
            <label for="town">Town:</label>
            <select name="town_id" id="town">
                <option value="">Select Town</option>
            </select>
        </div>

        <div class="filter-item">
            <label for="distance">Max Distance (km):</label>
            <input type="number" name="distance" id="distance" min="0">
        </div>

        <div class="filter-item">
            <label for="budget">Budget per night (LKR):</label>
            <input type="number" name="budget" id="budget" min="0" step="5000">
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
                    <p class = "accommodation-cost"><?= htmlspecialchars($acc['cost_per_night']) ?> LKR</p>
                    <p class="accommodation-address">
                        <?= htmlspecialchars($acc['address_line1']) ?><br>
                        <?= htmlspecialchars($acc['address_line2']) ?>
                    </p>
                    <p class="accommodation-contact"><strong>Contact:</strong> <?= htmlspecialchars($acc['contact_info']) ?></p>
                    <p class="accommodation-services"><strong>Services:</strong> <?= htmlspecialchars($acc['services_offered']) ?></p>
                    <button class="select-accommodation-button" 
                            data-id="<?= htmlspecialchars($acc['provider_id']) ?>" 
                            data-name="<?= htmlspecialchars($acc['name']) ?>" 
                            data-image="<?= htmlspecialchars($acc['image_path']) ?>" 
                            name="select-button">Book</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No accommodations found. Try changing the filters.</p>
    <?php endif; ?>
</div>
<br>

<button onclick = "location.href ='<?php echo $basePath; ?>/accommodation/get-booking-details';">View Bookings</button>
<br>
<br>

<?php include('process-booking.php'); ?> 

<script>
    const basePath = '<?php echo $basePath; ?>';
</script>

<script src="<?php echo $basePath; ?>/public/assets/js/accommodation.js"></script>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
