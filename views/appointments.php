<!-- views/appointments.php -->
<?php include 'templates/header.php'; ?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/sidebar.php'; ?>

    <div class="content">
        <!-- Page Heading -->
        <h1>Appointments</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="?page=appointments" method="GET">
                <input type="hidden" name="page" value="appointments">
                <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="appointments-section">
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
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7">No appointments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?php echo $appointment['avatar']; ?>" alt="<?php echo $appointment['name']; ?>" class="patient-avatar">
                                    <?php echo $appointment['name']; ?>
                                </td>
                                <td><?php echo $appointment['email']; ?></td>
                                <td><?php echo $appointment['date']; ?></td>
                                <td><?php echo $appointment['visit_time']; ?></td>
                                <td><?php echo $appointment['country']; ?></td>
                                <td><?php echo $appointment['condition']; ?></td>
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
