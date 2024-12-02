<link rel="stylesheet" href="./css/navbar.css">

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
                <a href="./overview.php">Dashboard</a>
            </li>
            <li class="<?= $page == 'user_management' ? 'active' : '' ?>">
                <i class="ri-user-line"></i>
                <a href="index.php">User Management</a>
            </li>
            <li class="<?= $page == 'appointments' ? 'active' : '' ?>">
                <i class="ri-calendar-line"></i>
                <a href="appointment.php">Appointments</a>
            </li>
            <li class="<?= $page == 'bookings' ? 'active' : '' ?>">
                <i class="ri-book-line"></i>
                <a href="bookings.php">Bookings</a>
            </li>
            <li>
                <i class="ri-chat-4-line"></i>
                <a href="#">Chat</a>
            </li>
        </ul>
    </nav>

</div>