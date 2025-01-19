<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="top-bar">
                <h1>Specialist Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stats-card patients-overview">
                    <div class="stats-header">
                        <h2><?php echo $stats['total']; ?></h2>
                        <p>Patients</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-group-line"></i>
                        <div class="stats-info">
                            <h3>All Patients</h3>
                            <p><?php echo $stats['total']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>Scheduled Appointments</h3>
                            <p><?php echo $stats['scheduled']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Appointment Requests Section -->
            <section class="appointments-section">
                <h2>New Appointment Requests</h2>
                <div class="appointments-list">
                    <?php if ($requests->num_rows > 0): ?>
                        <?php while ($request = $requests->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($request['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($request['appointment_date'])); ?></p>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn view-details" 
                                            data-id="<?php echo $request['appointment_id']; ?>"
                                            data-notes="<?php echo htmlspecialchars($request['patient_notes']); ?>">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="action-btn confirm-appointment" 
                                            data-id="<?php echo $request['appointment_id']; ?>">
                                        <i class="ri-check-line"></i>
                                    </button>
                                    <button class="action-btn reschedule-appointment" 
                                            data-id="<?php echo $request['appointment_id']; ?>">
                                        <i class="ri-time-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No new appointment requests</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Scheduled Appointments Section -->
            <section class="appointments-section">
                <h2>Scheduled Appointments</h2>
                <div class="appointments-list">
                    <?php if ($scheduled->num_rows > 0): ?>
                        <?php while ($appointment = $scheduled->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button class="action-btn">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No scheduled appointments</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Appointment Details Modal -->
            <div id="appointmentModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Appointment Details</h2>
                    <div id="appointmentDetails"></div>
                    <div class="form-actions">
                        <button class="submit-btn" id="confirmAppointment">Confirm</button>
                        <button class="cancel-btn" id="closeModal">Close</button>
                    </div>
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
                            <input type="date" id="newDate" required>
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
            // Modal Elements
            const appointmentModal = document.getElementById('appointmentModal');
            const rescheduleModal = document.getElementById('rescheduleModal');
            const closeModal = document.getElementById('closeModal');
            const closeRescheduleModal = document.getElementById('closeRescheduleModal');

            // View Details Functionality
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const notes = this.dataset.notes;
                    document.getElementById('appointmentDetails').innerHTML = `
                        <p><strong>Patient Notes:</strong></p>
                        <p>${notes}</p>
                    `;
                    appointmentModal.style.display = 'flex';
                });
            });

            // Confirm Appointment Functionality
            document.querySelectorAll('.confirm-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const appointmentId = this.dataset.id;
                    if (confirm('Are you sure you want to confirm this appointment?')) {
                        updateAppointmentStatus(appointmentId, 'Scheduled');
                    }
                });
            });

            // Reschedule Functionality
            document.querySelectorAll('.reschedule-appointment').forEach(button => {
                button.addEventListener('click', function() {
                    const appointmentId = this.dataset.id;
                    document.getElementById('rescheduleAppointmentId').value = appointmentId;
                    rescheduleModal.style.display = 'flex';
                });
            });

            // Form Submissions
            document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const appointmentId = document.getElementById('rescheduleAppointmentId').value;
                const newDate = document.getElementById('newDate').value;
                const newTime = document.getElementById('newTime').value;
                
                updateAppointmentStatus(appointmentId, 'Rescheduled', newDate, newTime);
            });

            // Close Modal Functions
            closeModal.addEventListener('click', () => appointmentModal.style.display = 'none');
            closeRescheduleModal.addEventListener('click', () => rescheduleModal.style.display = 'none');

            // Update Appointment Status Function
            function updateAppointmentStatus(appointmentId, status, newDate = null, newTime = null) {
                const formData = new FormData();
                formData.append('appointment_id', appointmentId);
                formData.append('status', status);
                if (newDate) formData.append('new_date', newDate);
                if (newTime) formData.append('new_time', newTime);

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

            // Close modals when clicking outside
            window.onclick = function(event) {
                if (event.target === appointmentModal) {
                    appointmentModal.style.display = 'none';
                }
                if (event.target === rescheduleModal) {
                    rescheduleModal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>