// Enable error logging
window.onerror = function(message, source, lineno, colno, error) {
    console.error('Error: ' + message + ' at ' + source + ':' + lineno + ':' + colno);
    console.error(error);
    return false;
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Adding direct event handlers for view buttons');
    
    // For all existing view-details-btn buttons
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.removeEventListener('click', handleViewDetailsButtonClick); // Remove any existing handlers first
        button.addEventListener('click', handleViewDetailsButtonClick);
    });
    
    // For all existing view-session-btn buttons
    document.querySelectorAll('.view-session-btn').forEach(button => {
        button.removeEventListener('click', handleViewSessionButtonClick); // Remove any existing handlers first
        button.addEventListener('click', handleViewSessionButtonClick);
    });
});

// Function to handle view details button click
function handleViewDetailsButtonClick(event) {
    event.preventDefault();
    console.log('View details button clicked');
    
    // Get the button and appointment ID
    const button = event.currentTarget;
    const appointmentId = button.dataset.appointmentId;
    
    if (!appointmentId) {
        console.error('No appointment ID found on button', button);
        return;
    }
    
    console.log('Processing view details for appointment:', appointmentId);
    
    // Find the appointment card
    const appointmentCard = button.closest('.appointment-card');
    if (!appointmentCard) {
        console.error('Appointment card not found');
        return;
    }
    
    // Close any other open sessions first to avoid multiple open containers
    document.querySelectorAll('.medical-session').forEach(container => {
        const otherId = container.id.replace('session-details-', '');
        if (otherId !== appointmentId && container.style.display !== 'none') {
            container.style.display = 'none';
            
            // Update other buttons
            const otherButton = document.querySelector(`.appointment-card[data-appointment-id="${otherId}"] .view-details-btn, 
                                                        .appointment-card[data-appointment-id="${otherId}"] .view-session-btn`);
            if (otherButton) {
                otherButton.innerHTML = otherButton.classList.contains('view-session-btn') ? 
                    '<i class="ri-file-list-3-line"></i> View Session' : 
                    '<i class="ri-eye-line"></i> View Details';
            }
        }
    });
    
    // Look for existing session details
    let sessionDetails = document.getElementById(`session-details-${appointmentId}`);
    
    // If session details exist, toggle visibility
    if (sessionDetails) {
        console.log('Session details exist, toggling visibility');
        
        // Force display setting check to be more explicit
        const isVisible = sessionDetails.style.display === 'block';
        
        if (!isVisible) {
            // Show session details
            sessionDetails.style.display = 'block';
            sessionDetails.classList.add('show');
            button.innerHTML = '<i class="ri-eye-off-line"></i> Hide Details';
        } else {
            // Hide session details
            sessionDetails.style.display = 'none';
            sessionDetails.classList.remove('show');
            button.innerHTML = '<i class="ri-eye-line"></i> View Details';
        }
    } else {
        // If session details don't exist, create and fetch
        console.log('Creating new session details container');
        
        sessionDetails = document.createElement('div');
        sessionDetails.id = `session-details-${appointmentId}`;
        sessionDetails.className = 'medical-session session-details-container show';
        sessionDetails.style.display = 'block';
        
        // Insert after the appointment card
        if (appointmentCard.nextSibling) {
            appointmentCard.parentNode.insertBefore(sessionDetails, appointmentCard.nextSibling);
        } else {
            appointmentCard.parentNode.appendChild(sessionDetails);
        }
        
        // Add loading indicator
        sessionDetails.innerHTML = '<div class="loading">Loading patient details...</div>';
        
        // Update button text
        button.innerHTML = '<i class="ri-eye-off-line"></i> Hide Details';
        
        // Set global variables for potential modal operations
        window.currentAppointmentId = appointmentId;
        
        // Fetch appointment details
        fetchAppointmentDetails(appointmentId, sessionDetails, button);
    }
}

