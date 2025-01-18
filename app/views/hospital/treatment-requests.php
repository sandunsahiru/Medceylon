<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment Requests | MedCeylon</title>
    <link rel="stylesheet" href="../assets/css/hospital.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .requests-section {
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
        
        .request-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            margin-bottom: 15px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .request-info {
            flex: 1;
        }

        .request-info h3 {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }

        .request-info p {
            font-size: 1.1rem;
            color: #777;
        }

        .request-status {
            background-color: #248c7f;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: capitalize;
        }

        .request-card .request-status {
            font-size: 1.1rem;
            width: 120px;
            text-align: center;
        }

        .request-card .view-btn,
        .request-card .respond-btn {
            background-color: #248c7f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .request-card .view-btn:hover,
        .request-card .respond-btn:hover {
            background-color: #1e7c69;
        }

        .request-card .approve-btn {
            background-color: #4caf50;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .request-card .approve-btn:hover {
            background-color: #45a049;
        }

        /* Add responsiveness for small screens */
        @media (max-width: 768px) {
            .request-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .request-status {
                margin-top: 10px;
                width: auto;
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
                <a href="treatment-requests.php" class="nav-item active">
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
                <h1>Treatment Requests</h1>
            </header>

            <section class="requests-section">
                <div class="section-header">
                    <h2>All Treatment Requests</h2>
                </div>

                <div class="requests-list">
                    <?php while ($request = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="request-info">
                            <h3><?php echo htmlspecialchars($request['first_name']) . ' ' . htmlspecialchars($request['last_name']); ?></h3>
                            <p><?php echo htmlspecialchars($request['treatment_type']); ?></p>
                        </div>
                        <div class="request-status"><?php echo $request['request_status']; ?></div>
                        <div class="request-actions">
                            <button class="view-btn" data-id="<?php echo $request['request_id']; ?>" title="View Details">
                                <i class="ri-eye-line"></i> View
                            </button>
                            <button class="respond-btn" data-id="<?php echo $request['request_id']; ?>" title="Respond">
                                <i class="ri-message-2-line"></i> Respond
                            </button>
                            <?php if ($request['request_status'] === 'Pending'): ?>
                            <button class="approve-btn" data-id="<?php echo $request['request_id']; ?>" title="Approve Request">
                                <i class="ri-check-line"></i> Approve
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="../assets/js/hospital.js"></script>
</body>
</html>
