<!-- views/patients.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'Patients';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/sidebar.php'; ?>

    <div class="content">
        <!-- Page Heading -->
        <h1>Patients</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="?page=patients" method="GET">
                <input type="hidden" name="page" value="patients">
                <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Patients Table -->
        <div class="patients-section">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Doctor</th>
                        <th>Condition</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr>
                            <td colspan="7">No patients found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?php echo $patient['avatar']; ?>" alt="<?php echo $patient['name']; ?>" class="patient-avatar">
                                    <?php echo $patient['name']; ?>
                                </td>
                                <td><?php echo $patient['email']; ?></td>
                                <td><?php echo $patient['country']; ?></td>
                                <td><?php echo $patient['status']; ?></td>
                                <td><?php echo $patient['doctor']; ?></td>
                                <td><?php echo $patient['condition']; ?></td>
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
