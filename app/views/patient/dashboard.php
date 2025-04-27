<?php

/**
 * Patient Dashboard View
 * 
 * This file displays the patient dashboard with appointment information
 * and medical session management.
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - MedCeylon</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Session management styles */
        .medical-session {
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
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .session-header h2 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .session-status {
            font-size: 0.85rem;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 12px;
        }

        .session-body {
            padding: 20px;
        }

        /* Progress Steps */
        .step-progress-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }

        .step-progress-container::before {
            content: "";
            position: absolute;
            top: 18px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }

        .step-progress-bar {
            position: absolute;
            top: 18px;
            left: 0;
            height: 2px;
            background-color: #2ecc71;
            z-index: 2;
            transition: width 0.3s ease;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 3;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            border: 2px solid #e0e0e0;
            font-weight: 500;
            position: relative;
            transition: all 0.2s ease;
        }

        .step-circle i {
            font-size: 18px;
        }

        .step-text {
            font-size: 0.8rem;
            color: #666;
            text-align: center;
            max-width: 90px;
        }

        .step-item.active .step-circle {
            background-color: var(--primary-color, #4AB1A8);
            border-color: var(--primary-color, #4AB1A8);
            color: white;
        }

        .step-item.completed .step-circle {
            background-color: #2ecc71;
            border-color: #2ecc71;
            color: white;
        }

        /* Session details container */
        .session-details-container {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .session-details-container h3 {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        /* Doctor card styles */
        .doctor-card {
            display: flex;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .doctor-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .doctor-avatar i {
            font-size: 30px;
            color: #888;
        }

        .doctor-info {
            flex: 1;
        }

        .doctor-info h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
        }

        .doctor-info p {
            margin: 0 0 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .appointment-meta {
            display: flex;
            margin-top: 10px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
        }

        .meta-item i {
            margin-right: 5px;
            color: #888;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .action-btn.primary {
            background-color: var(--primary-color, #4AB1A8);
            color: white;
        }

        .action-btn.secondary {
            background-color: #f0f0f0;
            color: #333;
        }

        .action-btn i {
            margin-right: 5px;
        }

        /* Alert boxes */
        .alert-box {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .alert-box i {
            font-size: 20px;
            margin-right: 12px;
        }

        .alert-box p {
            margin: 0;
            font-size: 0.9rem;
        }

        .alert-info {
            background-color: rgba(52, 152, 219, 0.1);
            border-left: 4px solid #3498db;
            color: #2980b9;
        }

        .alert-warning {
            background-color: rgba(241, 196, 15, 0.1);
            border-left: 4px solid #f1c40f;
            color: #f39c12;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            border-left: 4px solid #2ecc71;
            color: #27ae60;
        }

        /* Travel plan and treatment details styles */
        .treatment-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
        }

        .treatment-details p {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
        }

        .travel-plans {
            margin-top: 15px;
        }

        .travel-plan-option {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .travel-plan-option:hover {
            border-color: var(--primary-color, #4AB1A8);
            background-color: rgba(74, 177, 168, 0.05);
        }

        .travel-plan-option.selected {
            border-color: #2ecc71;
            background-color: rgba(46, 204, 113, 0.05);
        }

        .travel-plan-option .plan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .travel-plan-option .plan-name {
            font-weight: 600;
            font-size: 1rem;
            color: #333;
        }

        .travel-plan-option .plan-price {
            font-weight: 500;
            color: #666;
        }

        .travel-plan-option .plan-description {
            font-size: 0.85rem;
            color: #666;
        }

        .full-width-button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            margin-top: 15px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .full-width-button:hover {
            background-color: #3EA099;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .success-message {
            color: #4CAF50;
            margin-top: 10px;
            display: flex;
            align-items: center;
        }

        .success-message i {
            margin-right: 5px;
        }

        /* Start session button style */
        .session-start-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .session-start-btn:hover {
            background-color: #3EA099;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .session-start-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        /* Google Meet button styles */
        .meet-link-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background-color: #1a73e8;
            color: white !important;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .meet-link-btn:hover {
            background-color: #1557b0;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .meet-link-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        .meet-link-container {
            margin-top: 15px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        /* Navigation buttons for tabs */
        .tab-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nav-btn:hover {
            background-color: #3EA099;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-btn.disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .nav-btn i {
            font-size: 18px;
        }

        .nav-btn.prev i {
            margin-right: 8px;
        }

        .nav-btn.next i {
            margin-left: 8px;
        }

        /* Tab content styling */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Hotel & Transport, Travel Plan sections */
        .hotel-transport-section,
        .travel-plan-section {
            background-color: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .hotel-transport-section h3,
        .travel-plan-section h3 {
            margin: 0 0 15px 0;
            font-size: 1.1rem;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .booking-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 15px;
            transition: all 0.2s ease;
        }

        .booking-btn:hover {
            background-color: #3EA099;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .booking-btn i {
            margin-right: 8px;
            font-size: 18px;
        }

        /* Itinerary Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close-btn {
            color: white;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
            max-height: calc(80vh - 60px);
            overflow-y: auto;
        }

        /* Itinerary Timeline Styles */
        .itinerary-timeline {
            position: relative;
            margin: 30px 0;
        }

        .itinerary-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 80px;
            width: 2px;
            background-color: var(--primary-color, #4AB1A8);
        }

        .itinerary-item {
            display: flex;
            margin-bottom: 30px;
            position: relative;
        }

        .itinerary-day {
            flex: 0 0 70px;
            background-color: var(--primary-color, #4AB1A8);
            color: white;
            border-radius: 5px;
            text-align: center;
            padding: 10px 5px;
            font-weight: bold;
            margin-right: 30px;
            z-index: 2;
        }

        .itinerary-content {
            flex: 1;
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .itinerary-content h4 {
            margin-top: 0;
            color: var(--primary-color, #4AB1A8);
        }

        .destinations-list {
            padding-left: 20px;
        }

        .destinations-list li {
            margin-bottom: 10px;
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
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item active">
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

            <a href="<?php echo $basePath; ?>/home  " class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <!-- Header - Kept exactly as original -->
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <?php if ($this->session->hasFlash('success')): ?>
                <div class="alert-box alert-success">
                    <i class="ri-check-line"></i>
                    <p><?php echo $this->session->getFlash('success'); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($this->session->hasFlash('error')): ?>
                <div class="alert-box alert-warning">
                    <i class="ri-error-warning-line"></i>
                    <p><?php echo $this->session->getFlash('error'); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($this->session->hasFlash('info')): ?>
                <div class="alert-box alert-info">
                    <i class="ri-information-line"></i>
                    <p><?php echo $this->session->getFlash('info'); ?></p>
                </div>
            <?php endif; ?>

            <!-- Medical Session Section (If Active) -->
            <?php if (isset($activeMedicalSession) && $activeMedicalSession): ?>
                <section class="medical-session">
                    <div class="session-header">
                        <h2>Ongoing Medical Session</h2>
                        <div class="session-status"><?php echo htmlspecialchars($sessionData['status'] ?? 'Active'); ?></div>
                    </div>
                    <div class="session-body">
                        <!-- Enhanced Progress Steps -->
                        <div class="step-progress-container">
                            <div class="step-progress-bar" style="width: <?php
                                                                            // Calculate progress width
                                                                            $progress = 0;
                                                                            if ($sessionData['generalDoctorBooked']) $progress += 25;
                                                                            if ($sessionData['specialistBooked']) $progress += 25;
                                                                            if ($sessionData['treatmentPlanCreated']) $progress += 25;
                                                                            if ($sessionData['transportBooked']) $progress += 15;
                                                                            if ($sessionData['travelPlanSelected']) $progress += 10;
                                                                            echo $progress . '%';
                                                                            ?>"></div>

                            <!-- Step 1: General Doctor -->
                            <div class="step-item <?php echo $sessionData['generalDoctorBooked'] ? 'completed' : 'active'; ?>">
                                <div class="step-circle" data-tab="general-doctor-tab">
                                    <?php if ($sessionData['generalDoctorBooked']): ?>
                                        <i class="ri-check-line"></i>
                                    <?php else: ?>
                                        <i class="ri-stethoscope-line"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-text">General Doctor</div>
                            </div>

                            <!-- Step 2: Specialist -->
                            <div class="step-item <?php
                                                    if ($sessionData['specialistBooked']) echo 'completed';
                                                    elseif ($sessionData['generalDoctorBooked']) echo 'active';
                                                    ?>">
                                <div class="step-circle" data-tab="specialist-tab">
                                    <?php if ($sessionData['specialistBooked']): ?>
                                        <i class="ri-check-line"></i>
                                    <?php else: ?>
                                        <i class="ri-user-star-line"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-text">Specialist Doctor</div>
                            </div>

                            <!-- Step 3: Treatment Plan -->
                            <div class="step-item <?php
                                                    if ($sessionData['treatmentPlanCreated']) echo 'completed';
                                                    elseif ($sessionData['specialistBooked']) echo 'active';
                                                    ?>">
                                <div class="step-circle" data-tab="treatment-plan-tab">
                                    <?php if ($sessionData['treatmentPlanCreated']): ?>
                                        <i class="ri-check-line"></i>
                                    <?php else: ?>
                                        <i class="ri-file-list-line"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-text">Treatment Plan</div>
                            </div>

                            <!-- Step 4: Hotel & Transport -->
                            <div class="step-item <?php
                                                    if ($sessionData['transportBooked']) echo 'completed';
                                                    elseif ($sessionData['treatmentPlanCreated']) echo 'active';
                                                    ?>">
                                <div class="step-circle" data-tab="hotel-transport-tab">
                                    <?php if ($sessionData['transportBooked']): ?>
                                        <i class="ri-check-line"></i>
                                    <?php else: ?>
                                        <i class="ri-building-line"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-text">Hotel & Transport</div>
                            </div>

                            <!-- Step 5: Travel Plan -->
                            <div class="step-item <?php
                                                    if ($sessionData['travelPlanSelected']) echo 'completed';
                                                    elseif ($sessionData['transportBooked']) echo 'active';
                                                    ?>">
                                <div class="step-circle" data-tab="travel-plan-tab">
                                    <?php if ($sessionData['travelPlanSelected']): ?>
                                        <i class="ri-check-line"></i>
                                    <?php else: ?>
                                        <i class="ri-plane-line"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="step-text">Travel Plan</div>
                            </div>
                        </div>

                        <!-- Tab Content Section -->
                        <!-- General Doctor Tab -->
                        <div id="general-doctor-tab" class="tab-content <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'general-doctor') ? 'active' : ''; ?>">
                            <?php if ($sessionData['generalDoctorBooked']): ?>
                                <div class="session-details-container">
                                    <h3>General Doctor Consultation</h3>
                                    <div class="doctor-card">
                                        <div class="doctor-avatar">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div class="doctor-info">
                                            <h3>Dr. <?php echo htmlspecialchars($sessionData['generalDoctor']['name'] ?? 'Sahiru Bandara'); ?></h3>
                                            <p><?php echo htmlspecialchars($sessionData['generalDoctor']['specialty'] ?? 'General Medicine'); ?></p>

                                            <div class="appointment-meta">
                                                <div class="meta-item">
                                                    <i class="ri-calendar-line"></i>
                                                    <span><?php
                                                            if (isset($sessionData['generalDoctor']['appointmentDate'])) {
                                                                echo htmlspecialchars(date('d/m/Y', strtotime($sessionData['generalDoctor']['appointmentDate'])));
                                                            } else {
                                                                echo '28/04/2025'; // Default from your existing appointment
                                                            }
                                                            ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-time-line"></i>
                                                    <span><?php
                                                            if (isset($sessionData['generalDoctor']['appointmentDate'])) {
                                                                echo htmlspecialchars(date('H:i', strtotime($sessionData['generalDoctor']['appointmentDate'])));
                                                            } else {
                                                                echo '19:00'; // Default from your existing appointment
                                                            }
                                                            ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-building-line"></i>
                                                    <span><?php echo htmlspecialchars($sessionData['generalDoctor']['hospital'] ?? 'Kandy Teaching Hospital'); ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-user-location-line"></i>
                                                    <span>Mode: <?php echo htmlspecialchars($sessionData['generalDoctor']['appointmentMode'] ?? 'In-Person'); ?></span>
                                                </div>
                                            </div>

                                            <div class="action-buttons">
                                                <a href="<?php echo $basePath; ?>/patient/chat" class="action-btn secondary">
                                                    <i class="ri-message-3-line"></i> Chat with Doctor
                                                </a>
                                                <?php if (($sessionData['generalDoctor']['appointmentMode'] ?? '') === 'In-Person'): ?>
                                                    <a href="https://maps.google.com/?q=Kandy+Teaching+Hospital" target="_blank" class="action-btn secondary">
                                                        <i class="ri-map-pin-line"></i> Get Directions
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (isset($sessionData['generalDoctor']['appointmentMode']) && $sessionData['generalDoctor']['appointmentMode'] === 'Online'): ?>
                                                <div class="meet-link-container">
                                                    <p><strong>Online Meeting:</strong></p>
                                                    <?php
                                                    // Use a default meet link if one isn't provided
                                                    $meetLink = !empty($sessionData['generalDoctor']['meetLink'])
                                                        ? $sessionData['generalDoctor']['meetLink']
                                                        : 'https://meet.google.com/dyt-pdtg-xmy';
                                                    ?>
                                                    <a href="<?php echo htmlspecialchars($meetLink); ?>" target="_blank" class="meet-link-btn">
                                                        <i class="ri-video-chat-line"></i> Join Google Meet
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="alert-box alert-info">
                                        <i class="ri-information-line"></i>
                                        <p>Your general doctor will refer you to a specialist if needed after your consultation.</p>
                                    </div>

                                    <div class="tab-navigation">
                                        <button class="nav-btn prev disabled">
                                            <i class="ri-arrow-left-line"></i> Previous
                                        </button>
                                        <button class="nav-btn next" data-next-tab="specialist-tab">
                                            Next <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Specialist Tab -->
                        <div id="specialist-tab" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'specialist') ? 'active' : ''; ?>">
                            <?php if ($sessionData['generalDoctorBooked'] && !$sessionData['specialistBooked'] && isset($sessionData['waitingForSpecialist']) && $sessionData['waitingForSpecialist']): ?>
                                <div class="session-details-container">
                                    <h3>Specialist Referral</h3>
                                    <div class="alert-box alert-warning">
                                        <i class="ri-time-line"></i>
                                        <p>Your general doctor is in the process of booking a specialist for you. You will be notified once the booking is confirmed.</p>
                                    </div>

                                    <div class="tab-navigation">
                                        <button class="nav-btn prev" data-prev-tab="general-doctor-tab">
                                            <i class="ri-arrow-left-line"></i> Previous
                                        </button>
                                        <button class="nav-btn next" data-next-tab="treatment-plan-tab">
                                            Next <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($sessionData['specialistBooked']): ?>
                                <div class="session-details-container">
                                    <h3>Specialist Consultation</h3>
                                    <div class="doctor-card">
                                        <div class="doctor-avatar">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                        <div class="doctor-info">
                                            <h3>Dr. <?php echo htmlspecialchars($sessionData['specialist']['name']); ?></h3>
                                            <p><strong><?php echo htmlspecialchars($sessionData['specialist']['specialty']); ?></strong></p>
                                            <p><strong>Hospital:</strong> <?php echo htmlspecialchars($sessionData['specialist']['hospital']); ?></p>

                                            <div class="appointment-meta">
                                                <div class="meta-item">
                                                    <i class="ri-calendar-line"></i>
                                                    <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($sessionData['specialist']['appointmentDate']))); ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-time-line"></i>
                                                    <span><?php echo htmlspecialchars(date('H:i', strtotime($sessionData['specialist']['appointmentDate']))); ?></span>
                                                </div>
                                                <div class="meta-item">
                                                    <i class="ri-user-location-line"></i>
                                                    <span>Mode: <?php echo htmlspecialchars($sessionData['specialist']['appointmentMode']); ?></span>
                                                </div>
                                            </div>

                                            <div class="action-buttons">
                                                <a href="<?php echo $basePath; ?>/patient/chat" class="action-btn secondary">
                                                    <i class="ri-message-3-line"></i> Chat
                                                </a>
                                                <button class="action-btn secondary reschedule-btn" id="rescheduleAppointmentBtn">
                                                    <i class="ri-calendar-event-line"></i> Reschedule
                                                </button>
                                                <?php if ($sessionData['specialist']['appointmentMode'] === 'In-Person'): ?>
                                                    <a href="https://maps.google.com/?q=<?php echo urlencode($sessionData['specialist']['hospital']); ?>" target="_blank" class="action-btn secondary">
                                                        <i class="ri-map-pin-line"></i> Get Directions
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($sessionData['specialist']['appointmentMode'] === 'Online'): ?>
                                                <div class="meet-link-container">
                                                    <p><strong>Online Meeting:</strong></p>
                                                    <?php
                                                    // Use a default meet link if one isn't provided
                                                    $meetLink = !empty($sessionData['specialist']['meetLink'])
                                                        ? $sessionData['specialist']['meetLink']
                                                        : 'https://meet.google.com/dyt-pdtg-xmy';
                                                    ?>
                                                    <a href="<?php echo htmlspecialchars($meetLink); ?>" target="_blank" class="meet-link-btn">
                                                        <i class="ri-video-chat-line"></i> Join Google Meet
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="tab-navigation">
                                        <button class="nav-btn prev" data-prev-tab="general-doctor-tab">
                                            <i class="ri-arrow-left-line"></i> Previous
                                        </button>
                                        <button class="nav-btn next" data-next-tab="treatment-plan-tab">
                                            Next <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Treatment Plan Tab -->
                        <div id="treatment-plan-tab" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'treatment-plan') ? 'active' : ''; ?>">
                            <?php if ($sessionData['treatmentPlanCreated']): ?>
                                <div class="session-details-container">
                                    <h3>Treatment Plan</h3>

                                    <p><strong>Diagnosis:</strong>
                                        <?= !empty($sessionData['treatmentPlan']['diagnosis']) ? htmlspecialchars($sessionData['treatmentPlan']['diagnosis']) : 'Not specified' ?></p>

                                    <p><strong>Treatment description:</strong><br>
                                        <?= !empty($sessionData['treatmentPlan']['treatment_description']) ? nl2br(htmlspecialchars($sessionData['treatmentPlan']['treatment_description'])) : 'Not specified' ?></p>

                                    <p><strong>Medications:</strong>
                                        <?= !empty($sessionData['treatmentPlan']['medications']) ? htmlspecialchars($sessionData['treatmentPlan']['medications']) : 'None prescribed' ?></p>

                                    <p><strong>Duration:</strong>
                                        <?php
                                        if (!empty($sessionData['treatmentPlan']['treatment_duration'])) {
                                            echo htmlspecialchars($sessionData['treatmentPlan']['treatment_duration']);
                                        } else if (!empty($sessionData['treatmentPlan']['estimated_duration'])) {
                                            echo htmlspecialchars($sessionData['treatmentPlan']['estimated_duration']) . ' days';
                                        } else {
                                            echo 'Not specified';
                                        }
                                        ?></p>

                                    <p><strong>Follow-up:</strong>
                                        <?= !empty($sessionData['treatmentPlan']['follow_up']) ? nl2br(htmlspecialchars($sessionData['treatmentPlan']['follow_up'])) : 'No follow-up scheduled' ?></p>

                                    <p><strong>Travel restrictions:</strong>
                                        <?= !empty($sessionData['treatmentPlan']['travel_restrictions']) ? htmlspecialchars($sessionData['treatmentPlan']['travel_restrictions']) : 'None' ?></p>

                                    <p><strong>Estimated budget:</strong>
                                        LKR <?= isset($sessionData['treatmentPlan']['estimated_budget']) ? number_format((float)$sessionData['treatmentPlan']['estimated_budget'], 2) : '0.00' ?></p>

                                    <div class="tab-navigation">
                                        <button class="nav-btn prev" data-prev-tab="specialist-tab">
                                            <i class="ri-arrow-left-line"></i> Previous
                                        </button>
                                        <button class="nav-btn next" data-next-tab="hotel-transport-tab">
                                            Next <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="session-details-container">
                                    <h3>Treatment Plan</h3>
                                    <div class="alert-box alert-info">
                                        <i class="ri-information-line"></i>
                                        <p>Your treatment plan will be available after the specialist consultation.</p>
                                    </div>

                                    <div class="tab-navigation">
                                        <button class="nav-btn prev" data-prev-tab="specialist-tab">
                                            <i class="ri-arrow-left-line"></i> Previous
                                        </button>
                                        <button class="nav-btn next" data-next-tab="hotel-transport-tab">
                                            Next <i class="ri-arrow-right-line"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Hotel & Transport Tab -->
                        <div id="hotel-transport-tab" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'hotel-transport') ? 'active' : ''; ?>">
                            <div class="hotel-transport-section">
                                <h3>Hotel & Transportation</h3>

                                <?php if ($sessionData['transportBooked']): ?>
                                    <div class="alert-box alert-success">
                                        <i class="ri-check-line"></i>
                                        <p>Your hotel and transportation have been booked successfully.</p>
                                    </div>

                                    <div class="booking-details">
                                        <p><strong>Hotel:</strong> Cinnamon Grand Colombo</p>
                                        <p><strong>Check-in Date:</strong> July 13, 2025</p>
                                        <p><strong>Check-out Date:</strong> July 20, 2025</p>
                                        <p><strong>Room Type:</strong> Superior Double</p>
                                        <p><strong>Transportation:</strong> Private Airport Transfer</p>
                                    </div>
                                <?php else: ?>
                                    <div class="alert-box alert-info">
                                        <i class="ri-information-line"></i>
                                        <p>Book your accommodation and transportation for your medical treatment.</p>
                                    </div>

                                    <a href="<?php echo $basePath; ?>/accommodation/process-booking" class="booking-btn">
                                        <i class="ri-hotel-line"></i> Book Hotel & Transport
                                    </a>
                                <?php endif; ?>

                                <div class="tab-navigation">
                                    <button class="nav-btn prev" data-prev-tab="treatment-plan-tab">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </button>
                                    <button class="nav-btn next" data-next-tab="travel-plan-tab">
                                        Next <i class="ri-arrow-right-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Plan Tab -->
                        <div id="travel-plan-tab" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'travel-plan') ? 'active' : ''; ?>">
                            <div class="travel-plan-section">
                                <h3>Travel Plan</h3>

                                <?php if ($sessionData['travelPlanSelected'] && isset($sessionData['travelPlan']) && $sessionData['travelPlan']['hasTrip']): ?>
                                    <div class="alert-box alert-success">
                                        <i class="ri-check-line"></i>
                                        <p>Your travel plan has been selected successfully.</p>
                                    </div>

                                    <div class="travel-plan-details">
                                        <p><strong>Trip Name:</strong> <?= htmlspecialchars($sessionData['travelPlan']['trip']['name']) ?></p>
                                        <p><strong>Travel Date:</strong> <?= date('F j, Y', strtotime($sessionData['travelPlan']['trip']['start_date'])) ?></p>
                                        <p><strong>Return Date:</strong> <?= date('F j, Y', strtotime($sessionData['travelPlan']['trip']['end_date'])) ?></p>
                                        <p><strong>Duration:</strong> <?= htmlspecialchars($sessionData['travelPlan']['trip']['travel_days']) ?> days</p>

                                        <?php if ($sessionData['travelPlan']['hasDestinations']): ?>
                                            <p><strong>Destinations:</strong></p>
                                            <ul class="destinations-list">
                                                <?php foreach ($sessionData['travelPlan']['destinations'] as $destination): ?>
                                                    <li>
                                                        <strong><?= htmlspecialchars($destination['destination_name']) ?></strong> -
                                                        <?= date('F j, Y', strtotime($destination['check_in'])) ?>,
                                                        <?= $destination['time_spent_hours'] ?> hours
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p><em>No destinations have been added to this trip yet.</em></p>
                                        <?php endif; ?>
                                    </div>

                                    <button id="viewItinerary" class="booking-btn">
                                        <i class="ri-roadmap-line"></i> View Full Itinerary
                                    </button>
                                <?php else: ?>
                                    <div class="alert-box alert-info">
                                        <i class="ri-information-line"></i>
                                        <p>Select a travel plan that suits your needs during your medical visit.</p>
                                    </div>

                                    <a href="<?php echo $basePath; ?>/patient/transport" class="booking-btn">
                                        <i class="ri-plane-line"></i> Select Travel Plan
                                    </a>
                                <?php endif; ?>

                                <div class="tab-navigation">
                                    <button class="nav-btn prev" data-prev-tab="hotel-transport-tab">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </button>
                                    <button class="nav-btn next" data-next-tab="summary-tab">
                                        Next <i class="ri-arrow-right-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for Itinerary -->
                        <div id="itineraryModal" class="modal" style="display: none;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2>Travel Itinerary</h2>
                                    <button class="close-btn">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <?php if (isset($sessionData['travelPlan']) && $sessionData['travelPlan']['hasTrip'] && $sessionData['travelPlan']['hasDestinations']): ?>
                                        <div class="itinerary-container">
                                            <h3><?= htmlspecialchars($sessionData['travelPlan']['trip']['name']) ?></h3>
                                            <p><strong>Total Duration:</strong> <?= htmlspecialchars($sessionData['travelPlan']['trip']['total_duration_hours']) ?> hours</p>

                                            <div class="itinerary-timeline">
                                                <?php foreach ($sessionData['travelPlan']['destinations'] as $index => $destination): ?>
                                                    <div class="itinerary-item">
                                                        <div class="itinerary-day">Day <?= $index + 1 ?></div>
                                                        <div class="itinerary-content">
                                                            <h4><?= htmlspecialchars($destination['destination_name']) ?></h4>
                                                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($destination['check_in'])) ?></p>
                                                            <p><strong>Travel Time:</strong> <?= htmlspecialchars($destination['travel_time_hours']) ?> hours</p>
                                                            <p><strong>Time at Location:</strong> <?= htmlspecialchars($destination['time_spent_hours']) ?> hours</p>
                                                            <p><?= htmlspecialchars($destination['description']) ?></p>

                                                            <?php if ($destination['entry_fee'] > 0): ?>
                                                                <p><strong>Entry Fee:</strong> LKR <?= number_format($destination['entry_fee'], 2) ?></p>
                                                            <?php endif; ?>

                                                            <p>
                                                                <strong>Hours:</strong>
                                                                <?php if ($destination['opening_time'] != '00:00:00' && $destination['closing_time'] != '00:00:00'): ?>
                                                                    <?= date('g:i A', strtotime($destination['opening_time'])) ?> -
                                                                    <?= date('g:i A', strtotime($destination['closing_time'])) ?>
                                                                <?php else: ?>
                                                                    Open all day
                                                                <?php endif; ?>
                                                            </p>

                                                            <p><strong>Wheelchair Accessible:</strong> <?= $destination['wheelchair_accessibility'] ?></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p>No itinerary available. Please select a travel plan first.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
    

    <!-- Summary Tab Content -->
<div id="summary-tab" class="tab-content <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'summary') ? 'active' : ''; ?>">
    <div class="session-details-container">
        <h3>Medical Session Summary</h3>

        <div class="alert-box alert-success">
            <i class="ri-check-line"></i>
            <p>Your medical session is complete. Below is a summary of your treatment journey.</p>
        </div>

        <div class="summary-details">
            <h4>Medical Journey</h4>
            <ul>
                <?php if (isset($sessionData['generalDoctor'])): ?>
                <li><strong>General Doctor:</strong> Dr. <?php echo htmlspecialchars($sessionData['generalDoctor']['name']); ?></li>
                <?php endif; ?>

                <?php if ($sessionData['specialistBooked'] && isset($sessionData['specialist'])): ?>
                <li><strong>Specialist:</strong> Dr. <?php echo htmlspecialchars($sessionData['specialist']['name']); ?> 
                    (<?php echo htmlspecialchars($sessionData['specialist']['specialty']); ?>)</li>
                <?php endif; ?>

                <?php if ($sessionData['treatmentPlanCreated'] && isset($sessionData['treatmentPlan'])): ?>
                <li><strong>Diagnosis:</strong> <?php echo htmlspecialchars($sessionData['treatmentPlan']['diagnosis'] ?? 'Not specified'); ?></li>
                <li><strong>Treatment Cost:</strong> LKR <?php echo number_format($sessionData['treatmentPlan']['estimated_budget'] ?? 0, 2); ?></li>
                <?php endif; ?>
            </ul>

            <h4>Travel & Accommodation</h4>
            <ul>
                <?php if ($sessionData['transportBooked']): ?>
                <li><strong>Accommodation:</strong> Cinnamon Grand Colombo</li>
                <li><strong>Transportation:</strong> Private Airport Transfer</li>
                <?php else: ?>
                <li><em>No hotel or transportation booked yet</em></li>
                <?php endif; ?>

                <?php if ($sessionData['travelPlanSelected'] && isset($sessionData['travelPlan']) && $sessionData['travelPlan']['hasTrip']): ?>
                <li><strong>Travel Plan:</strong> <?php echo htmlspecialchars($sessionData['travelPlan']['trip']['name']); ?></li>
                <?php else: ?>
                <li><em>No travel plan selected yet</em></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="future-steps">
            <h4>Next Steps</h4>
            <p>Your medical journey with us doesn't end here. Here are some important next steps:</p>
            <ul>
                <li>Follow up with your specialist as prescribed in your treatment plan</li>
                <li>Keep all your medical documents organized for future reference</li>
                <li>Contact us if you need any assistance during your recovery</li>
            </ul>
        </div>

        <div class="tab-navigation">
            <button class="nav-btn prev" data-prev-tab="travel-plan-tab">
                <i class="ri-arrow-left-line"></i> Previous
            </button>
            <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-btn">
                <i class="ri-home-line"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
    </div>
    </div>
    </section>

    <!-- Start Medical Session Section
                <section class="appointments-section">
                    <div class="section-header">
                        <h2>Start New Medical Session</h2>
                        <form action="<?php echo $basePath; ?>/patient/start-medical-session" method="post" class="d-inline">
                            <?php if (isset($_SESSION['csrf_token'])): ?>
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <?php endif; ?>
                            <a href="<?php echo $basePath; ?>/patient/start-medical-session" class="session-start-btn">
                                <i class="ri-play-circle-line"></i> Start Medical Session
                            </a>
                        </form>
                    </div>
                    <div class="alert-box alert-success">
                        <i class="ri-check-line"></i>
                        <p>You already have a general doctor appointment. You can start your medical session with this appointment.</p>
                    </div>
                </section> -->
<?php endif; ?>

</main>
</div>

<!-- Modal for Reschedule -->
<div id="rescheduleModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Reschedule Appointment</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="reschedule-form">
            <div class="form-group">
                <label for="rescheduleDate">New Date</label>
                <input type="date" id="rescheduleDate" class="form-control">
            </div>
            <div class="form-group">
                <label for="rescheduleTime">New Time</label>
                <input type="time" id="rescheduleTime" class="form-control">
            </div>
            <div class="form-group">
                <label for="rescheduleReason">Reason for Rescheduling</label>
                <textarea id="rescheduleReason" class="form-control" rows="3"></textarea>
            </div>
            <button id="submitReschedule" class="full-width-button">Submit Request</button>
        </div>
    </div>
</div>



<script>
    const basePath = '<?php echo $basePath; ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const rescheduleModal = document.getElementById('rescheduleModal');
        const itineraryModal = document.getElementById('itineraryModal');

        // Tab navigation
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show the selected tab
            const activeTab = document.getElementById(tabId);
            if (activeTab) {
                activeTab.classList.add('active');

                // Update URL with tab parameter
                const params = new URLSearchParams(window.location.search);
                params.set('tab', tabId.replace('-tab', ''));
                const newUrl = window.location.pathname + '?' + params.toString();
                history.pushState({}, '', newUrl);
            }
        }

        // Set up navigation buttons
        document.querySelectorAll('.nav-btn').forEach(btn => {
            if (btn.classList.contains('prev') && btn.dataset.prevTab) {
                btn.addEventListener('click', function() {
                    showTab(this.dataset.prevTab);
                });
            }

            if (btn.classList.contains('next') && btn.dataset.nextTab) {
                btn.addEventListener('click', function() {
                    showTab(this.dataset.nextTab);
                });
            }
        });

        // Make step circles clickable for navigation
        document.querySelectorAll('.step-circle[data-tab]').forEach(circle => {
            circle.addEventListener('click', function() {
                showTab(this.dataset.tab);
            });
        });

        // Close modals
        document.querySelectorAll('.close-btn').forEach(element => {
            element.addEventListener('click', function() {
                if (rescheduleModal) rescheduleModal.style.display = 'none';
                if (itineraryModal) itineraryModal.style.display = 'none';
            });
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === rescheduleModal) {
                rescheduleModal.style.display = 'none';
            }
            if (event.target === itineraryModal) {
                itineraryModal.style.display = 'none';
            }
        });

        // Reschedule appointment button
        const rescheduleAppointmentBtn = document.getElementById('rescheduleAppointmentBtn');
        if (rescheduleAppointmentBtn) {
            rescheduleAppointmentBtn.addEventListener('click', function() {
                if (rescheduleModal) rescheduleModal.style.display = 'block';
            });
        }

        // Submit reschedule request
        const submitRescheduleBtn = document.getElementById('submitReschedule');
        if (submitRescheduleBtn) {
            submitRescheduleBtn.addEventListener('click', async function() {
                const date = document.getElementById('rescheduleDate').value;
                const time = document.getElementById('rescheduleTime').value;
                const reason = document.getElementById('rescheduleReason').value;

                try {
                    // In real implementation, send to server
                    alert("Reschedule request submitted successfully. Waiting for doctor's approval.");
                    rescheduleModal.style.display = 'none';
                } catch (error) {
                    console.error('Error:', error);
                    alert("Failed to submit request. Please try again.");
                }
            });
        }

        // View itinerary
        const viewItineraryBtn = document.getElementById('viewItinerary');
        if (viewItineraryBtn) {
            viewItineraryBtn.addEventListener('click', function() {
                itineraryModal.style.display = 'block';
            });
        }

        // Travel plan selection
        const travelPlanOptions = document.querySelectorAll('.travel-plan-option');
        let selectedPlan = null;

        travelPlanOptions.forEach(option => {
            option.addEventListener('click', function() {
                travelPlanOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedPlan = this.dataset.plan;
            });
        });

        const selectTravelPlanBtn = document.getElementById('selectTravelPlan');
        if (selectTravelPlanBtn) {
            selectTravelPlanBtn.addEventListener('click', function() {
                if (!selectedPlan) {
                    alert("Please select a travel plan");
                    return;
                }

                // In real implementation, send to server
                alert(`${selectedPlan === 'premium' ? 'Premium' : 'Basic'} plan selected successfully!`);
                window.location.reload();
            });
        }
    });
</script>
</body>

</html>