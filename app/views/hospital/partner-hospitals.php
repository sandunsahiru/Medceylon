<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/hospital.css">

    <div class="hospitals-container">
        <h1>Our Partner Hospitals</h1>
        <p>We proudly partner with Sri Lanka's leading hospitals to bring you the best in healthcare and medical tourism.</p>
        <div class="hospitals-grid">
        <?php if (!empty($hospitals)): ?> 
            <?php foreach ($hospitals as $hospital): ?>
                <div class="hospital-card">
                    <div class="hospital-image">
                        <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($hospital['image_path'] ?? 'default.jpg') ?>" 
                        alt="<?= htmlspecialchars($hospital['destination_name'] ?? 'Unknown') ?>">
                    </div>
                    <div class = "hospital-info">
                        <span class="hospital-name"><?= htmlspecialchars($hospital['name'] ?? 'Unknown') ?></span>
                        <p class="hospital-description"><?= htmlspecialchars($hospital['description'] ?? 'No description available.') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No Hospitals available. Please check back later.</p>
        <?php endif; ?>
        </div>
    </div>

    <br>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>