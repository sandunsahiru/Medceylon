<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/destinations.css">

    <br>
    <h2>Make Customized Travel Plan</h2>
    <!-- Trigger Button -->
    <button onclick="location.href='<?php echo $basePath; ?>/travelplan/travel-preferences';">Do it for Me</button><br><br>

    <?php error_log("Destinations data: " . print_r($destinations, true)); ?>
   
    <div class="travel-destinations-wrapper">
    <?php if (!empty($destinations)): ?> 
        <?php foreach ($destinations as $destination): ?>
    <div class="destination">
        <div class="destination-image">
            <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($destination['image_path'] ?? 'default.jpg') ?>" 
                 alt="<?= htmlspecialchars($destination['destination_name'] ?? 'Unknown') ?>">
        </div>
        <div class="destination-info">
            <span class="destination-name"><?= htmlspecialchars($destination['destination_name'] ?? 'Unknown') ?></span><br>
            <span class="destination-region"><?= htmlspecialchars($destination['province'] ?? 'Unknown') ?></span>
            <p class="destination-description"><?= htmlspecialchars($destination['description'] ?? 'No description available.') ?></p>
            <button class="add-destination-button" 
                data-id="<?= htmlspecialchars($destination['destination_id'] ?? '') ?>" 
                data-name="<?= htmlspecialchars($destination['destination_name'] ?? '') ?>" 
                data-image="<?= htmlspecialchars($destination['image_path'] ?? '') ?>" 
                name="add-button">Add</button>
        </div>
    </div>
<?php endforeach; ?>

    <?php else: ?>
        <p>No destinations available. Please check back later.</p>
    <?php endif; ?>
</div>

    <br>

    <button onclick="location.href='<?php echo $basePath; ?>/travelplan/travel-plans';">View All Plans</button>

    <br>
    <br>
    <br>

    <!-- footer -->
    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
    
    <?php include('add-destination.php'); ?>  

    <script src="<?php echo $basePath; ?>/public/assets/js/travel.js"></script>
    </body>