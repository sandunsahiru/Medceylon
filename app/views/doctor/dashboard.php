<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<head>
    <!-- Add the external CSS file reference -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/doctor-dashboard.css">
</head>

<main class="main-content">
    <header class="top-bar">
        <h1>Dashboard</h1>
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

    <!-- Appointments Section -->
    <section class="appointments-section">
        <h2>All Appointments</h2>

        <?php if (!empty($appointments)): ?>
            <div class="appointments-list">
                <?php foreach ($appointments as $appointment): ?>
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
                                        <?php if ($appointment['consultation_type'] === 'Online'): ?>
                                            <div class="meet-link-container">
                                                <p><strong>Online Meeting:</strong></p>
                                                <?php
                                                // Use default meet link if not provided
                                                $meetLink = !empty($appointment['meet_link'])
                                                    ? $appointment['meet_link']
                                                    : 'https://meet.google.com/dyt-pdtg-xmy'; // Default link
                                                ?>
                                                <a href="<?php echo htmlspecialchars($meetLink); ?>" target="_blank" class="meet-link-btn">
                                                    <i class="ri-video-chat-line"></i> Join Google Meet
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>

                            <!-- Doctor Notes Section -->
                            <div class="session-details-container">
                                <h3>Medical Notes</h3>
                                <textarea id="doctor-notes-<?php echo $appointment['appointment_id']; ?>" class="doctor-notes" placeholder="Enter your medical notes for this patient..."><?php echo htmlspecialchars($appointment['doctor_notes'] ?? ''); ?></textarea>
                                <button class="action-btn primary save-notes-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                    <i class="ri-save-line"></i> Save Notes
                                </button>
                            </div>

                            <!-- Specialist Booking Section - Shows button to all-doctors page if no specialist is booked -->
                            <?php if (!(isset($appointment['specialist_booked']) && $appointment['specialist_booked'])): ?>
                                <div class="session-details-container">
                                    <h3>Specialist Referral</h3>
                                    <p>Refer this patient to a specialist for further consultation.</p>
                                    <a href="<?php echo $basePath; ?>/doctor/all-doctors?session_id=<?php echo $appointment['session_id'] ?? ''; ?>&patient_id=<?php echo $appointment['patient_id']; ?>" class="full-width-button find-specialist-btn">
                                        <i class="ri-user-star-line"></i> Find and Book Specialist
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Specialist Information (if already booked) -->
                                <div class="session-details-container">
                                    <h3>Specialist Information</h3>
                                    <div class="doctor-card">
                                        <div class="doctor-avatar">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div class="doctor-info">
                                            <h3>Dr. <?php echo htmlspecialchars($appointment['specialist_name'] ?? 'Specialist Name'); ?></h3>
                                            <p><strong><?php echo htmlspecialchars($appointment['specialist_specialty'] ?? 'Specialty'); ?></strong></p>
                                            <p><strong>Hospital:</strong> <?php echo htmlspecialchars($appointment['specialist_hospital'] ?? 'Hospital Name'); ?></p>

                                            <div class="appointment-meta">
                                                <div class="meta-item">
                                                    <i class="ri-calendar-line"></i>
                                                    <span><?php echo $appointment['specialist_date'] ? date('d/m/Y', strtotime($appointment['specialist_date'])) : 'Date not set'; ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-time-line"></i>
                                                    <span><?php echo $appointment['specialist_time'] ? date('H:i', strtotime($appointment['specialist_time'])) : 'Time not set'; ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-user-location-line"></i>
                                                    <span>Mode: <?php echo htmlspecialchars($appointment['specialist_mode'] ?? 'In-Person'); ?></span>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="specialist-notes-<?php echo $appointment['appointment_id']; ?>">Notes for Specialist</label>
                                                <textarea id="specialist-notes-<?php echo $appointment['appointment_id']; ?>" class="specialist-notes" placeholder="Enter notes for the specialist..."><?php echo htmlspecialchars($appointment['specialist_notes'] ?? ''); ?></textarea>
                                                <button class="action-btn primary save-specialist-notes-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
                                                    <i class="ri-save-line"></i> Save Notes for Specialist
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Treatment Plan Section -->
                            <?php if (isset($appointment['specialist_booked']) && $appointment['specialist_booked'] && !(isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created'])): ?>
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
                            <?php elseif (isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created']): ?>
                                <!-- Treatment Plan Info (if already created) -->
                                <div class="session-details-container">
                                    <h3>Treatment Information</h3>
                                    <div class="treatment-details">
                                        <p><strong>Travel Restrictions:</strong> <?php echo htmlspecialchars($appointment['travel_restrictions'] ?? 'Can travel, but avoid high altitudes and long distance journeys.'); ?></p>
                                        <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($appointment['treatment_description'] ?? 'Routine checkups and medication management for hypertension and early signs of cardiovascular disease.'); ?></p>
                                        <p><strong>Estimated Budget:</strong> $<?php echo htmlspecialchars($appointment['estimated_budget'] ?? '3,900'); ?></p>
                                        <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($appointment['estimated_duration'] ?? '7'); ?> days</p>
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
                <p>No appointments scheduled</p>
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
                <label for="editEstimatedBudget">Estimated Budget (LKR)</label>
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

<!-- Add the variable for base path and include the JS file at the end of the document -->
<script>
    const basePath = '<?php echo $basePath; ?>';

    // Function to handle specialist booking updates
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're returning from a successful specialist booking
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('specialist_booked') && urlParams.has('appointment_id')) {
            const appointmentId = urlParams.get('appointment_id');
            const specialistBooked = urlParams.get('specialist_booked');

            if (specialistBooked === 'true' && appointmentId) {
                // Update the UI to reflect that a specialist has been booked
                updateSpecialistBookingStatus(appointmentId);

                // Show a success message
                alert('Specialist appointment booked successfully!');

                // Remove the query parameters to prevent refresh issues
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    });

    // Function to update UI after specialist booking
    function updateSpecialistBookingStatus(appointmentId) {
        // Find the appropriate session container
        const sessionContainer = document.getElementById(`session-details-${appointmentId}`);
        if (sessionContainer) {
            // Update progress steps
            const specialistStep = sessionContainer.querySelector('.step-item:nth-child(2)');
            if (specialistStep) {
                specialistStep.classList.remove('active');
                specialistStep.classList.add('completed');
                specialistStep.querySelector('.step-circle i').className = 'ri-check-line';
            }

            // Update progress bar
            const progressBar = sessionContainer.querySelector('.step-progress-bar');
            if (progressBar) {
                const currentWidth = parseInt(progressBar.style.width) || 25;
                progressBar.style.width = Math.min(currentWidth + 25, 100) + '%';
            }

            // Update treatment plan step to active
            const treatmentPlanStep = sessionContainer.querySelector('.step-item:nth-child(3)');
            if (treatmentPlanStep) {
                treatmentPlanStep.classList.add('active');
            }

            // Refresh the page to get updated session data
            // setTimeout(() => window.location.reload(), 2000);
        }
    }
</script>
<script src="<?php echo $basePath; ?>/public/assets/js/doctor-dashboard.js"></script>