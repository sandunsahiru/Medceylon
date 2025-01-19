<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

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
            <button class="filter-tab active" data-tab="new">New Appointments</button>
            <button class="filter-tab" data-tab="scheduled">Scheduled</button>
            <button class="filter-tab" data-tab="rescheduled">Rescheduled</button>
            <button class="filter-tab" data-tab="availability">Available Times</button>
        </div>
        <button class="action-btn" onclick="showAddAvailabilityForm()">
            <i class="ri-add-line"></i>
            Add Time Slot
        </button>
    </div>

    <!-- New Appointments Section -->
    <section id="newTab" class="appointments-wrapper">
        <?php if ($new_appointments->num_rows > 0): ?>
            <?php while ($appointment = $new_appointments->fetch_assoc()): ?>
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
                        <div class="info-item">
                            <i class="ri-file-text-line"></i>
                            <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                        </div>
                    </div>

                    <div class="row-actions">
                        <button class="edit-btn" onclick="confirmAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-check-line"></i>
                            Confirm
                        </button>
                        <button class="cancel-btn" onclick="showRescheduleForm(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-time-line"></i>
                            Reschedule
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No new appointment requests.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Scheduled Appointments Section -->
    <section id="scheduledTab" class="appointments-wrapper" style="display: none;">
        <?php if ($scheduled_appointments->num_rows > 0): ?>
            <?php while ($appointment = $scheduled_appointments->fetch_assoc()): ?>
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
                        <div class="info-item">
                            <i class="ri-file-text-line"></i>
                            <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                        </div>
                    </div>

                    <div class="row-actions">
                        <button class="edit-btn" onclick="completeAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-check-double-line"></i>
                            Complete
                        </button>
                        <button class="cancel-btn" onclick="showRescheduleForm(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-time-line"></i>
                            Reschedule
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No scheduled appointments.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Rescheduled Appointments Section -->
    <section id="rescheduledTab" class="appointments-wrapper" style="display: none;">
        <?php if ($rescheduled_appointments->num_rows > 0): ?>
            <?php while ($appointment = $rescheduled_appointments->fetch_assoc()): ?>
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
                        <div class="info-item">
                            <i class="ri-file-text-line"></i>
                            <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                        </div>
                    </div>

                    <div class="row-actions">
                        <button class="edit-btn" onclick="confirmAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-check-line"></i>
                            Confirm New Time
                        </button>
                        <button class="cancel-btn" onclick="cancelAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-close-line"></i>
                            Cancel
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No rescheduled appointments.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Availability Section -->
    <section id="availabilityTab" class="appointments-wrapper" style="display: none;">
        <div class="availability-grid">
            <?php if ($availability->num_rows > 0): ?>
                <?php while ($slot = $availability->fetch_assoc()): ?>
                    <div class="time-slot-card">
                        <div class="time-slot-info">
                            <h3><?php echo htmlspecialchars($slot['day_of_week']); ?></h3>
                            <div class="time">
                                <i class="ri-time-line"></i>
                                <span><?php echo date('H:i', strtotime($slot['start_time'])) . ' - ' . date('H:i', strtotime($slot['end_time'])); ?></span>
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
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No availability slots set.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Add Availability Modal -->
    <div id="availabilityModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Add Available Time Slot</h2>
            <form action="<?php echo $basePath; ?>/vpdoctor/manage-availability" method="POST">
                <input type="hidden" name="action" value="add_availability">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

                <div class="form-group">
                    <label>Day of Week:</label>
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
                    <label>Start Time:</label>
                    <input type="time" name="start_time" required>
                </div>
                <div class="form-group">
                    <label>End Time:</label>
                    <input type="time" name="end_time" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Save</button>
                    <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Availability Modal -->
    <div id="editAvailabilityModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Edit Available Time Slot</h2>
            <form action="<?php echo $basePath; ?>/vpdoctor/manage-availability" method="POST">
                <input type="hidden" name="action" value="edit_availability">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" id="edit_availability_id" name="availability_id">

                <div class="form-group">
                    <label>Day of Week:</label>
                    <select name="day_of_week" id="edit_day_of_week" required>
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
                    <label>Start Time:</label>
                    <input type="time" name="start_time" id="edit_start_time" required>
                </div>
                <div class="form-group">
                    <label>End Time:</label>
                    <input type="time" name="end_time" id="edit_end_time" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Update</button>
                    <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Reschedule Appointment</h2>
            <form id="rescheduleForm">
                <input type="hidden" id="rescheduleAppointmentId">
                <div class="form-group">
                    <label for="newDate">New Date:</label>
                    <input type="date" id="newDate" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="newTime">New Time:</label>
                    <input type="time" id="newTime" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Reschedule</button>
                    <button type="button" class="cancel-btn" id="closeRescheduleModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeTabs();
        initializeSearch();
        initializeModals();
        initializeRescheduleForm();
    });

    function initializeTabs() {
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Hide all sections
                document.getElementById('newTab').style.display = 'none';
                document.getElementById('scheduledTab').style.display = 'none';
                document.getElementById('rescheduledTab').style.display = 'none';
                document.getElementById('availabilityTab').style.display = 'none';

                // Show selected section
                const tabId = this.getAttribute('data-tab') + 'Tab';
                document.getElementById(tabId).style.display = 'block';
            });
        });
    }

    function initializeSearch() {
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.appointment-row').forEach(row => {
                const patientName = row.dataset.patientName.toLowerCase();
                row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    }

    function initializeModals() {
        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Close buttons
        document.querySelectorAll('.close-btn, .cancel-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });
    }

    function initializeRescheduleForm() {
        document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const appointmentId = document.getElementById('rescheduleAppointmentId').value;
            const newDate = document.getElementById('newDate').value;
            const newTime = document.getElementById('newTime').value;

            updateAppointmentStatus(appointmentId, 'Rescheduled', newDate, newTime);
        });

        document.getElementById('closeRescheduleModal').addEventListener('click', function() {
            document.getElementById('rescheduleModal').style.display = 'none';
        });
    }

    function showAddAvailabilityForm() {
        document.getElementById('availabilityModal').style.display = 'flex';
    }

    function hideModal() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }

    function showRescheduleForm(appointmentId) {
        document.getElementById('rescheduleAppointmentId').value = appointmentId;
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function confirmAppointment(appointmentId) {
        if (confirm('Are you sure you want to confirm this appointment?')) {
            updateAppointmentStatus(appointmentId, 'Scheduled');
        }
    }

    function completeAppointment(appointmentId) {
        if (confirm('Are you sure you want to mark this appointment as completed?')) {
            updateAppointmentStatus(appointmentId, 'Completed');
        }
    }

    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            updateAppointmentStatus(appointmentId, 'Canceled');
        }
    }

    function updateAppointmentStatus(appointmentId, status, newDate = null, newTime = null) {
        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('status', status);
        if (newDate) formData.append('new_date', newDate);
        if (newTime) formData.append('new_time', newTime);
        formData.append('csrf_token', '<?php echo $csrfToken; ?>');

        fetch(`${basePath}/vpdoctor/update-appointment-status`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to update appointment status. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }

    // Handle availability slot management
    function editAvailability(availabilityId) {
        // Get the availability slot data from the DOM
        const slotCard = document.querySelector(`[data-availability-id="${availabilityId}"]`);
        if (!slotCard) return;

        // Populate the edit form
        document.getElementById('edit_availability_id').value = availabilityId;
        document.getElementById('edit_day_of_week').value = slotCard.dataset.day;
        document.getElementById('edit_start_time').value = slotCard.dataset.startTime;
        document.getElementById('edit_end_time').value = slotCard.dataset.endTime;

        // Show the modal
        document.getElementById('editAvailabilityModal').style.display = 'flex';
    }

    function deleteAvailability(availabilityId) {
        if (confirm('Are you sure you want to delete this availability slot?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${basePath}/vpdoctor/manage-availability`;

            const fields = {
                'action': 'delete_availability',
                'availability_id': availabilityId,
                'csrf_token': '<?php echo $csrfToken; ?>'
            };

            for (const [key, value] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }
    }

    // Show success/error messages if they exist
    <?php if (isset($_GET['success'])): ?>
        alert('<?php echo htmlspecialchars($_GET['success']); ?>');
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        alert('<?php echo htmlspecialchars($_GET['error']); ?>');
    <?php endif; ?>
</script>
</body>

</html>