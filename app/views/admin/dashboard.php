<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>

    <div class="main-content">
        <header>
            <h1>Overview</h1>
        </header>

        <div class="stats-container">
            <div class="stat-card">
                <div><span><?= htmlspecialchars($patients_count); ?></span> Patients</div>
                <div class="stat-icon">
                    <i class="ri-user-heart-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= htmlspecialchars($doctors_count) ?></span>Doctors</div>
                <div class="stat-icon">
                    <i class="ri-stethoscope-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= htmlspecialchars($hospitals_count) ?></span>Hospitals</div>
                <div class="stat-icon">
                    <i class="ri-hospital-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?php $totalUsers; ?></span>Bookings In-progress</div>
                <div class="stat-icon">
                    <i class="ri-book-3-line"></i>
                </div>
            </div>

        </div>
        <div class="mid-container">
            <div class="pending-requests">
                <h2>Pending Requests</h2>
                <div class="info-card">
                    <h2>New Users </h2>
                    <h2>10</h2>
                </div>
                <div class="info-card">
                    <h2>Hotel Bookings - </h2>
                    <h2><?= htmlspecialchars((string)($booking_count ?? '')) ?></h2>

                </div>
                <div class="info-card">
                    <h2>New Doctors</h2>
                    <h2>10</h2>
                </div>
            </div>
            <div class="upcoming-appointments">
                <h2>Upcoming Appointments</h2>
                <?php if (empty($appointments)): ?>
                    <p>No upcoming appointments.</p>
                <?php else: ?>
                    <?php foreach (array_slice($appointments, 0, 4) as $appointment): ?>
                        <div class="info-card">
                            <div class="info-header">
                                <div class="header-details">
                                    <div class="org-name">
                                        <?= htmlspecialchars($appointment['doctor']['first_name'] . ' ' . $appointment['doctor']['last_name']) ?>
                                    </div>
                                    <div class="time-ago"><?= htmlspecialchars($appointment['specialization']) ?></div>
                                </div>
                            </div>
                            <div class="info-header">
                                <div class="header-details">
                                    <div class="org-name">
                                        <?= htmlspecialchars($appointment['patient']['first_name'] . ' ' . $appointment['patient']['last_name']) ?>
                                    </div>
                                    <div class="time-ago">Patient</div>
                                </div>
                            </div>
                            <div class="info-header">
                                <div class="header-details">
                                    <div class="org-name">
                                        <?= htmlspecialchars($appointment['appointment']['date'] . '|' . $appointment['appointment']['time']) ?>
                                    </div>
                                    <div class="time-ago">Date and Time</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>


        </div>
    </div>

</body>

</html>