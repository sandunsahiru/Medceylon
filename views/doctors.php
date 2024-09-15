<!-- views/doctors.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'Doctors';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/sidebar.php'; ?>

    <div class="content">
        <!-- Page Heading -->
        <h1>Doctors</h1>

        <!-- Search Bar -->
        <div class="search-bar">
            <form action="?page=doctors" method="GET">
                <input type="hidden" name="page" value="doctors">
                <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Doctors Table -->
        <div class="doctors-section">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Speciality</th>
                        <th>Contact No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doctors)): ?>
                        <tr>
                            <td colspan="5">No doctors found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/<?php echo $doctor['avatar']; ?>" alt="<?php echo $doctor['name']; ?>" class="doctor-avatar">
                                    <?php echo $doctor['name']; ?>
                                </td>
                                <td><?php echo $doctor['email']; ?></td>
                                <td><?php echo $doctor['speciality']; ?></td>
                                <td><?php echo $doctor['contact_no']; ?></td>
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
