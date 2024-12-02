<<<<<<< HEAD
<?php require APPROOT . '/views/includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/travel-plans.css">


    <br>
    <h2>Travel Destinations</h2>
   
    <div class="travel-destinations-wrapper">
    <?php if (!empty($destinations)): ?> 
        <?php foreach ($destinations as $destination): ?>
            <div class="destination">
                <div class="destination-image">
                    <img src="<?= URLROOT . '/' . htmlspecialchars($destination->image_path) ?>" 
                         alt="<?= htmlspecialchars($destination->destination_name) ?>">
                </div>
                <div class="destination-info">
                    <span class="destination-name"><?= htmlspecialchars($destination->destination_name) ?></span><br>
                    <span class="destination-region"><?= htmlspecialchars($destination->province) ?></span>
                    <p class="destination-description"><?= htmlspecialchars($destination->description) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No destinations available. Please check back later.</p>
    <?php endif; ?>
</div>

    <br>
    <button id="Add-Destination">Add New Destination</button>

    <br>
    <br>
   

    

        
        
        <!-- footer -->
        <?php require APPROOT . '/views/includes/footer.php'; ?>

=======
<?php require APPROOT . '/views/includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/travel-plans.css">


    <br>
    <h2>Travel Destinations</h2>
   
    <div class="travel-destinations-wrapper">
    <?php if (!empty($destinations)): ?> 
        <?php foreach ($destinations as $destination): ?>
            <div class="destination">
                <div class="destination-image">
                    <img src="<?= URLROOT . '/' . htmlspecialchars($destination->image_path) ?>" 
                         alt="<?= htmlspecialchars($destination->destination_name) ?>">
                </div>
                <div class="destination-info">
                    <span class="destination-name"><?= htmlspecialchars($destination->destination_name) ?></span><br>
                    <span class="destination-region"><?= htmlspecialchars($destination->province) ?></span>
                    <p class="destination-description"><?= htmlspecialchars($destination->description) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No destinations available. Please check back later.</p>
    <?php endif; ?>
</div>

    <br>
    <button id="Add-Destination">Add New Destination</button>

    <br>
    <br>
   

    

        
        
        <!-- footer -->
        <?php require APPROOT . '/views/includes/footer.php'; ?>

>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
    </body>