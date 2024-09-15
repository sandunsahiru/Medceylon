<!-- views/templates/patient_sidebar.php -->

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
            <li class="menu-item <?php echo ($page == 'book_appointment') ? 'active' : ''; ?>">
                <a href="?page=book_appointment">
                    <span class="icon">📅</span>
                    <span class="menu-text">Book Appointment</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'my_appointments') ? 'active' : ''; ?>">
                <a href="?page=my_appointments">
                    <span class="icon">🗓️</span>
                    <span class="menu-text">My Appointments</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'chat') ? 'active' : ''; ?>">
                <a href="?page=chat">
                    <span class="icon">💬</span>
                    <span class="menu-text">Chat</span>
                </a>
            </li>
            <li class="menu-item <?php echo ($page == 'profile') ? 'active' : ''; ?>">
                <a href="?page=profile">
                    <span class="icon">👤</span>
                    <span class="menu-text">Profile</span>
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
