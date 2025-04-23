<?php $basePath = $basePath ?? '/Medceylon'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="<?= $basePath ?>/public/assets/css/auth.css">
</head>
<body>
    <div class="form-container">
        <form action="<?= $basePath ?>/forgot-password" method="POST">
            <h2>Forgot Password</h2>

            <?php if (isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
