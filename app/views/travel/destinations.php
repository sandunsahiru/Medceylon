<?php require APPROOT . '/views/includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/destinations.css">


    <br>
    <h2>Make Customized Travel Plan</h2>
    <!-- Trigger Button -->
    <button id="openModal">Do It for Me</button><br><br>
   
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
                    <button class="add-destination-button" 
                        data-id="<?= htmlspecialchars($destination->destination_id) ?>" 
                        data-name="<?= htmlspecialchars($destination->destination_name) ?>" 
                        data-image="<?= htmlspecialchars($destination->image_path) ?>" 
                        name="add-button">Add</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No destinations available. Please check back later.</p>
    <?php endif; ?>
</div>

    <br>
    <button id="ViewPlan" onclick="window.location.href='<?php echo APPROOT; ?> . /controllers/destination/destinations';">View All Plans</button>

    <br>
    <br>
    
    <?php include('add-destination.php'); ?>
    <?php include('travel-preferences.php'); ?>
   

    

        
        
        <!-- footer -->
        <?php require APPROOT . '/views/includes/footer.php'; ?>

        <script src="<?php echo URLROOT;?>/js/add-destination.js"></script>
        <script src="<?php echo URLROOT;?>/js/travel.js"></script>
    </body>