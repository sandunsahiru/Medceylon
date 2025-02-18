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
            <div class="patient-percentage">
                <h2>Patient Percentage</h2>
                <div class="tabs">
                    <button class="active">Daily</button>
                    <button>Weekly</button>
                    <button>Monthly</button>
                </div>

                <div class="total-patient">
                    <div class="icon"><i class="ri-heart-line" style="color: #2ecc71; font-size: 24px;"></i></div>
                    <div class="count">
                        <h3>Total Patient</h3>
                        <p>5634</p>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="patientChart"></canvas>
                </div>

                <div class="legend">
                    <div><span style="background-color: #d4af37;"></span> 45% New Patient</div>
                    <div><span style="background-color: #2ecc71;"></span> 35% Recovered</div>
                    <div><span style="background-color: #34495e;"></span> 20% In Srilanka</div>
                </div>
            </div>


            <div class="upcoming-appointments">
                <h2>Upcoming Appointments</h2>
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. Lakmal</div>
                            <div class="time-ago">Cardiologist</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">William Smith</div>
                            <div class="time-ago">Patient</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">11 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. Sahiru Bandara</div>
                            <div class="time-ago">Neurologist</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">John keels</div>
                            <div class="time-ago">Patient</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">16 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. Kumara Jayaweera</div>
                            <div class="time-ago">General Doctor</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Lasith Chamara</div>
                            <div class="time-ago">Patient</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">20 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. Lakmal</div>
                            <div class="time-ago">Cardiologist</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">David Robert</div>
                            <div class="time-ago">Patient</div>
                        </div>
                    </div>
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">24 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

</body>

</html>