<?php $basePath = '/Medceylon'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - MedCeylon</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/reset_password.css">
</head>
<body>
    <div class="form-container">
        <h2>Reset Your Password</h2>

        <?php if (isset($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $basePath ?>/reset-password">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="field">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="field">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>
