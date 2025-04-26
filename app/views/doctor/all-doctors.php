<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Specialist Doctors</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search doctors..." id="searchInput">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-user-star-line"></i>
                <div class="stats-info">
                    <h3>Total Specialists</h3>
                    <p><?php echo $stats['total_doctors']; ?></p>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-heart-pulse-line"></i>
                <div class="stats-info">
                    <h3>Specializations</h3>
                    <p><?php echo $stats['total_specializations']; ?></p>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-hospital-line"></i>
                <div class="stats-info">
                    <h3>Hospitals</h3>
                    <p><?php echo $stats['total_hospitals']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <section class="doctors-wrapper">
        <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-row" data-doctor-name="<?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>">
                    <div class="doctor-info">
                        <div class="avatar">
                            <i class="ri-user-star-line"></i>
                        </div>
                        <div class="info-details">
                            <h3>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                            <span><?php echo htmlspecialchars($doctor['specializations']); ?></span>
                        </div>
                    </div>

                    <div class="doctor-stats">
                        <div class="stat-item">
                            <i class="ri-hospital-line"></i>
                            <span><?php echo htmlspecialchars($doctor['hospital_name']); ?></span>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <span><?php echo $doctor['years_of_experience']; ?> years experience</span>
                        </div>
                    </div>

                    <div class="row-actions">
                        <button class="view-btn" onclick="viewDoctorProfile(<?php echo $doctor['doctor_id']; ?>)">
                            <i class="ri-file-list-line"></i>
                            View Profile
                        </button>
                        <button class="schedule-btn" onclick="bookAppointment(<?php echo $doctor['doctor_id']; ?>)">
                            <i class="ri-calendar-line"></i>
                            Book
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No specialist doctors found.</p>
            </div>
        <?php endif; ?>
    </section>
</main>
</div>

<!-- Doctor Profile Modal -->
<div id="doctorModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Doctor Profile</h2>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>
        <div id="doctorContent"></div>
    </div>
</div>

