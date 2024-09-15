<!-- views/patient_profile.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'My Profile';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/patient_sidebar.php'; ?>

    <div class="content">
        <h1>My Profile</h1>

        <!-- Display success or error message -->
        <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
            <div class="alert success">Profile updated successfully.</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <div class="alert error">There was an error updating your profile.</div>
        <?php endif; ?>

        <form action="index.php?page=profile&action=update" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($patientProfile['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($patientProfile['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($patientProfile['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($patientProfile['phone_number']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address_line1">Address Line 1</label>
                <input type="text" id="address_line1" name="address_line1" value="<?php echo htmlspecialchars($patientProfile['address_line1']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($patientProfile['address_line2']); ?>">
            </div>

            <div class="form-group">
                <label for="city_id">City</label>
                <!-- You might want to replace this with a dropdown of cities -->
                <input type="text" id="city_id" name="city_id" value="<?php echo htmlspecialchars($patientProfile['city_name']); ?>" required>
            </div>

            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
