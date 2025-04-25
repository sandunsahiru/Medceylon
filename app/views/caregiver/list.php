<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}
$db = require ROOT_PATH . '/app/config/database.php';

$basePath = '/Medceylon';

use App\Models\CaregiverRatingModel;
$ratingModel = new CaregiverRatingModel($db);
?>

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

                    <?php
                        $avgRating = $ratingModel->getAverageRating($c['user_id']);
                        echo '<p><strong>Rating:</strong> ' . ($avgRating ? $avgRating . ' ⭐' : 'Not rated yet') . '</p>';
                    ?>

                    <div class="caregiver-actions">
                        <a href="<?= $basePath ?>/caregiver/profile/<?= $c['user_id'] ?>" class="btn">Contact</a>

                        <?php
                            $stmt = $db->prepare("SELECT status FROM caregiver_requests WHERE patient_id = ? AND caregiver_id = ? ORDER BY request_id DESC LIMIT 1");
                            $stmt->bind_param("ii", $_SESSION['user_id'], $c['user_id']);
                            $stmt->execute();
                            $checkResult = $stmt->get_result();
                            $requestStatus = $checkResult->fetch_assoc();
                        ?>

                        <?php if ($requestStatus && $requestStatus['status'] === 'Accepted'): ?>
                            <a href="<?= $basePath ?>/caregiver/rate/<?= $c['user_id'] ?>" class="btn">Rate Caregiver</a>
                        <?php else: ?>
                            <p style="font-size: 13px; color: #888;">You can rate this caregiver after your request is accepted.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
