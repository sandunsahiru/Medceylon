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
            <form id="bookingForm" method="POST" action="<?php echo $basePath; ?>/doctor/process-booking">
                <input type="hidden" name="specialist_id" id="specialist_id">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="patient_id">Book For:</label>
                    <select name="patient_id" id="patient_id" required>
                        <?php foreach($patients as $patient): ?>
                            <option value="<?php echo $patient['user_id']; ?>">
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="consultation_type">Consultation Type:</label>
                    <select name="consultation_type" required>
                        <option value="Online">Online</option>
                        <option value="In-Person">In-Person</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="medical_history">Notes:</label>
                    <textarea name="medical_history" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="preferred_date">Preferred Date:</label>
                    <input type="date" name="preferred_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">Request Appointment</button>
                    <button type="button" class="cancel-btn" onclick="closeBookingModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Doctor Profile Functions
        function viewDoctorProfile(doctorId) {
            fetch(`${basePath}/doctor/get-doctor-profile/${doctorId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('doctorContent').innerHTML = `
                        <div class="profile-details">
                            <p><strong>Qualifications:</strong> ${data.qualifications}</p>
                            <p><strong>Experience:</strong> ${data.years_of_experience} years</p>
                            <p><strong>Hospital:</strong> ${data.hospital_name}</p>
                            <p><strong>Contact:</strong> ${data.email}</p>
                            <p><strong>Profile:</strong> ${data.profile_description}</p>
                        </div>
                    `;
                    document.getElementById('doctorModal').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error fetching doctor profile');
                });
        }

        function closeModal() {
            document.getElementById('doctorModal').style.display = 'none';
        }

        // Booking Functions
        function bookAppointment(doctorId) {
            document.getElementById('specialist_id').value = doctorId;
            document.getElementById('bookingModal').style.display = 'block';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

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