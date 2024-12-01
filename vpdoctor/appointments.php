<?php
session_start();
require_once '../includes/config.php';

// Get doctor_id 
$_SESSION['user_id'] = 7;
$doctor_query = "SELECT doctor_id FROM doctors WHERE user_id = ?";
$stmt = $conn->prepare($doctor_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$doctor_data = $stmt->get_result()->fetch_assoc();
$doctor_id = $doctor_data['doctor_id'];
$stmt->close();

// Handle availability management
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_availability':
            $stmt = $conn->prepare("INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $doctor_id, $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time']);
            break;

        case 'edit_availability':
            $stmt = $conn->prepare("UPDATE doctor_availability SET day_of_week = ?, start_time = ?, end_time = ? WHERE availability_id = ? AND doctor_id = ?");
            $stmt->bind_param("sssii", $_POST['day_of_week'], $_POST['start_time'], $_POST['end_time'], $_POST['availability_id'], $doctor_id);
            break;

        case 'delete_availability':
            $stmt = $conn->prepare("UPDATE doctor_availability SET is_active = 0 WHERE availability_id = ? AND doctor_id = ?");
            $stmt->bind_param("ii", $_POST['availability_id'], $doctor_id);
            break;
    }

    if ($stmt) {
        $stmt->execute();
        $stmt->close();
    }
    header('Location: appointments.php');
    exit;
}

// Get appointments by status
$new_appointments_query = "SELECT 
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
   WHERE a.doctor_id = ? AND a.appointment_status = 'Asked'
   ORDER BY a.appointment_date, a.appointment_time";

