<?php
session_start();
require_once '../includes/config.php';

$patient_id = 1;

try {
    $appointments_query = "SELECT 
        a.appointment_id,
        a.appointment_date,
        a.appointment_time,
        a.appointment_status,
        a.consultation_type,
        a.reason_for_visit,
        u.first_name,
        u.last_name,
        s.name as specialization,
        h.name as hospital_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u ON d.user_id = u.user_id
        JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
        JOIN specializations s ON ds.specialization_id = s.specialization_id
        JOIN hospitals h ON d.hospital_id = h.hospital_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date, a.appointment_time 
        LIMIT 5";

    if (!$conn) throw new Exception("Database connection failed");

    $stmt = $conn->prepare($appointments_query);
    if (!$stmt) throw new Exception("Query preparation failed: " . $conn->error);

    $stmt->bind_param("i", $patient_id);
    if (!$stmt->execute()) throw new Exception("Query execution failed: " . $stmt->error);

    $appointments = $stmt->get_result();
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $appointments = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - MediCare</title>
    <link rel="stylesheet" href="../assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Medceylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-item active">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="book-appointment.php" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="medical-history.php" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="profile.php" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>
            
            <a href="../logout.php" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>My Appointments</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Appointments Section -->
            <section class="appointments-section">
                <div class="appointments-list">
                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                        <?php while ($appointment = $appointments->fetch_assoc()): ?>
                            <div class="appointment-card" data-appointment-id="<?php echo $appointment['appointment_id']; ?>">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3>Dr. <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($appointment['specialization']); ?></p>
                                    <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                                    <p><?php echo htmlspecialchars($appointment['hospital_name']); ?></p>
                                </div>
                                <div class="status-badge <?php echo strtolower($appointment['appointment_status']); ?>">
                                    <?php echo htmlspecialchars($appointment['appointment_status']); ?>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn view-details">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-appointments">
                            <p>No appointments found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="appointment-details">
                <!-- Details will be populated via JavaScript -->
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('appointmentModal');
        const closeBtn = modal.querySelector('.close-btn');
        
        // Add click event to all action buttons
        document.querySelectorAll('.action-btn.view-details').forEach(btn => {
            btn.addEventListener('click', async function() {
                const appointmentId = this.closest('.appointment-card').dataset.appointmentId;
                try {
                    const response = await fetch(`get_appointment_details.php?id=${appointmentId}`);
                    const data = await response.json();
                    
                    const detailsHtml = `
                        <div class="details-content">
                            <div class="doctor-details">
                                <h3>Doctor Information</h3>
                                <p><strong>Name:</strong> Dr. ${data.doctor.first_name} ${data.doctor.last_name}</p>
                                <p><strong>Specialization:</strong> ${data.specialization}</p>
                                <p><strong>Hospital:</strong> ${data.hospital}</p>
                            </div>
                            
                            <div class="appointment-details">
                                <h3>Appointment Information</h3>
                                <p><strong>Date:</strong> ${data.appointment.date}</p>
                                <p><strong>Time:</strong> ${data.appointment.time}</p>
                                <p><strong>Status:</strong> ${data.appointment.status}</p>
                                <p><strong>Type:</strong> ${data.appointment.consultation_type}</p>
                                <p><strong>Reason:</strong> ${data.appointment.reason_for_visit}</p>
                            </div>
                            
                            ${data.previous_appointment ? `
                                <div class="previous-appointment">
                                    <h3>Previous Appointment</h3>
                                    <p><strong>Date:</strong> ${data.previous_appointment.date}</p>
                                    <p><strong>Time:</strong> ${data.previous_appointment.time}</p>
                                    <p><strong>Status:</strong> ${data.previous_appointment.status}</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    modal.querySelector('.appointment-details').innerHTML = detailsHtml;
                    modal.style.display = 'flex';
                } catch (error) {
                    console.error('Error fetching appointment details:', error);
                }
            });
        });
        
        // Close modal when clicking close button or outside
        closeBtn.onclick = () => modal.style.display = 'none';
        window.onclick = (e) => {
            if (e.target === modal) modal.style.display = 'none';
        }
    });
    </script>
</body>
</html>