<!-- Book Appointment Modal -->
<div id="bookingModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Book Specialist Appointment</h2>
            <button onclick="closeBookingModal()" class="close-btn">&times;</button>
        </div>
        <form id="bookingForm" method="POST" action="<?php echo $basePath; ?>/doctor/processBooking">
            <input type="hidden" name="specialist_id" id="specialist_id">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div class="form-group">
                <label for="patient_id">Book For:*</label>
                <select name="patient_id" id="patient_id" required>
                    <option value="">Select Patient</option>
                </select>
            </div>

            <div class="form-group">
                <label for="consultation_type">Consultation Type:*</label>
                <select name="consultation_type" id="consultation_type" required>
                    <option value="">Select Type</option>
                    <option value="Online">Online</option>
                    <option value="In-Person">In-Person</option>
                </select>
            </div>

            <div class="form-group">
                <label for="preferred_date">Preferred Date:*</label>
                <input type="date" name="preferred_date" id="preferred_date"
                    required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <!-- Time slots will be dynamically inserted here -->
            <div id="time_slot_container"></div>

            <div class="form-group">
                <label for="medical_history">Notes:</label>
                <textarea name="medical_history" id="medical_history" rows="4"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Request Appointment</button>
                <button type="button" class="cancel-btn" onclick="closeBookingModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const basePath = '<?php echo $basePath; ?>';

    // ============================================================
    // DOCTOR PROFILE FUNCTIONS
    // ============================================================
    
    function viewDoctorProfile(doctorId) {
        fetch(`${basePath}/doctor/getDocProfile?doctor_id=${doctorId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                if (!response.success) {
                    throw new Error(response.error || 'Failed to fetch doctor profile');
                }

                const data = response.data;
                document.getElementById('doctorContent').innerHTML = `
                    <div class="profile-details">
                        <p><strong>Name:</strong> Dr. ${data.first_name} ${data.last_name}</p>
                        <p><strong>Specializations:</strong> ${data.specializations || 'Not specified'}</p>
                        <p><strong>Qualifications:</strong> ${data.qualifications || 'Not specified'}</p>
                        <p><strong>Experience:</strong> ${data.years_of_experience || 0} years</p>
                        <p><strong>Hospital:</strong> ${data.hospital_name || 'Not specified'}</p>
                        <p><strong>Email:</strong> ${data.email || 'Not specified'}</p>
                        <p><strong>Phone:</strong> ${data.phone_number || 'Not specified'}</p>
                        <p><strong>Profile:</strong> ${data.profile_description || 'No description available'}</p>
                    </div>
                `;
                document.getElementById('doctorModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching doctor profile: ' + error.message);
            });
    }

    function closeModal() {
        document.getElementById('doctorModal').style.display = 'none';
    }

    // ============================================================
    // BOOKING APPOINTMENT FUNCTIONS
    // ============================================================

    // Function to show booking modal and load patients
    function bookAppointment(doctorId) {
        // Set the specialist ID
        document.getElementById('specialist_id').value = doctorId;

        // Load patients first with error handling and logging
        fetch(`${basePath}/doctor/getPatients`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(response => {
                console.log('Received patients:', response); // Debug log

                const patientSelect = document.getElementById('patient_id');
                if (!response || response.error) {
                    throw new Error(response.error || 'Failed to load patients');
                }

                const patients = Array.isArray(response) ? response : [];

                if (patients.length === 0) {
                    patientSelect.innerHTML = '<option value="">No patients available</option>';
                } else {
                    patientSelect.innerHTML = '<option value="">Select Patient</option>' +
                        patients.map(patient =>
                            `<option value="${patient.user_id || patient.patient_id}">
                            ${patient.first_name} ${patient.last_name} 
                            ${patient.total_visits ? `(${patient.total_visits} visits)` : ''}
                         </option>`
                        ).join('');
                }

                // Show the modal after loading patients
                document.getElementById('bookingModal').style.display = 'block';

                // Set up date change listener for time slots
                setupDateListener(doctorId);
            })
            .catch(error => {
                console.error('Error loading patients:', error);
                const patientSelect = document.getElementById('patient_id');
                patientSelect.innerHTML = '<option value="">Error loading patients</option>';
                alert('Error loading patients: ' + error.message);
            });
    }

    // Function to setup date listener
    function setupDateListener(doctorId) {
        const dateInput = document.querySelector('input[name="preferred_date"]');
        if (dateInput) {
            // Clear existing event listeners by cloning
            const newDateInput = dateInput.cloneNode(true);
            dateInput.parentNode.replaceChild(newDateInput, dateInput);

            // Add new event listener
            newDateInput.addEventListener('change', function() {
                if (this.value) {
                    fetchTimeSlots(doctorId, this.value);
                }
            });
        }
    }

    // Function to fetch time slots
    function fetchTimeSlots(doctorId, date) {
        fetch(`${basePath}/doctor/getTimeSlots?doctor_id=${doctorId}&date=${date}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Time slots response:', data); // Debug log
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to fetch time slots');
                }

                // Get or create time slot container
                let timeSlotsContainer = document.getElementById('time_slot_container');
                if (!timeSlotsContainer) {
                    timeSlotsContainer = document.createElement('div');
                    timeSlotsContainer.id = 'time_slot_container';
                    timeSlotsContainer.className = 'form-group';
                    const formActions = document.querySelector('.form-actions');
                    formActions.parentNode.insertBefore(timeSlotsContainer, formActions);
                }

                if (!data.slots || data.slots.length === 0) {
                    timeSlotsContainer.innerHTML = `
                    <label>Available Time Slots:</label>
                    <p class="error-message">No time slots available for selected date</p>
                `;
                    return;
                }

                // Use the time slots directly as they come from the server
                timeSlotsContainer.innerHTML = `
                <label for="appointment_time">Available Time Slots:*</label>
                <select name="appointment_time" id="appointment_time" required>
                    <option value="">Select Time</option>
                    ${data.slots.map(slot => `<option value="${slot}">${slot}</option>`).join('')}
                </select>
            `;
            })
            .catch(error => {
                console.error('Error:', error);
                const timeSlotsContainer = document.getElementById('time_slot_container');
                if (timeSlotsContainer) {
                    timeSlotsContainer.innerHTML = `
                    <label>Available Time Slots:</label>
                    <p class="error-message">Error loading time slots: ${error.message}</p>
                `;
                }
            });
    }

    // ============================================================
    // FORM SUBMISSION & MODAL CONTROL
    // ============================================================

    // Form submission handler
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Log the form data
        const formData = new FormData(this);
        console.log('Form Data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Validate form
        const required = ['specialist_id', 'patient_id', 'consultation_type', 'preferred_date', 'appointment_time'];

        // Log validation results
        console.log('Validation Check:');
        for (const field of required) {
            const value = formData.get(field);
            console.log(`${field}: ${value}`);
            if (!value) {
                alert(`Please fill in all required fields (${field.replace('_', ' ')})`);
                return;
            }
        }

        // Submit form with proper headers
        console.log('Submitting form to:', this.action);
        fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text().then(text => {
                    console.log('Raw response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Server returned invalid response: ' + text);
                    }
                });
            })
            .then(data => {
                console.log('Parsed response data:', data);
                if (data.success) {
                    alert('Appointment request sent successfully!');
                    closeBookingModal();
                    window.location.reload();
                } else {
                    throw new Error(data.error || 'Failed to book appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error booking appointment: ' + error.message);
            });
    });

    // Function to close booking modal
    function closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
        
        // Reset form fields
        document.getElementById('bookingForm').reset();
        
        // Clear time slots container
        const timeSlotsContainer = document.getElementById('time_slot_container');
        if (timeSlotsContainer) {
            timeSlotsContainer.innerHTML = '';
        }
    }

    // ============================================================
    // OTHER FUNCTIONALITY
    // ============================================================

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.doctor-row').forEach(row => {
            const doctorName = row.dataset.doctorName.toLowerCase();
            row.style.display = doctorName.includes(searchTerm) ? 'flex' : 'none';
        });
    });

    // Show success/error messages if they exist
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('booking')) {
        const status = urlParams.get('booking');
        if (status === 'success') {
            alert('Appointment request sent successfully');
        } else if (status === 'error') {
            const message = urlParams.get('message') || 'Error booking appointment';
            alert(message);
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target === document.getElementById('doctorModal')) {
            closeModal();
        }
        if (event.target === document.getElementById('bookingModal')) {
            closeBookingModal();
        }
    }
</script>

</body>
</html>