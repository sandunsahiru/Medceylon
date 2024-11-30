<?php
session_start();
require_once '../includes/config.php';

// Basic error checking
if (!isset($conn)) {
    die("Database connection failed");
}

$doctor_id = 1;

// Get basic statistics
$stats_query = "SELECT 
    COUNT(DISTINCT patient_id) as total_patients,
    SUM(CASE WHEN appointment_status = 'Completed' THEN 1 ELSE 0 END) as completed_visits,
    SUM(CASE WHEN appointment_date = CURRENT_DATE THEN 1 ELSE 0 END) as today_visits,
    SUM(CASE WHEN appointment_status = 'Scheduled' THEN 1 ELSE 0 END) as upcoming_appointments
    FROM appointments 
    WHERE doctor_id = ?";

try {
    $stmt = $conn->prepare($stats_query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    $stats = [
        'total_patients' => 0,
        'completed_visits' => 0,
        'today_visits' => 0,
        'upcoming_appointments' => 0
    ];
}

// Get patients list
$patients_query = "SELECT 
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.phone_number,
    u.gender,
    COUNT(a.appointment_id) as total_visits,
    MAX(a.appointment_date) as last_visit
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    WHERE a.doctor_id = ?
    GROUP BY u.user_id
    ORDER BY last_visit DESC";

try {
    $stmt = $conn->prepare($patients_query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $patients = $stmt->get_result();
} catch (Exception $e) {
    die("Error fetching patient data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Patients</title>
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
                <a href="patients.php" class="nav-item active">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="all-doctors.php" class="nav-item">
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
                <h1>Patients</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search patients..." id="searchInput">
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
                        <i class="ri-user-heart-line"></i>
                        <div class="stats-info">
                            <h3>Total Patients</h3>
                            <p><?php echo $stats['total_patients']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>Completed Visits</h3>
                            <p><?php echo $stats['completed_visits']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-todo-line"></i>
                        <div class="stats-info">
                            <h3>Upcoming</h3>
                            <p><?php echo $stats['upcoming_appointments']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <section class="patients-wrapper">
                <?php while ($patient = $patients->fetch_assoc()): ?>
                    <div class="patient-row" data-patient-name="<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>">
                        <div class="patient-info">
                            <div class="avatar">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="info-details">
                                <h3><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h3>
                                <span><?php echo htmlspecialchars($patient['email']); ?></span>
                            </div>
                        </div>

                        <div class="patient-stats">
                            <div class="stat-item">
                                <i class="ri-phone-line"></i>
                                <span><?php echo htmlspecialchars($patient['phone_number'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="stat-item">
                                <i class="ri-calendar-check-line"></i>
                                <span><?php echo $patient['total_visits']; ?> visits</span>
                            </div>
                            <div class="stat-item">
                                <i class="ri-time-line"></i>
                                <span>Last: <?php echo date('d/m/Y', strtotime($patient['last_visit'])); ?></span>
                            </div>
                        </div>

                        <div class="row-actions">
                            <!-- <button class="view-btn" onclick="viewPatientHistory(<?php echo $patient['user_id']; ?>)">
                                <i class="ri-file-list-line"></i>
                                History
                            </button> -->
                            <button class="schedule-btn" onclick="scheduleAppointment(<?php echo $patient['user_id']; ?>)">
                                <i class="ri-calendar-line"></i>
                                History
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>
        </main>
    </div>

    <div id="historyModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Patient History</h2>
                <button onclick="closeHistoryModal()" class="close-btn">&times;</button>
            </div>
            <div id="historyContent"></div>
        </div>
    </div>
    <!-- Add this modal HTML before closing body tag -->
    <div id="scheduleModal" class="modal" style="display: none;">
        <div class="modal-content schedule-modal">
            <div class="modal-header">
                <h2>Appointment History</h2>
                <button onclick="closeScheduleModal()" class="close-btn">&times;</button>
            </div>
            <div id="appointmentContent" class="appointment-list"></div>
            
            </button>
        </div>
    </div>


    <script>
        function scheduleAppointment(patientId) {
            fetch(`get_patient_appointments.php?patient_id=${patientId}&doctor_id=<?php echo $doctor_id; ?>`)
                .then(response => response.json())
                .then(data => {
                    const appointmentContent = document.getElementById('appointmentContent');
                    appointmentContent.innerHTML = '';

                    data.forEach(appointment => {
                        const statusClass = `status-${appointment.appointment_status.toLowerCase()}`;
                        appointmentContent.innerHTML += `
                    <div class="appointment-item">
                        <div class="appointment-header">
                            <span class="appointment-date">
                                ${appointment.appointment_date} at ${appointment.appointment_time}
                            </span>
                            <span class="appointment-status ${statusClass}">
                                ${appointment.appointment_status}
                            </span>
                        </div>
                        <div class="appointment-details">
                            <p>Type: ${appointment.consultation_type}</p>
                            <p>Reason: ${appointment.reason_for_visit || 'Not specified'}</p>
                        </div>
                    </div>
                `;
                    });

                    document.getElementById('scheduleModal').style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').style.display = 'none';
        }
    </script>

</body>

</html>