<!-- views/patient_dashboard.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'Dashboard';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/patient_sidebar.php'; ?>

    <div class="content">
        <!-- Upcoming Appointments Section -->
        <div class="appointments-section">
            <h2>Upcoming Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="6">No upcoming appointments.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?>
                                </td>
                                <td><?= htmlspecialchars($appointment['specialization'] ?: 'General'); ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($appointment['appointment_time']))); ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_status']); ?></td>
                                <td>
                                    <a href="?page=appointment_detail&id=<?= $appointment['appointment_id']; ?>" class="view-btn">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Notifications Section -->
        <div class="notifications-section">
            <h2>Recent Notifications</h2>
            <ul class="notifications-list">
                <?php if (empty($notifications)): ?>
                    <li>No notifications.</li>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li class="<?= $notification['is_read'] ? 'read' : 'unread'; ?>">
                            <span class="notification-text"><?= htmlspecialchars($notification['notification_text']); ?></span>
                            <span class="notification-date"><?= htmlspecialchars(date('d M Y, h:i A', strtotime($notification['date_created']))); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
