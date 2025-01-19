<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - <?php echo $page_title ?? 'Special Doctor Dashboard'; ?></title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/doctordashboard.css">
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
                <a href="<?php echo $basePath; ?>/vpdoctor/dashboard" 
                   class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/vpdoctor/appointments" 
                   class="nav-item <?php echo $current_page === 'appointments' ? 'active' : ''; ?>">
                    <i class="ri-calendar-line"></i>
                    <span>Appointments</span>
                </a>
                <a href="<?php echo $basePath; ?>/vpdoctor/patients" 
                   class="nav-item <?php echo $current_page === 'patients' ? 'active' : ''; ?>">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <!-- <a href="<?php echo $basePath; ?>/doctor/all-doctors" 
                   class="nav-item <?php echo $current_page === 'doctors' ? 'active' : ''; ?>">
                    <i class="ri-nurse-line"></i>
                    <span>Doctors</span>
                </a> -->
                <a href="<?php echo $basePath; ?>/vpdoctor/profile" 
                   class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                    <i class="ri-user-settings-line"></i>
                    <span>Profile</span>
                </a>
                <a href="<?php echo $basePath; ?>/vpdoctor/chat" 
                   class="nav-item <?php echo $current_page === 'chat' ? 'active' : ''; ?>">
                    <i class="ri-chat-1-line"></i>
                    <span>Chat</span>
                </a>
            </nav>

            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>