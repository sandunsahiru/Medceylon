<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

<head>
    <!-- Add the external CSS file reference -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/doctor-dashboard.css">
</head>

<main class="main-content">
    <header class="top-bar">
        <a href="<?php echo $basePath; ?>/vpdoctor/dashboard" class="back-button">
            <i class="ri-arrow-left-line"></i> Back to Dashboard
        </a>
        <h1>Patient Session</h1>
        <div class="header-right">
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <div class="session-container">
        <!-- Patient Information -->
        <div class="session-details-container">
            <h3>Patient Information</h3>
            <div class="doctor-card">
                <div class="doctor-avatar">
                    <i class="ri-user-line"></i>
                </div>
                <div class="doctor-info">
                    <h3><?php echo htmlspecialchars($patientInfo['first_name'] . ' ' . $patientInfo['last_name']); ?></h3>
                    <p>Patient ID: <?php echo htmlspecialchars($patientInfo['user_id'] ?? 'P-'.rand(10000,99999)); ?></p>
                    
                    <?php if (isset($patientInfo['gender']) || isset($patientInfo['date_of_birth'])): ?>
                    <div class="patient-details">
                        <?php if (isset($patientInfo['gender'])): ?>
                        <span>Gender: <?php echo htmlspecialchars($patientInfo['gender']); ?></span>
                        <?php endif; ?>
                        
                        <?php if (isset($patientInfo['date_of_birth'])): ?>
                        <span>Age: <?php echo isset($patientInfo['age']) ? $patientInfo['age'] : ''; ?> years</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="appointment-meta">
                        <div class="meta-item">
                            <i class="ri-mail-line"></i>
                            <span><?php echo htmlspecialchars($patientInfo['email'] ?? 'No email provided'); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-phone-line"></i>
                            <span><?php echo htmlspecialchars($patientInfo['phone_number'] ?? 'No phone provided'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Session Progress -->
        <div class="session-details-container">
            <h3>Treatment Progress</h3>
            <div class="step-progress-container">
                <div class="step-progress-bar" style="width: <?php 
                    // Calculate progress width based on session data
                    $progress = 25; // Assuming general doctor appointment is completed
                    if ($sessionData['specialistBooked']) $progress += 25;
                    if ($sessionData['treatmentPlanCreated']) $progress += 25;
                    if ($sessionData['transportBooked']) $progress += 25;
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
                <div class="step-item <?php echo $sessionData['specialistBooked'] ? 'completed' : 'active'; ?>">
                    <div class="step-circle">
                        <?php if ($sessionData['specialistBooked']): ?>
                            <i class="ri-check-line"></i>
                        <?php else: ?>
                            <i class="ri-user-star-line"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-text">Specialist Doctor</div>
                </div>
                
                <!-- Step 3: Treatment Plan -->
                <div class="step-item <?php 
                    if ($sessionData['treatmentPlanCreated']) echo 'completed';
                    elseif ($sessionData['specialistBooked']) echo 'active';
                ?>">
                    <div class="step-circle">
                        <?php if ($sessionData['treatmentPlanCreated']): ?>
                            <i class="ri-check-line"></i>
                        <?php else: ?>
                            <i class="ri-file-list-line"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-text">Treatment Plan</div>
                </div>
                
                <!-- Step 4: Travel & Accommodation -->
                <div class="step-item <?php 
                    if ($sessionData['transportBooked']) echo 'completed';
                    elseif ($sessionData['treatmentPlanCreated']) echo 'active';
                ?>">
                    <div class="step-circle">
                        <?php if ($sessionData['transportBooked']): ?>
                            <i class="ri-check-line"></i>
                        <?php else: ?>
                            <i class="ri-building-line"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-text">Travel & Accommodation</div>
                </div>
            </div>
        </div>

        <!-- General Doctor Referral Information -->
        <?php if ($sessionData['generalDoctorBooked'] && !empty($sessionData['generalDoctor'])): ?>
        <div class="session-details-container">
            <h3>Referring Doctor Information</h3>
            <div class="doctor-card">
                <div class="doctor-avatar">
                    <i class="ri-user-star-line"></i>
                </div>
                <div class="doctor-info">
                    <h3>Dr. <?php echo htmlspecialchars($sessionData['generalDoctor']['name']); ?></h3>
                    <p><strong><?php echo htmlspecialchars($sessionData['generalDoctor']['specialty']); ?></strong></p>
                    
                    <div class="appointment-meta">
                        <div class="meta-item">
                            <i class="ri-calendar-line"></i>
                            <span>Appointment: <?php echo date('d/m/Y', strtotime($sessionData['generalDoctor']['appointmentDate'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="ri-user-location-line"></i>
                            <span>Mode: <?php echo htmlspecialchars($sessionData['generalDoctor']['appointmentMode']); ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($sessionData['general_doctor_notes'])): ?>
                    <div class="form-group">
                        <label>Referral Notes</label>
                        <div class="notes-display">
                            <?php echo nl2br(htmlspecialchars($sessionData['general_doctor_notes'])); ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert-box alert-info">
                        <i class="ri-information-line"></i>
                        <p>No referral notes provided by the general doctor.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Specialist Notes Section -->
        <div class="session-details-container">
            <h3>Specialist Notes</h3>
            <form method="POST" action="<?php echo $basePath; ?>/vpdoctor/saveSpecialistNotes">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
                
                <div class="form-group">
                    <textarea name="specialist_notes" class="form-control specialist-notes" rows="5" placeholder="Enter your specialist notes for this patient..."><?php echo htmlspecialchars($sessionData['specialist_notes'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="action-btn primary">
                    <i class="ri-save-line"></i> Save Notes
                </button>
            </form>
        </div>
        
        <!-- Treatment Plan Section -->
        <?php if (!$sessionData['treatmentPlanCreated']): ?>
        <div class="session-details-container">
            <h3>Create Treatment Plan</h3>
            <form method="POST" action="<?php echo $basePath; ?>/vpdoctor/createTreatmentPlan">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
                <input type="hidden" name="appointment_id" value="<?php echo $appointmentData['appointment_id'] ?? 0; ?>">
                
                <div class="form-group">
                    <label for="travel_restrictions">Travel Restrictions</label>
                    <select name="travel_restrictions" id="travel_restrictions" class="form-control">
                        <option value="None">No Restrictions</option>
                        <option value="Can travel, but avoid high altitudes">Can travel, but avoid high altitudes</option>
                        <option value="Can travel, but need wheelchair assistance">Can travel, but need wheelchair assistance</option>
                        <option value="Can travel with medical escort only">Can travel with medical escort only</option>
                        <option value="Limited to short flights only">Limited to short flights only</option>
                        <option value="Not fit for air travel at this time">Not fit for air travel at this time</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="treatment_description">Treatment Description</label>
                    <textarea name="treatment_description" id="treatment_description" class="form-control" rows="4" placeholder="Describe the recommended treatment plan..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="estimated_budget">Estimated Budget (USD)</label>
                    <input type="number" name="estimated_budget" id="estimated_budget" class="form-control" placeholder="Enter estimated cost">
                </div>
                
                <div class="form-group">
                    <label for="estimated_duration">Estimated Duration (Days)</label>
                    <input type="number" name="estimated_duration" id="estimated_duration" class="form-control" placeholder="Enter estimated duration">
                </div>
                
                <button type="submit" class="full-width-button">
                    <i class="ri-file-list-3-line"></i> Create Treatment Plan
                </button>
            </form>
        </div>
        <?php else: ?>
        <!-- Treatment Plan Info (if already created) -->
        <div class="session-details-container">
            <h3>Treatment Information</h3>
            <div class="treatment-details">
                <p><strong>Travel Restrictions:</strong> <?php echo htmlspecialchars($sessionData['travelRestrictions'] ?? 'None'); ?></p>
                <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($sessionData['treatment_description'] ?? 'No description provided.'); ?></p>
                <p><strong>Estimated Budget:</strong> $<?php echo htmlspecialchars(number_format((float)$sessionData['estimatedBudget'], 2) ?? '0.00'); ?></p>
                <p><strong>Estimated Duration:</strong> <?php echo htmlspecialchars($sessionData['treatment_duration'] ?? '0'); ?> days</p>
            </div>
            
            <form method="POST" action="<?php echo $basePath; ?>/vpdoctor/updateTreatmentPlan">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
                <input type="hidden" name="appointment_id" value="<?php echo $appointmentData['appointment_id'] ?? 0; ?>">
                
                <button type="button" class="action-btn secondary edit-treatment-plan-btn">
                    <i class="ri-edit-line"></i> Edit Treatment Plan
                </button>
                
                <div class="edit-treatment-form" style="display: none; margin-top: 15px;">
                    <div class="form-group">
                        <label for="edit_travel_restrictions">Travel Restrictions</label>
                        <select name="travel_restrictions" id="edit_travel_restrictions" class="form-control">
                            <option value="None" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'None' ? 'selected' : ''; ?>>No Restrictions</option>
                            <option value="Can travel, but avoid high altitudes" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'Can travel, but avoid high altitudes' ? 'selected' : ''; ?>>Can travel, but avoid high altitudes</option>
                            <option value="Can travel, but need wheelchair assistance" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'Can travel, but need wheelchair assistance' ? 'selected' : ''; ?>>Can travel, but need wheelchair assistance</option>
                            <option value="Can travel with medical escort only" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'Can travel with medical escort only' ? 'selected' : ''; ?>>Can travel with medical escort only</option>
                            <option value="Limited to short flights only" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'Limited to short flights only' ? 'selected' : ''; ?>>Limited to short flights only</option>
                            <option value="Not fit for air travel at this time" <?php echo ($sessionData['travelRestrictions'] ?? '') == 'Not fit for air travel at this time' ? 'selected' : ''; ?>>Not fit for air travel at this time</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_treatment_description">Treatment Description</label>
                        <textarea name="treatment_description" id="edit_treatment_description" class="form-control" rows="4"><?php echo htmlspecialchars($sessionData['treatment_description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_estimated_budget">Estimated Budget (USD)</label>
                        <input type="number" name="estimated_budget" id="edit_estimated_budget" class="form-control" value="<?php echo htmlspecialchars($sessionData['estimatedBudget'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_estimated_duration">Estimated Duration (Days)</label>
                        <input type="number" name="estimated_duration" id="edit_estimated_duration" class="form-control" value="<?php echo htmlspecialchars($sessionData['treatment_duration'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="full-width-button">
                        <i class="ri-save-line"></i> Update Treatment Plan
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Medical Records Section -->
        <div class="session-details-container">
            <h3>Medical Records</h3>
            
            <?php if (!empty($medicalRecords)): ?>
            <div class="medical-records-list">
                <?php foreach ($medicalRecords as $record): ?>
                <div class="medical-record-item">
                    <div class="record-icon">
                        <i class="ri-file-text-line"></i>
                    </div>
                    <div class="record-info">
                        <h4><?php echo htmlspecialchars($record['report_name']); ?></h4>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($record['report_type']); ?></p>
                        <p><strong>Uploaded:</strong> <?php echo htmlspecialchars($record['upload_date']); ?></p>
                        <p><?php echo htmlspecialchars($record['description']); ?></p>
                        <a href="<?php echo $basePath . '/' . $record['file_path']; ?>" target="_blank" class="action-btn primary">
                            <i class="ri-download-line"></i> View Report
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert-box alert-info">
                <i class="ri-information-line"></i>
                <p>No medical records available for this patient.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="session-actions-container">
            <button class="action-btn primary request-medical-btn">
                <i class="ri-test-tube-line"></i> Request Medical Tests
            </button>
            
            <button class="action-btn secondary cancel-treatment-btn">
                <i class="ri-close-circle-line"></i> Cancel Treatment
            </button>
        </div>
    </div>
</main>

<!-- Medical Tests Request Modal -->
<div id="medicalTestsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Request Medical Tests</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form method="POST" action="<?php echo $basePath; ?>/vpdoctor/requestMedicalTests">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patientInfo['user_id']; ?>">
            <input type="hidden" name="appointment_id" value="<?php echo $appointmentData['appointment_id'] ?? 0; ?>">
            
            <div class="medical-tests-form">
                <div class="form-group">
                    <label for="test_type">Test Type</label>
                    <select name="test_type" id="test_type" class="form-control" required>
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
                    <label for="test_description">Test Description</label>
                    <textarea name="test_description" id="test_description" class="form-control" rows="3" placeholder="Provide specific instructions for the test..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="requires_fasting">Requires Fasting</label>
                    <select name="requires_fasting" id="requires_fasting" class="form-control">
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="urgency">Urgency</label>
                    <select name="urgency" id="urgency" class="form-control">
                        <option value="routine">Routine</option>
                        <option value="urgent">Urgent</option>
                        <option value="immediate">Immediate</option>
                    </select>
                </div>
                <button type="submit" class="full-width-button">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Treatment Modal -->
<div id="cancelTreatmentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Cancel Treatment</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form method="POST" action="<?php echo $basePath; ?>/vpdoctor/cancelTreatment">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
            <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
            <input type="hidden" name="appointment_id" value="<?php echo $appointmentData['appointment_id'] ?? 0; ?>">
            
            <div class="cancel-treatment-form">
                <div class="alert-box alert-warning">
                    <i class="ri-error-warning-line"></i>
                    <p>Are you sure you want to cancel this treatment? This action cannot be undone.</p>
                </div>
                <div class="form-group">
                    <label for="cancel_reason">Reason for Cancellation</label>
                    <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="3" placeholder="Please provide a reason for cancellation..." required></textarea>
                </div>
                <button type="submit" class="full-width-button">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal handling
        const medicalTestsModal = document.getElementById('medicalTestsModal');
        const cancelTreatmentModal = document.getElementById('cancelTreatmentModal');
        
        // Open medical tests modal
        document.querySelector('.request-medical-btn').addEventListener('click', function() {
            medicalTestsModal.style.display = 'block';
        });
        
        // Open cancel treatment modal
        document.querySelector('.cancel-treatment-btn').addEventListener('click', function() {
            cancelTreatmentModal.style.display = 'block';
        });
        
        // Toggle edit treatment plan form
        document.querySelector('.edit-treatment-plan-btn')?.addEventListener('click', function() {
            const editForm = document.querySelector('.edit-treatment-form');
            if (editForm) {
                editForm.style.display = editForm.style.display === 'none' ? 'block' : 'none';
                this.innerHTML = editForm.style.display === 'none' ? 
                    '<i class="ri-edit-line"></i> Edit Treatment Plan' : 
                    '<i class="ri-close-line"></i> Cancel Editing';
            }
        });
        
        // Close modals
        document.querySelectorAll('.close-btn').forEach(button => {
            button.addEventListener('click', function() {
                medicalTestsModal.style.display = 'none';
                cancelTreatmentModal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === medicalTestsModal) {
                medicalTestsModal.style.display = 'none';
            }
            if (event.target === cancelTreatmentModal) {
                cancelTreatmentModal.style.display = 'none';
            }
        });
        
        // Success and error messages
        <?php if (isset($_SESSION['success'])): ?>
        showToast('<?php echo $_SESSION['success']; ?>', 'success');
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
        showToast('<?php echo $_SESSION['error']; ?>', 'error');
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        // Toast notification function
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
    });
</script>