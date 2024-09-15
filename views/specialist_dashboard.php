<!-- views/specialist_dashboard.php -->
<?php 
include 'templates/header.php'; 
$pageTitle = 'Specialist Dashboard';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/sidebar.php'; ?>

    <div class="content">
        <!-- Widgets Section -->
        <div class="widgets">
            <div class="widget">
                <div class="widget-icon">
                    <img src="assets/images/new_appointments_icon.png" alt="New Appointments Icon" class="widget-img">
                </div>
                <div class="widget-info">
                    <h3>New Appointments</h3>
                    <p><?= htmlspecialchars($summaryData['new_appointments']); ?></p>
                </div>
            </div>

            <div class="widget">
                <div class="widget-icon">
                    <img src="assets/images/completed_appointments_icon.png" alt="Completed Appointments Icon" class="widget-img">
                </div>
                <div class="widget-info">
                    <h3>Completed Appointments</h3>
                    <p><?= htmlspecialchars($summaryData['completed_appointments']); ?></p>
                </div>
            </div>

            <div class="widget">
                <div class="widget-icon">
                    <img src="assets/images/patients_icon.png" alt="Patients Icon" class="widget-img">
                </div>
                <div class="widget-info">
                    <h3>Patients</h3>
                    <p><?= htmlspecialchars($summaryData['patients']); ?></p>
                </div>
            </div>
        </div>

        <!-- New Appointments Section -->
        <div class="appointments-section">
            <h2>New Appointments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Country</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7">No appointments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?= htmlspecialchars($appointment['profile_picture'] ?: 'default_avatar.png'); ?>" alt="<?= htmlspecialchars($appointment['first_name']); ?>" class="patient-avatar">
                                    <?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                </td>
                                <td><?= htmlspecialchars($appointment['email']); ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                                <td><?= htmlspecialchars(date('h:i A', strtotime($appointment['appointment_time']))); ?></td>
                                <td><?= htmlspecialchars($appointment['country_name'] ?: 'N/A'); ?></td>
                                <td><?= htmlspecialchars($appointment['reason_for_visit'] ?: 'N/A'); ?></td>
                                <td><button class="view-btn">View</button></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
