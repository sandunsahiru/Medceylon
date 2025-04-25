<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | MedCeylon</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/hospital.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
                    <p style="text-align: center;">Hospital Admin</p>
                    <h1>MedCeylon</h1>
                </a>
            </div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/hospital/dashboard" class="nav-item <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/hospital/treatment-requests" class="nav-item <?php echo $currentPage === 'treatment-requests' ? 'active' : ''; ?>">
                    <i class="ri-file-list-3-line"></i>
                    <span>Treatment Requests</span>
                </a>
                <a href="<?php echo $basePath; ?>/hospital/patients" class="nav-item <?php echo $currentPage === 'patients' ? 'active' : ''; ?>">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="<?php echo $basePath; ?>/hospital/departments" class="nav-item <?php echo $currentPage === 'departments' ? 'active' : ''; ?>">
                    <i class="ri-hospital-line"></i>
                    <span>Departments</span>
                </a>
                <a href="<?php echo $basePath; ?>/hospital/doctors" class="nav-item <?php echo $currentPage === 'doctors' ? 'active' : ''; ?>">
                    <i class="ri-nurse-line"></i>
                    <span>Doctors</span>
                </a>
                <a href="<?php echo $basePath; ?>/hospital/messages" class="nav-item <?php echo $currentPage === 'messages' ? 'active' : ''; ?>">
                    <i class="ri-message-2-line"></i>
                    <span>Messages</span>
                </a>
            </nav>
            
            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>