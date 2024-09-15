<!-- views/templates/topbar.php -->

<div class="topbar">
    <div class="topbar-right">
        <span class="notification-icon">🔔</span>
        <img src="assets/images/<?= htmlspecialchars($user['profile_picture'] ?: 'default_avatar.png'); ?>" alt="User Avatar" class="user-avatar">
    </div>
</div>

