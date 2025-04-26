<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/editProfile.css">
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>


    <div class="profile-edit-container">
        <h1>Edit Profile</h1>
        <?php if (!empty($user)): ?>
            <?php if (isset($user['user_id'])): ?>
                <?php $users = $user; ?>
                <div class="profile-pic-container">
                    <!-- <img src="<?php echo $users['profile_picture']; ?>" alt="Profile Picture" class="profile-pic"> -->

                    <!-- Edit Icon -->
                    <label for="profile_picture" class="edit-icon">
                        <i class="fa fa-pencil-alt"></i>
                    </label>
                    <!-- Hidden File Input -->
                    <input type="file" name="profile_picture" id="profile_picture">
                </div>

                <form method="POST" action="<?php echo $basePath; ?>/admin/updateProfile" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                    <!-- General User Details -->
                    <div>
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                            value="<?php echo htmlspecialchars($users['first_name']); ?>" required>
                    </div>
                    <div>
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                            value="<?php echo htmlspecialchars($users['last_name']); ?>" required>
                    </div>
                    <div>
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($users['email']); ?>"
                            required>
                    </div>
                    <div>
                        <label for="phone_number">Contact Number</label>
                        <input type="text" id="phone_number" name="phone_number"
                            value="<?php echo htmlspecialchars($users['phone_number']); ?>">
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address"
                        value="<?php echo htmlspecialchars($users['address_line1'] ?? ''); ?>">

                    </div>
                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city_id" name="city_id" value="<?php echo htmlspecialchars($user['city_id']); ?>">
                    </div>
                    <div>
                        <label for="country">Country</label>
                        <input type="text" id="country_name" name="country_name"
                            value="<?php echo htmlspecialchars($users['country_name']??''); ?>">
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="">
                    </div>

                    <!-- Specialization Details -->

                    <div class="last-container">
                        <button type="submit" class="save-button">Update Profile</button>
                        <button type="submit" name="delete_user" class="delete-button">Deactivate User</button>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Modal Popup for Error Message -->
        <!-- <?php if (isset($city_error)): ?>
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
    </div> -->
</body>

</html>