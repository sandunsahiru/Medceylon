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
                            <div class="step-circle">
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
                            <div class="step-circle">
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
                            <div class="step-circle">
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
                            <div class="step-circle">
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
                            <div class="step-circle">
                                <?php if ($sessionData['travelPlanSelected']): ?>
                                    <i class="ri-check-line"></i>
                                <?php else: ?>
                                    <i class="ri-plane-line"></i>
                                <?php endif; ?>
                            </div>
                            <div class="step-text">Travel Plan</div>
                        </div>
                    </div>

                    <!-- General Doctor Section -->
                    <?php if ($sessionData['generalDoctorBooked'] && !$sessionData['specialistBooked']): ?>
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
                                            if(isset($sessionData['generalDoctor']['appointmentDate'])) {
                                                echo htmlspecialchars(date('d/m/Y', strtotime($sessionData['generalDoctor']['appointmentDate'])));
                                            } else {
                                                echo '28/04/2025'; // Default from your existing appointment
                                            }
                                        ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="ri-time-line"></i>
                                        <span><?php 
                                            if(isset($sessionData['generalDoctor']['appointmentDate'])) {
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
                                    <?php if(($sessionData['generalDoctor']['appointmentMode'] ?? '') === 'In-Person'): ?>
                                    <a href="https://maps.google.com/?q=Kandy+Teaching+Hospital" target="_blank" class="action-btn secondary">
                                        <i class="ri-map-pin-line"></i> Get Directions
                                    </a>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (isset($sessionData['generalDoctor']['appointmentMode']) && $sessionData['generalDoctor']['appointmentMode'] === 'Online' && !empty($sessionData['generalDoctor']['meetLink'])): ?>
                                <div class="meet-link-container">
                                    <p><strong>Online Meeting:</strong></p>
                                    <a href="<?php echo htmlspecialchars($sessionData['generalDoctor']['meetLink']); ?>" target="_blank" class="meet-link-btn">
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
                    </div>
                    <?php endif; ?>
                    
                    <!-- Waiting for Specialist Section -->
                    <?php if ($sessionData['generalDoctorBooked'] && !$sessionData['specialistBooked'] && isset($sessionData['waitingForSpecialist']) && $sessionData['waitingForSpecialist']): ?>
                    <div class="session-details-container">
                        <h3>Specialist Referral</h3>
                        <div class="alert-box alert-warning">
                            <i class="ri-time-line"></i>
                            <p>Your general doctor is in the process of booking a specialist for you. You will be notified once the booking is confirmed.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Specialist Doctor Section -->
                    <?php if ($sessionData['specialistBooked']): ?>
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
                                    <?php if($sessionData['specialist']['appointmentMode'] === 'In-Person'): ?>
                                    <a href="https://maps.google.com/?q=<?php echo urlencode($sessionData['specialist']['hospital']); ?>" target="_blank" class="action-btn secondary">
                                        <i class="ri-map-pin-line"></i> Get Directions
                                    </a>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($sessionData['specialist']['appointmentMode'] === 'Online' && !empty($sessionData['specialist']['meetLink'])): ?>
                                <div class="meet-link-container">
                                    <p><strong>Online Meeting:</strong></p>
                                    <a href="<?php echo htmlspecialchars($sessionData['specialist']['meetLink']); ?>" target="_blank" class="meet-link-btn">
                                        <i class="ri-video-chat-line"></i> Join Google Meet
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Treatment Plan Section -->
                    <?php if ($sessionData['treatmentPlanCreated']): ?>
                    <div class="session-details-container">
                        <h3>Treatment Information</h3>
                        <div class="treatment-details">
                            <p><strong>Travel Restrictions:</strong> <?php echo htmlspecialchars($sessionData['travelRestrictions']); ?></p>
                            <p><strong>Estimated Budget:</strong> <?php echo htmlspecialchars($sessionData['estimatedBudget']); ?></p>
                            
                            <?php if (!$sessionData['transportBooked']): ?>
                            <div class="alert-box alert-info">
                                <i class="ri-information-line"></i>
                                <p>You need to arrange accommodation and transportation for your visit to Sri Lanka.</p>
                            </div>
                            <button id="bookHotelTransport" class="full-width-button">
                                <i class="ri-building-2-line"></i> Book Hotel & Transport Now
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($sessionData['transportBooked'] && !$sessionData['travelPlanSelected']): ?>
                            <div class="success-message">
                                <i class="ri-check-line"></i> Transport & Accommodation booked
                            </div>
                            
                            <div class="travel-plans">
                                <h3>Select Travel Plan (Optional):</h3>
                                
                                <div class="travel-plan-option" data-plan="basic">
                                    <div class="plan-header">
                                        <span class="plan-name">Basic Plan</span>
                                        <span class="plan-price">$150</span>
                                    </div>
                                    <p class="plan-description">Airport to hospital transfers</p>
                                </div>
                                
                                <div class="travel-plan-option" data-plan="premium">
                                    <div class="plan-header">
                                        <span class="plan-name">Premium Plan</span>
                                        <span class="plan-price">$350</span>
                                    </div>
                                    <p class="plan-description">Guided city tour + VIP hospital care</p>
                                </div>
                                
                                <button id="selectTravelPlan" class="full-width-button">
                                    <i class="ri-check-double-line"></i> Select Travel Plan
                                </button>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($sessionData['travelPlanSelected']): ?>
                            <div class="success-message">
                                <i class="ri-check-line"></i> Transport & Accommodation booked
                            </div>
                            <div class="success-message">
                                <i class="ri-check-line"></i> Travel Plan selected
                            </div>
                            
                            <div class="alert-box alert-success">
                                <i class="ri-flight-takeoff-line"></i>
                                <p>Your trip is fully booked and planned. Safe travels!</p>
                            </div>
                            
                            <button id="viewItinerary" class="full-width-button">
                                <i class="ri-file-list-3-line"></i> View Complete Itinerary
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo $basePath; ?>/patient/dashboard/"><button class="submit-btn">Next Page</button></a>
            </section>
            <?php else: ?>
            <!-- Start Medical Session Section -->
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
            </section>
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

    <!-- View Itinerary Modal -->
    <div id="itineraryModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Your Travel Itinerary</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="itinerary-details">
                <div class="session-details-container">
                    <h3>Trip Summary</h3>
                    <div class="card-outline">
                        <p><strong>Patient:</strong> <?php echo $sessionData['patientName'] ?? 'Patient Name'; ?></p>
                        <p><strong>Travel Date:</strong> July 13, 2025</p>
                        <p><strong>Return Date:</strong> July 20, 2025</p>
                        <p><strong>Hospital:</strong> Lanka Heart Centre</p>
                        <p><strong>Accommodation:</strong> Cinnamon Grand Colombo</p>
                        <p><strong>Transportation:</strong> Private Airport Transfer</p>
                    </div>

                    <h3>Medical Appointments</h3>
                    <div class="card-outline">
                        <p><strong>Specialist:</strong> Dr. Nuwan Perera, Cardiologist</p>
                        <p><strong>Appointment Date:</strong> July 15, 2025</p>
                        <p><strong>Appointment Time:</strong> 10:00 AM</p>
                        <p><strong>Hospital Address:</strong> Lanka Heart Centre, 123 Hospital Road, Colombo</p>
                    </div>

                    <h3>Travel Requirements</h3>
                    <div class="card-outline">
                        <p><strong>Medical Advice:</strong> Can travel, but avoid high altitudes and long distance journeys.</p>
                        <p><strong>Required Documents:</strong> Passport, Visa, Medical Reports, Insurance</p>
                        <p><strong>Special Assistance:</strong> Wheelchair at airport</p>
                    </div>

                    <h3>Budget Estimate</h3>
                    <div class="card-outline">
                        <p><strong>Medical Treatment:</strong> $3,900</p>
                        <p><strong>Accommodation:</strong> $700</p>
                        <p><strong>Transportation:</strong> $150</p>
                        <p><strong>Travel Plan:</strong> $350 (Premium Plan)</p>
                        <p><strong>Total Estimate:</strong> $5,100</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';

        document.addEventListener('DOMContentLoaded', function() {
            const rescheduleModal = document.getElementById('rescheduleModal');
            const itineraryModal = document.getElementById('itineraryModal');

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
            
            // Hotel & Transport booking
            const bookHotelTransportBtn = document.getElementById('bookHotelTransport');
            if (bookHotelTransportBtn) {
                bookHotelTransportBtn.addEventListener('click', function() {
                    window.location.href = `${basePath}/patient/book-hotel-transport`;
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