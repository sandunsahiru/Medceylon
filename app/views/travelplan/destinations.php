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

    <h2>Local Site Seeing</h2>

    <div class="travel-plan-section">
    <h3>Build Your Travel Plan</h3>
    
    <div class="selected-destinations">
        <h4>Selected Destinations:</h4>
        <ul id="selectedDestinations"></ul>
    </div>
    
    <button id="calculatePlanBtn" class="btn">Calculate Travel Plan</button><br><br>
    <button id="savePlanBtn" class="btn" style="display:none;">Save Travel Plan</button>
    
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $this->session->getCSRFToken() ?>">
    
<div id="travelPlanContainer"></div>
</div>

    <div class="filter-bar">
        <form method="get" action="<?php echo $this->url('travelplan/destinations'); ?>">
            <div class="filter-item">
                <label for="province">Select Province:</label>
                <select id="province" name="province_id">
                    <option value="">Select Province</option>
                    <?php foreach ($provinces as $province): ?>
                        <option value="<?= $province['province_id'] ?>" 
                            <?= (!empty($_GET['province_id']) && $_GET['province_id'] == $province['province_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($province['province_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-item">
                <label for="wheelchair">Wheelchair Accessibility:</label>
                <select name="wheelchair" id="wheelchair">
                    <option value="" <?= empty($_GET['wheelchair']) ? 'selected' : '' ?>>Any</option>
                    <option value="Yes" <?= isset($_GET['wheelchair']) && $_GET['wheelchair'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                    <option value="No" <?= isset($_GET['wheelchair']) && $_GET['wheelchair'] === 'No' ? 'selected' : '' ?>>No</option>
                </select>
            </div>

            <div class="filter-item">
                <label for="destinationType">Destination Type</label>
                <select id="destinationType" name="type_id">
                    <option value="">Select Type</option>
                    <?php foreach ($destinationTypes as $destinationType): ?>
                        <option value="<?= $destinationType['type_id'] ?>"
                            <?= (!empty($_GET['type_id']) && $_GET['type_id'] == $destinationType['type_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($destinationType['type_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-item">
                <label for="cost_category">Cost Category:</label>
                <select name="cost_category" id="cost_category">
                    <option value="" <?= empty($_GET['cost_category']) ? 'selected' : '' ?>>Any</option>
                    <option value="Low" <?= isset($_GET['cost_category']) && $_GET['cost_category'] === 'Low' ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= isset($_GET['cost_category']) && $_GET['cost_category'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= isset($_GET['cost_category']) && $_GET['cost_category'] === 'High' ? 'selected' : '' ?>>High</option>
                </select>
            </div>

            <div class="filter-item">
                <button type="submit">Filter</button>
                <?php if (!empty($_GET)): ?>
                    <a href="<?php echo $this->url('travelplan/destinations'); ?>" class="clear-filters">Clear Filters</a>
                <?php endif; ?>
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
    
    <?php include('edit-destination.php'); ?>  

    <script>
    const travelPlanUrl = '<?= $this->url('travelplan/destinations') ?>';
    </script>
    <script src="<?php echo $basePath; ?>/public/assets/js/travel.js"></script>
    <script src="<?php echo $basePath; ?>/public/assets/js/travel-plan.js"></script>
      <!-- footer -->
    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>