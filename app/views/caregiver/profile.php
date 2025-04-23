<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Caregiver Profile</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/caregiver.css">
</head>
<body>
    <h2><?= $caregiver['first_name'] . ' ' . $caregiver['last_name'] ?></h2>
    <p>Experience: <?= $caregiver['experience_years'] ?> years</p>

    <form action="<?= $basePath ?>/caregiver/send-message/<?= $id ?>" method="POST">
        <textarea name="message" required placeholder="Type your message here..."></textarea>
        <div class="button-group">
            <button type="submit" class="btn">Send</button>
            <a href="<?= $basePath ?>/caregiver/chat/<?= $id ?>" class="btn secondary">← View Full Chat</a>
        </div>
    </form>
</body>
</html>
