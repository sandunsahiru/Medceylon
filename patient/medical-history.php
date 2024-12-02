<?php
session_start();
require_once '../includes/config.php';

$patient_id = 1; // Hardcoded for now

$query = "SELECT hr.record_id, hr.patient_id, hr.doctor_id, hr.appointment_id, 
          hr.diagnosis, hr.treatment_plan, hr.prescriptions, hr.test_results, 
          hr.date_created, hr.date_modified,
          CONCAT(u.first_name, ' ', u.last_name) as doctor_name,
          h.name as hospital_name,
          a.appointment_date,
          a.appointment_time
          FROM healthrecords hr
          JOIN doctors d ON hr.doctor_id = d.doctor_id
          JOIN users u ON d.user_id = u.user_id
          JOIN appointments a ON hr.appointment_id = a.appointment_id
          JOIN hospitals h ON d.hospital_id = h.hospital_id
          WHERE hr.patient_id = ?
          ORDER BY hr.date_created DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$records = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History - MediCare</title>
    <link rel="stylesheet" href="../assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Next button in the top-right corner */
        .next-button {
            position: absolute;
            bottom: 50px;
            right: 20px;
            padding: 10px 20px;
            background-color: #299d97;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .next-button:hover {
            background-color: #247f7a;
        }
    </style>
</head>

<body>
    <!-- Next button -->
    <a href="../caregivers.php">
        <button class="next-button">Next</button>
    </a>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <h1>Medceylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="book-appointment.php" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="medical-history.php" class="nav-item active">
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

        <main class="main-content">
            <header class="top-bar">
                <h1>Medical History</h1>
            </header>

            <section class="appointments-section">
                <div class="medical-records">
                    <?php if ($records->num_rows > 0): ?>
                        <?php while ($record = $records->fetch_assoc()): ?>
                            <div class="record-card">
                                <div class="record-header">
                                    <div class="record-date">
                                        <i class="ri-calendar-line"></i>
                                        <?php echo date('F j, Y', strtotime($record['appointment_date'])); ?>
                                        at <?php echo date('g:i A', strtotime($record['appointment_time'])); ?>
                                    </div>
                                    <div class="record-doctor">
                                        <i class="ri-user-heart-line"></i>
                                        Dr. <?php echo htmlspecialchars($record['doctor_name']); ?>
                                    </div>
                                </div>

                                <div class="record-content">
                                    <div class="record-item">
                                        <h3>Hospital</h3>
                                        <p><?php echo htmlspecialchars($record['hospital_name']); ?></p>
                                    </div>

                                    <div class="record-item">
                                        <h3>Diagnosis</h3>
                                        <p><?php echo htmlspecialchars($record['diagnosis']); ?></p>
                                    </div>

                                    <div class="record-item">
                                        <h3>Treatment Plan</h3>
                                        <p><?php echo htmlspecialchars($record['treatment_plan']); ?></p>
                                    </div>

                                    <?php if ($record['prescriptions']): ?>
                                        <div class="record-item">
                                            <h3>Prescriptions</h3>
                                            <p><?php echo nl2br(htmlspecialchars($record['prescriptions'])); ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($record['test_results']): ?>
                                        <div class="record-item">
                                            <h3>Test Results</h3>
                                            <p><?php echo nl2br(htmlspecialchars($record['test_results'])); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-records">
                            <p>No medical records found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>

</html>