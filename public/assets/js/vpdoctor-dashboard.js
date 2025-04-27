// VPDoctor Dashboard JS File
// Handles all interactive functionality for the specialist doctor dashboard

// Global variables to store current IDs for modals
window.currentAppointmentId = null;
window.currentSessionId = null;
window.selectedTravelRestrictions = {};

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    console.log('VPDoctor dashboard initialized');
    window.basePath = document.querySelector('meta[name="base-path"]')?.getAttribute('content') || '';
    
    // Initialize travel restriction checkboxes
    initTravelRestrictionCheckboxes();
     
    // Handle treatment plan creation
    const createTreatmentPlanBtns = document.querySelectorAll('.create-treatment-plan-btn');
    
    // In vpdoctor-dashboard.js
createTreatmentPlanBtns.forEach(btn => {
    btn.addEventListener('click', function(event) {
        // Prevent default form submission if this is inside a form
        if (event.preventDefault) {
            event.preventDefault();
        }
        
        const appointmentId = this.dataset.appointmentId;
        const sessionId = this.dataset.sessionId;
        
        console.log(`Creating treatment plan for appointment: ${appointmentId}, session: ${sessionId}`);
        
        // Get base path properly
        const basePath = window.basePath || '';
        
        // Log all form values for debugging
        const travelRestrictionsGroup = document.getElementById('travel-restrictions-' + appointmentId);
        const checkedRestriction = travelRestrictionsGroup.querySelector('input[type="checkbox"]:checked');
        const travelRestrictions = checkedRestriction ? checkedRestriction.value : 'None';
        
        const vehicleType = document.getElementById('vehicle-type-' + appointmentId).value;
        const arrivalDeadline = document.getElementById('arrival-deadline-' + appointmentId).value;
        const treatmentDescription = document.getElementById('treatment-description-' + appointmentId).value;
        const estimatedBudget = document.getElementById('estimated-budget-' + appointmentId).value;
        const estimatedDuration = document.getElementById('estimated-duration-' + appointmentId).value;
        
        console.log('Form values:', {
            travelRestrictions,
            vehicleType,
            arrivalDeadline,
            treatmentDescription,
            estimatedBudget,
            estimatedDuration
        });
        
        // Create form data
        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('session_id', sessionId);
        formData.append('appointment_id', appointmentId);
        formData.append('travel_restrictions', travelRestrictions);
        formData.append('vehicle_type', vehicleType);
        formData.append('arrival_deadline', arrivalDeadline);
        formData.append('treatment_description', treatmentDescription);
        formData.append('estimated_budget', estimatedBudget);
        formData.append('estimated_duration', estimatedDuration);
        
        // Send request to server with full URL including basePath
        const url = `${basePath}/vpdoctor/createTreatmentPlan`;
        console.log('Submitting to URL:', url);
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                window.showToast('Treatment plan created successfully!');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert('Error: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            console.error('Error message:', error.message);
            alert('An error occurred while creating the treatment plan: ' + error.message);
        });
    });
});





    // Add event listeners for view session buttons
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
    
    // Initialize travel restriction checkboxes for all appointments
    function initTravelRestrictionCheckboxes() {
        // Handle checkboxes in creation form
        document.querySelectorAll('[id^="travel-restrictions-"]').forEach(container => {
            const appointmentId = container.id.replace('travel-restrictions-', '');
            const checkboxes = container.querySelectorAll('input[type="checkbox"]');
            
            // Initialize selected restrictions for this appointment
            if (!window.selectedTravelRestrictions[appointmentId]) {
                window.selectedTravelRestrictions[appointmentId] = [];
            }
            
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Handle "No Restrictions" checkbox special case
                    if (this.id.includes('no-restrictions') && this.checked) {
                        // Uncheck all other checkboxes
                        checkboxes.forEach(cb => {
                            if (cb.id !== this.id) {
                                cb.checked = false;
                            }
                        });
                        window.selectedTravelRestrictions[appointmentId] = ['None'];
                    } else if (this.checked) {
                        // If any other checkbox is checked, uncheck "No Restrictions"
                        const noRestrictionsCheckbox = document.getElementById(`no-restrictions-${appointmentId}`);
                        if (noRestrictionsCheckbox) {
                            noRestrictionsCheckbox.checked = false;
                        }
                        
                        // Add this restriction to the array if it's not already there
                        if (!window.selectedTravelRestrictions[appointmentId].includes(this.value)) {
                            window.selectedTravelRestrictions[appointmentId].push(this.value);
                        }
                    } else {
                        // Remove this restriction from the array if unchecked
                        window.selectedTravelRestrictions[appointmentId] = window.selectedTravelRestrictions[appointmentId]
                            .filter(r => r !== this.value);
                    }
                    
                    console.log(`Travel restrictions for appointment ${appointmentId}:`, window.selectedTravelRestrictions[appointmentId]);
                });
            });
        });
        
        // Handle checkboxes in edit form
        const editTravelRestrictions = document.getElementById('editTravelRestrictions');
        if (editTravelRestrictions) {
            const editCheckboxes = editTravelRestrictions.querySelectorAll('input[type="checkbox"]');
            
            editCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Handle "No Restrictions" checkbox special case
                    if (this.id === 'edit-no-restrictions' && this.checked) {
                        // Uncheck all other checkboxes
                        editCheckboxes.forEach(cb => {
                            if (cb.id !== this.id) {
                                cb.checked = false;
                            }
                        });
                        window.selectedTravelRestrictions.edit = ['None'];
                    } else if (this.checked) {
                        // If any other checkbox is checked, uncheck "No Restrictions"
                        const noRestrictionsCheckbox = document.getElementById('edit-no-restrictions');
                        if (noRestrictionsCheckbox) {
                            noRestrictionsCheckbox.checked = false;
                        }
                        
                        // Initialize edit array if needed
                        if (!window.selectedTravelRestrictions.edit) {
                            window.selectedTravelRestrictions.edit = [];
                        }
                        
                        // Add this restriction to the array if it's not already there
                        if (!window.selectedTravelRestrictions.edit.includes(this.value)) {
                            window.selectedTravelRestrictions.edit.push(this.value);
                        }
                    } else {
                        // Remove this restriction from the array if unchecked
                        if (window.selectedTravelRestrictions.edit) {
                            window.selectedTravelRestrictions.edit = window.selectedTravelRestrictions.edit
                                .filter(r => r !== this.value);
                        }
                    }
                    
                    console.log('Edit travel restrictions:', window.selectedTravelRestrictions.edit);
                });
            });
        }
    }
    
    // Add event listeners for various buttons
    document.querySelectorAll('.save-notes-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            const sessionId = this.dataset.sessionId || '';
            const notesId = `specialist-notes-${appointmentId}`;
            const notes = document.getElementById(notesId)?.value || '';
            
            if (!notes.trim()) {
                alert('Please enter some notes before saving');
                return;
            }
            
            window.saveSpecialistNotes(appointmentId, sessionId, notes);
        });
    });
    
    document.querySelectorAll('.create-treatment-plan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.dataset.appointmentId;
            const sessionId = this.dataset.sessionId || '';
            
            window.createTreatmentPlan(appointmentId, sessionId);
        });
    });
    
    // Using event delegation for dynamically added elements
    document.addEventListener('click', function(event) {
        // Edit treatment plan buttons
        if (event.target && event.target.id && event.target.id.startsWith('editTreatmentPlanBtn-')) {
            const appointmentId = event.target.dataset.appointmentId;
            window.currentAppointmentId = appointmentId;
            window.currentSessionId = event.target.dataset.sessionId || '';
            
            window.openEditTreatmentPlanModal(appointmentId);
        }
        
        // Medical test request buttons
        if (event.target && event.target.classList.contains('request-medical-btn')) {
            window.currentAppointmentId = event.target.dataset.appointmentId;
            window.currentSessionId = event.target.dataset.sessionId || '';
            
            window.openMedicalTestsModal();
        }
        
        // Cancel treatment buttons
        if (event.target && event.target.classList.contains('cancel-treatment-btn')) {
            window.currentAppointmentId = event.target.dataset.appointmentId;
            window.currentSessionId = event.target.dataset.sessionId || '';
            
            window.openCancelTreatmentModal();
        }
    });
    
    // Modal action buttons
    document.getElementById('submitMedicalTest')?.addEventListener('click', window.submitMedicalTestRequest);
    document.getElementById('confirmCancelTreatment')?.addEventListener('click', window.confirmCancelTreatment);
    document.getElementById('updateTreatmentPlan')?.addEventListener('click', window.updateTreatmentPlan);
    
    // Close buttons for modals
    document.querySelectorAll('.close-btn').forEach(element => {
        element.addEventListener('click', window.closeAllModals);
    });
    
    // Click outside to close modal
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            document.querySelectorAll('.appointment-card').forEach(card => {
                const patientName = card.querySelector('h3').textContent.toLowerCase();
                if (patientName.includes(searchTerm) || searchTerm === '') {
                    card.style.display = 'flex';
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
    
    // Check for URL parameters to handle redirects
    function checkUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Check for success or error messages
        if (urlParams.has('success')) {
            window.showToast(decodeURIComponent(urlParams.get('success')));
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        if (urlParams.has('error')) {
            window.showToast(decodeURIComponent(urlParams.get('error')), 'error');
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
    
    // Run the check on page load
    checkUrlParameters();
    
    console.log('VPDoctor dashboard fully initialized');
});

// Global function for saving specialist notes
window.saveSpecialistNotes = function(appointmentId, sessionId, notes) {
    console.log('Saving notes for appointment:', appointmentId);
    
    // Create form to submit data to the server
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${window.basePath || ''}/vpdoctor/saveSpecialistNotes`;
    form.style.display = 'none';

    // Add CSRF token
    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = 'csrf_token';
    csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
    form.appendChild(csrfField);

    // Add session ID, appointment ID and notes
    const fieldData = {
        'session_id': sessionId,
        'appointment_id': appointmentId,
        'specialist_notes': notes
    };
    
    // Add all fields to the form
    Object.entries(fieldData).forEach(([name, value]) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    });

    // Add to document and submit
    document.body.appendChild(form);

    // Show success toast
    window.showToast('Notes saved successfully');

    // Submit the form
    form.submit();
};

// Global toast notification function
window.showToast = function(message, type = 'success') {
    console.log('Showing toast:', message);
    
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
    const colors = {
        'success': '#4CAF50',
        'error': '#F44336',
        'warning': '#FF9800'
    };
    
    toast.style.backgroundColor = colors[type] || colors.success;
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
};

// Global function for submitting medical test requests
window.submitMedicalTestRequest = function() {
    console.log('Submitting medical test request');
    
    const testType = document.getElementById('testType')?.value;
    const testDescription = document.getElementById('testDescription')?.value;
    const testRequiredFasting = document.getElementById('testRequiredFasting')?.value;
    const testUrgency = document.getElementById('testUrgency')?.value;
    
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
    form.action = `${window.basePath || ''}/vpdoctor/requestMedicalTests`;
    form.style.display = 'none';

    // Add CSRF token
    const csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = 'csrf_token';
    csrfField.value = document.querySelector('input[name="csrf_token"]')?.value || '';
    form.appendChild(csrfField);

    // Get patient ID from the session
    const sessionDetails = document.getElementById(`session-details-${window.currentAppointmentId}`);
    let patientId = '';
    if (sessionDetails) {
        const patientIdText = sessionDetails.querySelector('.doctor-info p:nth-child(2)')?.textContent || '';
        patientId = patientIdText.match(/Patient ID: (\w+-\d+|\d+)/)?.[1] || '';
    }

    // Add all fields to the form
    const fieldData = {
        'session_id': window.currentSessionId || '',
        'appointment_id': window.currentAppointmentId || '',
        'patient_id': patientId,
        'test_type': testType,
        'test_description': testDescription,
        'requires_fasting': testRequiredFasting,
        'urgency': testUrgency
    };
    
    // Add all fields to the form
    Object.entries(fieldData).forEach(([name, value]) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    });

    // Show visual feedback
    window.showToast('Medical test requested successfully');

    // Close the modal
    const medicalTestsModal = document.getElementById('medicalTestsModal');
    if (medicalTestsModal) medicalTestsModal.style.display = 'none';

    // Add to document and submit
    document.body.appendChild(form);
    form.submit();
};

// Global function for canceling treatment
window.confirmCancelTreatment = function() {
    const cancellationReasonField = document.getElementById('cancellationReason');
    if (!cancellationReasonField) {
        console.error('Cancellation reason field not found');
        return;
    }

    const cancelReason = cancellationReasonField.value.trim();
    if (!cancelReason) {
        alert('Please provide a reason for cancellation');
        return;
    }

    // Create form to submit data to the server
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${window.basePath || ''}/vpdoctor/cancelTreatment`;
    form.style.display = 'none';

    // Add CSRF token and fields
    const fieldData = {
        'csrf_token': document.querySelector('input[name="csrf_token"]')?.value || '',
        'session_id': window.currentSessionId || '',
        'appointment_id': window.currentAppointmentId || '',
        'cancel_reason': cancelReason
    };
    
    // Add all fields to the form
    Object.entries(fieldData).forEach(([name, value]) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    });

    // Show toast feedback
    window.showToast('Treatment canceled: ' + cancelReason);

    // Close the modal
    const cancelTreatmentModal = document.getElementById('cancelTreatmentModal');
    if (cancelTreatmentModal) cancelTreatmentModal.style.display = 'none';

    // Add to document and submit
    document.body.appendChild(form);
    form.submit();
};

// Function for creating treatment plan
window.createTreatmentPlan = function(appointmentId, sessionId) {
    // Get travel restrictions
    const travelRestrictions = window.selectedTravelRestrictions[appointmentId] || [];
    if (travelRestrictions.length === 0) {
        travelRestrictions.push('None');
    }
    
    // Get other fields
    const vehicleType = document.getElementById(`vehicle-type-${appointmentId}`)?.value || 'Regular Vehicle';
    const arrivalDeadline = document.getElementById(`arrival-deadline-${appointmentId}`)?.value || '';
    const treatmentDescription = document.getElementById(`treatment-description-${appointmentId}`)?.value || '';
    const estimatedBudget = document.getElementById(`estimated-budget-${appointmentId}`)?.value || '';
    const estimatedDuration = document.getElementById(`estimated-duration-${appointmentId}`)?.value || '';

    if (!treatmentDescription) {
        alert('Please provide a treatment description');
        return;
    }

    if (!estimatedBudget) {
        alert('Please provide an estimated budget in LKR');
        return;
    }

    if (!estimatedDuration) {
        alert('Please provide an estimated duration');
        return;
    }

    // Create form to submit data to the server
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${window.basePath || ''}/vpdoctor/createTreatmentPlan`;
    form.style.display = 'none';

    // Add all fields to the form
    const fieldData = {
        'csrf_token': document.querySelector('input[name="csrf_token"]')?.value || '',
        'session_id': sessionId,
        'appointment_id': appointmentId,
        'travel_restrictions': travelRestrictions.join(', '),
        'vehicle_type': vehicleType,
        'arrival_deadline': arrivalDeadline,
        'treatment_description': treatmentDescription,
        'estimated_budget': estimatedBudget,
        'estimated_duration': estimatedDuration
    };
    
    Object.entries(fieldData).forEach(([name, value]) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    });

    // Add to document
    document.body.appendChild(form);

    // Show toast notification
    window.showToast('Treatment plan created!');

    // Submit the form
    form.submit();
};

// Helper function to close all modals
window.closeAllModals = function() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
};

// Function to open medical tests modal
window.openMedicalTestsModal = function() {
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
};

// Function to open cancel treatment modal
window.openCancelTreatmentModal = function() {
    // Clear previous input
    const cancelReasonField = document.getElementById('cancellationReason');
    if (cancelReasonField) cancelReasonField.value = '';

    // Show modal
    const cancelTreatmentModal = document.getElementById('cancelTreatmentModal');
    if (cancelTreatmentModal) cancelTreatmentModal.style.display = 'block';
};

// Function to open edit treatment plan modal
window.openEditTreatmentPlanModal = function(appointmentId) {
    // Find the treatment details container
    const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
    if (!sessionDetails) {
        console.error(`Session details not found for appointment ${appointmentId}`);
        return;
    }

    const treatmentDetailsContainer = sessionDetails.querySelector('.treatment-details');

    if (treatmentDetailsContainer) {
        // Reset selected restrictions for edit form
        window.selectedTravelRestrictions.edit = [];
        
        // Extract current values
        const paragraphs = treatmentDetailsContainer.querySelectorAll('p');
        if (paragraphs.length >= 6) { // Updated to account for the new fields
            const travelRestrictionsText = paragraphs[0].textContent.split(':')[1].trim();
            const vehicleType = paragraphs[1].textContent.split(':')[1].trim();
            const arrivalDeadline = paragraphs[2].textContent.includes('Not specified') ? '' : 
                                    paragraphs[2].textContent.split(':')[1].trim().split('/').reverse().join('-');
            const treatmentDescription = paragraphs[3].textContent.split(':')[1].trim();
            const estimatedBudget = paragraphs[4].textContent.split('LKR')[1].trim().replace(/,/g, '');
            const estimatedDuration = paragraphs[5].textContent.split(':')[1].trim().split(' ')[0];
            
            // Set checkboxes based on travel restrictions
            const travelRestrictions = travelRestrictionsText.split(', ');
            window.selectedTravelRestrictions.edit = travelRestrictions;
            
            // Set checkboxes
            document.querySelectorAll('#editTravelRestrictions input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = travelRestrictions.includes(checkbox.value);
                
                // Special handling for "No Restrictions"
                if (checkbox.id === 'edit-no-restrictions' && travelRestrictions.includes('None')) {
                    checkbox.checked = true;
                }
            });
            
            // Populate other edit form fields
            const fields = {
                'editVehicleType': vehicleType,
                'editArrivalDeadline': arrivalDeadline,
                'editTreatmentDescription': treatmentDescription,
                'editEstimatedBudget': estimatedBudget,
                'editEstimatedDuration': estimatedDuration
            };
            
            Object.entries(fields).forEach(([id, value]) => {
                const field = document.getElementById(id);
                if (field) field.value = value;
            });

            // Show modal
            const editTreatmentModal = document.getElementById('editTreatmentModal');
            if (editTreatmentModal) editTreatmentModal.style.display = 'block';
        } else {
            console.error('Treatment details paragraphs not found or incomplete');
        }
    } else {
        console.error('Treatment details container not found');
    }
};

// Function for updating treatment plan
window.updateTreatmentPlan = function() {
    // Get travel restrictions
    const travelRestrictions = window.selectedTravelRestrictions.edit || [];
    if (travelRestrictions.length === 0) {
        travelRestrictions.push('None');
    }
    
    // Get other fields
    const vehicleType = document.getElementById('editVehicleType')?.value || 'Regular Vehicle';
    const arrivalDeadline = document.getElementById('editArrivalDeadline')?.value || '';
    const treatmentDescription = document.getElementById('editTreatmentDescription')?.value;
    const estimatedBudget = document.getElementById('editEstimatedBudget')?.value;
    const estimatedDuration = document.getElementById('editEstimatedDuration')?.value;

    if (!treatmentDescription) {
        alert('Please provide a treatment description');
        return;
    }

    if (!estimatedBudget) {
        alert('Please provide an estimated budget in LKR');
        return;
    }

    if (!estimatedDuration) {
        alert('Please provide an estimated duration');
        return;
    }

    // Create form to submit data to the server
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${window.basePath || ''}/vpdoctor/updateTreatmentPlan`;
    form.style.display = 'none';

    // Add all fields to the form
    const fieldData = {
        'csrf_token': document.querySelector('input[name="csrf_token"]')?.value || '',
        'session_id': window.currentSessionId || '',
        'appointment_id': window.currentAppointmentId || '',
        'travel_restrictions': travelRestrictions.join(', '),
        'vehicle_type': vehicleType,
        'arrival_deadline': arrivalDeadline,
        'treatment_description': treatmentDescription,
        'estimated_budget': estimatedBudget,
        'estimated_duration': estimatedDuration
    };
    
    Object.entries(fieldData).forEach(([name, value]) => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    });

    // Show visual feedback
    window.showToast('Treatment plan updated successfully');

    // Close modal
    const editTreatmentModal = document.getElementById('editTreatmentModal');
    if (editTreatmentModal) editTreatmentModal.style.display = 'none';

    // Add to document and submit
    document.body.appendChild(form);
    form.submit();
};
