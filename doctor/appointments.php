<?php
session_start();
require_once 'config.php';

// Check if doctor is logged in
// if (!isset($_SESSION['doctor_id'])) {
//     header('Location: login.php');
//     exit();
// }

// $doctor_id = $_SESSION['doctor_id'];

$doctor_id = 1;
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_availability':
                $day = $_POST['day_of_week'];
                $start_time = $_POST['start_time'];
                $end_time = $_POST['end_time'];
                
                $stmt = $conn->prepare("INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $doctor_id, $day, $start_time, $end_time);
                $stmt->execute();
                break;

            case 'update_appointment':
                $appointment_id = $_POST['appointment_id'];
                $status = $_POST['status'];
                
                $stmt = $conn->prepare("UPDATE appointments SET appointment_status = ? WHERE appointment_id = ? AND doctor_id = ?");
                $stmt->bind_param("sii", $status, $appointment_id, $doctor_id);
                $stmt->execute();
                break;
        }
    }
}

// Get all appointments
$appointments_query = "SELECT 
    a.appointment_id,
    a.appointment_date,
    a.appointment_time,
    a.appointment_status,
    a.consultation_type,
    a.reason_for_visit,
    u.first_name,
    u.last_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    WHERE a.doctor_id = ?
    ORDER BY a.appointment_date, a.appointment_time";

$stmt = $conn->prepare($appointments_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result();

// Get doctor's availability
$availability_query = "SELECT * FROM doctor_availability WHERE doctor_id = ? AND is_active = 1 ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$stmt = $conn->prepare($availability_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$availability = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Dashboard - Appointments</title>
    <link rel="stylesheet" href="styles.css">
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
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="appointments.php" class="nav-item active">
                    <i class="ri-calendar-line"></i>
                    <span>Appointments</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="ri-nurse-line"></i>
                    <span>Profile</span>
                </a>
            </nav>

            <a href="#" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Appointments</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search appointments..." id="searchInput">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Tab Navigation -->
            <div class="filters-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-tab="appointments">All Appointments</button>
                    <button class="filter-tab" data-tab="availability">Set Available Times</button>
                </div>
                <button class="action-btn" onclick="showAddAvailabilityForm()">
                    <i class="ri-add-line"></i>
                    Add Time Slot
                </button>
            </div>

            <!-- Appointments Section -->
            <section id="appointmentsTab" class="appointments-wrapper">
                <?php while($appointment = $appointments->fetch_assoc()): ?>
                <div class="appointment-row" data-patient-name="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>">
                    <div class="patient-info">
                        <div class="avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="info-details">
                            <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                            <span><?php echo htmlspecialchars($appointment['consultation_type']); ?></span>
                        </div>
                    </div>

                    <div class="appointment-info">
                        <div class="info-item">
                            <i class="ri-calendar-line"></i>
                            <span><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="ri-time-line"></i>
                            <span><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></span>
                        </div>
                        <span class="status"><?php echo htmlspecialchars($appointment['appointment_status']); ?></span>
                    </div>

                    <div class="row-actions">
                        <button class="edit-btn" onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-pencil-line"></i>
                            Update Status
                        </button>
                        <?php if($appointment['appointment_status'] === 'Scheduled'): ?>
                        <button class="cancel-btn" onclick="cancelAppointment(<?php echo $appointment['appointment_id']; ?>)">
                            <i class="ri-close-line"></i>
                            Cancel
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </section>

            <!-- Availability Section -->
            <section id="availabilityTab" class="appointments-wrapper" style="display: none;">
                <div class="availability-grid">
                    <?php while($slot = $availability->fetch_assoc()): ?>
                    <div class="time-slot-card">
                        <div class="time-slot-info">
                            <h3><?php echo htmlspecialchars($slot['day_of_week']); ?></h3>
                            <div class="time">
                                <i class="ri-time-line"></i>
                                <span><?php echo date('H:i', strtotime($slot['start_time'])) . ' - ' . date('H:i', strtotime($slot['end_time'])); ?></span>
                            </div>
                        </div>
                        <div class="slot-actions">
                            <button class="edit-btn" onclick="editAvailability(<?php echo $slot['availability_id']; ?>)">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button class="cancel-btn" onclick="deleteAvailability(<?php echo $slot['availability_id']; ?>)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>

            <!-- Add Availability Form Modal -->
            <div id="availabilityModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Add Available Time Slot</h2>
                    <form id="availabilityForm" action="appointments.php" method="POST">
                        <input type="hidden" name="action" value="add_availability">
                        <div class="form-group">
                            <label for="day_of_week">Day of Week:</label>
                            <select name="day_of_week" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Start Time:</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">End Time:</label>
                            <input type="time" name="end_time" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const tabId = this.dataset.tab;
                document.getElementById('appointmentsTab').style.display = tabId === 'appointments' ? 'block' : 'none';
                document.getElementById('availabilityTab').style.display = tabId === 'availability' ? 'block' : 'none';
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.appointment-row').forEach(row => {
                const patientName = row.dataset.patientName.toLowerCase();
                row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
            });
        });

        // Modal functions
        function showAddAvailabilityForm() {
            document.getElementById('availabilityModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('availabilityModal').style.display = 'none';
        }

        // Appointment functions
        function updateAppointmentStatus(appointmentId) {
            const status = prompt('Enter new status (Scheduled/Completed/Canceled/Rescheduled):');
            if (status) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="${appointmentId}">
                    <input type="hidden" name="status" value="${status}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_appointment">
                    <input type="hidden" name="appointment_id" value="${appointmentId}">
                    <input type="hidden" name="status" value="Canceled">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Availability functions
        function editAvailability(availabilityId) {
            // Add your edit availability logic here
        }

        function deleteAvailability(availabilityId) {
            if (confirm('Are you sure you want to delete this availability slot?')) {
                // Add your delete availability logic here
            }
        }
    </script>
</body>
</html>