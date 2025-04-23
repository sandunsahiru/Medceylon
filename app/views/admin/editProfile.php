<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/editProfile.css">
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>

    <body>
    <div class="profile-edit-container">
        <h1>Edit Profile</h1>
        <div class="profile-pic-container">
            <!--img src="<//?php echo $['profile_picture']; ?>" alt="Profile Picture" class="profile-pic"-->
            <!-- Edit Icon -->
            <label for="profile_picture" class="edit-icon">
                <i class="fa fa-pencil-alt"></i>
            </label>
            <!-- Hidden File Input -->
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <form method="POST" action="edit_profile.php">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">    

            <!-- General User Details -->
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name"
                    value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name"
                    value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    required>
            </div>
            <div>
                <label for="phone_number">Contact Number</label>
                <input type="text" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            <div>
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                    value="<?php echo htmlspecialchars($user['address_line1']); ?>">
            </div>
            <div>
                <label for="city">City</label>
                <input type="text" id="city_name" name="city_name"
                    value="<?php echo htmlspecialchars($user['city_name']); ?>">
                <input type="hidden" id="city" name="city" value="<?php echo htmlspecialchars($user['city_id']); ?>">
            </div>
            <div>
                <label for="country">Country</label>
                <input type="text" id="country_name" name="country_name"
                    value="<?php echo htmlspecialchars($user['country_name']); ?>">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="">
            </div>

            <!-- Specialization Details -->
            <?php if (in_array($user['role_name'], ['General Doctor', 'Specialist Doctor'])): ?>

                <!-- Doctor Details -->
                <div class="doctor-details-box">

                    <h2>Doctor Details</h2>
                    <?php if (!empty($specializations)): ?>
                        <?php foreach ($specializations as $specialization): ?>
                            <div>
                                <label
                                    for="specialization_<?php echo $specialization['specialization_id']; ?>">Specialization</label>
                                <input type="text" id="specialization_<?php echo $specialization['specialization_id']; ?>"
                                    name="specializations[]"
                                    value="<?php echo htmlspecialchars($specialization['specialization_name']); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No specialization details found.</p>
                    <?php endif; ?>
                    <div>
                        <label for="license_number">License Number</label>
                        <input type="text" id="license_number" name="license_number"
                            value="<?php echo htmlspecialchars($user['license_number']); ?>">
                    </div>
                    <div>
                        <label for="years_experience">Years of Experience</label>
                        <input type="text" id="years_experience" name="years_experience"
                            value="<?php echo htmlspecialchars($user['years_of_experience']); ?>">
                    </div>

                    <div>
                        <label for="hospital_affiliation">Hospital Affiliation</label>
                        <input type="text" id="hospital_affiliation" name="hospital_affiliation"
                            value="<?php echo htmlspecialchars($user['hospital_name']); ?>" readonly>
                    </div>
                </div>
            <?php endif; ?>

            <div class="last-container">
                <button type="submit" class="save-button">Update Profile</button>
                <button type="submit" name="delete_user" class="delete-button">Delete User</button>
            </div>
        </form>
        <!-- Modal Popup for Error Message -->
        <?php if (isset($city_error)): ?>
            <div id="errorModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <p><?php echo $city_error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <script>
            // Show the modal if there is a city error
            <?php if (isset($city_error)): ?>
                document.getElementById('errorModal').style.display = "block";
            <?php endif; ?>

            // Function to close the modal
            function closeModal() {
                document.getElementById('errorModal').style.display = "none";
            }
        </script>
    </div>
</body>

</html>