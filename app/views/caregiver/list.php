<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Caregivers</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/caregiver.css">
    <style>
        .caregiver-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <h2>Available Caregivers</h2>

    <form method="GET" action="<?= $basePath ?>/caregivers" class="filter-form">
        <input type="text" name="search" placeholder="Search by name" value="<?= $_GET['search'] ?? '' ?>">

        <label>Min Experience:
            <input type="number" name="experience" value="<?= $_GET['experience'] ?? '' ?>">
        </label>

        <label>Max Age:
            <input type="number" name="age" value="<?= $_GET['age'] ?? '' ?>">
        </label>

        <label>Sort by:
            <select name="sort">
                <option value="">--</option>
                <option value="experience" <?= ($_GET['sort'] ?? '') === 'experience' ? 'selected' : '' ?>>Most Experienced</option>
                <option value="youngest" <?= ($_GET['sort'] ?? '') === 'youngest' ? 'selected' : '' ?>>Youngest</option>
            </select>
        </label>

        <button type="submit">Apply</button>
    </form>

    <div class="caregiver-grid">
        <?php while ($c = $result->fetch_assoc()): ?>
            <div class="caregiver-card">
                <div class="caregiver-img" style="background-image: url('<?= $c['profile_picture'] ? $basePath . '/public/uploads/' . $c['profile_picture'] : 'https://via.placeholder.com/100' ?>');"></div>
                <div class="caregiver-details">
                    <h3><?= $c['first_name'] . ' ' . $c['last_name'] ?></h3>
                    <p><strong>Experience:</strong> <?= $c['experience_years'] ?> yrs</p>
                    <p><strong>Age:</strong> <?= $c['age'] ?> y/o</p>
                    <div class="caregiver-actions">
                        <a href="<?= $basePath ?>/caregiver/profile/<?= $c['user_id'] ?>" class="btn">Contact</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
