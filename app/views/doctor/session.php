<script>
    document.addEventListener('DOMContentLoaded', function() {
    const basePath = '<?php echo $basePath; ?>';
    
    // Modal elements
    const referSpecialistModal = document.getElementById('referSpecialistModal');
    const treatmentPlanModal = document.getElementById('treatmentPlanModal');
    const editTreatmentPlanModal = document.getElementById('editTreatmentPlanModal');
    
    // Button elements
    const referToSpecialistBtn = document.getElementById('referToSpecialistBtn');
    const createTreatmentPlanBtn = document.getElementById('createTreatmentPlanBtn');
    const editTreatmentPlanBtn = document.getElementById('editTreatmentPlanBtn');
    
    // Show/hide modals
    if (referToSpecialistBtn) {
        referToSpecialistBtn.addEventListener('click', function() {
            referSpecialistModal.style.display = 'block';
        });
    }
    
    if (createTreatmentPlanBtn) {
        createTreatmentPlanBtn.addEventListener('click', function() {
            treatmentPlanModal.style.display = 'block';
        });
    }
    
    if (editTreatmentPlanBtn) {
        editTreatmentPlanBtn.addEventListener('click', function() {
            editTreatmentPlanModal.style.display = 'block';
        });
    }
    
    // Close buttons
    document.querySelectorAll('.close-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (referSpecialistModal) referSpecialistModal.style.display = 'none';
            if (treatmentPlanModal) treatmentPlanModal.style.display = 'none';
            if (editTreatmentPlanModal) editTreatmentPlanModal.style.display = 'none';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === referSpecialistModal) referSpecialistModal.style.display = 'none';
        if (event.target === treatmentPlanModal) treatmentPlanModal.style.display = 'none';
        if (event.target === editTreatmentPlanModal) editTreatmentPlanModal.style.display = 'none';
    });
    
    // Specialization change handler
    const specializationSelect = document.getElementById('specialization');
    const specialistDoctorSelect = document.getElementById('specialist_doctor'); // Renamed to avoid conflict
    
    if (specializationSelect && specialistDoctorSelect) {
        specializationSelect.addEventListener('change', function() {
            const specializationId = this.value;
            
            if (!specializationId) {
                specialistDoctorSelect.disabled = true;
                specialistDoctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                return;
            }
            
            // Enable and load doctors for this specialization
            specialistDoctorSelect.innerHTML = '<option value="">Loading doctors...</option>';
            
            fetch(`${basePath}/doctor/get-specialists-by-specialization?specialization_id=${specializationId}`)
                .then(response => response.json())
                .then(data => {
                    specialistDoctorSelect.innerHTML = '<option value="">Select Doctor</option>';
                    
                    if (data.length > 0) {
                        data.forEach(doctor => {
                            const option = document.createElement('option');
                            option.value = doctor.doctor_id;
                            option.textContent = `Dr. ${doctor.first_name} ${doctor.last_name}`;
                            specialistDoctorSelect.appendChild(option);
                        });
                        
                        specialistDoctorSelect.disabled = false;
                    } else {
                        specialistDoctorSelect.innerHTML = '<option value="">No doctors available</option>';
                        specialistDoctorSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching specialists:', error);
                    specialistDoctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
                });
        });
    }
    
    // Doctor selection to load available time slots
    const preferredDateInput = document.getElementById('preferred_date');
    const preferredTimeSelect = document.getElementById('preferred_time');
    
    if (specialistDoctorSelect && preferredDateInput && preferredTimeSelect) {
        const loadTimeSlots = function() {
            const doctorId = specialistDoctorSelect.value;
            const date = preferredDateInput.value;
            
            if (!doctorId || !date) {
                preferredTimeSelect.disabled = true;
                preferredTimeSelect.innerHTML = '<option value="">Select Time</option>';
                return;
            }
            
            // Create form data for post request
            const formData = new FormData();
            formData.append('doctor_id', doctorId);
            formData.append('date', date);
            
            // Fetch available time slots
            fetch(`${basePath}/doctor/get-time-slots`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                preferredTimeSelect.innerHTML = '<option value="">Select Time</option>';
                
                if (data.length > 0) {
                    data.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = slot.formatted_time;
                        preferredTimeSelect.appendChild(option);
                    });
                    
                    preferredTimeSelect.disabled = false;
                } else {
                    preferredTimeSelect.innerHTML = '<option value="">No time slots available</option>';
                    preferredTimeSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error fetching time slots:', error);
                preferredTimeSelect.innerHTML = '<option value="">Error loading time slots</option>';
            });
        };
        
        specialistDoctorSelect.addEventListener('change', loadTimeSlots);
        preferredDateInput.addEventListener('change', loadTimeSlots);
    }
});

    
    function viewMedicalHistory(patientId) {
        window.location.href = `${basePath}/doctor/patient-medical-history/${patientId}`;
    }
