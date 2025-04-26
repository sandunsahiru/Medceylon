<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

<head>
    <!-- Add the external CSS file reference -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/doctor-dashboard.css">
</head>

<main class="main-content">
    <header class="top-bar">
        <h1>Specialist Dashboard</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search" id="searchInput">
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
                <h2><?php echo $stats['total_patients'] ?? 0; ?></h2>
                <p>Patients</p>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-group-line"></i>
                <div class="stats-info">
                    <h3>All Patients</h3>
                    <p><?php echo $stats['total_patients'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-calendar-check-line"></i>
                <div class="stats-info">
                    <h3>All Appointments</h3>
                    <p><?php echo $stats['upcoming_appointments'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral Appointments Section -->
    <section class="appointments-section">
        <h2>Referral Appointments</h2>

        <?php if (!empty($referralAppointments)): ?>
            <div class="appointments-list">
                <?php foreach ($referralAppointments as $appointment): ?>
                    <div class="appointment-card" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                        <div class="appointment-time">
                            <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                        </div>
                        <div class="appointment-info">
                            <h3><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h3>
                            <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                            <span class="status <?php echo strtolower($appointment['appointment_status']); ?>">
                                <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                            </span>
                        </div>
                        <div class="appointment-actions">
                            <button class="action-btn primary view-session-btn">
                                <i class="ri-file-list-3-line"></i> View Session
                            </button>
                        </div>
                    </div>

                    <!-- Session Details Container (Initially Hidden) -->
                    <div id="session-details-<?php echo $appointment['appointment_id']; ?>" class="medical-session session-details-container" style="display: none;">
                        <div class="session-header">
                            <h2>Medical Session: <?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h2>
                            <div class="session-status"><?php echo htmlspecialchars($appointment['appointment_status']); ?></div>
                        </div>
                        <div class="session-body">
                            <!-- Progress Steps -->
                            <div class="step-progress-container">
                                <div class="step-progress-bar" style="width: <?php
                                                                                // Calculate progress width based on session data
                                                                                $progress = 25; // Assuming general doctor appointment is completed
                                                                                if (isset($appointment['specialist_booked']) && $appointment['specialist_booked']) $progress += 25;
                                                                                if (isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created']) $progress += 25;
                                                                                if (isset($appointment['transport_booked']) && $appointment['transport_booked']) $progress += 25;
                                                                                echo $progress . '%';
                                                                                ?>"></div>

                                <!-- Step 1: General Doctor -->
                                <div class="step-item completed">
                                    <div class="step-circle">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <div class="step-text">General Doctor</div>
                                </div>

                                <!-- Step 2: Specialist -->
                                <div class="step-item <?php echo (isset($appointment['specialist_booked']) && $appointment['specialist_booked']) ? 'completed' : 'active'; ?>">
                                    <div class="step-circle">
                                        <?php if (isset($appointment['specialist_booked']) && $appointment['specialist_booked']): ?>
                                            <i class="ri-check-line"></i>
                                        <?php else: ?>
                                            <i class="ri-user-star-line"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="step-text">Specialist Doctor</div>
                                </div>

                                <!-- Step 3: Treatment Plan -->
                                <div class="step-item <?php
                                                        if (isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created']) echo 'completed';
                                                        elseif (isset($appointment['specialist_booked']) && $appointment['specialist_booked']) echo 'active';
                                                        ?>">
                                    <div class="step-circle">
                                        <?php if (isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created']): ?>
                                            <i class="ri-check-line"></i>
                                        <?php else: ?>
                                            <i class="ri-file-list-line"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="step-text">Treatment Plan</div>
                                </div>

                                <!-- Step 4: Travel & Accommodation -->
                                <div class="step-item <?php
                                                        if (isset($appointment['transport_booked']) && $appointment['transport_booked']) echo 'completed';
                                                        elseif (isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created']) echo 'active';
                                                        ?>">
                                    <div class="step-circle">
                                        <?php if (isset($appointment['transport_booked']) && $appointment['transport_booked']): ?>
                                            <i class="ri-check-line"></i>
                                        <?php else: ?>
                                            <i class="ri-building-line"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="step-text">Travel & Accommodation</div>
                                </div>
                            </div>

                            <!-- Patient Information -->
                            <div class="session-details-container">
                                <h3>Patient Information</h3>
                                <div class="doctor-card">
                                    <div class="doctor-avatar">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <div class="doctor-info">
                                        <h3><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h3>
                                        <p>Patient ID: <?php echo htmlspecialchars($appointment['patient_id'] ?? 'P-' . rand(10000, 99999)); ?></p>

                                        <div class="appointment-meta">
                                            <div class="meta-item">
                                                <i class="ri-calendar-line"></i>
                                                <span><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="ri-time-line"></i>
                                                <span><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="ri-user-location-line"></i>
                                                <span>Mode: <?php echo htmlspecialchars($appointment['consultation_type'] ?? 'In-Person'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Referring Doctor Information -->
                            <?php if (isset($appointment['referring_doctor_id']) && $appointment['referring_doctor_id']): ?>
                                <div class="session-details-container">
                                    <h3>Referring Doctor Information</h3>
                                    <div class="doctor-card">
                                        <div class="doctor-avatar">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div class="doctor-info">
                                            <h3>Dr. <?php echo htmlspecialchars($appointment['referring_doctor_name'] ?? 'General Doctor'); ?></h3>
                                            <p><strong>General Practitioner</strong></p>

                                            <div class="form-group">
                                                <label for="referral-notes-<?php echo $appointment['appointment_id']; ?>">Referral Notes</label>
                                                <div class="referral-notes">
                                                    <?php echo htmlspecialchars($appointment['referral_notes'] ?? 'No referral notes provided.'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Specialist Notes Section -->
                            <div class="session-details-container">
                                <h3>Specialist Notes</h3>
                                <textarea id="specialist-notes-<?php echo $appointment['appointment_id']; ?>" class="doctor-notes" placeholder="Enter your specialist notes for this patient..."><?php echo htmlspecialchars($appointment['specialist_notes'] ?? ''); ?></textarea>
                                <button class="action-btn primary save-notes-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                    <i class="ri-save-line"></i> Save Notes
                                </button>
                            </div>

                            <!-- Treatment Plan Section -->
                            <?php if (!(isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created'])): ?>
                                <div class="session-details-container">
                                    <h3>Create Treatment Plan</h3>
                                    <div class="treatment-plan-form">
                                        <div class="form-group">
                                            <label for="travel-restrictions-<?php echo $appointment['appointment_id']; ?>">Travel Restrictions</label>
                                            <select id="travel-restrictions-<?php echo $appointment['appointment_id']; ?>" class="travel-restrictions form-control">
                                                <option value="None">No Restrictions</option>
                                                <option value="Can travel, but avoid high altitudes">Can travel, but avoid high altitudes</option>
                                                <option value="Can travel, but need wheelchair assistance">Can travel, but need wheelchair assistance</option>
                                                <option value="Can travel with medical escort only">Can travel with medical escort only</option>
                                                <option value="Limited to short flights only">Limited to short flights only</option>
                                                <option value="Not fit for air travel at this time">Not fit for air travel at this time</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="treatment-description-<?php echo $appointment['appointment_id']; ?>">Treatment Description</label>
                                            <textarea id="treatment-description-<?php echo $appointment['appointment_id']; ?>" class="treatment-description form-control" rows="4" placeholder="Describe the recommended treatment plan..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="estimated-budget-<?php echo $appointment['appointment_id']; ?>">Estimated Budget (USD)</label>
                                            <input type="number" id="estimated-budget-<?php echo $appointment['appointment_id']; ?>" class="estimated-budget form-control" placeholder="Enter estimated cost">
                                        </div>
                                        <div class="form-group">
                                            <label for="estimated-duration-<?php echo $appointment['appointment_id']; ?>">Estimated Duration (Days)</label>
                                            <input type="number" id="estimated-duration-<?php echo $appointment['appointment_id']; ?>" class="estimated-duration form-control" placeholder="Enter estimated duration">
                                        </div>
                                        <button class="full-width-button create-treatment-plan-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                            <i class="ri-file-list-3-line"></i> Create Treatment Plan
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Treatment Plan Info (if already created) -->
                                <div class="session-details-container">
                                    <h3>Treatment Information</h3>
                                    <div class="treatment-details">
                                        <p><strong>Travel Restrictions:</strong> <?php echo htmlspecialchars($appointment['travel_restrictions'] ?? 'None'); ?></p>
                                        <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($appointment['treatment_description'] ?? 'No description provided.'); ?></p>
                                        <p><strong>Estimated Budget:</strong> $<?php echo htmlspecialchars($appointment['estimated_budget'] ?? '0'); ?></p>
                                        <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($appointment['estimated_duration'] ?? '0'); ?> days</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="action-btn secondary" id="editTreatmentPlanBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                            <i class="ri-edit-line"></i> Edit Treatment Plan
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Medical Request & Cancel Buttons -->
                            <div class="session-actions">
                                <button class="action-btn primary request-medical-btn" id="requestMedicalBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                    <i class="ri-test-tube-line"></i> Request Medical Tests
                                </button>
                                <button class="action-btn secondary cancel-treatment-btn" id="cancelTreatmentBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                    <i class="ri-close-circle-line"></i> Cancel Treatment
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-appointments">
                <p>No referral appointments scheduled</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Regular Appointments Section -->
    <section class="appointments-section">
        <h2>Regular Appointments</h2>

        <?php if (!empty($regularAppointments)): ?>
            <div class="appointments-list">
                <?php foreach ($regularAppointments as $appointment): ?>
                    <div class="appointment-card" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                        <!-- Existing card content -->
                        <div class="appointment-time">
                            <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                        </div>
                        <div class="appointment-info">
                            <h3><?php echo htmlspecialchars($appointment['patient_first_name'] . ' ' . $appointment['patient_last_name']); ?></h3>
                            <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                            <span class="status <?php echo strtolower($appointment['appointment_status']); ?>">
                                <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                            </span>
                        </div>
                        <div class="appointment-actions">
                            <button class="action-btn primary view-details-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                <i class="ri-eye-line"></i> View Details
                            </button>
                        </div>
                    </div>

                    <!-- This div will be populated dynamically when View Details is clicked -->
                    <div id="session-details-<?php echo $appointment['appointment_id']; ?>" class="medical-session session-details-container" style="display: none;">
                        <!-- Content will be populated dynamically -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-appointments">
                <p>No regular appointments scheduled</p>
            </div>
        <?php endif; ?>
    </section>
</main>

<!-- Medical Tests Request Modal -->
<div id="medicalTestsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Request Medical Tests</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="medical-tests-form">
            <div class="form-group">
                <label for="testType">Test Type</label>
                <select id="testType" class="form-control">
                    <option value="">Select Test Type</option>
                    <option value="blood">Blood Test</option>
                    <option value="urine">Urine Test</option>
                    <option value="imaging">Imaging (X-ray, MRI, CT Scan)</option>
                    <option value="ecg">Electrocardiogram (ECG)</option>
                    <option value="ultrasound">Ultrasound</option>
                    <option value="endoscopy">Endoscopy</option>
                    <option value="biopsy">Biopsy</option>
                    <option value="colonoscopy">Colonoscopy</option>
                    <option value="allergy">Allergy Testing</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="testDescription">Test Description</label>
                <textarea id="testDescription" class="form-control" rows="3" placeholder="Provide specific instructions for the test..."></textarea>
            </div>
            <div class="form-group">
                <label for="testRequiredFasting">Requires Fasting</label>
                <select id="testRequiredFasting" class="form-control">
                    <option value="no">No</option>
                    <option value="yes">Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label for="testUrgency">Urgency</label>
                <select id="testUrgency" class="form-control">
                    <option value="routine">Routine</option>
                    <option value="urgent">Urgent</option>
                    <option value="immediate">Immediate</option>
                </select>
            </div>
            <button id="submitMedicalTest" class="full-width-button">Submit Request</button>
        </div>
    </div>
</div>

<!-- Cancel Treatment Modal -->
<div id="cancelTreatmentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cancel Treatment</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="cancel-treatment-form">
            <div class="alert-box alert-warning">
                <i class="ri-error-warning-line"></i>
                <p>Are you sure you want to cancel this treatment? This action cannot be undone.</p>
            </div>
            <div class="form-group">
                <label for="cancellationReason">Reason for Cancellation</label>
                <textarea id="cancellationReason" class="form-control" rows="3" placeholder="Please provide a reason for cancellation..."></textarea>
            </div>
            <button id="confirmCancelTreatment" class="full-width-button">Confirm Cancellation</button>
        </div>
    </div>
</div>

<!-- Edit Treatment Plan Modal -->
<div id="editTreatmentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Treatment Plan</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="treatment-plan-form">
            <div class="form-group">
                <label for="editTravelRestrictions">Travel Restrictions</label>
                <select id="editTravelRestrictions" class="form-control">
                    <option value="None">No Restrictions</option>
                    <option value="Can travel, but avoid high altitudes">Can travel, but avoid high altitudes</option>
                    <option value="Can travel, but need wheelchair assistance">Can travel, but need wheelchair assistance</option>
                    <option value="Can travel with medical escort only">Can travel with medical escort only</option>
                    <option value="Limited to short flights only">Limited to short flights only</option>
                    <option value="Not fit for air travel at this time">Not fit for air travel at this time</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editTreatmentDescription">Treatment Description</label>
                <textarea id="editTreatmentDescription" class="form-control" rows="4" placeholder="Describe the recommended treatment plan..."></textarea>
            </div>
            <div class="form-group">
                <label for="editEstimatedBudget">Estimated Budget (USD)</label>
                <input type="number" id="editEstimatedBudget" class="form-control" placeholder="Enter estimated cost">
            </div>
            <div class="form-group">
                <label for="editEstimatedDuration">Estimated Duration (Days)</label>
                <input type="number" id="editEstimatedDuration" class="form-control" placeholder="Enter estimated duration">
            </div>
            <button id="updateTreatmentPlan" class="full-width-button">Update Treatment Plan</button>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Appointment Details</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div id="appointmentDetailsContent" class="modal-body">
            <!-- Content will be dynamically populated -->
        </div>
        <div class="modal-footer">
            <button class="action-btn secondary" id="closeAppointmentDetails">Close</button>
        </div>
    </div>
</div>

<!-- Add the variable for base path and include the JS file at the end of the document -->
<script>
    const basePath = '<?php echo $basePath; ?>';

    // Global variables to store current IDs for modals
    let currentAppointmentId = null;
    let currentSessionId = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.appointment-card').forEach(card => {
                    const patientName = card.querySelector('h3').textContent.toLowerCase();
                    if (patientName.includes(searchTerm)) {
                        card.style.display = 'flex';
                        // Also hide any open session details
                        const appointmentId = card.dataset.appointmentId;
                        const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
                        if (sessionDetails) {
                            sessionDetails.style.display = 'none';
                        }
                    } else {
                        card.style.display = 'none';
                        // Also hide any open session details
                        const appointmentId = card.dataset.appointmentId;
                        const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
                        if (sessionDetails) {
                            sessionDetails.style.display = 'none';
                        }
                    }
                });
            });
        }

        // View Session button click
        document.querySelectorAll('.view-session-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentCard = this.closest('.appointment-card');
                const appointmentId = appointmentCard.dataset.appointmentId;
                const sessionDetails = document.getElementById(`session-details-${appointmentId}`);

                if (!sessionDetails) {
                    console.error(`Session details container not found for appointment ${appointmentId}`);
                    return;
                }

                // Close all other open sessions first
                document.querySelectorAll('.medical-session').forEach(container => {
                    if (container.id !== `session-details-${appointmentId}` && container.style.display !== 'none') {
                        container.style.display = 'none';

                        // Find the associated appointment card and update button text
                        const otherAppointmentId = container.id.replace('session-details-', '');
                        const otherButton = document.querySelector(`.appointment-card[data-appointment-id="${otherAppointmentId}"] .view-session-btn`);
                        if (otherButton) {
                            otherButton.innerHTML = '<i class="ri-file-list-3-line"></i> View Session';
                        }
                    }
                });

                // Toggle session details visibility
                if (sessionDetails.style.display === 'none' || !sessionDetails.style.display) {
                    sessionDetails.style.display = 'block';
                    this.innerHTML = '<i class="ri-eye-off-line"></i> Hide Session';
                } else {
                    sessionDetails.style.display = 'none';
                    this.innerHTML = '<i class="ri-file-list-3-line"></i> View Session';
                }
            });
        });

        // View Regular Appointment Details
        document.querySelectorAll('.view-details-btn').forEach(button => {
    button.addEventListener('click', function() {
        const appointmentId = this.dataset.appointmentId;
        const appointmentCard = this.closest('.appointment-card');
        
        // Check if we already have session details showing
        const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
        if (sessionDetails) {
            // Toggle visibility if session details already exist
            if (sessionDetails.style.display === 'none' || !sessionDetails.style.display) {
                sessionDetails.style.display = 'block';
                this.innerHTML = '<i class="ri-eye-off-line"></i> Hide Details';
            } else {
                sessionDetails.style.display = 'none';
                this.innerHTML = '<i class="ri-eye-line"></i> View Details';
            }
            return;
        }
        
        // If no session details exist yet, create them
        const newSessionDetails = document.createElement('div');
        newSessionDetails.id = `session-details-${appointmentId}`;
        newSessionDetails.className = 'medical-session session-details-container';
        
        // Insert as next sibling of appointment card
        appointmentCard.parentNode.insertBefore(newSessionDetails, appointmentCard.nextSibling);
        
        // Add loading indicator
        newSessionDetails.innerHTML = '<div class="loading">Loading patient details...</div>';
        newSessionDetails.style.display = 'block';
        
        // Change button text
        this.innerHTML = '<i class="ri-eye-off-line"></i> Hide Details';
        
        // Fetch appointment details
        fetch(`${basePath}/vpdoctor/get-appointment-details?appointment_id=${appointmentId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Create the session details content
                    const details = data.data;
                    const sessionHTML = `
                        <div class="session-header">
                            <h2>Medical Session: ${details.patient_name}</h2>
                            <div class="session-status">${details.appointment_status}</div>
                        </div>
                        <div class="session-body">
                            <!-- Patient Information -->
                            <div class="session-details-container">
                                <h3>Patient Information</h3>
                                <div class="doctor-card">
                                    <div class="doctor-avatar">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <div class="doctor-info">
                                        <h3>${details.patient_name}</h3>
                                        <p>Patient ID: ${details.patient_id || 'P-' + Math.floor(10000 + Math.random() * 90000)}</p>
                                        
                                        <div class="appointment-meta">
                                            <div class="meta-item">
                                                <i class="ri-calendar-line"></i>
                                                <span>${details.appointment_date}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="ri-time-line"></i>
                                                <span>${details.appointment_time}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="ri-user-location-line"></i>
                                                <span>Mode: ${details.consultation_type || 'Online'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Notes Section -->
                            <div class="session-details-container">
                                <h3>Medical Notes</h3>
                                <textarea id="specialist-notes-${details.appointment_id}" class="doctor-notes" placeholder="Enter your specialist notes for this patient...">${details.notes || ''}</textarea>
                                <button class="action-btn primary save-notes-btn" data-appointment-id="${details.appointment_id}">
                                    <i class="ri-save-line"></i> Save Notes
                                </button>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="session-actions">
                                <button class="action-btn primary request-medical-btn" data-appointment-id="${details.appointment_id}">
                                    <i class="ri-test-tube-line"></i> Request Medical Tests
                                </button>
                                <button class="action-btn secondary cancel-treatment-btn" data-appointment-id="${details.appointment_id}">
                                    <i class="ri-close-circle-line"></i> Cancel Treatment
                                </button>
                            </div>
                        </div>
                    `;
                    newSessionDetails.innerHTML = sessionHTML;
                    
                    // Add event listeners to buttons in the new session details
                    addSessionButtonEventListeners(newSessionDetails);
                } else {
                    newSessionDetails.innerHTML = `<div class="error-message">Failed to load appointment details: ${data.error || 'Unknown error'}</div>`;
                }
            })
            .catch(error => {
                console.error('Error fetching appointment details:', error);
                newSessionDetails.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
            });
    });
});
        // Helper function to toggle session visibility
        function toggleSessionVisibility(appointmentId, button) {
            const sessionDetails = document.getElementById(`session-details-${appointmentId}`);

            // Close all other open sessions first
            document.querySelectorAll('.medical-session').forEach(container => {
                if (container.id !== `session-details-${appointmentId}` && container.style.display !== 'none') {
                    container.style.display = 'none';

                    // Find the associated appointment card and update button text
                    const otherAppointmentId = container.id.replace('session-details-', '');
                    const otherButton = document.querySelector(`.appointment-card[data-appointment-id="${otherAppointmentId}"] .view-details-btn, .appointment-card[data-appointment-id="${otherAppointmentId}"] .view-session-btn`);
                    if (otherButton) {
                        otherButton.innerHTML = otherButton.classList.contains('view-session-btn') ?
                            '<i class="ri-file-list-3-line"></i> View Session' :
                            '<i class="ri-eye-line"></i> View Details';
                    }
                }
            });

            // Toggle session details visibility
            if (sessionDetails.style.display === 'none' || !sessionDetails.style.display) {
                sessionDetails.style.display = 'block';
                button.innerHTML = '<i class="ri-eye-off-line"></i> Hide Details';
            } else {
                sessionDetails.style.display = 'none';
                button.innerHTML = '<i class="ri-eye-line"></i> View Details';
            }
        }

        // Helper function to create session details HTML
        function createSessionDetailsHTML(details) {
            return `
        <div class="session-header">
            <h2>Medical Session: ${details.patient_name}</h2>
            <div class="session-status">${details.appointment_status}</div>
        </div>
        <div class="session-body">
            <!-- Patient Information -->
            <div class="session-details-container">
                <h3>Patient Information</h3>
                <div class="doctor-card">
                    <div class="doctor-avatar">
                        <i class="ri-user-line"></i>
                    </div>
                    <div class="doctor-info">
                        <h3>${details.patient_name}</h3>
                        <p>Patient ID: ${details.patient_id || 'P-' + Math.floor(10000 + Math.random() * 90000)}</p>
                        
                        <div class="appointment-meta">
                            <div class="meta-item">
                                <i class="ri-calendar-line"></i>
                                <span>${details.appointment_date}</span>
                            </div>
                            <div class="meta-item">
                                <i class="ri-time-line"></i>
                                <span>${details.appointment_time}</span>
                            </div>
                            <div class="meta-item">
                                <i class="ri-user-location-line"></i>
                                <span>Mode: ${details.consultation_type || 'Online'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notes Section -->
            <div class="session-details-container">
                <h3>Medical Notes</h3>
                <textarea id="specialist-notes-${details.appointment_id}" class="doctor-notes" placeholder="Enter your specialist notes for this patient...">${details.notes || ''}</textarea>
                <button class="action-btn primary save-notes-btn" data-appointment-id="${details.appointment_id}">
                    <i class="ri-save-line"></i> Save Notes
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div class="session-actions">
                <button class="action-btn primary" onclick="completeAppointment(${details.appointment_id})">
                    <i class="ri-check-line"></i> Complete Appointment
                </button>
                <button class="action-btn secondary" onclick="cancelAppointment(${details.appointment_id})">
                    <i class="ri-close-line"></i> Cancel Appointment
                </button>
            </div>
        </div>
    `;
        }

        // Save Notes button click
        document.querySelectorAll('.save-notes-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.appointmentId;
                const sessionId = this.dataset.sessionId || '';
                const notesId = `specialist-notes-${appointmentId}`;
                const notes = document.getElementById(notesId).value;

                if (!notes.trim()) {
                    alert('Please enter some notes before saving');
                    return;
                }

                // Create form to submit data to the server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${basePath}/vpdoctor/saveSpecialistNotes`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = 'csrf_token';
                csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
                form.appendChild(csrfField);

                // Add session ID and notes
                const sessionIdField = document.createElement('input');
                sessionIdField.type = 'hidden';
                sessionIdField.name = 'session_id';
                sessionIdField.value = sessionId;
                form.appendChild(sessionIdField);

                // Add specialist notes
                const notesField = document.createElement('input');
                notesField.type = 'hidden';
                notesField.name = 'specialist_notes';
                notesField.value = notes;
                form.appendChild(notesField);

                // Add to document and submit
                document.body.appendChild(form);

                // Show success message while form is submitting
                showSuccessAlert(appointmentId, 'Specialist notes saved successfully!');
                showToast('Notes saved successfully');

                // Submit the form
                form.submit();
            });
        });

        // Create Treatment Plan button click
        document.querySelectorAll('.create-treatment-plan-btn').forEach(button => {
            button.addEventListener('click', function() {
                const appointmentId = this.dataset.appointmentId;
                const sessionId = this.dataset.sessionId || '';
                const travelRestrictionsId = `travel-restrictions-${appointmentId}`;
                const treatmentDescriptionId = `treatment-description-${appointmentId}`;
                const estimatedBudgetId = `estimated-budget-${appointmentId}`;
                const estimatedDurationId = `estimated-duration-${appointmentId}`;

                const travelRestrictions = document.getElementById(travelRestrictionsId).value;
                const treatmentDescription = document.getElementById(treatmentDescriptionId).value;
                const estimatedBudget = document.getElementById(estimatedBudgetId).value;
                const estimatedDuration = document.getElementById(estimatedDurationId).value;

                if (!treatmentDescription) {
                    alert('Please provide a treatment description');
                    return;
                }

                if (!estimatedBudget) {
                    alert('Please provide an estimated budget');
                    return;
                }

                if (!estimatedDuration) {
                    alert('Please provide an estimated duration');
                    return;
                }

                // Create form to submit data to the server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${basePath}/vpdoctor/createTreatmentPlan`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = 'csrf_token';
                csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
                form.appendChild(csrfField);

                // Add session ID
                const sessionIdField = document.createElement('input');
                sessionIdField.type = 'hidden';
                sessionIdField.name = 'session_id';
                sessionIdField.value = sessionId;
                form.appendChild(sessionIdField);

                // Add appointment ID
                const appointmentIdField = document.createElement('input');
                appointmentIdField.type = 'hidden';
                appointmentIdField.name = 'appointment_id';
                appointmentIdField.value = appointmentId;
                form.appendChild(appointmentIdField);

                // Add treatment plan fields
                const travelRestrictionsField = document.createElement('input');
                travelRestrictionsField.type = 'hidden';
                travelRestrictionsField.name = 'travel_restrictions';
                travelRestrictionsField.value = travelRestrictions;
                form.appendChild(travelRestrictionsField);

                const treatmentDescriptionField = document.createElement('input');
                treatmentDescriptionField.type = 'hidden';
                treatmentDescriptionField.name = 'treatment_description';
                treatmentDescriptionField.value = treatmentDescription;
                form.appendChild(treatmentDescriptionField);

                const estimatedBudgetField = document.createElement('input');
                estimatedBudgetField.type = 'hidden';
                estimatedBudgetField.name = 'estimated_budget';
                estimatedBudgetField.value = estimatedBudget;
                form.appendChild(estimatedBudgetField);

                const estimatedDurationField = document.createElement('input');
                estimatedDurationField.type = 'hidden';
                estimatedDurationField.name = 'estimated_duration';
                estimatedDurationField.value = estimatedDuration;
                form.appendChild(estimatedDurationField);

                // Add to document and submit
                document.body.appendChild(form);

                // Show success message while form is submitting
                showSuccessAlert(appointmentId, 'Treatment plan created successfully!');
                showToast('Treatment plan created!');

                // Submit the form
                form.submit();
            });
        });

        // Handle edit treatment plan button click
        document.addEventListener('click', function(event) {
            if (event.target && event.target.id && event.target.id.startsWith('editTreatmentPlanBtn-')) {
                const appointmentId = event.target.dataset.appointmentId;
                const sessionId = event.target.dataset.sessionId || '';
                currentAppointmentId = appointmentId;
                currentSessionId = sessionId;

                // Find the treatment details container
                const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
                if (!sessionDetails) {
                    console.error(`Session details not found for appointment ${appointmentId}`);
                    return;
                }

                const treatmentDetailsContainer = sessionDetails.querySelector('.treatment-details');

                if (treatmentDetailsContainer) {
                    // Extract current values
                    const travelRestrictions = treatmentDetailsContainer.querySelector('p:nth-of-type(1)').textContent.split(':')[1].trim();
                    const treatmentDescription = treatmentDetailsContainer.querySelector('p:nth-of-type(2)').textContent.split(':')[1].trim();
                    const estimatedBudget = treatmentDetailsContainer.querySelector('p:nth-of-type(3)').textContent.split(':')[1].trim().replace('$', '').replace(',', '');
                    const estimatedDuration = treatmentDetailsContainer.querySelector('p:nth-of-type(4)').textContent.split(':')[1].trim().split(' ')[0];

                    // Populate the edit form
                    document.getElementById('editTravelRestrictions').value = travelRestrictions;
                    document.getElementById('editTreatmentDescription').value = treatmentDescription;
                    document.getElementById('editEstimatedBudget').value = estimatedBudget;
                    document.getElementById('editEstimatedDuration').value = estimatedDuration;

                    // Show modal
                    const editTreatmentModal = document.getElementById('editTreatmentModal');
                    editTreatmentModal.style.display = 'block';
                }
            }
        });

        // Handle medical test request
        document.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('request-medical-btn')) {
                const appointmentId = event.target.dataset.appointmentId;
                const sessionId = event.target.dataset.sessionId || '';
                currentAppointmentId = appointmentId;
                currentSessionId = sessionId;

                // Clear previous selections
                const testTypeField = document.getElementById('testType');
                const testDescriptionField = document.getElementById('testDescription');
                const testUrgencyField = document.getElementById('testUrgency');
                const testRequiredFastingField = document.getElementById('testRequiredFasting');

                if (testTypeField) testTypeField.value = '';
                if (testDescriptionField) testDescriptionField.value = '';
                if (testUrgencyField) testUrgencyField.value = 'routine';
                if (testRequiredFastingField) testRequiredFastingField.value = 'no';

                // Show modal
                const medicalTestsModal = document.getElementById('medicalTestsModal');
                if (medicalTestsModal) medicalTestsModal.style.display = 'block';
            }
        });

        // Handle treatment cancellation request
        document.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('cancel-treatment-btn')) {
                const appointmentId = event.target.dataset.appointmentId;
                const sessionId = event.target.dataset.sessionId || '';
                currentAppointmentId = appointmentId;
                currentSessionId = sessionId;

                // Clear previous input
                const cancelReasonField = document.getElementById('cancellationReason');
                if (cancelReasonField) cancelReasonField.value = '';

                // Show modal
                const cancelTreatmentModal = document.getElementById('cancelTreatmentModal');
                if (cancelTreatmentModal) cancelTreatmentModal.style.display = 'block';
            }
        });

        // Close modals
        document.querySelectorAll('.close-btn').forEach(element => {
            element.addEventListener('click', function() {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
            });
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Submit Medical Test Request
        const submitMedicalTestBtn = document.getElementById('submitMedicalTest');
        if (submitMedicalTestBtn) {
            submitMedicalTestBtn.addEventListener('click', function() {
                const testTypeField = document.getElementById('testType');
                const testDescriptionField = document.getElementById('testDescription');
                const testUrgencyField = document.getElementById('testUrgency');
                const testRequiredFastingField = document.getElementById('testRequiredFasting');

                if (!testTypeField || !testDescriptionField || !testUrgencyField || !testRequiredFastingField) {
                    console.error('Test form fields not found');
                    return;
                }

                const testType = testTypeField.value;
                const testDescription = testDescriptionField.value;
                const testUrgency = testUrgencyField.value;
                const testRequiredFasting = testRequiredFastingField.value;

                if (!testType) {
                    alert('Please select a test type');
                    return;
                }

                if (!testDescription) {
                    alert('Please provide a test description');
                    return;
                }

                // Create form to submit data to the server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${basePath}/vpdoctor/requestMedicalTests`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = 'csrf_token';
                csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
                form.appendChild(csrfField);

                // Add session ID
                const sessionIdField = document.createElement('input');
                sessionIdField.type = 'hidden';
                sessionIdField.name = 'session_id';
                sessionIdField.value = currentSessionId;
                form.appendChild(sessionIdField);

                // Add appointment ID
                const appointmentIdField = document.createElement('input');
                appointmentIdField.type = 'hidden';
                appointmentIdField.name = 'appointment_id';
                appointmentIdField.value = currentAppointmentId;
                form.appendChild(appointmentIdField);

                // Add patient ID (from the patient info in the session)
                const patientIdField = document.createElement('input');
                patientIdField.type = 'hidden';
                patientIdField.name = 'patient_id';
                // Try to get patient ID from the session details
                const sessionDetails = document.getElementById(`session-details-${currentAppointmentId}`);
                let patientId = '';
                if (sessionDetails) {
                    const patientIdText = sessionDetails.querySelector('.doctor-info p:nth-child(2)')?.textContent || '';
                    patientId = patientIdText.match(/Patient ID: (\w+-\d+|\d+)/)?.[1] || '';
                }
                patientIdField.value = patientId;
                form.appendChild(patientIdField);

                // Add test details
                const testTypeInputField = document.createElement('input');
                testTypeInputField.type = 'hidden';
                testTypeInputField.name = 'test_type';
                testTypeInputField.value = testType;
                form.appendChild(testTypeInputField);

                const testDescInputField = document.createElement('input');
                testDescInputField.type = 'hidden';
                testDescInputField.name = 'test_description';
                testDescInputField.value = testDescription;
                form.appendChild(testDescInputField);

                const fastingField = document.createElement('input');
                fastingField.type = 'hidden';
                fastingField.name = 'requires_fasting';
                fastingField.value = testRequiredFasting;
                form.appendChild(fastingField);

                const urgencyField = document.createElement('input');
                urgencyField.type = 'hidden';
                urgencyField.name = 'urgency';
                urgencyField.value = testUrgency;
                form.appendChild(urgencyField);

                // Show visual feedback first
                showSuccessAlert(currentAppointmentId, `Medical test requested: ${testType} (${testUrgency})`);
                showToast('Medical test requested successfully');

                // Close the modal
                const medicalTestsModal = document.getElementById('medicalTestsModal');
                if (medicalTestsModal) medicalTestsModal.style.display = 'none';

                // Add to document and submit
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Confirm Cancel Treatment
        const confirmCancelBtn = document.getElementById('confirmCancelTreatment');
        if (confirmCancelBtn) {
            confirmCancelBtn.addEventListener('click', function() {
                const cancelReasonField = document.getElementById('cancellationReason');
                if (!cancelReasonField) {
                    console.error('Cancellation reason field not found');
                    return;
                }

                const cancelReason = cancelReasonField.value;

                if (!cancelReason) {
                    alert('Please provide a reason for cancellation');
                    return;
                }

                // Create form to submit data to the server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${basePath}/vpdoctor/cancelTreatment`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = 'csrf_token';
                csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
                form.appendChild(csrfField);

                // Add session ID
                const sessionIdField = document.createElement('input');
                sessionIdField.type = 'hidden';
                sessionIdField.name = 'session_id';
                sessionIdField.value = currentSessionId;
                form.appendChild(sessionIdField);

                // Add appointment ID
                const appointmentIdField = document.createElement('input');
                appointmentIdField.type = 'hidden';
                appointmentIdField.name = 'appointment_id';
                appointmentIdField.value = currentAppointmentId;
                form.appendChild(appointmentIdField);

                // Add cancellation reason
                const reasonField = document.createElement('input');
                reasonField.type = 'hidden';
                reasonField.name = 'cancel_reason';
                reasonField.value = cancelReason;
                form.appendChild(reasonField);

                // Update the UI first to give immediate feedback
                const sessionDetails = document.getElementById(`session-details-${currentAppointmentId}`);
                if (sessionDetails) {
                    const sessionStatus = sessionDetails.querySelector('.session-status');
                    if (sessionStatus) {
                        sessionStatus.textContent = 'Cancelled';
                        sessionStatus.style.backgroundColor = '#f44336';
                    }

                    // Add alert box explanation
                    const alertBox = document.createElement('div');
                    alertBox.className = 'alert-box alert-warning';
                    alertBox.innerHTML = `
                        <i class="ri-close-circle-line"></i>
                        <p>Treatment cancelled: ${cancelReason}</p>
                    `;

                    // Add to the beginning of the session body
                    const sessionBody = sessionDetails.querySelector('.session-body');
                    if (sessionBody) {
                        sessionBody.insertBefore(alertBox, sessionBody.firstChild);
                    }
                }

                // Update appointment card status too
                const appointmentCard = document.querySelector(`.appointment-card[data-appointment-id="${currentAppointmentId}"]`);
                if (appointmentCard) {
                    const statusSpan = appointmentCard.querySelector('.status');
                    if (statusSpan) {
                        statusSpan.textContent = 'Cancelled';
                        statusSpan.className = 'status canceled';
                    }
                }

                showToast('Treatment cancelled');

                // Close the modal
                const cancelTreatmentModal = document.getElementById('cancelTreatmentModal');
                if (cancelTreatmentModal) cancelTreatmentModal.style.display = 'none';

                // Add to document and submit
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Update Treatment Plan
        const updateTreatmentPlanBtn = document.getElementById('updateTreatmentPlan');
        if (updateTreatmentPlanBtn) {
            updateTreatmentPlanBtn.addEventListener('click', function() {
                const travelRestrictionsField = document.getElementById('editTravelRestrictions');
                const treatmentDescriptionField = document.getElementById('editTreatmentDescription');
                const estimatedBudgetField = document.getElementById('editEstimatedBudget');
                const estimatedDurationField = document.getElementById('editEstimatedDuration');

                if (!travelRestrictionsField || !treatmentDescriptionField || !estimatedBudgetField || !estimatedDurationField) {
                    console.error('Treatment plan fields not found');
                    return;
                }

                const travelRestrictions = travelRestrictionsField.value;
                const treatmentDescription = treatmentDescriptionField.value;
                const estimatedBudget = estimatedBudgetField.value;
                const estimatedDuration = estimatedDurationField.value;

                if (!treatmentDescription) {
                    alert('Please provide a treatment description');
                    return;
                }

                if (!estimatedBudget) {
                    alert('Please provide an estimated budget');
                    return;
                }

                if (!estimatedDuration) {
                    alert('Please provide an estimated duration');
                    return;
                }

                // Create form to submit data to the server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${basePath}/vpdoctor/updateTreatmentPlan`;
                form.style.display = 'none';

                // Add CSRF token
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = 'csrf_token';
                csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
                form.appendChild(csrfField);

                // Add session ID
                const sessionIdField = document.createElement('input');
                sessionIdField.type = 'hidden';
                sessionIdField.name = 'session_id';
                sessionIdField.value = currentSessionId;
                form.appendChild(sessionIdField);

                // Add appointment ID
                const appointmentIdField = document.createElement('input');
                appointmentIdField.type = 'hidden';
                appointmentIdField.name = 'appointment_id';
                appointmentIdField.value = currentAppointmentId;
                form.appendChild(appointmentIdField);

                // Add treatment plan fields
                const travelRestrictionsInputField = document.createElement('input');
                travelRestrictionsInputField.type = 'hidden';
                travelRestrictionsInputField.name = 'travel_restrictions';
                travelRestrictionsInputField.value = travelRestrictions;
                form.appendChild(travelRestrictionsInputField);

                const treatmentDescriptionInputField = document.createElement('input');
                treatmentDescriptionInputField.type = 'hidden';
                treatmentDescriptionInputField.name = 'treatment_description';
                treatmentDescriptionInputField.value = treatmentDescription;
                form.appendChild(treatmentDescriptionInputField);

                const estimatedBudgetInputField = document.createElement('input');
                estimatedBudgetInputField.type = 'hidden';
                estimatedBudgetInputField.name = 'estimated_budget';
                estimatedBudgetInputField.value = estimatedBudget;
                form.appendChild(estimatedBudgetInputField);

                const estimatedDurationInputField = document.createElement('input');
                estimatedDurationInputField.type = 'hidden';
                estimatedDurationInputField.name = 'estimated_duration';
                estimatedDurationInputField.value = estimatedDuration;
                form.appendChild(estimatedDurationInputField);

                // Update the UI first
                const sessionDetails = document.getElementById(`session-details-${currentAppointmentId}`);
                if (sessionDetails) {
                    const treatmentDetails = sessionDetails.querySelector('.treatment-details');
                    if (treatmentDetails) {
                        const paragraphs = treatmentDetails.querySelectorAll('p');
                        paragraphs[0].innerHTML = `<strong>Travel Restrictions:</strong> ${travelRestrictions}`;
                        paragraphs[1].innerHTML = `<strong>Treatment Description:</strong> ${treatmentDescription}`;
                        paragraphs[2].innerHTML = `<strong>Estimated Budget:</strong> ${Number(estimatedBudget).toLocaleString()}`;
                        paragraphs[3].innerHTML = `<strong>Estimated Duration:</strong> ${estimatedDuration} days`;
                    }

                    // Add success message
                    showSuccessAlert(currentAppointmentId, 'Treatment plan updated successfully!');
                }

                showToast('Treatment plan updated successfully');

                // Close the modal
                const editTreatmentModal = document.getElementById('editTreatmentModal');
                if (editTreatmentModal) editTreatmentModal.style.display = 'none';

                // Add to document and submit
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Close appointment details modal
        document.getElementById('closeAppointmentDetails').addEventListener('click', function() {
            document.getElementById('appointmentDetailsModal').style.display = 'none';
        });
    });

    // Helper functions for UI interactions

    // Complete appointment
    function completeAppointment(appointmentId) {
        if (confirm('Are you sure you want to mark this appointment as completed?')) {
            submitAppointmentAction(appointmentId, 'complete');
        }
    }

    // Cancel appointment
    function cancelAppointment(appointmentId) {
        const reason = prompt('Please provide a reason for cancellation:');
        if (reason) {
            submitAppointmentAction(appointmentId, 'cancel', {
                reason: reason
            });
        }
    }

    // Submit appointment action
    function submitAppointmentAction(appointmentId, action, extraData = {}) {
        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('action', action);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]')?.value || '');

        // Add any extra data
        for (const key in extraData) {
            formData.append(key, extraData[key]);
        }

        fetch(`${basePath}/vpdoctor/appointment-action`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Action completed successfully');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
    }

    // Helper functions
    function showSuccessAlert(appointmentId, message) {
        const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
        if (!sessionDetails) return;

        const alertBox = document.createElement('div');
        alertBox.className = 'alert-box alert-success';
        alertBox.innerHTML = `
            <i class="ri-check-line"></i>
            <p>${message}</p>
        `;

        // Find where to insert the alert
        const sessionBody = sessionDetails.querySelector('.session-body');
        if (!sessionBody) return;

        const existingAlerts = sessionBody.querySelectorAll('.alert-box');
        if (existingAlerts.length > 0) {
            sessionBody.insertBefore(alertBox, existingAlerts[0]);
        } else {
            sessionBody.insertBefore(alertBox, sessionBody.firstChild);
        }

        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transition = 'opacity 0.5s';
            setTimeout(() => alertBox.remove(), 500);
        }, 5000);
    }

    function showToast(message, type = 'success') {
        // Check if toast container exists, if not create it
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '1000';
            document.body.appendChild(toastContainer);
        }

        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = message;

        // Style based on type
        if (type === 'success') {
            toast.style.backgroundColor = '#4CAF50';
        } else if (type === 'error') {
            toast.style.backgroundColor = '#F44336';
        } else if (type === 'warning') {
            toast.style.backgroundColor = '#FF9800';
        }

        toast.style.color = 'white';
        toast.style.padding = '12px 20px';
        toast.style.marginBottom = '10px';
        toast.style.borderRadius = '4px';
        toast.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        toast.style.minWidth = '250px';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        toast.style.transition = 'opacity 0.3s, transform 0.3s';

        // Add to container
        toastContainer.appendChild(toast);

        // Show after a brief delay (to allow transition to work)
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);

        // Remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
</script>