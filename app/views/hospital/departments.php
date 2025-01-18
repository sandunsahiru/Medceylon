<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments | MedCeylon</title>
    <link rel="stylesheet" href="../assets/css/hospital.css">
    <style>
        .departments-section {
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
        
        .departments-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        
        .department-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 20px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .department-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .department-info h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }

        .department-info p {
            font-size: 1.1rem;
            color: #777;
        }

        @media (max-width: 768px) {
            .department-card {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .department-card {
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
                <a href="#" class="nav-item active">
                    <i class="ri-hospital-line"></i>
                    <span>Departments</span>
                </a>
                <a href="doctors.php" class="nav-item">
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
                <h1>Departments</h1>
            </header>

            <section class="departments-section">
                <div class="section-header">
                    <h2>All Departments</h2>
                </div>

                <div class="departments-list">
                    <?php while ($department = $departments->fetch_assoc()): ?>
                    <div class="department-card">
                        <div class="department-info">
                            <h3><?php echo htmlspecialchars($department['department_name']); ?></h3>
                            <p><?php echo htmlspecialchars($department['description']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
