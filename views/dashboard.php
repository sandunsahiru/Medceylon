<!-- views/dashboard.php -->
<?php include 'templates/header.php'; ?>
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
                    <p><?= $summaryData['new_appointments']; ?></p>
                </div>
            </div>

            <div class="widget">
                <div class="widget-icon">
                    <img src="assets/images/completed_appointments_icon.png" alt="Completed Appointments Icon" class="widget-img">
                </div>
                <div class="widget-info">
                    <h3>Completed Appointments</h3>
                    <p><?= $summaryData['completed_appointments']; ?></p>
                </div>
            </div>

            <div class="widget">
                <div class="widget-icon">
                    <img src="assets/images/patients_icon.png" alt="Patients Icon" class="widget-img">
                </div>
                <div class="widget-info">
                    <h3>Patients</h3>
                    <p><?= $summaryData['patients']; ?></p>
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
                        <th>Visit Time</th>
                        <th>Country</th>
                        <th>Conditions</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td>
                                <img src="assets/images/<?= $appointment['avatar']; ?>" alt="<?= $appointment['name']; ?>" class="patient-avatar">
                                <?= $appointment['name']; ?>
                            </td>
                            <td><?= $appointment['email']; ?></td>
                            <td><?= $appointment['date']; ?></td>
                            <td><?= $appointment['visit_time']; ?></td>
                            <td><?= $appointment['country']; ?></td>
                            <td><?= $appointment['condition']; ?></td>
                            <td><button class="view-btn">View</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>