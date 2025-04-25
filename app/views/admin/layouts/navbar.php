<div class="sidebar">
    <div class="profile">
        <span class="material-symbols-rounded" style="font-size: 48px;">account_circle</span>
        <div>
            <h2>Admin</h2>
            <p>MedCeylon</p>
        </div>
    </div>
    <nav>
        <ul>

            <li class="<?= $page == 'overview' ? 'active' : '' ?>">
                <i class="ri-dashboard-line"></i>
                <a href="<?php echo $basePath; ?>/admin/dashboard">Dashboard</a>
            </li>
            <li class="<?= $page == 'userManagement' ? 'active' : '' ?>">
                <i class="ri-user-line"></i>
                <a href="<?php echo $basePath; ?>/admin/user-management">User Management</a>
            </li>
            <li>
                <i class="ri-calendar-line"></i>
                <a href="<?php echo $basePath; ?>/admin/appointments">Appointments</a>
            </li>
            <li>
                <i class="ri-calendar-line"></i>
                <a href="<?php echo $basePath; ?>/admin/bookings">Bookings</a>
            </li>
            <li>
                <i class="ri-calendar-line"></i>
                <a href="<?php echo $basePath; ?>/admin/hotelbookings">Hotel Bookings</a>
            </li>
            <li>
                <i class="ri-chat-4-line"></i>
                <a href="#">Chat</a>
            </li>

            <a href="<?php echo $basePath; ?>/home  " class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </ul>
    </nav>

</div>