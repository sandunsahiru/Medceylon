<!-- app/views/patient/book-appointment.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
                    <h1>Medceylon</h1>
                </a>
            </div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item active">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/chat" class="nav-item">
                    <i class="ri-message-3-line"></i>
                    <span>Chat</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/profile" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>

            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Book Appointment</h1>
            </header>

            <?php if ($this->session->hasFlash('success')): ?>
                <div class="success-message"><?php echo $this->session->getFlash('success'); ?></div>
            <?php endif; ?>

            <?php if ($this->session->hasFlash('error')): ?>
                <div class="error-message"><?php echo $this->session->getFlash('error'); ?></div>
            <?php endif; ?>

            <section class="appointments-section">
                <!-- Form with field names that match controller expectations -->
                <form id="appointmentForm" class="appointment-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">

                    <?php if (isset($startMedicalSession) && $startMedicalSession): ?>
                        <input type="hidden" name="start_medical_session" value="1">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="doctor">Select Doctor:</label>
                        <select name="doctor_id" id="doctor" required>
                            <option value="">Select a doctor</option>
                            <?php if ($doctors): ?>
                                <?php while ($doctor = $doctors->fetch_assoc()): ?>
                                    <option value="<?php echo $doctor['doctor_id']; ?>">
                                        Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                                        (<?php echo htmlspecialchars($doctor['hospital_name']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_date">Select Date:</label>
                        <input type="date" id="appointment_date" name="preferred_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="time_slot">Available Time Slots:</label>
                        <select name="appointment_time" id="time_slot" required disabled>
                            <option value="">Select date and doctor first</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="consultation_type">Consultation Type:</label>
                        <select name="consultation_type" id="consultation_type" required>
                            <option value="Online">Online (Google Meet link will be generated)</option>
                            <option value="In-Person">In-Person</option>
                        </select>
                    </div>

                   <button type="submit" class="submit-btn">Book Appointment</button>


               </form>
               <a href="<?php echo $basePath; ?>/patient/dashboard/"><button class="submit-btn">Next Page</button></a>
           </section>
       </main>
   </div>

                    <div class="form-group">
                        <label for="reason">Reason for Visit:</label>
                        <textarea name="reason_for_visit" id="reason" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="medical_history">Medical History (Optional):</label>
                        <textarea name="medical_history" id="medical_history"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="documents">Upload Documents (Optional):</label>
                        <input type="file" name="documents[]" id="documents" multiple accept=".pdf,.jpg,.jpeg,.png">
                        <small>Maximum file size: 5MB per file</small>
                    </div>

                    <button type="submit" class="submit-btn" id="bookButton">Book Appointment</button>
                    
                    <!-- Loading indicator (initially hidden) -->
                    <div id="loading-indicator" style="display: none; text-align: center; margin-top: 15px;">
                        <div class="spinner" style="display: inline-block; width: 24px; height: 24px; border: 3px solid rgba(0, 0, 0, 0.1); border-radius: 50%; border-top-color: #3498db; animation: spin 1s ease-in-out infinite;"></div>
                        <p style="margin-top: 10px;" id="loading-message">Processing appointment... This may take a moment if creating a Google Meet link.</p>
                    </div>

                    <!-- Error message container -->
                    <div id="error-container" style="display: none; color: #e74c3c; margin-top: 15px; padding: 10px; border-left: 4px solid #e74c3c; background-color: #fadbd8;">
                        <h3 style="margin-top: 0;">Error</h3>
                        <p id="error-message"></p>
                    </div>

                    <!-- Success message container -->
                    <div id="success-container" style="display: none; color: #27ae60; margin-top: 15px; padding: 10px; border-left: 4px solid #27ae60; background-color: #d4f7e6;">
                        <h3 style="margin-top: 0;">Success</h3>
                        <p id="success-message"></p>
                        <div id="meet-link-container" style="display: none; margin-top: 10px;">
                            <p>Your Google Meet link: <a href="#" id="meet-link-url" target="_blank"></a></p>
                        </div>
                        <button type="button" id="go-to-dashboard" style="margin-top: 10px; background-color: #27ae60; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Go to Dashboard</button>
                    </div>

                    <style>
                        @keyframes spin {
                            to { transform: rotate(360deg); }
                        }
                    </style>
                </form>
            </section>
        </main>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';

        document.addEventListener('DOMContentLoaded', function() {
            const doctorSelect = document.getElementById('doctor');
            const dateInput = document.getElementById('appointment_date');
            const timeSlotSelect = document.getElementById('time_slot');
            const reasonInput = document.getElementById('reason');
            const consultationType = document.getElementById('consultation_type');
            const appointmentForm = document.getElementById('appointmentForm');
            const loadingIndicator = document.getElementById('loading-indicator');
            const loadingMessage = document.getElementById('loading-message');
            const bookButton = document.getElementById('bookButton');
            const errorContainer = document.getElementById('error-container');
            const errorMessage = document.getElementById('error-message');
            const successContainer = document.getElementById('success-container');
            const successMessage = document.getElementById('success-message');
            const meetLinkContainer = document.getElementById('meet-link-container');
            const meetLinkUrl = document.getElementById('meet-link-url');
            const goToDashboard = document.getElementById('go-to-dashboard');

            goToDashboard.addEventListener('click', function() {
                window.location.href = `${basePath}/patient/dashboard`;
            });

            async function loadTimeSlots() {
                if (!doctorSelect.value || !dateInput.value) return;

                timeSlotSelect.innerHTML = '<option value="">Loading...</option>';
                timeSlotSelect.disabled = true;

                try {
                    // Send the date parameter with the correct name for backend
                    const response = await fetch(`${basePath}/patient/get-time-slots`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `doctor_id=${doctorSelect.value}&date=${dateInput.value}`
                    });

                    const slots = await response.json();
                    console.log("Received time slots:", slots);
                    
                    timeSlotSelect.innerHTML = '';
                    timeSlotSelect.disabled = false;

                    if (!Array.isArray(slots) || slots.length === 0) {
                        timeSlotSelect.innerHTML = '<option value="">No available slots</option>';
                        return;
                    }

                    // First option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "Select a time slot";
                    timeSlotSelect.appendChild(defaultOption);

                    // Add all available time slots
                    slots.forEach(slot => {
                        const option = document.createElement('option');

                        // Check if slot is a string or an object
                        if (typeof slot === 'string') {
                            option.value = slot;
                            option.textContent = slot;
                        } else if (typeof slot === 'object' && slot !== null) {
                            // If it's an object, try to get a suitable property
                            const value = slot.time || slot.value || JSON.stringify(slot);
                            const display = slot.display || slot.label || value;

                            option.value = value;
                            option.textContent = display;
                        }

                        timeSlotSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error("Error loading time slots:", error);
                    timeSlotSelect.innerHTML = '<option value="">Error loading slots</option>';
                }
            }

            doctorSelect.addEventListener('change', loadTimeSlots);
            dateInput.addEventListener('change', loadTimeSlots);

            // Form validation and AJAX submission
            appointmentForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                console.log("Form submitted with values:", {
                    doctor: doctorSelect.value,
                    date: dateInput.value,
                    timeSlot: timeSlotSelect.value,
                    reason: reasonInput.value,
                    consultationType: consultationType.value
                });
                
                if (!doctorSelect.value || !dateInput.value || !timeSlotSelect.value || !reasonInput.value) {
                    alert('Please fill all required fields');
                    return;
                }
                
                // Reset any previous messages
                errorContainer.style.display = 'none';
                successContainer.style.display = 'none';
                
                // Show loading indicator
                bookButton.disabled = true;
                loadingIndicator.style.display = 'block';
                
                if (consultationType.value === 'Online') {
                    loadingMessage.textContent = 'Processing appointment and creating Google Meet link. This might take up to 30 seconds...';
                } else {
                    loadingMessage.textContent = 'Processing appointment...';
                }
                
                try {
                    // Create form data for AJAX submission
                    const formData = new FormData(appointmentForm);
                    
                    console.log("Submitting form to:", `${basePath}/patient/process-appointment-with-meet-link`);
                    
                    // Submit the form via AJAX
                    const response = await fetch(`${basePath}/patient/process-appointment-with-meet-link`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    // Check if we got a valid response
                    const contentType = response.headers.get('content-type');
                    console.log("Response status:", response.status, "Content-Type:", contentType);
                    
                    let result;
                    
                    if (contentType && contentType.includes('application/json')) {
                        // Response is JSON
                        try {
                            result = await response.json();
                            console.log("Received JSON response:", result);
                        } catch (jsonError) {
                            console.error("Error parsing JSON:", jsonError);
                            const responseText = await response.text();
                            console.error("Raw response:", responseText);
                            throw new Error("Failed to parse JSON response: " + jsonError.message);
                        }
                    } else {
                        // Response is not JSON, get the text and log it
                        const responseText = await response.text();
                        console.error('Received non-JSON response:', responseText);
                        
                        // Try to extract error message from HTML response
                        let errorMessage = 'Server returned an invalid response';
                        
                        // Check if this is an HTML error page
                        if (responseText.includes('<body>')) {
                            // Try to extract error message from common PHP error format
                            const errorMatch = responseText.match(/<b>Fatal error<\/b>:(.+?)<br/i);
                            if (errorMatch && errorMatch[1]) {
                                errorMessage = 'PHP Error: ' + errorMatch[1].trim();
                            }
                        }
                        
                        throw new Error(errorMessage);
                    }
                    
                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';
                    
                    if (result.success) {
                        // Show success message
                        successMessage.textContent = result.message;
                        successContainer.style.display = 'block';
                        
                        // If it's an online appointment and a meet link was generated
                        if (consultationType.value === 'Online' && result.meet_link) {
                            meetLinkUrl.href = result.meet_link;
                            meetLinkUrl.textContent = result.meet_link;
                            meetLinkContainer.style.display = 'block';
                        } else {
                            meetLinkContainer.style.display = 'none';
                        }
                        
                        // Clear the form
                        appointmentForm.reset();
                        timeSlotSelect.innerHTML = '<option value="">Select date and doctor first</option>';
                        timeSlotSelect.disabled = true;
                        
                        // Show warning if Meet link failed for online appointment
                        if (consultationType.value === 'Online' && result.meet_link_failed) {
                            const warningDiv = document.createElement('div');
                            warningDiv.style.color = '#e67e22';
                            warningDiv.style.marginTop = '10px';
                            warningDiv.style.padding = '8px';
                            warningDiv.style.backgroundColor = '#fdf2e9';
                            warningDiv.style.borderLeft = '4px solid #e67e22';
                            warningDiv.innerHTML = '<strong>Note:</strong> The appointment was booked, but we couldn\'t generate a Google Meet link. You can view your appointment on the dashboard.';
                            successContainer.appendChild(warningDiv);
                        }
                    } else {
                        // Show error message
                        errorMessage.textContent = result.message || 'Failed to book appointment.';
                        errorContainer.style.display = 'block';
                        bookButton.disabled = false;
                    }
                } catch (error) {
                    console.error("Error booking appointment:", error);
                    loadingIndicator.style.display = 'none';
                    bookButton.disabled = false;
                    
                    let errorText = 'Failed to book appointment. Please try again.';
                    if (error.message) {
                        errorText = error.message;
                    }
                    
                    errorMessage.textContent = errorText;
                    errorContainer.style.display = 'block';
                }
            });

            // Show a note when online consultation is selected
            consultationType.addEventListener('change', function() {
                if (this.value === 'Online') {
                    if (!document.getElementById('meet-note')) {
                        const noteDiv = document.createElement('div');
                        noteDiv.id = 'meet-note';
                        noteDiv.className = 'info-message';
                        noteDiv.style.marginTop = '10px';
                        noteDiv.innerHTML = 'A Google Meet link will be automatically generated for your online consultation.';
                        consultationType.parentNode.appendChild(noteDiv);
                    }
                } else {
                    const noteDiv = document.getElementById('meet-note');
                    if (noteDiv) {
                        noteDiv.remove();
                    }
                }
            });

            // Trigger the change event to show the note if Online is already selected
            if (consultationType.value === 'Online') {
                consultationType.dispatchEvent(new Event('change'));
            }

            // Document file size validation
            document.getElementById('documents').addEventListener('change', function(e) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                let files = e.target.files;

                for (let file of files) {
                    if (file.size > maxSize) {
                        alert('File ' + file.name + ' is too large. Maximum size is 5MB');
                        e.target.value = '';
                        return;
                    }
                }
            });
        });
    </script>
</body>

</html>