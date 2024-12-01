<?php
// Example Data
$totalUsers = 6;
$activeAppointments = 2;
$pendingVisaApplications = 1;

$tickets = [
    ['id' => 123, 'user' => 'Riley Smith', 'type' => 'Patient', 'subject' => 'Submit Visa Applic', 'status' => 'Open'],
    ['id' => 124, 'user' => 'Dr Emilia Johnson', 'type' => 'Doctor', 'subject' => 'Accept Appointment', 'status' => 'Open'],
    ['id' => 125, 'user' => 'Ethan Williams', 'type' => 'Patient', 'subject' => 'Book Appointment', 'status' => 'Closed'],
];

include 'includes/header.php';
?>

<body>
    <link rel="stylesheet" href="./css/overview.css">
    <?php include "./includes/navbar.php" ?>
    <div class="main-content">
        <header>
            <h1>Overview</h1>
        </header>

        <div class="stats-container">
            <div class="stat-card">
                <div><span><?= $totalUsers; ?></span>Patients</div>
                <div class="stat-icon">
                    <i class="ri-user-heart-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= $totalUsers; ?></span>Doctors</div>
                <div class="stat-icon">
                    <i class="ri-stethoscope-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= $totalUsers; ?></span>Hospitals</div>
                <div class="stat-icon">
                    <i class="ri-hospital-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= $totalUsers; ?></span>Bookings In-progress</div>
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
                        <p>562,084</p>
                    </div>
                    <div class="avatars">
                        <img src="user1.jpg" alt="User 1">
                        <img src="user2.jpg" alt="User 2">
                        <img src="user3.jpg" alt="User 3">
                        <img src="user4.jpg" alt="User 4">
                        <img src="user5.jpg" alt="User 5">
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="patientChart"></canvas>
                </div>

                <div class="legend">
                    <div><span style="background-color: #d4af37;"></span> 64% New Patient</div>
                    <div><span style="background-color: #2ecc71;"></span> 73% Recovered</div>
                    <div><span style="background-color: #34495e;"></span> 48% In Treatment</div>
                </div>
            </div>


            <div class="upcoming-appointments">
                <h2>Upcoming Appointments</h2>
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. William Smith</div>
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
                            <div class="org-name">20 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. William Smith</div>
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
                            <div class="org-name">20 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. William Smith</div>
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
                            <div class="org-name">20 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-header">
                        <div class="header-details">
                            <div class="org-name">Dr. William Smith</div>
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
                            <div class="org-name">20 / 12 / 2024</div>
                            <div class="time-ago">Date and Time</div>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>


        

    </div>
</body>

</html>

<script>
    document.querySelector('.calendar-header').addEventListener('click', (e) => {
        if (e.target.tagName === 'BUTTON') {
            alert('Calendar navigation is not implemented.');
        }
    });
</script>


<script>
    const ctx = document.getElementById('patientChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['New Patient', 'Recovered', 'In Treatment'],
            datasets: [{
                data: [64, 73, 48],
                backgroundColor: ['#d4af37', '#2ecc71', '#34495e'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

</script>