<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Appointments</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search appointments..." id="searchInput">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Tab Navigation -->
            <div class="filters-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-tab="appointments">All Appointments</button>
                    <button class="filter-tab" data-tab="availability">Set Available Times</button>
                </div>
                <button class="action-btn" onclick="showAddAvailabilityForm()">
                    <i class="ri-add-line"></i>
                    Add Time Slot
                </button>
            </div>

            <!-- Appointments Section -->
            <section id="appointmentsTab" class="appointments-wrapper">
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-row" data-patient-name="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>">
                            <div class="patient-info">
                                <div class="avatar">
                                    <i class="ri-user-line"></i>
                                </div>
                                <div class="info-details">
                                    <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                                    <span><?php echo htmlspecialchars($appointment['consultation_type']); ?></span>
                                </div>
                            </div>

                            <div class="appointment-info">
                                <div class="info-item">
                                    <i class="ri-calendar-line"></i>
                                    <span><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="ri-time-line"></i>
                                    <span><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></span>
                                </div>
                                <span class="status <?php echo strtolower($appointment['appointment_status']); ?>">
                                    <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                </span>
                            </div>

                            <div class="row-actions">
                                <button class="edit-btn" onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>)">
                                    <i class="ri-pencil-line"></i>
                                    Update Status
                                </button>
                                <?php if($appointment['appointment_status'] === 'Scheduled'): ?>
                                    <button class="cancel-btn" onclick="cancelAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                        <i class="ri-close-line"></i>
                                        Cancel
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <p>No appointments found.</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Availability Section -->
            <section id="availabilityTab" class="appointments-wrapper" style="display: none;">
                <div class="availability-grid">
                    <?php if (!empty($availability)): ?>
                        <?php foreach ($availability as $slot): ?>
                            <div class="time-slot-card">
                                <div class="time-slot-info">
                                    <h3><?php echo htmlspecialchars($slot['day_of_week']); ?></h3>
                                    <div class="time">
                                        <i class="ri-time-line"></i>
                                        <span>
                                            <?php 
                                            echo date('H:i', strtotime($slot['start_time'])) . 
                                                 ' - ' . 
                                                 date('H:i', strtotime($slot['end_time'])); 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="slot-actions">
                                    <button class="edit-btn" onclick="editAvailability(<?php echo $slot['availability_id']; ?>)">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button class="cancel-btn" onclick="deleteAvailability(<?php echo $slot['availability_id']; ?>)">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No availability slots set.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Add Availability Form Modal -->
            <div id="availabilityModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2>Add Available Time Slot</h2>
                        <button onclick="hideModal()" class="close-btn">&times;</button>
                    </div>
                    <form id="availabilityForm" action="<?php echo $basePath; ?>/doctor/appointments" method="POST">
                        <input type="hidden" name="action" value="add_availability">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        
                        <div class="form-group">
                            <label for="day_of_week">Day of Week:</label>
                            <select name="day_of_week" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Start Time:</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">End Time:</label>
                            <input type="time" name="end_time" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const tabId = this.dataset.tab;
                document.getElementById('appointmentsTab').style.display = tabId === 'appointments' ? 'block' : 'none';
                document.getElementById('availabilityTab').style.display = tabId === 'availability' ? 'block' : 'none';
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.appointment-row').forEach(row => {
                const patientName = row.dataset.patientName.toLowerCase();
                row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        // Modal functions
        function showAddAvailabilityForm() {
            document.getElementById('availabilityModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('availabilityModal').style.display = 'none';
        }

        // Appointment functions
        function updateAppointmentStatus(appointmentId) {
            const validStatuses = ['Scheduled', 'Completed', 'Canceled', 'Rescheduled'];
            const status = prompt('Update status to: ' + validStatuses.join(', '));
            
            if (status && validStatuses.includes(status)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="${appointmentId}">
                    <input type="hidden" name="status" value="${status}">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="${appointmentId}">
                    <input type="hidden" name="status" value="Canceled">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editAvailability(availabilityId) {
            // Will be implemented when editing functionality is needed
            alert('Edit functionality will be implemented soon');
        }

        function deleteAvailability(availabilityId) {
            if (confirm('Are you sure you want to delete this availability slot?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_availability">
                    <input type="hidden" name="availability_id" value="${availabilityId}">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Show success/error messages if they exist in URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            alert('Operation completed successfully');
        } else if (urlParams.has('error')) {
            alert('An error occurred: ' + urlParams.get('error'));
        }
    </script>

</body>
</html>