$stmt = $conn->prepare($new_appointments_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$new_appointments = $stmt->get_result();
$stmt->close();

// Get scheduled appointments
$scheduled_query = "SELECT 
   a.appointment_id,
   a.appointment_date,
   a.appointment_time,
   a.consultation_type,
   a.reason_for_visit,
   u.first_name,
   u.last_name
   FROM appointments a
   JOIN users u ON a.patient_id = u.user_id
   WHERE a.doctor_id = ? AND a.appointment_status = 'Scheduled'
   ORDER BY a.appointment_date, a.appointment_time";

$stmt = $conn->prepare($scheduled_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$scheduled_appointments = $stmt->get_result();
$stmt->close();

// Get rescheduled appointments
$rescheduled_query = "SELECT 
   a.appointment_id,
   a.appointment_date,
   a.appointment_time,
   a.consultation_type,
   a.reason_for_visit,
   u.first_name,
   u.last_name
   FROM appointments a
   JOIN users u ON a.patient_id = u.user_id
   WHERE a.doctor_id = ? AND a.appointment_status = 'Rescheduled'
   ORDER BY a.appointment_date, a.appointment_time";

$stmt = $conn->prepare($rescheduled_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$rescheduled_appointments = $stmt->get_result();
$stmt->close();

// Get availability slots
$availability_query = "SELECT * FROM doctor_availability 
   WHERE doctor_id = ? AND is_active = 1 
   ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$stmt = $conn->prepare($availability_query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$availability = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specialist Dashboard - Appointments</title>
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
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="appointments.php" class="nav-item active">
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
                    <button class="filter-tab active" data-tab="new">New Appointments</button>
                    <button class="filter-tab" data-tab="scheduled">Scheduled</button>
                    <button class="filter-tab" data-tab="rescheduled">Rescheduled</button>
                    <button class="filter-tab" data-tab="availability">Available Times</button>
                </div>
                <button class="action-btn" onclick="showAddAvailabilityForm()">
                    <i class="ri-add-line"></i>
                    Add Time Slot
                </button>
            </div>

            <!-- New Appointments Section -->
            <section id="newTab" class="appointments-wrapper">
                <?php while ($appointment = $new_appointments->fetch_assoc()): ?>
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
                            <div class="info-item">
                                <i class="ri-file-text-line"></i>
                                <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                            </div>
                        </div>

                        <div class="row-actions">
                            <button class="edit-btn" onclick="confirmAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-check-line"></i>
                                Confirm
                            </button>
                            <button class="cancel-btn" onclick="showRescheduleForm(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-time-line"></i>
                                Reschedule
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>


            <!-- Scheduled Appointments Section -->
            <section id="scheduledTab" class="appointments-wrapper" style="display: none;">
                <?php while ($appointment = $scheduled_appointments->fetch_assoc()): ?>
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
                            <div class="info-item">
                                <i class="ri-file-text-line"></i>
                                <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                            </div>
                        </div>

                        <div class="row-actions">
                            <button class="edit-btn" onclick="completeAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-check-double-line"></i>
                                Complete
                            </button>
                            <button class="cancel-btn" onclick="showRescheduleForm(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-time-line"></i>
                                Reschedule
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>

            <!-- Rescheduled Appointments Section -->
            <section id="rescheduledTab" class="appointments-wrapper" style="display: none;">
                <?php while ($appointment = $rescheduled_appointments->fetch_assoc()): ?>
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
                            <div class="info-item">
                                <i class="ri-file-text-line"></i>
                                <span><?php echo htmlspecialchars($appointment['reason_for_visit']); ?></span>
                            </div>
                        </div>

                        <div class="row-actions">
                            <button class="edit-btn" onclick="confirmAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-check-line"></i>
                                Confirm New Time
                            </button>
                            <button class="cancel-btn" onclick="cancelAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                <i class="ri-close-line"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </section>

            <!-- Also add this to your Add Availability Modal section -->
            <div id="availabilityModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Add Available Time Slot</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add_availability">
                        <div class="form-group">
                            <label>Day of Week:</label>
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
                            <label>Start Time:</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>End Time:</label>
                            <input type="time" name="end_time" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Availability Section -->
            <section id="availabilityTab" class="appointments-wrapper" style="display: none;">
                <div class="availability-grid">
                    <?php while ($slot = $availability->fetch_assoc()): ?>
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

            <!-- Add Availability Modal -->
            <div id="availabilityModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Add Available Time Slot</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add_availability">
                        <div class="form-group">
                            <label>Day of Week:</label>
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
                            <label>Start Time:</label>
                            <input type="time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>End Time:</label>
                            <input type="time" name="end_time" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Save</button>
                            <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Availability Modal -->
            <div id="editAvailabilityModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>Edit Available Time Slot</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="edit_availability">
                        <input type="hidden" id="edit_availability_id" name="availability_id">
                        <div class="form-group">
                            <label>Day of Week:</label>
                            <select name="day_of_week" id="edit_day_of_week" required>
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
                            <label>Start Time:</label>
                            <input type="time" name="start_time" id="edit_start_time" required>
                        </div>
                        <div class="form-group">
                            <label>End Time:</label>
                            <input type="time" name="end_time" id="edit_end_time" required>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Update</button>
                            <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reschedule Modal -->
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
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeTabs();
            initializeSearch();
            initializeModals();
            initializeRescheduleForm();
        });

        function initializeTabs() {
            document.querySelectorAll('.filter-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    // Hide all sections
                    document.getElementById('newTab').style.display = 'none';
                    document.getElementById('scheduledTab').style.display = 'none';
                    document.getElementById('rescheduledTab').style.display = 'none';
                    document.getElementById('availabilityTab').style.display = 'none';

                    // Show selected section
                    const tabId = this.getAttribute('data-tab') + 'Tab';
                    document.getElementById(tabId).style.display = 'block';
                });
            });
        }

        function initializeSearch() {
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.appointment-row').forEach(row => {
                    const patientName = row.dataset.patientName.toLowerCase();
                    row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        }

        function showRescheduleForm(appointmentId) {
            document.getElementById('rescheduleAppointmentId').value = appointmentId;
            document.getElementById('rescheduleModal').style.display = 'flex';
        }

        function confirmAppointment(appointmentId) {
            if (confirm('Are you sure you want to confirm this appointment?')) {
                window.location.href = 'update_appointment.php?id=' + appointmentId + '&status=Scheduled';
            }
        }

        function completeAppointment(appointmentId) {
            if (confirm('Are you sure you want to mark this appointment as completed?')) {
                window.location.href = 'update_appointment.php?id=' + appointmentId + '&status=Completed';
            }
        }

        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                window.location.href = 'update_appointment.php?id=' + appointmentId + '&status=Canceled';
            }
        }

        function showAddAvailabilityForm() {
            document.getElementById('availabilityModal').style.display = 'flex';
        }

        function hideModal() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        }

        document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('rescheduleAppointmentId').value;
            const date = document.getElementById('newDate').value;
            const time = document.getElementById('newTime').value;
            window.location.href = `update_appointment.php?id=${id}&status=Rescheduled&new_date=${date}&new_time=${time}`;
        });
    </script>
    <script src="js/vpappointments.js"></script>
</body>

</html>