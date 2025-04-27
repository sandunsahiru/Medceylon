<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

<head>
    <!-- Add meta tag for base path that JavaScript can access -->
    <meta name="base-path" content="<?php echo $basePath; ?>">
    <!-- Add the external CSS file reference -->
    <!-- <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/session-details-fix.css"> -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/vpdoctor-dashboard.css">

    <style>
        /* Fix black color issue - more comprehensive fix */
        .session-body {
            background-color: #fff !important;
            color: #333 !important;
        }

        .step-progress-container {
            background-color: transparent !important;
        }

        .step-text {
            color: #666 !important;
        }

        /* Make sure all text in the session is visible */
        .session-body h3,
        .session-body p,
        .session-body label,
        .session-body .doctor-info h3,
        .session-body .doctor-info p,
        .session-body .meta-item span,
        .session-body .form-group label,
        .session-body .referral-notes,
        .session-body .treatment-details p,
        .session-body .checkbox-container label {
            color: #333 !important;
        }

        /* Form elements with proper color */
        .form-control,
        textarea.form-control,
        select.form-control,
        .doctor-notes {
            background-color: #fff !important;
            color: #333 !important;
        }

        /* Referral badge styling */
        .referral-badge {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* Travel restrictions checkbox style */
        .travel-restrictions-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
        }

        .checkbox-container label {
            color: #333 !important;
            margin-bottom: 0;
        }

        /* Deadline and vehicle selection styles */
        .additional-fields {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .additional-fields .form-group {
            flex: 1;
        }

        /* Override modal content colors */
        .modal-content .form-group label,
        .modal-content textarea,
        .modal-content select,
        .modal-content input,
        .modal-content p {
            color: #333 !important;
        }
    </style>
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

    <!-- CSRF Token Hidden Field -->
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

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
        <h2>Appointments</h2>

        <?php
        // Merge all appointments into a single array
        $allAppointments = [];
        if (!empty($referralAppointments)) {
            foreach ($referralAppointments as $appointment) {
                $appointment['is_referral'] = true;
                $allAppointments[] = $appointment;
            }
        }
        if (!empty($regularAppointments)) {
            foreach ($regularAppointments as $appointment) {
                $appointment['is_referral'] = false;
                $allAppointments[] = $appointment;
            }
        }

        // Sort by date and time if needed
        usort($allAppointments, function ($a, $b) {
            $dateA = strtotime($a['appointment_date'] . ' ' . $a['appointment_time']);
            $dateB = strtotime($b['appointment_date'] . ' ' . $b['appointment_time']);
            return $dateB - $dateA; // Descending (newest first)
        });
        ?>

        <?php if (!empty($allAppointments)): ?>
            <div class="appointments-list">
            <?php 
        $displayedIds = []; // Track already displayed appointments
        foreach ($allAppointments as $appointment): 
            // Skip if already displayed
            if (in_array($appointment['appointment_id'], $displayedIds)) {
                continue;
            }
            $displayedIds[] = $appointment['appointment_id'];
        ?>
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
                            <?php if ($appointment['is_referral']): ?>
                                <span class="referral-badge">Referral</span>
                            <?php endif; ?>
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

                            <!-- Referring Doctor Information - Only show if this is a referral appointment -->
                            <?php if ($appointment['is_referral'] && isset($appointment['referring_doctor_id']) && $appointment['referring_doctor_id']): ?>
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
                                <button class="action-btn primary save-notes-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
                                    <i class="ri-save-line"></i> Save Notes
                                </button>
                            </div>

                            <!-- Treatment Plan Section -->
                            <?php if (!(isset($appointment['treatment_plan_created']) && $appointment['treatment_plan_created'])): ?>
                                <div class="session-details-container">
                                    <h3>Create Treatment Plan</h3>
                                    <div class="treatment-plan-form">
                                        <div class="form-group">
                                            <label>Travel Restrictions</label>
                                            <div class="travel-restrictions-group" id="travel-restrictions-<?php echo $appointment['appointment_id']; ?>">
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="no-restrictions-<?php echo $appointment['appointment_id']; ?>" value="None">
                                                    <label for="no-restrictions-<?php echo $appointment['appointment_id']; ?>">No Restrictions</label>
                                                </div>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="high-altitude-<?php echo $appointment['appointment_id']; ?>" value="Can travel, but avoid high altitudes">
                                                    <label for="high-altitude-<?php echo $appointment['appointment_id']; ?>">Can travel, but avoid high altitudes</label>
                                                </div>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="wheelchair-<?php echo $appointment['appointment_id']; ?>" value="Can travel, but need wheelchair assistance">
                                                    <label for="wheelchair-<?php echo $appointment['appointment_id']; ?>">Can travel, but need wheelchair assistance</label>
                                                </div>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="escort-<?php echo $appointment['appointment_id']; ?>" value="Can travel with medical escort only">
                                                    <label for="escort-<?php echo $appointment['appointment_id']; ?>">Can travel with medical escort only</label>
                                                </div>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="short-flights-<?php echo $appointment['appointment_id']; ?>" value="Limited to short flights only">
                                                    <label for="short-flights-<?php echo $appointment['appointment_id']; ?>">Limited to short flights only</label>
                                                </div>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="no-air-travel-<?php echo $appointment['appointment_id']; ?>" value="Not fit for air travel at this time">
                                                    <label for="no-air-travel-<?php echo $appointment['appointment_id']; ?>">Not fit for air travel at this time</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="additional-fields">
                                            <div class="form-group">
                                                <label for="vehicle-type-<?php echo $appointment['appointment_id']; ?>">Required Vehicle Type</label>
                                                <select id="vehicle-type-<?php echo $appointment['appointment_id']; ?>" class="form-control">
                                                    <option value="Regular Vehicle">Regular Vehicle</option>
                                                    <option value="Wheelchair Accessible">Wheelchair Accessible</option>
                                                    <option value="Ambulance">Ambulance</option>
                                                    <option value="Medical Transport with Bed">Medical Transport with Bed</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="arrival-deadline-<?php echo $appointment['appointment_id']; ?>">Arrival Deadline in Sri Lanka</label>
                                                <input type="date" id="arrival-deadline-<?php echo $appointment['appointment_id']; ?>" class="form-control">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="treatment-description-<?php echo $appointment['appointment_id']; ?>">Treatment Description</label>
                                            <textarea id="treatment-description-<?php echo $appointment['appointment_id']; ?>" class="treatment-description form-control" rows="4" placeholder="Describe the recommended treatment plan..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="estimated-budget-<?php echo $appointment['appointment_id']; ?>">Estimated Budget (LKR)</label>
                                            <input type="number" id="estimated-budget-<?php echo $appointment['appointment_id']; ?>" class="estimated-budget form-control" placeholder="Enter estimated cost in Sri Lankan Rupees">
                                        </div>
                                        <div class="form-group">
                                            <label for="estimated-duration-<?php echo $appointment['appointment_id']; ?>">Estimated Duration (Days)</label>
                                            <input type="number" id="estimated-duration-<?php echo $appointment['appointment_id']; ?>" class="estimated-duration form-control" placeholder="Enter estimated duration">
                                        </div>
                                        <button class="full-width-button create-treatment-plan-btn" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
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
                                        <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($appointment['vehicle_type'] ?? 'Regular Vehicle'); ?></p>
                                        <p><strong>Arrival Deadline:</strong> <?php echo !empty($appointment['arrival_deadline']) ? date('d/m/Y', strtotime($appointment['arrival_deadline'])) : 'Not specified'; ?></p>
                                        <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($appointment['treatment_description'] ?? 'No description provided.'); ?></p>
                                        <p><strong>Estimated Budget:</strong> LKR <?php echo htmlspecialchars(number_format($appointment['estimated_budget'] ?? 0)); ?></p>
                                        <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($appointment['estimated_duration'] ?? '0'); ?> days</p>
                                    </div>

                                    <div class="action-buttons">
                                        <button class="action-btn secondary" id="editTreatmentPlanBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
                                            <i class="ri-edit-line"></i> Edit Treatment Plan
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Medical Request & Cancel Buttons -->
                            <div class="session-actions">
                                <button class="action-btn primary request-medical-btn" id="requestMedicalBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
                                    <i class="ri-test-tube-line"></i> Request Medical Tests
                                </button>
                                <button class="action-btn secondary cancel-treatment-btn" id="cancelTreatmentBtn-<?php echo $appointment['appointment_id']; ?>" data-appointment-id="<?php echo $appointment['appointment_id']; ?>" data-session-id="<?php echo $appointment['session_id'] ?? ''; ?>">
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
                <label>Travel Restrictions</label>
                <div class="travel-restrictions-group" id="editTravelRestrictions">
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-no-restrictions" value="None">
                        <label for="edit-no-restrictions">No Restrictions</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-high-altitude" value="Can travel, but avoid high altitudes">
                        <label for="edit-high-altitude">Can travel, but avoid high altitudes</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-wheelchair" value="Can travel, but need wheelchair assistance">
                        <label for="edit-wheelchair">Can travel, but need wheelchair assistance</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-escort" value="Can travel with medical escort only">
                        <label for="edit-escort">Can travel with medical escort only</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-short-flights" value="Limited to short flights only">
                        <label for="edit-short-flights">Limited to short flights only</label>
                    </div>
                    <div class="checkbox-container">
                        <input type="checkbox" id="edit-no-air-travel" value="Not fit for air travel at this time">
                        <label for="edit-no-air-travel">Not fit for air travel at this time</label>
                    </div>
                </div>
            </div>

            <div class="additional-fields">
                <div class="form-group">
                    <label for="editVehicleType">Required Vehicle Type</label>
                    <select id="editVehicleType" class="form-control">
                        <option value="Regular Vehicle">Regular Vehicle</option>
                        <option value="Wheelchair Accessible">Wheelchair Accessible</option>
                        <option value="Ambulance">Ambulance</option>
                        <option value="Medical Transport with Bed">Medical Transport with Bed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editArrivalDeadline">Arrival Deadline in Sri Lanka</label>
                    <input type="date" id="editArrivalDeadline" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="editTreatmentDescription">Treatment Description</label>
                <textarea id="editTreatmentDescription" class="form-control" rows="4" placeholder="Describe the recommended treatment plan..."></textarea>
            </div>
            <div class="form-group">
                <label for="editEstimatedBudget">Estimated Budget (LKR)</label>
                <input type="number" id="editEstimatedBudget" class="form-control" placeholder="Enter estimated cost in Sri Lankan Rupees">
            </div>
            <div class="form-group">
                <label for="editEstimatedDuration">Estimated Duration (Days)</label>
                <input type="number" id="editEstimatedDuration" class="form-control" placeholder="Enter estimated duration">
            </div>
            <button id="updateTreatmentPlan" class="full-width-button">Update Treatment Plan</button>
        </div>
    </div>
</div>

<!-- Make sure the script order in the footer is: -->
<script src="<?php echo $basePath; ?>/public/assets/js/view-details-fix.js"></script>
<script src="<?php echo $basePath; ?>/public/assets/js/vpdoctor-dashboard.js"></script>