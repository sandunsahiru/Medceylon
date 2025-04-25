<?php include_once 'partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Departments</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" id="searchInput" placeholder="Search departments...">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <section class="departments-section">
                <div class="section-header">
                    <h2>All Departments</h2>
                    <button class="add-btn" id="addDepartmentBtn">
                        <i class="ri-add-line"></i>
                        Add Department
                    </button>
                </div>

                <div class="departments-list">
                    <?php while ($department = $departments->fetch_assoc()): ?>
                        <div class="department-card" data-id="<?php echo $department['department_id']; ?>">
                            <div class="department-info">
                                <h3>
                                    <i class="ri-hospital-line"></i>
                                    <?php echo htmlspecialchars($department['department_name']); ?>
                                </h3>
                                <p class="description">
                                    <?php echo htmlspecialchars($department['description']); ?>
                                </p>
                                <?php if (!empty($department['head_doctor'])): ?>
                                    <p class="head-doctor">
                                        <i class="ri-user-star-line"></i>
                                        Head: Dr. <?php echo htmlspecialchars($department['head_doctor']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (isset($department['doctor_count'])): ?>
                                    <p class="doctor-count">
                                        <i class="ri-team-line"></i>
                                        Doctors: <?php echo $department['doctor_count']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="department-actions">
                                <button class="action-btn edit-btn" data-id="<?php echo $department['department_id']; ?>" 
                                        title="Edit Department">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php if ($department['can_delete']): ?>
                                    <button class="action-btn delete-btn" data-id="<?php echo $department['department_id']; ?>"
                                            title="Delete Department">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Add/Edit Department Modal -->
    <div id="departmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close-btn">&times;</button>
                <h2 id="modalTitle">Add Department</h2>
            </div>
            <form id="departmentForm">
                <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
                <input type="hidden" name="department_id" id="departmentId">
                
                <div class="form-group">
                    <label for="departmentName">Department Name</label>
                    <input type="text" id="departmentName" name="department_name" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="headDoctor">Head Doctor</label>
                    <select id="headDoctor" name="head_doctor">
                        <option value="">Select Head Doctor</option>
                        <?php if (isset($doctors)): foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['doctor_id']; ?>">
                                Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="ri-save-line"></i>
                        Save Department
                    </button>
                    <button type="button" class="close-btn" onclick="closeModal()">
                        <i class="ri-close-line"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close-btn">&times;</button>
                <h2>Delete Department</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this department? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button id="confirmDelete" class="delete-btn">
                    <i class="ri-delete-bin-line"></i>
                    Delete
                </button>
                <button class="close-btn" onclick="closeDeleteModal()">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';
        document.addEventListener('DOMContentLoaded', function () {
    // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const departmentCards = document.querySelectorAll('.department-card');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            departmentCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        // Modal Handling
        const departmentModal = document.getElementById('departmentModal');
        const deleteModal = document.getElementById('deleteModal');
        const departmentForm = document.getElementById('departmentForm');
        let currentDepartmentId = null;

        // Add Department Button
        document.getElementById('addDepartmentBtn').addEventListener('click', function () {
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
                currentDepartmentId = this.dataset.id;
                deleteModal.classList.add('show'); // Show the delete modal
            });
        });

        // Form Submission
        departmentForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch(`${basePath}/hospital/save-department`, {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'An error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the department');
            }
        });

        // Confirm Delete
        document.getElementById('confirmDelete').addEventListener('click', async function () {
            try {
                const response = await fetch(`${basePath}/hospital/delete-department`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `department_id=${currentDepartmentId}&csrf_token=${document.querySelector('[name="csrf_token"]').value}`
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
        function closeModal() {
            departmentModal.classList.remove('show'); // Hide the modal
            departmentForm.reset();
        }

        function closeDeleteModal() {
            deleteModal.classList.remove('show'); // Hide the delete modal
            currentDepartmentId = null;
        }

        document.querySelectorAll('.close-btn').forEach(btn => {
            btn.onclick = function () {
                const modal = this.closest('.modal');
                if (modal === departmentModal) {
                    closeModal();
                } else if (modal === deleteModal) {
                    closeDeleteModal();
                }
            };
        });

        window.onclick = function (event) {
            if (event.target === departmentModal) {
                closeModal();
            } else if (event.target === deleteModal) {
                closeDeleteModal();
            }
        };
    });
    </script>
</body>
</html>