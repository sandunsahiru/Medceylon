<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Rate Caregiver</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/caregiver.css">
</head>
<body>
    <h2>Rate <?= $caregiver['first_name'] ?> <?= $caregiver['last_name'] ?></h2>
    <form method="POST" action="<?= $basePath ?>/caregiver/save-rating/<?= $caregiver['user_id'] ?>">
        <label for="rating">Rating (1–5):</label>
        <input type="number" name="rating" min="1" max="5" required>

        <label for="review">Your Review:</label>
        <textarea name="review" required></textarea>

        <button type="submit" class="btn">Submit</button>
    </form>
</body>
</html>
