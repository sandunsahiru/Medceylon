<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Dashboard</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/caregiver.css">
</head>
<body>
    <h2>Your Patient Messages</h2>

    <?php if ($messages->num_rows > 0): ?>
        <div class="caregiver-grid">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <div class="caregiver-card">
                    <div class="caregiver-img"></div>
                    <div class="caregiver-details">
                        <h3><?= $msg['first_name'] . ' ' . $msg['last_name'] ?></h3>
                        <a href="<?= $basePath ?>/caregiver/chat/<?= $msg['sender_id'] ?>">Open Chat</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No messages yet.</p>
    <?php endif; ?>
</body>
</html>
