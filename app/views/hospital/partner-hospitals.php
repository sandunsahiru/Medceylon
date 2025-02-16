<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Partner Hospitals</title>
    <link rel="stylesheet" href="./assets/css/partner-hospitals.css">
</head>
<body>
    <div class="hospitals-container">
        <h1>Our Partner Hospitals</h1>
        <p>We proudly partner with Sri Lanka's leading private hospitals to bring you the best in healthcare and medical tourism.</p>
        <div class="hospitals-grid">
            <?php foreach ($partner_hospitals as $hospital): ?>
                <div class="hospital-card">
                    <img src="<?php echo $hospital['image']; ?>" alt="<?php echo $hospital['name']; ?>">
                    <h2><?php echo $hospital['name']; ?></h2>
                    <p><?php echo $hospital['description']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
</body>
</html>