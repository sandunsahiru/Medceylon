<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <link rel="stylesheet" href="/Medceylon/public/assets/css/caregiver.css">
</head>
<body>
    <h2>Chat History</h2>

    <?php while ($msg = $messages->fetch_assoc()): ?>
        <p><strong><?= $msg['sender_id'] == $_SESSION['user_id'] ? 'You' : 'Them' ?>:</strong>
        <?= htmlspecialchars($msg['message']) ?> 
        <small>(<?= $msg['sent_at'] ?>)</small></p>
    <?php endwhile; ?>

    <a href="<?= $basePath ?>/caregiver/profile/<?= $id ?>">← Back to Profile</a>
</body>
</html>
