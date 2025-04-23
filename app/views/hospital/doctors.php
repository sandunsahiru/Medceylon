<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Doctors</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search doctors...">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <section class="doctors-section">
        <div class="section-header">
            <h2>All Doctors</h2>
            <div class="section-actions">
                <select id="departmentFilter" class="filter-select">
                    <option value="">All Departments</option>
                    <?php if (isset($departments)): foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['department_id']; ?>">
                                <?php echo htmlspecialchars($dept['department_name']); ?>
                            </option>
                    <?php endforeach;
                    endif; ?>
                </select>
                <button class="add-btn" id="addDoctorBtn">
                    <i class="ri-add-line"></i>
                    Add Doctor
                </button>
            </div>
        </div>

        <div class="doctors-list">
            <?php while ($doctor = $doctors->fetch_assoc()): ?>
                <div class="doctor-card"
                    data-department="<?php echo $doctor['department_id']; ?>"
                    data-status="<?php echo $doctor['is_active'] ? 'active' : 'inactive'; ?>">
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                        <?php if (!empty($doctor['specialization'])): ?>
                            <p class="specialty">
                                <i class="ri-stethoscope-line"></i>
                                <?php echo htmlspecialchars($doctor['specialization']); ?>
                            </p>
                        <?php endif; ?>

                        <p class="department">
                            <i class="ri-hospital-line"></i>
                            <?php echo htmlspecialchars($doctor['department_name']); ?>
                        </p>
                        <p class="contact">
                            <i class="ri-mail-line"></i>
                            <?php echo htmlspecialchars($doctor['email']); ?>
                        </p>
                        <?php if (!empty($doctor['phone_number'])): ?>
                            <p class="phone">
                                <i class="ri-phone-line"></i>
                                <?php echo htmlspecialchars($doctor['phone_number']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($doctor['license_number'])): ?>
                            <p class="license">
                                <i class="ri-profile-line"></i>
                                License: <?php echo htmlspecialchars($doctor['license_number']); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="availability-status <?php echo $doctor['is_available'] ? 'available' : 'unavailable'; ?>">
                        <?php echo $doctor['is_available'] ? 'Available' : 'Unavailable'; ?>
                    </div>

                    <div class="doctor-actions">
                        <button class="action-btn view-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="action-btn edit-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="Edit Doctor">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="action-btn schedule-btn" data-id="<?php echo $doctor['doctor_id']; ?>"
                            title="Manage Schedule">
                            <i class="ri-calendar-2-line"></i>
                        </button>
                        <button class="action-btn toggle-status-btn"
                            data-id="<?php echo $doctor['doctor_id']; ?>"
                            data-active="<?php echo $doctor['is_active']; ?>"
                            title="Toggle Status">
                            <i class="ri-toggle-line"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>
</div>

