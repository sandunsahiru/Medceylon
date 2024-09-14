<!-- views/templates/sidebar.php -->

<div class="sidebar">
    <div class="logo">
        <h1>MedCeylon</h1>
    </div>
    <div class="sidebar-menu">
        <ul>
            <li class="menu-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                <a href="?page=dashboard">
                    <span class="icon">🏠</span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'appointments') ? 'active' : ''; ?>">
                <a href="?page=appointments">
                    <span class="icon">📅</span>
                    <span class="menu-text">Appointments</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'doctors') ? 'active' : ''; ?>">
                <a href="?page=doctors">
                    <span class="icon">👨‍⚕️</span>
                    <span class="menu-text">Doctors</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'patients') ? 'active' : ''; ?>">
                <a href="?page=patients">
                    <span class="icon">👥</span>
                    <span class="menu-text">Patients</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'chat') ? 'active' : ''; ?>">
                <a href="?page=chat">
                    <span class="icon">💬</span>
                    <span class="menu-text">Chat</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="?page=help">
                    <span class="icon">❓</span>
                    <span class="menu-text">Help</span>
                </a>
            </li>
        </ul>
    </div>
</div>