<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Doctor Profile</h1>
                <div class="header-right">
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Profile Section -->
            <section class="profile-section">
                <div class="profile-container">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="profile-title">
                            <h2>Dr. <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h2>
                            <p><?php echo htmlspecialchars($profile['specializations'] ?? 'General Practitioner'); ?></p>
                        </div>
                    </div>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            Profile updated successfully!
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $basePath; ?>/doctor/profile" method="POST" class="profile-form">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($profile['first_name']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" value="<?php echo htmlspecialchars($profile['last_name']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" value="<?php echo htmlspecialchars($profile['phone_number']); ?>" disabled>
                            </div>

                            <div class="form-group">
                                <label for="hospital_id">Hospital *</label>
                                <select name="hospital_id" required>
                                    <?php foreach ($hospitals as $hospital): ?>
                                        <option value="<?php echo $hospital['hospital_id']; ?>" 
                                            <?php echo ($hospital['hospital_id'] == $profile['hospital_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($hospital['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="experience">Years of Experience *</label>
                                <input type="number" name="experience" value="<?php echo $profile['years_of_experience']; ?>" required min="0">
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="qualifications">Qualifications *</label>
                            <textarea name="qualifications" rows="3" required><?php echo htmlspecialchars($profile['qualifications'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="description">Profile Description</label>
                            <textarea name="description" rows="5"><?php echo htmlspecialchars($profile['profile_description'] ?? ''); ?></textarea>
                        </div>

                        

                        <div class="form-actions">
                            <button type="submit" class="submit-btn">
                                <i class="ri-save-line"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <style>
        .profile-section {
            padding: 20px;
        }

        .profile-container {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }

        .profile-avatar i {
            font-size: 40px;
            color: #666;
        }

        .profile-title h2 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .profile-title p {
            margin: 0;
            color: #666;
            font-size: 0.9em;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .specializations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            padding: 5px;
            cursor: pointer;
        }

        .checkbox-container input {
            margin-right: 8px;
            width: auto;
        }

        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .submit-btn {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .submit-btn:hover {
            background: #45a049;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>

    <script>
        // Form validation
        document.querySelector('.profile-form').addEventListener('submit', function(e) {
            const experience = document.querySelector('input[name="experience"]').value;
            const qualifications = document.querySelector('textarea[name="qualifications"]').value;
            const hospital = document.querySelector('select[name="hospital_id"]').value;

            if (!experience || !qualifications || !hospital) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>

</body>
</html>