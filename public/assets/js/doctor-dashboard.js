/**
 * Doctor Dashboard JavaScript
 * Handles all interactive functionality for the doctor dashboard
 */

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

    // Save Notes button click
    document.querySelectorAll('.save-notes-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            const sessionId = this.dataset.sessionId || '';
            const notesId = `doctor-notes-${appointmentId}`;
            const notes = document.getElementById(notesId).value;
            
            if (!notes.trim()) {
                alert('Please enter some notes before saving');
                return;
            }
            
            // Create form to submit data to the server
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${basePath}/doctor/sessionSaveNotes`;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = 'csrf_token';
            csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
            form.appendChild(csrfField);
            
            // Add session ID and doctor ID
            const sessionIdField = document.createElement('input');
            sessionIdField.type = 'hidden';
            sessionIdField.name = 'session_id';
            sessionIdField.value = sessionId;
            form.appendChild(sessionIdField);
            
            const doctorIdField = document.createElement('input');
            doctorIdField.type = 'hidden';
            doctorIdField.name = 'doctor_id';
            doctorIdField.value = appointmentId; // Using appointment_id as doctor_id for simplicity
            form.appendChild(doctorIdField);
            
            // Add doctor notes
            const notesField = document.createElement('input');
            notesField.type = 'hidden';
            notesField.name = 'doctor_notes';
            notesField.value = notes;
            form.appendChild(notesField);
            
            // Add to document and submit
            document.body.appendChild(form);
            
            // Show success message while form is submitting
            showSuccessAlert(appointmentId, 'Medical notes saved successfully!');
            showToast('Notes saved successfully');
            
            // Submit the form
            form.submit();
        });
    });

    // Save Specialist Notes button click
    document.querySelectorAll('.save-specialist-notes-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            const sessionId = this.dataset.sessionId || '';
            const specialistNotesId = `specialist-notes-${appointmentId}`;
            const notes = document.getElementById(specialistNotesId).value;
            
            if (!notes.trim()) {
                alert('Please enter some notes before saving');
                return;
            }
            
            // Create form to submit data to the server
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${basePath}/doctor/saveSpecialistNotes`;
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
            showToast('Specialist notes saved successfully');
            
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
            form.action = `${basePath}/doctor/createTreatmentPlan`;
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
                const estimatedBudget = treatmentDetailsContainer.querySelector('p:nth-of-type(3)').textContent.split('$')[1].trim().replace(',', '');
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
            form.action = `${basePath}/doctor/requestMedicalTests`;
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
            form.action = `${basePath}/doctor/cancelTreatment`;
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
            form.action = `${basePath}/doctor/updateTreatmentPlan`;
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
                    paragraphs[2].innerHTML = `<strong>Estimated Budget:</strong> $${Number(estimatedBudget).toLocaleString()}`;
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
    
    // Check for URL parameters to handle redirects
    function checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Check for specialist_booked parameter
        if (urlParams.has('specialist_booked') && urlParams.has('appointment_id')) {
            const appointmentId = urlParams.get('appointment_id');
            const specialistBooked = urlParams.get('specialist_booked');
            
            if (specialistBooked === 'true' && appointmentId) {
                // Update the UI to reflect that a specialist has been booked
                updateSpecialistBookingStatus(appointmentId);
                
                // Show a success message
                showToast('Specialist appointment booked successfully!');
                
                // Remove the query parameters to prevent refresh issues
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
        
        // Check for success or error messages
        if (urlParams.has('success')) {
            showToast(decodeURIComponent(urlParams.get('success')));
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        if (urlParams.has('error')) {
            showToast(decodeURIComponent(urlParams.get('error')), 'error');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
    
    // Run the check on page load
    checkUrlParameters();
    
    // Function to update specialist booking status in the UI
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
        }
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
});