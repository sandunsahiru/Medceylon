<?php
$page = 'overview';
include './includes/db_connection.php'; // Include your database connection

// Query to count the number of patients
$query_patients = "SELECT COUNT(*) AS total_patients FROM users WHERE role_id = 1";  // Assuming role_id 1 for patients
$result_patients = $conn->query($query_patients);
$patients_count = 0;
if ($result_patients) {
    $row = $result_patients->fetch_assoc();
    $patients_count = $row['total_patients'];
}

// Query to count the number of doctors
$query_doctors = "SELECT COUNT(*) AS total_doctors FROM users WHERE role_id IN (2, 3)";  // Assuming role_id 2 and 3 for doctors
$result_doctors = $conn->query($query_doctors);
$doctors_count = 0;
if ($result_doctors) {
    $row = $result_doctors->fetch_assoc();
    $doctors_count = $row['total_doctors'];
}

// Query to count the number of hospitals
$query_hospitals = "SELECT COUNT(*) AS total_hospitals FROM hospitals";  // Assuming the hospitals table exists
$result_hospitals = $conn->query($query_hospitals);
$hospitals_count = 0;
if ($result_hospitals) {
    $row = $result_hospitals->fetch_assoc();
    $hospitals_count = $row['total_hospitals'];
}

// Close the connection
$conn->close();

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
                <div><span><?= $patients_count; ?></span>Patients</div>
                <div class="stat-icon">
                    <i class="ri-user-heart-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?=  $doctors_count; ?></span>Doctors</div>
                <div class="stat-icon">
                    <i class="ri-stethoscope-line"></i>
                </div>
            </div>

            <div class="stat-card">
                <div><span><?= $hospitals_count; ?></span>Hospitals</div>
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