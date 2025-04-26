<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>
<link rel="stylesheet" href="/Medceylon/public/assets/css/caregiver.css?v=5">
<link rel="stylesheet" href="/Medceylon/public/assets/css/header.css?v=1">
<link rel="stylesheet" href="/Medceylon/public/assets/css/footer.css?v=1">

<div class="page-wrapper">
    <div class="container">
        <h2 class="page-title">Find a Caregiver</h2>

        <!-- Filter + Sort Form -->
        <form method="GET" action="/Medceylon/caregivers" class="filter-form">
            <select name="filter">
                <option value="">-- Filter --</option>
                <option value="experience" <?= ($_GET['filter'] ?? '') == 'experience' ? 'selected' : '' ?>>5+ Years Experience</option>
                <option value="young" <?= ($_GET['filter'] ?? '') == 'young' ? 'selected' : '' ?>>Age below 30</option>
            </select>

            <select name="sort">
                <option value="">-- Sort By --</option>
                <option value="experience" <?= ($_GET['sort'] ?? '') == 'experience' ? 'selected' : '' ?>>Most Experienced</option>
                <option value="young" <?= ($_GET['sort'] ?? '') == 'young' ? 'selected' : '' ?>>Youngest</option>
                <option value="rating" <?= ($_GET['sort'] ?? '') == 'rating' ? 'selected' : '' ?>>Highest Rated</option>
            </select>

            <button type="submit" class="btn">Apply</button>
        </form>

        <!-- Caregiver Cards -->
        <?php if (empty($caregivers)): ?>
            <p class="empty">No caregivers available at the moment.</p>
        <?php else: ?>
            <div class="caregiver-cards-grid">
                <?php foreach ($caregivers as $caregiver): ?>
                    <div class="caregiver-card" onclick="openProfileModal(<?= $caregiver['user_id'] ?>)">
                        <img src="<?= htmlspecialchars($caregiver['profile_picture'] ?? '/Medceylon/public/assets/img/default-profile.png') ?>" alt="Profile" class="caregiver-image">

                        <h3><?= htmlspecialchars($caregiver['first_name'] . ' ' . $caregiver['last_name']) ?></h3>
                        <p>Age: <?= htmlspecialchars($caregiver['age']) ?></p>
                        <p>Experience: <?= htmlspecialchars($caregiver['experience_years']) ?> years</p>
                        <p>Average Rating: ⭐ <?= number_format($caregiver['average_rating'] ?? 0, 1) ?>/5</p>

                        <?php if ($caregiver['already_requested']): ?>
                            <button class="btn requested-btn" disabled>✅ Request Sent</button>
                        <?php else: ?>
                            <form method="POST" action="/Medceylon/caregivers/request/<?= $caregiver['user_id'] ?>" onclick="event.stopPropagation();">
                                <button type="submit" class="btn request-btn">👉🏻 Request Caregiver</button>
                            </form>
                        <?php endif; ?>

                    </div> <!-- caregiver-card -->
                <?php endforeach; ?> <!-- foreach end -->
            </div> <!-- caregiver-cards-grid -->
        <?php endif; ?> <!-- if caregivers end -->

    </div> <!-- container -->
</div> <!-- page-wrapper -->

<!-- Profile Popup Modal -->
<div id="profileModal" class="profile-modal">
    <div class="profile-modal-content">
        <span class="close-btn" onclick="closeProfileModal()">&times;</span>
        <div id="profileDetails"></div>
    </div>
</div>

<style>
/* Popup Modal */
.profile-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.profile-modal-content {
    background-color: #fff;
    margin: auto;
    padding: 30px;
    border-radius: 14px;
    width: 400px;
    box-shadow: 0 4px 12px rgba(41, 157, 151, 0.2);
    text-align: center;
    position: relative;
}

.close-btn {
    position: absolute;
    right: 14px;
    top: 10px;
    font-size: 24px;
    font-weight: bold;
    color: #299D97;
    cursor: pointer;
}
</style>

<script>
function openProfileModal(caregiverId) {
    fetch(`/Medceylon/caregivers/profile/${caregiverId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('profileDetails').innerHTML = data;
            document.getElementById('profileModal').style.display = "block";
        })
        .catch(error => {
            console.error('Error loading profile:', error);
        });
}

function closeProfileModal() {
    document.getElementById('profileModal').style.display = "none";
}
</script>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
