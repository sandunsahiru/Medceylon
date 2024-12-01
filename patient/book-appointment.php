<!-- book-appointment.php -->
<?php
session_start();
require_once '../includes/config.php';

// Fetch general doctors
$doctors_query = "SELECT d.doctor_id, u.first_name, u.last_name, h.name as hospital_name 
                 FROM doctors d 
                 JOIN users u ON d.user_id = u.user_id 
                 JOIN hospitals h ON d.hospital_id = h.hospital_id
                 JOIN userroles ur ON u.username = ur.username
                 WHERE ur.role_id = 2 AND d.is_active = 1";

$doctors_result = $conn->query($doctors_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - MediCare</title>
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
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="book-appointment.php" class="nav-item active">
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
                <h1>Book Appointment</h1>
            </header>

            <section class="appointments-section">
                <form id="appointmentForm" action="process-appointment.php" method="POST" enctype="multipart/form-data" class="appointment-form">
                    <div class="form-group">
                        <label for="doctor">Select Doctor:</label>
                        <select name="doctor_id" id="doctor" required>
                            <option value="">Select a doctor</option>
                            <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                <option value="<?php echo $doctor['doctor_id']; ?>">
                                    Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?> 
                                    (<?php echo htmlspecialchars($doctor['hospital_name']); ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_date">Select Date:</label>
                        <input type="date" id="appointment_date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="time_slot">Available Time Slots:</label>
                        <select name="time_slot" id="time_slot" required disabled>
                            <option value="">Select date and doctor first</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="consultation_type">Consultation Type:</label>
                        <select name="consultation_type" id="consultation_type" required>
                            <option value="Online">Online</option>
                            <option value="In-Person">In-Person</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason for Visit:</label>
                        <textarea name="reason" id="reason" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="medical_history">Medical History (Optional):</label>
                        <textarea name="medical_history" id="medical_history"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="documents">Upload Documents (Optional):</label>
                        <input type="file" name="documents[]" id="documents" multiple accept=".pdf,.jpg,.jpeg,.png">
                    </div>

                    <button type="submit" class="submit-btn">Book Appointment</button>
                </form>
            </section>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const doctorSelect = document.getElementById('doctor');
        const dateInput = document.getElementById('appointment_date');
        const timeSlotSelect = document.getElementById('time_slot');

        async function loadTimeSlots() {
            if (!doctorSelect.value || !dateInput.value) return;

            const response = await fetch('get_time_slots.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `doctor_id=${doctorSelect.value}&date=${dateInput.value}`
            });

            const slots = await response.json();
            timeSlotSelect.innerHTML = '';
            timeSlotSelect.disabled = false;

            if (slots.length === 0) {
                timeSlotSelect.innerHTML = '<option value="">No available slots</option>';
                return;
            }

            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = slot;
                timeSlotSelect.appendChild(option);
            });
        }

        doctorSelect.addEventListener('change', loadTimeSlots);
        dateInput.addEventListener('change', loadTimeSlots);
    });
    </script>
</body>
</html>