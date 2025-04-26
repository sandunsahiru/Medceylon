<?php
/**
 * Start Medical Session View
 * 
 * This file handles starting a new medical session from an existing appointment
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Medical Session - MedCeylon</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Start Session Page Styles */
        .session-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .session-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        
        .session-header {
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .session-header h2 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .session-body {
            padding: 25px;
        }
        
        .session-body h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.2rem;
            color: #333;
        }
        
        .doctor-info-card {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border: 1px solid #eaeaea;
        }
        
        .doctor-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .doctor-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .doctor-avatar i {
            font-size: 28px;
            color: #666;
        }
        
        .doctor-name {
            flex: 1;
        }
        
        .doctor-name h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }
        
        .doctor-name p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .appointment-details {
            border-top: 1px solid #eaeaea;
            padding-top: 15px;
        }
        
        .appointment-details .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .detail-label {
            flex: 0 0 120px;
            font-weight: 500;
            color: #555;
        }
        
        .detail-value {
            flex: 1;
        }
        
        .info-box {
            background-color: rgba(74, 177, 168, 0.1);
            border-left: 4px solid var(--primary-color, #4AB1A8);
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .info-box h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }
        
        .info-box p {
            margin: 0;
            color: #555;
            font-size: 0.95rem;
        }
        
        .session-footer {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            border-top: 1px solid #eaeaea;
        }
        
        .action-button {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        
        .primary-button {
            background-color: var(--primary-color, #4AB1A8);
            color: white;
        }
        
        .primary-button:hover {
            background-color: #3EA099;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .secondary-button {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .secondary-button:hover {
            background-color: #e0e0e0;
        }
        
        .action-button i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        .flow-steps {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }
        
        .flow-steps::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .step {
            flex: 1;
            text-align: center;
            z-index: 2;
            position: relative;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background-color: white;
            border: 2px solid #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
            color: #666;
        }
        
        .step-active .step-number {
            background-color: var(--primary-color, #4AB1A8);
            border-color: var(--primary-color, #4AB1A8);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #666;
        }
        
        .step-active .step-label {
            color: #333;
            font-weight: 500;
        }
        
        .mt-6 {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Sidebar - Kept exactly as original -->
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
                <a href="<?php echo $basePath; ?>/patient/chat" class="nav-item">
                    <i class="ri-message-3-line"></i>
                    <span>Chat</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item">
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

        <main class="main-content">
            <!-- Header with the same style as dashboard -->
            <header class="top-bar">
                <h1>Start Medical Session</h1>
                <div class="header-right">
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Session Start Content -->
            <div class="session-container">
                <!-- Step Indicator -->
                <div class="flow-steps">
                    <div class="step step-active">
                        <div class="step-number">1</div>
                        <div class="step-label">Review Appointment</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Start Session</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Specialist Referral</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-label">Treatment Plan</div>
                    </div>
                </div>

                <div class="session-card">
                    <div class="session-header">
                        <h2>Confirm Medical Session</h2>
                    </div>
                    <div class="session-body">
                        <h3>Your General Doctor Appointment</h3>
                        
                        <div class="doctor-info-card">
                            <div class="doctor-header">
                                <div class="doctor-avatar">
                                    <i class="ri-user-line"></i>
                                </div>
                                <div class="doctor-name">
                                    <h4>Dr. Sahiru Bandara</h4>
                                    <p>General Medicine</p>
                                </div>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="detail-row">
                                    <div class="detail-label">Date:</div>
                                    <div class="detail-value">28/04/2025</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Time:</div>
                                    <div class="detail-value">19:00</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Location:</div>
                                    <div class="detail-value">Kandy Teaching Hospital</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Status:</div>
                                    <div class="detail-value">Asked</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <h4>What is a Medical Session?</h4>
                            <p>A medical session starts with your general doctor appointment. After your initial consultation, the doctor can refer you to a specialist if needed. Your treatment journey will be tracked in one place, making it easier to manage appointments, travel plans, and accommodation.</p>
                        </div>
                        
                        <div class="info-box">
                            <h4>What happens next?</h4>
                            <p>Once you start a medical session, you will see your progress on your dashboard. After your general doctor consultation, you may be referred to a specialist. The specialist can then create a treatment plan that may include arrangements for travel to Sri Lanka.</p>
                        </div>
                    </div>
                    <div class="session-footer">
                        <a href="<?php echo $basePath; ?>/patient/dashboard" class="action-button secondary-button">
                            <i class="ri-arrow-left-line"></i> Back to Dashboard
                        </a>
                        <form action="<?php echo $basePath; ?>/patient/confirm-medical-session" method="post">
                            <?php if (isset($_SESSION['csrf_token'])): ?>
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <?php endif; ?>
                            <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointmentId ?? ''); ?>">
                            <button type="submit" class="action-button primary-button">
                                <i class="ri-check-line"></i> Start Medical Session
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Simple script for any interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // You can add any client-side functionality here if needed
        });
    </script>
</body>
</html>