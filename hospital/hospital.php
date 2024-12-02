<?php
session_start();
require_once '../includes/config.php';

// Get total requests and their status distribution
$total_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN request_status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN request_status = 'Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN request_status = 'Completed' THEN 1 ELSE 0 END) as completed
    FROM treatment_requests WHERE is_active = 1";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc();

// Get latest treatment requests
$requests_query = "SELECT 
    tr.request_id,
    u.first_name, 
    u.last_name,
    tr.preferred_date,
    tr.treatment_type,
    tr.doctor_preference,
    tr.special_requirements,
    tr.request_status,
    tr.estimated_cost,
    tr.request_date
    FROM treatment_requests tr
    JOIN users u ON tr.patient_id = u.user_id
    ORDER BY tr.request_date DESC
    LIMIT 5";
$requests = $conn->query($requests_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard | MedCeylon</title>
    <link rel="stylesheet" href="../assets/css/hospital.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <h1>MedCeylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-item active">
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
            <!-- Top Bar -->
            <header class="top-bar">
                <h1>Hospital Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search requests, patients...">
                    </div>
                    <div class="notifications">
                        <i class="ri-notification-3-line"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stats-card primary">
                    <div class="stats-header">
                        <h2><?php echo $total_data['total']; ?></h2>
                        <p>Total Requests</p>
                    </div>
                    <div class="stats-details">
                        <div class="request-stat">
                            <i class="ri-time-line"></i>
                            <span>Pending: <?php echo $total_data['pending']; ?></span>
                        </div>
                        <div class="request-stat">
                            <i class="ri-check-line"></i>
                            <span>Approved: <?php echo $total_data['approved']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-heart-pulse-line"></i>
                        <div class="stats-info">
                            <h3>Active Treatments</h3>
                            <p><?php echo $total_data['approved']; ?></p>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-check-double-line"></i>
                        <div class="stats-info">
                            <h3>Completed</h3>
                            <p><?php echo $total_data['completed']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treatment Requests Section -->
            <section class="requests-section">
                <div class="section-header">
                    <h2>Recent Treatment Requests</h2>
                    <a href="treatment-requests.php" class="view-all">View All</a>
                </div>
                
                <div class="requests-list">
                    <?php while ($request = $requests->fetch_assoc()): ?>
                    <div class="request-card">
                        <div class="request-status <?php echo strtolower($request['request_status']); ?>">
                            <?php echo $request['request_status']; ?>
                        </div>
                        <div class="request-info">
                            <h3><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></h3>
                            <p class="treatment-type">
                                <i class="ri-medicine-bottle-line"></i>
                                <?php echo htmlspecialchars($request['treatment_type']); ?>
                            </p>
                            <p class="doctor-preference">
                                <i class="ri-user-star-line"></i>
                                Dr. <?php echo htmlspecialchars($request['doctor_preference']); ?>
                            </p>
                            <p class="preferred-date">
                                <i class="ri-calendar-check-line"></i>
                                <?php echo date('d/m/Y', strtotime($request['preferred_date'])); ?>
                            </p>
                        </div>
                        <div class="request-details">
                            <?php if ($request['estimated_cost']): ?>
                            <p class="cost">
                                <i class="ri-money-dollar-circle-line"></i>
                                Est. Cost: $<?php echo number_format($request['estimated_cost'], 2); ?>
                            </p>
                            <?php endif; ?>
                            <?php if ($request['special_requirements']): ?>
                            <p class="requirements" title="<?php echo htmlspecialchars($request['special_requirements']); ?>">
                                <i class="ri-file-list-3-line"></i>
                                Special Requirements
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="request-actions">
                            <button class="action-btn view-btn" data-id="<?php echo $request['request_id']; ?>" 
                                    title="View Details">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="action-btn respond-btn" data-id="<?php echo $request['request_id']; ?>"
                                    title="Respond">
                                <i class="ri-message-2-line"></i>
                            </button>
                            <?php if ($request['request_status'] === 'Pending'): ?>
                            <button class="action-btn approve-btn" data-id="<?php echo $request['request_id']; ?>"
                                    title="Approve Request">
                                <i class="ri-check-line"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Response Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Respond to Treatment Request</h2>
            </div>
            <form id="responseForm">
                <input type="hidden" name="request_id" id="request_id">
                <div class="form-group">
                    <label for="estimated_cost">Estimated Cost ($)</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" 
                           min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="response_message">Response Message</label>
                    <textarea id="response_message" name="response_message" 
                            rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="additional_requirements">Additional Requirements</label>
                    <textarea id="additional_requirements" name="additional_requirements" 
                            rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="ri-send-plane-line"></i>
                        Send Response
                    </button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">
                        <i class="ri-close-line"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/hospital.js"></script>
</body>
</html>