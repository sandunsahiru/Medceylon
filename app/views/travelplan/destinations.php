<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travel-plan.css">

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

    <h2>Make Customized Travel Plan</h2>
    
    <button onclick="location.href='<?php echo $basePath; ?>/travelplan/travel-preferences';">Do it for Me</button>

    <div class="filter-bar">
        <form method="get" action="http://localhost/Medceylon/travelplan/destinations">
        <div class="filter-item">
            <label for="province">Select Province:</label>
            <select id="province" name="province_id">
                <option value="">Select Province</option>
                <?php foreach ($provinces as $province): ?>
                    <option value="<?= $province['province_id'] ?>"><?= htmlspecialchars($province['province_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

            <div class="filter-item">
                <label for="wheelchair">Wheelchair Accessiblity:</label>
                <select name="wheelchair" id="wheelchair">
                    <option value="" <?= empty($_GET['wheelchair']) ? 'selected' : '' ?>>Any</option>
                    <option value="Yes" <?= isset($_GET['wheelchair']) && $_GET['wheelchair'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                    <option value="No" <?= isset($_GET['wheelchair']) && $_GET['wheelchair'] === 'No' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="filter-item">
                <label for="destinationType">Destinastion Type</label>
                <select id="destinationType" name="type_id">
                    <option value="">Select Type</option>
                    <?php foreach ($destinationTypes as $destinationType): ?>
                        <option value="<?= $destinationType['type_id'] ?>"><?= htmlspecialchars($destinationType['type_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-item">
                <label for="cost_category">Cost Category:</label>
                <select name="cost_category" id="cost_category">
                    <option value="">Any</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div class="filter-item">
                <button type="submit">Filter</button>
            </div>
        </form>
    </div>
   
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
                    <span class="destination-region"><?= htmlspecialchars($destination['province_name'] ?? 'Unknown') ?></span>
                    <p class="destination-description"><?= htmlspecialchars($destination['description'] ?? 'No description available.') ?></p>
                    <button class="add-destination-button" 
                        data-id="<?= htmlspecialchars($destination['destination_id'] ?? '') ?>" 
                        data-name="<?= htmlspecialchars($destination['destination_name'] ?? '') ?>" 
                        data-image="<?= htmlspecialchars($destination['image_path'] ?? '') ?>" 
                        data-opening="<?= htmlspecialchars($destination['opening_time'] ?? '') ?>"
                        data-closing="<?= htmlspecialchars($destination['closing_time'] ?? '') ?>" 
                        data-entry="<?= htmlspecialchars($destination['entry_fee'] ?? '') ?>"
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
    
    <?php include('add-destination.php'); ?>  

    <script src="<?php echo $basePath; ?>/public/assets/js/travel.js"></script>
      <!-- footer -->
    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>