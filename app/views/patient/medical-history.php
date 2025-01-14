<!-- app/views/patient/medical-history.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
        <div class="logo">
    <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
        <h1>Medceylon</h1>
    </a>
</div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item active">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/profile" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>
            
            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Medical History</h1>
            </header>

            <section class="appointments-section">
                <div class="medical-records">
                    <?php if ($records && $records->num_rows > 0): ?>
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