</script><?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<main class="main-content">
    <header class="top-bar">
        <h1>Patient Medical Session</h1>
        <div class="header-right">
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <?php if ($this->session->hasFlash('success')): ?>
        <div class="alert-box alert-success">
            <i class="ri-check-line"></i>
            <p><?php echo $this->session->getFlash('success'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($this->session->hasFlash('error')): ?>
        <div class="alert-box alert-warning">
            <i class="ri-error-warning-line"></i>
            <p><?php echo $this->session->getFlash('error'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($this->session->hasFlash('info')): ?>
        <div class="alert-box alert-info">
            <i class="ri-information-line"></i>
            <p><?php echo $this->session->getFlash('info'); ?></p>
        </div>
    <?php endif; ?>

    <!-- Patient Information Section -->
    <div class="patient-info-card">
        <div class="patient-avatar">
            <i class="ri-user-line"></i>
        </div>
        <div class="patient-details">
            <h2><?php echo htmlspecialchars($patientInfo['first_name'] . ' ' . $patientInfo['last_name']); ?></h2>
            <div class="patient-meta">
                <span><i class="ri-phone-line"></i> <?php echo htmlspecialchars($patientInfo['phone_number'] ?? 'N/A'); ?></span>
                <span><i class="ri-mail-line"></i> <?php echo htmlspecialchars($patientInfo['email'] ?? 'N/A'); ?></span>
                <span><i class="ri-calendar-line"></i> <?php echo isset($patientInfo['dob']) ? date('d/m/Y', strtotime($patientInfo['dob'])) : 'N/A'; ?></span>
                <span><i class="ri-user-settings-line"></i> <?php echo htmlspecialchars($patientInfo['gender'] ?? 'N/A'); ?></span>
            </div>
        </div>
        <div class="patient-actions">
            <button class="action-btn" onclick="viewMedicalHistory(<?php echo $patientInfo['user_id']; ?>)">
                <i class="ri-file-list-line"></i> Medical History
            </button>
            <a href="<?php echo $basePath; ?>/doctor/chat/patient/<?php echo $patientInfo['user_id']; ?>" class="action-btn">
                <i class="ri-message-3-line"></i> Chat
            </a>
        </div>
    </div>

    <!-- Medical Session Section -->
    <section class="medical-session">
        <div class="session-header">
            <h2>Ongoing Medical Session</h2>
            <div class="session-status"><?php echo htmlspecialchars($sessionData['status'] ?? 'Active'); ?></div>
        </div>
        <div class="session-body">
            <!-- Progress Steps -->
            <div class="step-progress-container">
                <div class="step-progress-bar" style="width: <?php 
                    // Calculate progress width
                    $progress = 0;
                    if ($sessionData['generalDoctorBooked']) $progress += 25;
                    if ($sessionData['specialistBooked']) $progress += 25;
                    if ($sessionData['treatmentPlanCreated']) $progress += 25;
                    if ($sessionData['transportBooked']) $progress += 15;
                    if ($sessionData['travelPlanSelected']) $progress += 10;
                    echo $progress . '%';
                ?>"></div>
                
                <!-- Step 1: General Doctor -->
                <div class="step-item <?php echo $sessionData['generalDoctorBooked'] ? 'completed' : 'active'; ?>">
                    <div class="step-circle">
                        <?php if ($sessionData['generalDoctorBooked']): ?>
                            <i class="ri-check-line"></i>
                        <?php else: ?>
                            <i class="ri-stethoscope-line"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-text">General Doctor</div>
                </div>
                
                <!-- Step 2: Specialist -->
                <div class="step-item <?php 
                    if ($sessionData['specialistBooked']) echo 'completed';
                    elseif ($sessionData['generalDoctorBooked']) echo 'active';
                ?>">
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
                
                <!-- Step 4: Hotel & Transport -->
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
                    <div class="step-text">Hotel & Transport</div>
                </div>
                
                <!-- Step 5: Travel Plan -->
                <div class="step-item <?php 
                    if ($sessionData['travelPlanSelected']) echo 'completed';
                    elseif ($sessionData['transportBooked']) echo 'active';
                ?>">
                    <div class="step-circle">
                        <?php if ($sessionData['travelPlanSelected']): ?>
                            <i class="ri-check-line"></i>
                        <?php else: ?>
                            <i class="ri-plane-line"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-text">Travel Plan</div>
                </div>
            </div>

            <!-- General Doctor Section - Show if this doctor is the general doctor for this session -->
            <?php if ($isGeneralDoctor && $sessionData['generalDoctorBooked'] && !$sessionData['specialistBooked']): ?>
            <div class="session-details-container">
                <h3>General Doctor Actions</h3>
                
                <div class="action-card">
                    <div class="action-description">
                        <h4>Current Appointment Information</h4>
                        <p><?php echo htmlspecialchars($appointmentData['reason_for_visit'] ?? 'No reason specified'); ?></p>
                        <?php if (isset($appointmentData['medical_history']) && !empty($appointmentData['medical_history'])): ?>
                            <p><strong>Medical History:</strong> <?php echo htmlspecialchars($appointmentData['medical_history']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Notes for Doctor -->
                <div class="notes-section">
                    <form action="<?php echo $basePath; ?>/doctor/session/save-notes" method="post">
                        <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
                        <input type="hidden" name="doctor_id" value="<?php echo $doctorInfo['doctor_id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <label for="doctor_notes">Medical Assessment Notes:</label>
                        <textarea id="doctor_notes" name="doctor_notes" placeholder="Enter your medical assessment notes here..."><?php echo htmlspecialchars($sessionData['general_doctor_notes'] ?? ''); ?></textarea>
                        
                        <button type="submit" class="full-width-button">
                            <i class="ri-save-line"></i> Save Notes
                        </button>
                    </form>
                </div>
                
                <div class="action-card">
                    <div class="action-description">
                        <h4>Refer to Specialist</h4>
                        <p>Refer this patient to a specialist doctor based on your diagnosis</p>
                    </div>
                    <button class="full-width-button" id="referToSpecialistBtn">
                        <i class="ri-user-star-line"></i> Refer to Specialist
                    </button>
                </div>
                
                <div class="alert-box alert-info">
                    <i class="ri-information-line"></i>
                    <p>You can refer this patient to a specialist after your consultation.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Specialist Doctor Section - Show if this doctor is the specialist doctor for this session -->
            <?php if ($isSpecialistDoctor && $sessionData['specialistBooked'] && !$sessionData['treatmentPlanCreated']): ?>
            <div class="session-details-container">
                <h3>Specialist Doctor Actions</h3>
                
                <?php if (isset($sessionData['general_doctor_notes']) && !empty($sessionData['general_doctor_notes'])): ?>
                <div class="alert-box alert-info">
                    <i class="ri-information-line"></i>
                    <p><strong>General Doctor Notes:</strong> <?php echo htmlspecialchars($sessionData['general_doctor_notes']); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="action-card">
                    <div class="action-description">
                        <h4>Create Treatment Plan</h4>
                        <p>Create a comprehensive treatment plan for this patient</p>
                    </div>
                    <button class="full-width-button" id="createTreatmentPlanBtn">
                        <i class="ri-file-list-3-line"></i> Create Treatment Plan
                    </button>
                </div>
                
                <?php if (isset($sessionData['referral_reason']) && !empty($sessionData['referral_reason'])): ?>
                <div class="action-card">
                    <div class="action-description">
                        <h4>Referral Reason</h4>
                        <p><?php echo htmlspecialchars($sessionData['referral_reason']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="alert-box alert-info">
                    <i class="ri-information-line"></i>
                    <p>Creating a treatment plan will help the patient plan their medical travel accordingly.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- General/Specialist Doctor View Only Section (If treatment plan is created) -->
            <?php if (($isGeneralDoctor || $isSpecialistDoctor) && $sessionData['treatmentPlanCreated']): ?>
            <div class="session-details-container">
                <h3>Treatment Plan Information</h3>
                
                <div class="info-card">
                    <h4>Treatment Details</h4>
                    <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($sessionData['diagnosis'] ?? 'Not specified'); ?></p>
                    <p><strong>Treatment Description:</strong> <?php echo htmlspecialchars($sessionData['treatment_description'] ?? 'Not specified'); ?></p>
                    <p><strong>Medications:</strong> <?php echo htmlspecialchars($sessionData['medications'] ?? 'None'); ?></p>
                    <p><strong>Travel Restrictions:</strong> <?php echo htmlspecialchars($sessionData['travelRestrictions'] ?? 'None specified'); ?></p>
                    <p><strong>Estimated Budget:</strong> <?php echo htmlspecialchars($sessionData['estimatedBudget'] ?? 'Not estimated'); ?></p>
                    <p><strong>Follow-up:</strong> <?php echo htmlspecialchars($sessionData['follow_up'] ?? 'Not specified'); ?></p>
                </div>
                
                <!-- Doctor actions for completed treatment plan -->
                <div class="action-card">
                    <div class="action-description">
                        <h4>Treatment Plan Management</h4>
                        <p>Update or modify the current treatment plan</p>
                    </div>
                    <div class="action-buttons-row">
                        <button class="action-btn secondary" id="editTreatmentPlanBtn">
                            <i class="ri-edit-2-line"></i> Edit Treatment Plan
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Medical Records Section -->
            <div class="session-details-container">
                <h3>Patient Medical Records</h3>
                
                <?php if (!empty($medicalRecords)): ?>
                <div class="medical-records">
                    <?php foreach ($medicalRecords as $record): ?>
                    <div class="record-item">
                        <span class="record-name"><?php echo htmlspecialchars($record['report_name']); ?></span>
                        <div class="record-actions">
                            <a href="<?php echo $basePath; ?>/public/uploads/medical-reports/<?php echo $record['file_path']; ?>" target="_blank">
                                <i class="ri-eye-line"></i> View
                            </a>
                            <a href="<?php echo $basePath; ?>/public/uploads/medical-reports/<?php echo $record['file_path']; ?>" download>
                                <i class="ri-download-line"></i> Download
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
            
            <!-- Session Management Actions -->
            <div class="session-details-container">
                <h3>Session Management</h3>
                
                <div class="action-buttons">
                    <?php if ($isGeneralDoctor || $isSpecialistDoctor): ?>
                    <!-- Google Meet Link for Online Appointments -->
                    <?php if (($isGeneralDoctor && 
                          $sessionData['generalDoctor']['appointmentMode'] == 'Online' && 
                          !empty($sessionData['generalDoctor']['meetLink'])) || 
                         ($isSpecialistDoctor && 
                          $sessionData['specialist']['appointmentMode'] == 'Online' && 
                          !empty($sessionData['specialist']['meetLink']))): ?>
                        
                        <?php 
                        $meetLink = '';
                        if ($isGeneralDoctor) {
                            $meetLink = $sessionData['generalDoctor']['meetLink'];
                        } else {
                            $meetLink = $sessionData['specialist']['meetLink'];
                        }
                        ?>
                        
                        <a href="<?php echo $meetLink; ?>" target="_blank" class="meet-link-btn">
                            <i class="ri-video-chat-line"></i> Join Google Meet
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($sessionData['treatmentPlanCreated'] && ($isGeneralDoctor || $isSpecialistDoctor)): ?>
                    <!-- Option to complete/end the session -->
                    <form action="<?php echo $basePath; ?>/doctor/session/complete" method="post" class="d-inline" style="margin-left: auto;">
                        <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="action-btn danger" onclick="return confirm('Are you sure you want to mark this session as complete? This action cannot be undone.');">
                            <i class="ri-check-double-line"></i> Complete Session
                        </button>
                    </form>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Refer to Specialist Modal -->
<div id="referSpecialistModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Refer to Specialist</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="referToSpecialistForm" action="<?php echo $basePath; ?>/doctor/refer-to-specialist" method="post">
            <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patientInfo['user_id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="specialization">Specialization</label>
                <select id="specialization" name="specialization" class="form-control" required>
                    <option value="">Select Specialization</option>
                    <?php foreach ($specializations as $specialization): ?>
                        <option value="<?php echo $specialization['specialization_id']; ?>"><?php echo htmlspecialchars($specialization['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="specialist_doctor">Specialist Doctor</label>
                <select id="specialist_doctor" name="specialist_doctor" class="form-control" required disabled>
                    <option value="">Select Doctor</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="referral_reason">Reason for Referral</label>
                <textarea id="referral_reason" name="referral_reason" class="form-control" rows="5" placeholder="Explain why you are referring this patient to a specialist..." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="preferred_date">Preferred Date</label>
                <input type="date" id="preferred_date" name="preferred_date" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="preferred_time">Preferred Time</label>
                <select id="preferred_time" name="preferred_time" class="form-control" required disabled>
                    <option value="">Select Time</option>
                </select>
            </div>
            
            <button type="submit" class="full-width-button">Submit Referral</button>
        </form>
    </div>
</div>

<!-- Create Treatment Plan Modal -->
<div id="treatmentPlanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create Treatment Plan</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="treatmentPlanForm" action="<?php echo $basePath; ?>/doctor/create-treatment-plan" method="post">
            <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patientInfo['user_id']; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="diagnosis">Diagnosis</label>
                <textarea id="diagnosis" name="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis..." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="treatment_description">Treatment Description</label>
                <textarea id="treatment_description" name="treatment_description" class="form-control" rows="5" placeholder="Describe the treatment plan..." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="medications">Medications</label>
                <textarea id="medications" name="medications" class="form-control" rows="3" placeholder="List prescribed medications and dosages..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="travel_restrictions">Travel Restrictions</label>
                <textarea id="travel_restrictions" name="travel_restrictions" class="form-control" rows="3" placeholder="Any travel restrictions or considerations..."></textarea>
            </div>
            
            <div class="form-group">
                <label for="estimated_budget">Estimated Budget (USD)</label>
                <input type="text" id="estimated_budget" name="estimated_budget" class="form-control" placeholder="e.g. $2,000 - $3,500" required>
            </div>
            
            <div class="form-group">
                <label for="treatment_duration">Estimated Treatment Duration</label>
                <input type="text" id="treatment_duration" name="treatment_duration" class="form-control" placeholder="e.g. 2 weeks" required>
            </div>
            
            <div class="form-group">
                <label for="follow_up">Follow-up Instructions</label>
                <textarea id="follow_up" name="follow_up" class="form-control" rows="3" placeholder="Follow-up instructions..."></textarea>
            </div>
            
            <button type="submit" class="full-width-button">Create Treatment Plan</button>
        </form>
    </div>
</div>

<!-- Edit Treatment Plan Modal -->
<div id="editTreatmentPlanModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Treatment Plan</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="editTreatmentPlanForm" action="<?php echo $basePath; ?>/doctor/update-treatment-plan" method="post">
            <input type="hidden" name="session_id" value="<?php echo $sessionData['id']; ?>">
            <input type="hidden" name="treatment_plan_id" value="<?php echo $sessionData['treatment_plan_id'] ?? ''; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="edit_diagnosis">Diagnosis</label>
                <textarea id="edit_diagnosis" name="diagnosis" class="form-control" rows="3" required><?php echo htmlspecialchars($sessionData['diagnosis'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_treatment_description">Treatment Description</label>
                <textarea id="edit_treatment_description" name="treatment_description" class="form-control" rows="5" required><?php echo htmlspecialchars($sessionData['treatment_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_medications">Medications</label>
                <textarea id="edit_medications" name="medications" class="form-control" rows="3"><?php echo htmlspecialchars($sessionData['medications'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_travel_restrictions">Travel Restrictions</label>
                <textarea id="edit_travel_restrictions" name="travel_restrictions" class="form-control" rows="3"><?php echo htmlspecialchars($sessionData['travelRestrictions'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_estimated_budget">Estimated Budget (USD)</label>
                <input type="text" id="edit_estimated_budget" name="estimated_budget" class="form-control" value="<?php echo htmlspecialchars($sessionData['estimatedBudget'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="edit_treatment_duration">Estimated Treatment Duration</label>
                <input type="text" id="edit_treatment_duration" name="treatment_duration" class="form-control" value="<?php echo htmlspecialchars($sessionData['treatment_duration'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="edit_follow_up">Follow-up Instructions</label>
                <textarea id="edit_follow_up" name="follow_up" class="form-control" rows="3"><?php echo htmlspecialchars($sessionData['follow_up'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="full-width-button">Update Treatment Plan</button>
        </form>
    </div>
</div>

<style>
/* Medical Session styles */
.medical-session {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.session-header {
    background-color: var(--primary-color, #4AB1A8);
    color: white;
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.session-header h2 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.session-status {
    font-size: 0.85rem;
    background-color: rgba(255, 255, 255, 0.2);
    padding: 4px 10px;
    border-radius: 12px;
}

.session-body {
    padding: 20px;
}

/* Progress Steps */
.step-progress-container {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 30px;
}

.step-progress-container::before {
    content: "";
    position: absolute;
    top: 18px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #e0e0e0;
    z-index: 1;
}

.step-progress-bar {
    position: absolute;
    top: 18px;
    left: 0;
    height: 2px;
    background-color: #2ecc71;
    z-index: 2;
    transition: width 0.3s ease;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 3;
}

.step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #f0f0f0;
    color: #333;
    text-decoration: none;
}

.action-btn i {
    margin-right: 5px;
}

.action-btn:hover {
    background-color: #e0e0e0;
}

.action-btn.primary {
    background-color: var(--primary-color, #4AB1A8);
    color: white;
}

.action-btn.primary:hover {
    background-color: #3a9a92;
}

.action-btn.danger {
    background-color: #ffebee;
    color: #c62828;
}

.action-btn.danger:hover {
    background-color: #ffcdd2;
}

/* Session details container */
.session-details-container {
    background-color: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.session-details-container h3 {
    margin: 0 0 15px 0;
    font-size: 1.1rem;
    color: #333;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0f0f0;
}

/* Action card styles */
.action-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.action-description h4 {
    margin: 0 0 5px 0;
    font-size: 1rem;
}

.action-description p {
    margin: 0 0 15px 0;
    color: #666;
    font-size: 0.9rem;
}

.full-width-button {
    width: 100%;
    padding: 12px;
    background-color: var(--primary-color, #4AB1A8);
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.full-width-button i {
    margin-right: 8px;
}

.full-width-button:hover {
    background-color: #3EA099;
}

.action-buttons-row {
    display: flex;
    gap: 10px;
}

/* Info card styles */
.info-card {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.info-card h4 {
    margin: 0 0 10px 0;
    font-size: 1rem;
}

.info-card p {
    margin: 0 0 5px 0;
    font-size: 0.9rem;
}

/* Alert boxes */
.alert-box {
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.alert-box i {
    font-size: 20px;
    margin-right: 12px;
}

.alert-box p {
    margin: 0;
    font-size: 0.9rem;
}

.alert-info {
    background-color: rgba(52, 152, 219, 0.1);
    border-left: 4px solid #3498db;
    color: #2980b9;
}

.alert-warning {
    background-color: rgba(241, 196, 15, 0.1);
    border-left: 4px solid #f1c40f;
    color: #f39c12;
}

.alert-success {
    background-color: rgba(46, 204, 113, 0.1);
    border-left: 4px solid #2ecc71;
    color: #27ae60;
}

/* Notes section */
.notes-section {
    margin-bottom: 20px;
}

.notes-section label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.notes-section textarea {
    width: 100%;
    min-height: 120px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: vertical;
    margin-bottom: 10px;
}

/* Medical records */
.medical-records {
    margin-top: 15px;
}

.record-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    margin-bottom: 8px;
}

.record-actions {
    display: flex;
    gap: 10px;
}

.record-actions a {
    color: var(--primary-color, #4AB1A8);
    text-decoration: none;
    display: flex;
    align-items: center;
}

.record-actions a i {
    margin-right: 3px;
}

/* Google Meet button */
.meet-link-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 15px;
    background-color: #1a73e8;
    color: white !important;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    margin-top: 10px;
}

.meet-link-btn:hover {
    background-color: #1557b0;
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.meet-link-btn i {
    margin-right: 8px;
    font-size: 18px;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    overflow-y: auto;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.3rem;
}

.close-btn {
    font-size: 24px;
    color: #666;
    background: none;
    border: none;
    cursor: pointer;
}

/* Form styles */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #333;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.9rem;
}

select.form-control {
    height: 40px;
}

textarea.form-control {
    resize: vertical;
}

.form-control:focus {
    border-color: var(--primary-color, #4AB1A8);
    outline: none;
}

.step-circle i {
    font-size: 18px;
}

.step-text {
    font-size: 0.8rem;
    color: #666;
    text-align: center;
    max-width: 90px;
}

.step-item.active .step-circle {
    background-color: var(--primary-color, #4AB1A8);
    border-color: var(--primary-color, #4AB1A8);
    color: white;
}

.step-item.completed .step-circle {
    background-color: #2ecc71;
    border-color: #2ecc71;
    color: white;
}

/* Patient info card */
.patient-info-card {
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.patient-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.patient-avatar i {
    font-size: 30px;
    color: #888;
}

.patient-details {
    flex: 1;
}

.patient-details h2 {
    margin: 0 0 10px 0;
    font-size: 1.3rem;
}

.patient-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.patient-meta span {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
}

.patient-meta span i {
    margin-right: 5px;
    color: #888;
}

.patient-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background-color 0.2s;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    border: 2px solid #e0e0e0;
    font-weight: 500;
    position: relative;
    transition: all 0.2s ease;
}

.step-circle i {
    font-size: 18px;
}

.step-text {
    font-size: 0.8rem;
    color: #666;
    text-align: center;
    max-width: 90px;
}

.step-item.active .step-circle {
    background-color: var(--primary-color, #4AB1A8);
    border-color: var(--primary-color, #4AB1A8);
    color: white;
}

.step-item.completed .step-circle {
    background-color: #2ecc71;
    border-color: #2ecc71;
    color: white;
}

/* Patient info card */
.patient-info-card {
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.patient-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
}

.patient-avatar i {
    font-size: 30px;
    color: #888;
}

.patient-details {
    flex: 1;
}

.patient-details h2 {
    margin: 0 0 10px 0;
    font-size: 1.3rem;
}

.patient-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.patient-meta span {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
}

.patient-meta span i {
    margin-right: 5px;
    color: #888;
}

.patient-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 4px;
    transition: background-color 0.2s;
    background-color: #f0f0f0;
    color: #333;
    text-decoration: none;
}

/* Responsive styles for smaller screens */
@media screen and (max-width: 768px) {
    .patient-info-card {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .patient-avatar {
        margin-bottom: 15px;
        margin-right: 0;
    }
    
    .patient-actions {
        margin-top: 15px;
        width: 100%;
    }
    
    .patient-meta {
        flex-direction: column;
        gap: 5px;
    }
    
    .action-buttons-row {
        flex-direction: column;
    }
    
    .step-progress-container {
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .step-item {
        min-width: 80px;
    }
    
    .modal-content {
        width: 95%;
        margin: 10% auto;
    }
}

/* Print styles - hide certain elements when printing */
@media print {
    .action-btn, 
    .full-width-button,
    .session-start-btn,
    .close-btn,
    .header-right,
    .sidebar {
        display: none !important;
    }
    
    .modal {
        position: static;
        display: block;
        background: none;
    }
    
    .modal-content {
        box-shadow: none;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    .main-content {
        margin-left: 0;
        padding: 0;
    }
    
    .container {
        display: block;
    }
    
    body {
        background: white;
    }
    
    .session-header {
        background-color: #f0f0f0 !important;
        color: black !important;
        -webkit-print-color-adjust: exact;
    }
    
    .step-item.active .step-circle,
    .step-item.completed .step-circle {
        border: 2px solid black;
        background-color: #f0f0f0 !important;
        color: black !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>