<?php
session_start();
require_once '../includes/config.php';

// Manual session for doctor id 
$_SESSION['user_id'] = 7;

// Get doctor_id from doctors table using user_id
$doctor_query = "SELECT doctor_id FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($doctor_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$doctor_result = $stmt->get_result();
$doctor_data = $doctor_result->fetch_assoc();
$doctor_id = $doctor_data['doctor_id'];

// Get total patients count and gender distribution
$total_query = "SELECT 
    COUNT(DISTINCT a.patient_id) as total,
    SUM(CASE WHEN u.gender = 'Female' THEN 1 ELSE 0 END) as women,
    SUM(CASE WHEN u.gender = 'Male' THEN 1 ELSE 0 END) as men
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id 
    WHERE a.doctor_id = ?";

$stmt = $conn->prepare($total_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_data = $total_result->fetch_assoc();

if (!$total_data) {
    $total_data = ['total' => 0, 'women' => 0, 'men' => 0];
}

// Get appointment requests (status = 'Asked')
$requests_query = "SELECT 
    u.first_name, 
    u.last_name,
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.reason_for_visit as patient_notes,
    a.consultation_type
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    WHERE a.doctor_id = ? 
    AND a.appointment_status = 'Asked'
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5";

$stmt = $conn->prepare($requests_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$requests = $stmt->get_result();

// Get scheduled appointments
$scheduled_query = "SELECT 
    u.first_name, 
    u.last_name,
    a.appointment_date,
    a.appointment_time,
    a.consultation_type
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    WHERE a.doctor_id = ?
    AND a.appointment_status = 'Scheduled'
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5";

$stmt = $conn->prepare($scheduled_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$scheduled = $stmt->get_result();

// Get counts for stats cards
$total_appointments_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN appointment_status = 'Scheduled' THEN 1 ELSE 0 END) as scheduled,
    SUM(CASE WHEN appointment_status = 'Asked' THEN 1 ELSE 0 END) as pending
    FROM appointments 
    WHERE doctor_id = ?";

$stmt = $conn->prepare($total_appointments_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments_count = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specialist Dashboard</title>
    <link rel="stylesheet" href="../assets/css/doctordashboard.css">
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
                <a href="appointments.php" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Appointments</span>
                </a>
                <a href="patients.php" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="ri-chat-1-line"></i>
                    <span>Chat</span>
                </a>
            </nav>
            <a href="../logout.php" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <!-- Header -->
            <header class="top-bar">
                <h1>Specialist Dashboard</h1>
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

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stats-card patients-overview">
                    <div class="stats-header">
                        <h2><?php echo $total_data['total']; ?></h2>
                        <p>Patients</p>
                    </div>
                    <!-- <div class="stats-details">
                        <div class="gender-stat">
                            <i class="ri-women-line"></i>
                            <span>Women <?php echo $total_data['total'] > 0 ? round(($total_data['women'] / $total_data['total']) * 100) : 0; ?>%</span>
                        </div>
                        <div class="gender-stat">
                            <i class="ri-men-line"></i>
                            <span>Men <?php echo $total_data['total'] > 0 ? round(($total_data['men'] / $total_data['total']) * 100) : 0; ?>%</span>
                        </div>
                    </div> -->
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-group-line"></i>
                        <div class="stats-info">
                            <h3>All Patients</h3>
                            <p><?php echo $total_data['total']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>Scheduled Appointments</h3>
                            <p><?php echo $scheduled->num_rows; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Appointment Requests Section -->
            <section class="appointments-section">
                <h2>New Appointment Requests</h2>
                <div class="appointments-list">
                    <?php if ($requests->num_rows > 0): ?>
                        <?php while ($request = $requests->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($request['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($request['appointment_date'])); ?></p>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn view-details" data-id="<?php echo $request['appointment_id']; ?>"
                                        data-notes="<?php echo htmlspecialchars($request['patient_notes']); ?>">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button class="action-btn confirm-appointment" data-id="<?php echo $request['appointment_id']; ?>">
                                        <i class="ri-check-line"></i>
                                    </button>
                                    <button class="action-btn reschedule-appointment" data-id="<?php echo $request['appointment_id']; ?>">
                                        <i class="ri-time-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No new appointment requests</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Scheduled Appointments Section -->
            <section class="appointments-section">
                <h2>Scheduled Appointments</h2>
                <div class="appointments-list">
                    <?php if ($scheduled->num_rows > 0): ?>
                        <?php while ($appointment = $scheduled->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button class="action-btn">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-data">No scheduled appointments</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Appointment Details Modal -->
            <div id="appointmentModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Appointment Details</h2>
                    <div id="appointmentDetails"></div>
                    <div class="form-actions">
                        <button class="submit-btn" id="confirmAppointment">Confirm</button>
                        <button class="cancel-btn" id="closeModal">Close</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Appointment Rechedule Modal -->
    <div id="rescheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Reschedule Appointment</h2>
        <form id="rescheduleForm">
            <input type="hidden" id="rescheduleAppointmentId">
            <div class="form-group">
                <label for="newDate">New Date:</label>
                <input type="date" id="newDate" required>
            </div>
            <div class="form-group">
                <label for="newTime">New Time:</label>
                <input type="time" id="newTime" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn">Reschedule</button>
                <button type="button" class="cancel-btn" id="closeRescheduleModal">Cancel</button>
            </div>
        </form>
    </div>
</div>

    <script src="../assets/js/doctorscript.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('appointmentModal');
            const closeModal = document.getElementById('closeModal');

            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const notes = this.dataset.notes;
                    const appointmentId = this.dataset.id;
                    document.getElementById('appointmentDetails').innerHTML = `
                        <p><strong>Patient Notes:</strong></p>
                        <p>${notes}</p>
                    `;
                    modal.style.display = 'flex';
                });
            });

            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            document.getElementById('confirmAppointment').addEventListener('click', function() {
                // Add AJAX call to update appointment status
                const appointmentId = document.querySelector('.view-details').dataset.id;
                fetch('update_appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `appointment_id=${appointmentId}&status=Scheduled`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                modal.style.display = 'none';
            });
        });
        // Add this to your existing script section
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('appointmentModal');
            const rescheduleModal = document.getElementById('rescheduleModal');
            const confirmBtns = document.querySelectorAll('.confirm-appointment');
            const rescheduleBtns = document.querySelectorAll('.reschedule-appointment');

            confirmBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const appointmentId = this.dataset.id;
                    if (confirm('Are you sure you want to confirm this appointment?')) {
                        updateAppointmentStatus(appointmentId, 'Scheduled');
                    }
                });
            });

            rescheduleBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const appointmentId = this.dataset.id;
                    document.getElementById('rescheduleAppointmentId').value = appointmentId;
                    rescheduleModal.style.display = 'flex';
                });
            });

            document.getElementById('closeRescheduleModal').addEventListener('click', function() {
                rescheduleModal.style.display = 'none';
            });

            document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const appointmentId = document.getElementById('rescheduleAppointmentId').value;
                const newDate = document.getElementById('newDate').value;
                const newTime = document.getElementById('newTime').value;

                fetch('update_appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `appointment_id=${appointmentId}&status=Rescheduled&new_date=${newDate}&new_time=${newTime}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Failed to reschedule appointment. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });

            function updateAppointmentStatus(appointmentId, status) {
                fetch('update_appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `appointment_id=${appointmentId}&status=${status}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Failed to update appointment status. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            }
        });
    </script>
</body>

</html>