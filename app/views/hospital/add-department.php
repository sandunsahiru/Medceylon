<link rel="stylesheet" href="http://localhost/Medceylon/public/assets/css/hospital.css">

<div id="departmentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Add Department</h2>
            <button class="close-btn">&times;</button>
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
                <select id="headDoctor" name="head_doctor_id">
                    <option value="">Select Head Doctor</option>
                    <?php if (isset($doctors)): foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['doctor_id']; ?>">
                            Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="doctorCount">Number of Doctors</label>
                <input type="number" id="doctorCount" name="doctor_count" min="0" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-save-line"></i>
                    Save Department
                </button>
                <button type="button" class="cancel-btn" onclick="closeModal()">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>