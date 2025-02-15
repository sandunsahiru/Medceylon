<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - <?php echo $page_title ?? 'Doctor Dashboard'; ?></title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/doctordashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <style>
/* Override and extend modal styles */
.large-modal {
    width: 90% !important;
    max-width: 900px !important;
    margin: 2vh auto !important;
    height: auto !important;
    max-height: 96vh !important;
}

.modal-body {
    max-height: calc(90vh - 120px);
    overflow-y: auto;
    padding: 20px;
}

.report-item {
    background: var(--bg-light);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
}

.report-info h4 {
    color: var(--text-dark);
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.report-info p {
    color: var(--text-light);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.report-info p strong {
    color: var(--text-dark);
    margin-right: 0.5rem;
}

.report-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
}

.report-actions .view-btn {
    background: var(--primary-light);
    color: var(--primary-color);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.report-actions .view-btn:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.loading-spinner {
    text-align: center;
    padding: 3rem;
    color: var(--text-light);
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: var(--text-light);
    font-size: 1.1rem;
}

.error-message {
    color: #DC2626;
    text-align: center;
    padding: 2rem;
    background: #FEE2E2;
    border-radius: var(--border-radius);
    margin: 1rem 0;
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
                <a href="<?php echo $basePath; ?>/doctor/dashboard" 
                   class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/doctor/appointments" 
                   class="nav-item <?php echo $current_page === 'appointments' ? 'active' : ''; ?>">
                    <i class="ri-calendar-line"></i>
                    <span>Appointments</span>
                </a>
                <a href="<?php echo $basePath; ?>/doctor/patients" 
                   class="nav-item <?php echo $current_page === 'patients' ? 'active' : ''; ?>">
                    <i class="ri-user-line"></i>
                    <span>Patients</span>
                </a>
                <a href="<?php echo $basePath; ?>/doctor/all-doctors" 
                   class="nav-item <?php echo $current_page === 'doctors' ? 'active' : ''; ?>">
                    <i class="ri-nurse-line"></i>
                    <span>Doctors</span>
                </a>
                <a href="<?php echo $basePath; ?>/doctor/profile" 
                   class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                    <i class="ri-user-settings-line"></i>
                    <span>Profile</span>
                </a>
                <a href="<?php echo $basePath; ?>/doctor/chat" 
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