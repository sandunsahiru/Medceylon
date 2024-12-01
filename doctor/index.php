<?php
session_start();
require_once '../includes/config.php';

// Get total patients count and gender distribution
$total_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as women,
    SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as men
    FROM users WHERE is_active = 1";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc();

// Calculate percentages
$women_percentage = ($total_data['women'] / $total_data['total']) * 100;
$men_percentage = ($total_data['men'] / $total_data['total']) * 100;

// Get appointments
$appointments_query = "SELECT 
    u.first_name, 
    u.last_name,
    a.appointment_date,
    a.appointment_time
    FROM appointments a
    JOIN users u ON a.patient_id = u.user_id
    WHERE a.appointment_status = 'Scheduled'
    ORDER BY a.appointment_date, a.appointment_time
    LIMIT 5";
$appointments = $conn->query($appointments_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Dashboard</title>
    <link rel="stylesheet" href="../assets/css/doctordashboard.css">
    <!-- Include Remix Icons -->
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        Friday, 29.11.2024
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stats-card patients-overview">
                    <div class="stats-header">
                        <h2>5</h2>
                        <p>Patients</p>
                    </div>
                    <div class="stats-details">
                        <div class="gender-stat">
                            <i class="ri-women-line"></i>
                            <span>Women 40%</span>
                        </div>
                        <div class="gender-stat">
                            <i class="ri-men-line"></i>
                            <span>Men 20%</span>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-group-line"></i>
                        <div class="stats-info">
                            <h3>All Patients</h3>
                            <p>5</p>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>All Appointments</h3>
                            <p>2</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Section -->
            <section class="appointments-section">
                <h2>All Appointments</h2>
                <div class="appointments-list">
                    <?php while ($appointment = $appointments->fetch_assoc()): ?>
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
                </div>
            </section>

        </main>
    </div>
    <script src="../assets/js/doctorscript.js"></script>
</body>

</html>