<?php
session_start();
require_once '../includes/config.php';

// Get all doctors
$doctors_query = "SELECT * FROM doctors d JOIN users u ON d.doctor_id = u.user_id ORDER BY u.first_name ASC";
$doctors = $conn->query($doctors_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors | MedCeylon</title>
    <link rel="stylesheet" href="../assets/css/hospital.css">
    <style>
        .doctors-section {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #248c7f;
        }
        
        .doctors-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        
        .doctor-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 20px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .doctor-info h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }

        @media (max-width: 768px) {
            .doctor-card {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .doctor-card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>MedCeylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="hospital.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="treatment-requests.php" class="nav-item">
                    <i class="ri-file-list-3-line"></i>
                    <span>Treatment Requests</span>
                </a>
                <a href="patients.php" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="departments.php" class="nav-item">
                    <i class="ri-hospital-line"></i>
                    <span>Departments</span>
                </a>
                <a href="#" class="nav-item active">
                    <i class="ri-nurse-line"></i>
                    <span>Doctors</span>
                </a>
                <a href="messages.php" class="nav-item">
                    <i class="ri-message-2-line"></i>
                    <span>Messages</span>
                </a>
            </nav>
            
            <a href="logout.php" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Doctors</h1>
            </header>

            <section class="doctors-section">
                <div class="section-header">
                    <h2>All Doctors</h2>
                </div>

                <div class="doctors-list">
                    <?php while ($doctor = $doctors->fetch_assoc()): ?>
                    <div class="doctor-card">
                        <div class="doctor-info">
                            <h3>Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
