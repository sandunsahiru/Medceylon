<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

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
                <div class="appointment-row" data-patient-name="<?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?>">
                    <div class="patient-info">
                        <div class="avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="info-details">
                            <h3><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h3>
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
                        <button class="view-btn" onclick="viewAppointmentDetails(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-eye-line"></i>
                            View
                        </button>
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

    <!-- Appointment Details Modal -->
    <div id="appointmentDetailsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <button onclick="hideAppointmentDetails()" class="close-btn">&times;</button>
            </div>
            <div id="appointmentDetails" class="modal-body">
                <!-- Details will be loaded here dynamically -->
            </div>
        </div>
    </div>
</main>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    position: relative;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.close-btn {
    font-size: 24px;
    color: #666;
    background: none;
    border: none;
    cursor: pointer;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.detail-row {
    margin-bottom: 15px;
}

.detail-row strong {
    display: block;
    color: #666;
    margin-bottom: 5px;
}

.view-btn {
    background-color: #4a90e2;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-right: 8px;
}

.view-btn:hover {
    background-color: #357abd;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}

.status.scheduled { background-color: #4CAF50; color: white; }
.status.completed { background-color: #2196F3; color: white; }
.status.canceled { background-color: #f44336; color: white; }
.status.rescheduled { background-color: #FF9800; color: white; }
</style>

<script>
    const basePath = '<?php echo $basePath; ?>';
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
        function viewAppointmentDetails(appointmentId) {
    // Show loading state
    const modalContent = document.getElementById('appointmentDetails');
    modalContent.innerHTML = '<div class="loading-spinner">Loading...</div>';
    document.getElementById('appointmentDetailsModal').style.display = 'block';

    // Use the correct endpoint matching the route in index.php
    fetch(`${basePath}/doctor/getAppointmentDetails?appointment_id=${appointmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            const htmlContent = `
                <div class="appointment-details">
                    <div class="detail-row">
                        <strong>Patient Name:</strong>
                        <span>${data.first_name} ${data.last_name}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Contact Information:</strong>
                        <span>Email: ${data.email || 'N/A'}</span><br>
                        <span>Phone: ${data.phone_number || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Appointment Date:</strong>
                        <span>${data.appointment_date}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Appointment Time:</strong>
                        <span>${data.appointment_time}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Consultation Type:</strong>
                        <span>${data.consultation_type || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Status:</strong>
                        <span class="status ${data.appointment_status.toLowerCase()}">${data.appointment_status}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Reason for Visit:</strong>
                        <p>${data.reason_for_visit || 'Not specified'}</p>
                    </div>
                    <div class="detail-row">
                        <strong>Medical History:</strong>
                        <p>${data.medical_history || 'Not provided'}</p>
                    </div>
                </div>
            `;
            modalContent.innerHTML = htmlContent;
        })
        .catch(error => {
            modalContent.innerHTML = `
                <div class="error-message">
                    <p>Error loading appointment details: ${error.message}</p>
                    <p>Please try again or contact support if the problem persists.</p>
                </div>
            `;
            console.error('Error:', error);
        });
}

function hideAppointmentDetails() {
    document.getElementById('appointmentDetailsModal').style.display = 'none';
}


    // Close modal when clicking outside
    window.onclick = function(event) {
        const appointmentModal = document.getElementById('appointmentDetailsModal');
        const availabilityModal = document.getElementById('availabilityModal');
        
        if (event.target === appointmentModal) {
            hideAppointmentDetails();
        }
        if (event.target === availabilityModal) {
            hideModal();
        }
    }
</script>

</body>
</html>