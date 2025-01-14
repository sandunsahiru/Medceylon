<!-- app/views/patient/profile.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
        <div class="logo">
    <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
        <h1>Medceylon</h1>
    </a>
</div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/profile" class="nav-item active">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>
            
            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Profile Settings</h1>
            </header>

            <?php if ($this->session->hasFlash('success')): ?>
                <div class="success-message"><?php echo $this->session->getFlash('success'); ?></div>
            <?php endif; ?>

            <section class="profile-section">
                <form id="profileForm" action="<?php echo $basePath; ?>/patient/update-profile" method="POST" class="profile-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?php echo isset($user['first_name']) ? htmlspecialchars($user['first_name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?php echo isset($user['last_name']) ? htmlspecialchars($user['last_name']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone_number" value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?php echo isset($user['date_of_birth']) ? $user['date_of_birth'] : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <?php
                                $genders = ['Male', 'Female', 'Other'];
                                foreach($genders as $gender): ?>
                                    <option value="<?php echo $gender; ?>" <?php echo (isset($user['gender']) && $user['gender'] === $gender) ? 'selected' : ''; ?>>
                                        <?php echo $gender; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Address Line 1</label>
                            <input type="text" name="address_line1" value="<?php echo isset($user['address_line1']) ? htmlspecialchars($user['address_line1']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" name="address_line2" value="<?php echo isset($user['address_line2']) ? htmlspecialchars($user['address_line2']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <select name="city_id">
                                <option value="">Select City</option>
                                <?php if ($cities): while($city = $cities->fetch_assoc()): ?>
                                    <option value="<?php echo $city['city_id']; ?>" 
                                            <?php echo (isset($user['city_id']) && $user['city_id'] == $city['city_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($city['city_name']); ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nationality</label>
                            <select name="nationality">
                                <option value="">Select Country</option>
                                <?php if ($countries): while($country = $countries->fetch_assoc()): ?>
                                    <option value="<?php echo $country['country_code']; ?>" 
                                            <?php echo (isset($user['nationality']) && $user['nationality'] === $country['country_code']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($country['country_name']); ?>
                                    </option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Passport Number</label>
                            <input type="text" name="passport_number" value="<?php echo isset($user['passport_number']) ? htmlspecialchars($user['passport_number']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                    </div>
                </form>

                <div class="danger-zone">
                    <h2>Delete Account</h2>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button id="deleteAccount" class="delete-btn">Delete Account</button>
                </div>

                <div id="deleteModal" class="modal">
                    <div class="modal-content">
                        <h2>Are you sure?</h2>
                        <p>This action cannot be undone. All your data will be permanently deleted.</p>
                        <div class="modal-actions">
                            <form action="<?php echo $basePath; ?>/patient/delete-profile" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
                                <button type="submit" class="confirm-delete-btn">Yes, Delete My Account</button>
                                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const deleteBtn = document.getElementById('deleteAccount');
        const modal = document.getElementById('deleteModal');

        deleteBtn.onclick = function() {
            modal.style.display = "flex";
        }

        function closeModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>