<!-- Add/Edit Doctor Modal -->
<div id="doctorModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Doctor</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="doctorForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="doctor_id" id="doctorId">

            <div class="form-grid">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone_number">
                </div>

                <div class="form-group">
                    <label for="specialization">Specialization</label>
                    <input type="text" id="specialization" name="specialization" required>
                </div>

                <div class="form-group">
                    <label for="licenseNumber">License Number</label>
                    <input type="text" id="licenseNumber" name="license_number" required>
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php if (isset($departments)): foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['department_id']; ?>">
                                    <?php echo htmlspecialchars($dept['department_name']); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-save-line"></i>
                    Save Doctor
                </button>
                <button type="button" class="cancel-btn" onclick="closeModal()">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Manage Schedule</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="scheduleForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="doctor_id" id="scheduleDoctor">

            <div class="schedule-grid">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day):
                ?>
                    <div class="schedule-day">
                        <label><?php echo $day; ?></label>
                        <div class="time-slots">
                            <div class="time-input">
                                <input type="time" name="schedule[<?php echo strtolower($day); ?>][start]">
                                <span>to</span>
                                <input type="time" name="schedule[<?php echo strtolower($day); ?>][end]">
                            </div>
                            <label class="checkbox-label">
                                <input type="checkbox" name="schedule[<?php echo strtolower($day); ?>][available]" value="1">
                                Available
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-save-line"></i>
                    Save Schedule
                </button>
                <button type="button" class="cancel-btn" onclick="closeScheduleModal()">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const basePath = '<?php echo $basePath; ?>';
    document.addEventListener('DOMContentLoaded', function () {
    // Modal Elements
    const departmentModal = document.getElementById('departmentModal');
    const deleteModal = document.getElementById('deleteModal');
    const doctorModal = document.getElementById('doctorModal');
    const scheduleModal = document.getElementById('scheduleModal');

    // Form Elements
    const departmentForm = document.getElementById('departmentForm');
    const doctorForm = document.getElementById('doctorForm');
    const scheduleForm = document.getElementById('scheduleForm');

    let currentEntityId = null;

    // Add Department Button
    document.getElementById('addDepartmentBtn')?.addEventListener('click', function () {
        document.getElementById('modalTitle').textContent = 'Add Department';
        departmentForm.reset();
        document.getElementById('departmentId').value = '';
        departmentModal.classList.add('show'); // Show the modal
    });

    // Edit Department Buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const departmentId = this.dataset.id;
            try {
                const response = await fetch(`${basePath}/hospital/get-department-details?id=${departmentId}`);
                const data = await response.json();

                document.getElementById('modalTitle').textContent = 'Edit Department';
                document.getElementById('departmentId').value = data.department_id;
                document.getElementById('departmentName').value = data.department_name;
                document.getElementById('description').value = data.description;
                document.getElementById('headDoctor').value = data.head_doctor_id || '';

                departmentModal.classList.add('show'); // Show the modal
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to fetch department details');
            }
        });
    });

    // Delete Department Buttons
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentEntityId = this.dataset.id;
            deleteModal.classList.add('show'); // Show the delete modal
        });
    });

    // Add Doctor Button
    document.getElementById('addDoctorBtn')?.addEventListener('click', function () {
        document.getElementById('modalTitle').textContent = 'Add Doctor';
        doctorForm.reset();
        document.getElementById('doctorId').value = '';
        doctorModal.classList.add('show'); // Show the modal
    });

    // Edit Doctor Buttons
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const doctorId = this.dataset.id;
            try {
                const response = await fetch(`${basePath}/hospital/get-doctor-details?id=${doctorId}`);
                const data = await response.json();

                document.getElementById('modalTitle').textContent = 'Edit Doctor';
                document.getElementById('doctorId').value = data.doctor_id;
                document.getElementById('firstName').value = data.first_name;
                document.getElementById('lastName').value = data.last_name;
                document.getElementById('email').value = data.email;
                document.getElementById('phone').value = data.phone_number || '';
                document.getElementById('specialization').value = data.specialization;
                document.getElementById('licenseNumber').value = data.license_number;
                document.getElementById('department').value = data.department_id;

                doctorModal.classList.add('show'); // Show the modal
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to fetch doctor details');
            }
        });
    });

    // Confirm Delete
    document.getElementById('confirmDelete')?.addEventListener('click', async function () {
        try {
            const response = await fetch(`${basePath}/hospital/delete-department`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `department_id=${currentEntityId}&csrf_token=${document.querySelector('[name="csrf_token"]').value}`
            });
            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'An error occurred');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while deleting the department');
        }
    });

    // Close Modals
    function closeModal(modal) {
        modal.classList.remove('show'); // Hide the modal
        if (modal === departmentModal) departmentForm.reset();
        if (modal === doctorModal) doctorForm.reset();
        if (modal === scheduleModal) scheduleForm.reset();
    }

    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.onclick = function () {
            const modal = this.closest('.modal');
            closeModal(modal);
        };
    });

    window.onclick = function (event) {
        if (event.target === departmentModal) closeModal(departmentModal);
        if (event.target === deleteModal) closeModal(deleteModal);
        if (event.target === doctorModal) closeModal(doctorModal);
        if (event.target === scheduleModal) closeModal(scheduleModal);
    };
});
</script>
</body>

</html>