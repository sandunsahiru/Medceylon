<?php
session_start();
require_once '../includes/config.php';

// Get all specialist doctors
$doctors_query = "SELECT 
    d.doctor_id,
    u.first_name,
    u.last_name,
    u.email,
    u.phone_number,
    d.qualifications,
    d.years_of_experience,
    h.name as hospital_name,
    GROUP_CONCAT(DISTINCT s.name) as specializations
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id 
    JOIN hospitals h ON d.hospital_id = h.hospital_id
    LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
    LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
    WHERE u.role_id = 3 AND d.is_active = 1
    GROUP BY d.doctor_id, u.first_name, u.last_name, u.email, 
             u.phone_number, d.qualifications, d.years_of_experience, h.name";


$result = $conn->query($doctors_query);

// Get statistics
$stats_query = "SELECT 
    COUNT(DISTINCT d.doctor_id) as total_doctors,
    COUNT(DISTINCT s.specialization_id) as total_specializations,
    COUNT(DISTINCT h.hospital_id) as total_hospitals
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id
    LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
    LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
    JOIN hospitals h ON d.hospital_id = h.hospital_id
    WHERE u.role_id = 3";

$stats = $conn->query($stats_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - Specialist Doctors</title>
    <link rel="stylesheet" href="../assets/css/doctordashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <h1>MedCeylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="appointments.php" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Appointments</span>
                </a>
                <a href="patients.php" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="all-doctors.php" class="nav-item active">
                    <i class="ri-nurse-line"></i>
                    <span>Doctors</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="ri-chat-1-line"></i>
                    <span>Chat</span>
                </a>
            </nav>

            <a href="#" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($doctor = $result->fetch_assoc()): ?>
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
                    <?php endwhile; ?>
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
        <form id="bookingForm" method="POST" action="process_booking.php">
            <input type="hidden" name="specialist_id" id="specialist_id">
            <div class="form-group">
                <label for="patient_id">Book For:</label>
                <select name="patient_id" id="patient_id" required>
                    <?php 
                    $patients_query = "SELECT DISTINCT 
                        u.user_id, 
                        u.first_name, 
                        u.last_name 
                        FROM users u 
                        JOIN appointments a ON u.user_id = a.patient_id 
                        WHERE a.doctor_id = 1";
                    $patients = $conn->query($patients_query);
                    while($patient = $patients->fetch_assoc()):
                    ?>
                    <option value="<?php echo $patient['user_id']; ?>">
                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                    </option>
                    <?php endwhile; ?>
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
        function viewDoctorProfile(doctorId) {
            fetch(`get_doctor_profile.php?doctor_id=${doctorId}`)
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
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('doctorModal').style.display = 'none';
        }

        function bookAppointment(doctorId) {
            window.location.href = `book-appointment.php?doctor_id=${doctorId}`;
        }

        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.doctor-row').forEach(row => {
                const doctorName = row.dataset.doctorName.toLowerCase();
                row.style.display = doctorName.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        function bookAppointment(doctorId) {
            document.getElementById('specialist_id').value = doctorId;
            document.getElementById('bookingModal').style.display = 'block';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }

        // Add this to handle form submission success/error messages
        if (window.location.search.includes('booking=success')) {
            alert('Appointment request sent successfully');
        } else if (window.location.search.includes('booking=error')) {
            alert('Error booking appointment. Please try again.');
        }
    </script>
</body>

</html>