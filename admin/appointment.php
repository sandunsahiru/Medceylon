<?php
$page = "appointments";
include "./includes/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_appointment_status') {
        $appointment_id = $_POST['appointment_id'];
        $new_status = $_POST['new_status'];

        // Update appointment status in the database
        $stmt = $conn->prepare("UPDATE appointments SET appointment_status = ? WHERE appointment_id = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);
        $stmt->execute();

        // Redirect or handle the response
        header('Location: appointments.php');
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'cancel_appointment') {
        $appointment_id = $_POST['appointment_id'];

        // Cancel the appointment (set status to 'Cancelled' or delete it)
        $stmt = $conn->prepare("UPDATE appointments SET appointment_status = 'Cancelled' WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        // Redirect or handle the response
        header('Location: appointments.php');
        exit();
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


<body>
    <link rel="stylesheet" href="./css/appointment.css">
    <?php include "./includes/navbar.php" ?>

    <div class="main-content">
        <header>
            <h1>User Management</h1>
        </header>

        <div class="container">
            <div class="bottom">
                <!-- Appointment Schedule -->
                <div class="follow-up">
                    <div class="calendar-header">
                        <h2>Appointment Schedule</h2>
                    </div>
                    <div class="calendar-container">
                        <div class="calendar">
                            <div class="calendar-nav">
                                <button id="prev-month">&lt;</button>
                                <span id="month-year">May 2024</span>
                                <button id="next-month">&gt;</button>
                            </div>
                            <table class="calendar-grid">
                                <thead>
                                    <tr>
                                        <th>Su</th>
                                        <th>Mo</th>
                                        <th>Tu</th>
                                        <th>We</th>
                                        <th>Th</th>
                                        <th>Fr</th>
                                        <th>Sa</th>
                                    </tr>
                                </thead>
                                <tbody id="calendar-body">
                                    <!-- Calendar dates will populate here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="appointment-list">
                            <!-- Appointments Section -->
                            <section id="appointmentsTab" class="appointments-wrapper">
                                <?php while ($appointment = $appointments->fetch_assoc()): ?>
                                    <div class="appointment-row"
                                        data-patient-name="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>">
                                        <div class="patient-info">
                                            <div class="avatar">
                                                <i class="ri-user-line"></i>
                                            </div>
                                            <div class="info-details">
                                                <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                                </h3>
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
                                            <span
                                                class="status"><?php echo htmlspecialchars($appointment['appointment_status']); ?></span>
                                        </div>

                                        <div class="row-actions">
                                            <button class="edit-btn"
                                                onclick="updateAppointmentStatus(<?php echo $appointment['appointment_id']; ?>)">
                                                <i class="ri-pencil-line"></i>
                                                Update Status
                                            </button>
                                            <?php if ($appointment['appointment_status'] === 'Scheduled'): ?>
                                                <button class="cancel-btn"
                                                    onclick="cancelAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                                    <i class="ri-close-line"></i>
                                                    Cancel
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <div class="upper">
                <!-- On-Going Appointments -->
                <div class="ongoing-appointments">
                    <h2>On Going Appointments</h2>
                    <p>Brooklyn Simmons - On Consultation</p>
                    <div class="details">
                        <p>Doctor: Dr. Joseph Carla</p>
                        <p>Time: 11:00 AM - 12:00 PM</p>
                    </div>
                    <textarea placeholder="Consultation Notes"></textarea>
                    <button>Reschedule</button>
                    <button>Finish Consultation</button>
                </div>

            </div>

        </div>
    </div>
</body>

</html>

<script>
    // Update appointment status function
    function updateAppointmentStatus(appointmentId) {
        // Show a modal or prompt to update the status
        const newStatus = prompt('Enter new status for the appointment:');
        if (newStatus) {
            // Send request to update the status (AJAX or form submission)
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
            <input type="hidden" name="action" value="update_appointment_status">
            <input type="hidden" name="appointment_id" value="${appointmentId}">
            <input type="hidden" name="new_status" value="${newStatus}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Cancel appointment function
    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            // Send a request to cancel the appointment (AJAX or form submission)
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
            <input type="hidden" name="action" value="cancel_appointment">
            <input type="hidden" name="appointment_id" value="${appointmentId}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }

</script>