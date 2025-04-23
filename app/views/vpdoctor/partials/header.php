<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - <?php echo $page_title ?? 'Special Doctor Dashboard'; ?></title>
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

/* Chat styles from doctor header */
.chat-container {
    display: flex;
    height: calc(100vh - 140px);
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 20px;
}

.chat-sidebar {
    width: 300px;
    border-right: 1px solid #e0e0e0;
    overflow-y: auto;
}

.chat-list {
    padding: 10px;
}

.chat-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.chat-item:hover {
    background-color: #f5f5f5;
}

.chat-item.active {
    background-color: var(--primary-color);
    color: white;
}

.chat-item img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
}

.chat-item-info {
    flex: 1;
}

.chat-item-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.chat-item-preview {
    font-size: 0.9em;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
}

.chat-header img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    background-color: #f8f9fa;
}

.message {
    margin-bottom: 20px;
    max-width: 70%;
    display: flex;
    flex-direction: column;
}

.message.sent {
    align-self: flex-end;
}

.message-content {
    padding: 12px 16px;
    border-radius: 12px 12px 12px 2px;
    background-color: #fff;
    display: inline-block;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.message.sent .message-content {
    background-color: var(--primary-color);
    color: white;
    border-radius: 12px 12px 2px 12px;
}

.message-time {
    font-size: 0.8em;
    color: #666;
    margin-top: 5px;
    text-align: left;
}

.message.sent .message-time {
    text-align: right;
}

.chat-input {
    padding: 20px;
    border-top: 1px solid #e0e0e0;
    background-color: #fff;
}

.chat-input form {
    display: flex;
    gap: 10px;
}

.chat-input input {
    flex: 1;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    outline: none;
}

.chat-input button {
    padding: 12px 24px;
    background-color: var(--primary-color);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.chat-input button:hover {
    background-color: var(--primary-dark);
}

.chat-input-container {
    display: flex;
    gap: 10px;
    align-items: center;
    width: 100%;
}

.attachment-btn {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    color: var(--primary-color);
}

.hidden {
    display: none;
}

.attachment-preview {
    font-size: 0.8em;
    color: #666;
    margin-top: 5px;
    padding: 4px 8px;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.05);
}

.message.sent .attachment-preview {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.error-message {
    color: #dc3545;
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    background-color: #fbe7e9;
    display: none;
}

.chat-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #666;
    font-size: 1.1em;
    background-color: #f8f9fa;
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