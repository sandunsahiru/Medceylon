<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <h1>Specialist Profile</h1>
        <div class="header-right">
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <div class="profile-section">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <i class="ri-checkbox-circle-line"></i>
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); 
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <i class="ri-error-warning-line"></i>
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); 
                ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <form action="<?php echo $basePath; ?>/vpdoctor/profile" method="POST" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <!-- Basic Information -->
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

                    <!-- Professional Information -->
                    <div class="form-group">
                        <label for="hospital_id">Hospital <span class="required">*</span></label>
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
                        <label for="experience">Years of Experience <span class="required">*</span></label>
                        <input type="number" name="experience" value="<?php echo $profile['years_of_experience']; ?>" required min="0">
                    </div>
                </div>

                <!-- Qualifications -->
                <div class="form-group full-width">
                    <label for="qualifications">Qualifications <span class="required">*</span></label>
                    <textarea name="qualifications" rows="3" required><?php echo htmlspecialchars($profile['qualifications'] ?? ''); ?></textarea>
                </div>

                <!-- Profile Description -->
                <div class="form-group full-width">
                    <label for="description">Profile Description</label>
                    <textarea name="description" rows="5" placeholder="Enter your professional description here..."><?php echo htmlspecialchars($profile['profile_description'] ?? ''); ?></textarea>
                </div>

                <!-- Specializations -->
                <div class="form-group full-width">
                    <label>Specializations <span class="required">*</span></label>
                    <div class="specializations-grid">
                        <?php foreach ($specializations as $specialization): ?>
                            <label class="checkbox-container">
                                <input type="checkbox" 
                                       name="specializations[]" 
                                       value="<?php echo $specialization['specialization_id']; ?>"
                                       <?php echo in_array($specialization['specialization_id'], 
                                             array_column($doctor_specializations, 'specialization_id')) ? 'checked' : ''; ?>>
                                <span class="checkbox-label"><?php echo htmlspecialchars($specialization['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="ri-save-line"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
/* Profile Section Styles */
:root {
    --primary-color: #299D97;
    --primary-light: #E5F3F2;
    --text-dark: #2D3748;
    --text-light: #718096;
    --bg-light: #F7FAFC;
    --white: #FFFFFF;
    --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --border-radius: 16px;
}

.profile-section {
    padding: 2rem;
}

.profile-container {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

/* Alert Styles */
.alert {
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert i {
    font-size: 1.25rem;
}

.alert-success {
    background-color: #DEF7EC;
    color: #046C4E;
    border: 1px solid #31C48D;
}

.alert-danger {
    background-color: #FEE2E2;
    color: #DC2626;
    border: 1px solid #F87171;
}

/* Form Styles */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.95rem;
}

.required {
    color: #DC2626;
    margin-left: 0.25rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 0.95rem;
    color: var(--text-dark);
    transition: all 0.2s ease;
    background: var(--white);
}

.form-group input:disabled {
    background: var(--bg-light);
    color: var(--text-light);
    cursor: not-allowed;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(41, 157, 151, 0.1);
}

/* Specializations Grid */
.specializations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    padding: 1.5rem;
    background: var(--bg-light);
    border-radius: 8px;
    border: 1px solid #E2E8F0;
}

.checkbox-container {
    display: flex;
    align-items: center;
    padding: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.checkbox-container:hover {
    background: var(--white);
    border-radius: 6px;
}

.checkbox-container input[type="checkbox"] {
    margin-right: 0.75rem;
    width: 1.125rem;
    height: 1.125rem;
    accent-color: var(--primary-color);
}

.checkbox-label {
    color: var(--text-dark);
    font-size: 0.95rem;
}

/* Form Actions */
.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #E2E8F0;
    text-align: right;
}

.submit-btn {
    padding: 0.75rem 1.5rem;
    background: var(--primary-color);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.submit-btn:hover {
    background: #238783;
    transform: translateY(-1px);
}

.submit-btn i {
    font-size: 1.25rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .specializations-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .profile-container {
        padding: 1.5rem;
    }
    
    .profile-section {
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.profile-form');
    
    form.addEventListener('submit', function(e) {
        const experience = document.querySelector('input[name="experience"]');
        const qualifications = document.querySelector('textarea[name="qualifications"]');
        const hospital = document.querySelector('select[name="hospital_id"]');
        const specializations = Array.from(document.querySelectorAll('input[name="specializations[]"]:checked'));
        
        let hasError = false;
        let errorMessage = [];

        // Clear previous error styles
        document.querySelectorAll('.error-field').forEach(el => {
            el.classList.remove('error-field');
        });

        // Validate experience
        if (!experience.value || experience.value < 0) {
            experience.classList.add('error-field');
            errorMessage.push('Please enter valid years of experience');
            hasError = true;
        }

        // Validate qualifications
        if (!qualifications.value.trim()) {
            qualifications.classList.add('error-field');
            errorMessage.push('Please enter your qualifications');
            hasError = true;
        }

        // Validate hospital
        if (!hospital.value) {
            hospital.classList.add('error-field');
            errorMessage.push('Please select a hospital');
            hasError = true;
        }

        // Validate specializations
        if (specializations.length === 0) {
            document.querySelector('.specializations-grid').classList.add('error-field');
            errorMessage.push('Please select at least one specialization');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            alert(errorMessage.join('\n'));
        }
    });
});
</script>