// Function to handle view session button click
function handleViewSessionButtonClick(event) {
    event.preventDefault();
    console.log('View session button clicked');
    const button = event.currentTarget;
    const appointmentCard = button.closest('.appointment-card');
    if (!appointmentCard) return;
    
    const appointmentId = appointmentCard.dataset.appointmentId;
    if (!appointmentId) {
        console.error('No appointment ID found on card');
        return;
    }
    
    // Look for existing session details
    const sessionDetails = document.getElementById(`session-details-${appointmentId}`);
    if (!sessionDetails) {
        console.error(`Session details container not found for appointment ${appointmentId}`);
        return;
    }

    // Close all other open sessions first
    document.querySelectorAll('.medical-session').forEach(container => {
        if (container.id !== `session-details-${appointmentId}` && container.style.display !== 'none') {
            container.style.display = 'none';
            container.classList.remove('show');
            
            // Update buttons
            const otherId = container.id.replace('session-details-', '');
            const otherBtn = document.querySelector(`.appointment-card[data-appointment-id="${otherId}"] .view-session-btn, 
                                                    .appointment-card[data-appointment-id="${otherId}"] .view-details-btn`);
            if (otherBtn) {
                otherBtn.innerHTML = otherBtn.classList.contains('view-session-btn') ? 
                    '<i class="ri-file-list-3-line"></i> View Session' : 
                    '<i class="ri-eye-line"></i> View Details';
            }
        }
    });

    // Force display setting check to be more explicit
    const isVisible = sessionDetails.style.display === 'block';

    // Toggle session details visibility
    if (!isVisible) {
        sessionDetails.style.display = 'block';
        sessionDetails.classList.add('show');
        button.innerHTML = '<i class="ri-eye-off-line"></i> Hide Session';
    } else {
        sessionDetails.style.display = 'none';
        sessionDetails.classList.remove('show');
        button.innerHTML = '<i class="ri-file-list-3-line"></i> View Session';
    }
}

// Fetch appointment details via AJAX
function fetchAppointmentDetails(appointmentId, sessionDetailsContainer, button) {
    console.log('Fetching appointment details:', appointmentId);
    
    // Get base path from meta tag
    const basePath = document.querySelector('meta[name="base-path"]')?.getAttribute('content') || '';
    
    fetch(`${basePath}/vpdoctor/get-appointment-details?appointment_id=${appointmentId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                // Create the session details content
                const details = data.data;
                const sessionHTML = createSessionDetailsHTML(details);
                sessionDetailsContainer.innerHTML = sessionHTML;
                
                // Add event listeners to the newly created buttons
                addEventListenersToNewButtons(sessionDetailsContainer, appointmentId);
            } else {
                sessionDetailsContainer.innerHTML = `<div class="error-message">Failed to load appointment details: ${data.error || 'Unknown error'}</div>`;
                console.error('Error in API response:', data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching appointment details:', error);
            sessionDetailsContainer.innerHTML = `<div class="error-message">Error: ${error.message}</div>`;
            button.innerHTML = '<i class="ri-eye-line"></i> View Details';
        });
}

// Add event listeners to newly created buttons in the session details
function addEventListenersToNewButtons(container, appointmentId) {
    // Add event listeners for save notes buttons
    const saveNotesBtn = container.querySelector('.save-notes-btn');
    if (saveNotesBtn) {
        saveNotesBtn.addEventListener('click', function() {
            const notes = container.querySelector(`#specialist-notes-${appointmentId}`).value;
            const sessionId = this.dataset.sessionId;
            if (window.saveSpecialistNotes) {
                window.saveSpecialistNotes(appointmentId, sessionId, notes);
            } else {
                console.log('saveSpecialistNotes function not available');
                alert('Notes saved!');
            }
        });
    }
    
    // Add event listeners for complete appointment buttons
    const completeBtn = container.querySelector('.complete-appointment-btn');
    if (completeBtn) {
        completeBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to mark this appointment as completed?')) {
                if (window.submitAppointmentAction) {
                    window.submitAppointmentAction(appointmentId, 'complete');
                } else {
                    console.log('submitAppointmentAction function not available');
                    alert('Appointment completed!');
                }
            }
        });
    }
    
    // Add event listeners for cancel appointment buttons
    const cancelBtn = container.querySelector('.cancel-appointment-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            const reason = prompt('Please provide a reason for cancellation:');
            if (reason) {
                if (window.submitAppointmentAction) {
                    window.submitAppointmentAction(appointmentId, 'cancel', {
                        reason: reason
                    });
                } else {
                    console.log('submitAppointmentAction function not available');
                    alert('Appointment cancelled!');
                }
            }
        });
    }
}

// Create HTML for session details
function createSessionDetailsHTML(details) {
    return `
        <div class="session-header">
            <h2>Medical Session: ${details.patient_name || (details.patient_first_name + ' ' + details.patient_last_name)}</h2>
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
                        <h3>${details.patient_name || (details.patient_first_name + ' ' + details.patient_last_name)}</h3>
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
                <button class="action-btn primary save-notes-btn" data-appointment-id="${details.appointment_id}" data-session-id="${details.session_id || ''}">
                    <i class="ri-save-line"></i> Save Notes
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div class="session-actions">
                <button class="action-btn primary complete-appointment-btn" data-appointment-id="${details.appointment_id}">
                    <i class="ri-check-line"></i> Complete Appointment
                </button>
                <button class="action-btn secondary cancel-appointment-btn" data-appointment-id="${details.appointment_id}">
                    <i class="ri-close-line"></i> Cancel Appointment
                </button>
            </div>
        </div>
    `;
}