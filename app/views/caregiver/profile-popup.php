<link rel="stylesheet" href="/Medceylon/public/assets/css/caregiver.css?v=4">


<div class="popup-content">
    <img src="<?= htmlspecialchars($caregiver['profile_picture'] ?? '/Medceylon/public/assets/img/default-profile.png') ?>" style="width:80px; height:80px; border-radius:50%; margin-bottom:10px;">
    <h3><?= htmlspecialchars($caregiver['first_name'] . ' ' . $caregiver['last_name']) ?></h3>
    <p>Age: <?= htmlspecialchars($caregiver['age']) ?></p>
    <p>Experience: <?= htmlspecialchars($caregiver['experience_years']) ?> years</p>
    <p>⭐ Average Rating: <?= number_format($caregiver['average_rating'] ?? 0, 1) ?>/5</p>

    <h4 style="margin-top:20px;">Patient Reviews:</h4>
    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <p>⭐ <?= $review['rating'] ?>/5 - <?= htmlspecialchars($review['review']) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
