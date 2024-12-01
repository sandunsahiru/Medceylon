<?php
$page = "appointments";
include "./includes/header.php";
?>


<body>
    <link rel="stylesheet" href="./css/appointment.css">
    <?php include "./includes/navbar.php" ?>

    <div class="main-content">
        <header>
            <h1>User Management</h1>
        </header>

        <div class="container">
            <div class="upper">
                <!-- Today Appointments -->
                <div class="today-appointments">
                    <h2>Today Appointments</h2>
                    <div class="appointment-status">
                        <button>New Patients</button>
                        <button>Follow-Up Patients</button>
                    </div>
                    <div class="appointment-list">
                        <h3>Appointment (46)</h3>
                        <ul>
                            <li>
                                <p>Kristin Watson</p>
                                <span>Stomach Pain</span>
                                <span>08:00 AM</span>
                            </li>
                            <li>
                                <p>Brooklyn Simmons</p>
                                <span>On Consultation</span>
                                <span>11:25 AM</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- On-Going Appointments -->
                <div class="ongoing-appointments">
                    <h2>On Going Appointments</h2>
                    <p>Brooklyn Simmons - On Consultation</p>
                    <div class="details">
                        <p>Doctor: Dr. Joseph Carla</p>
                        <p>Time: 11:00 AM - 12:00 PM</p>
                    </div>
                    <textarea placeholder="Consultation Notes"></textarea>
                    <button>Reschedule</button>
                    <button>Finish Consultation</button>
                </div>





            </div>
            <div class="bottom">
                <!-- Appointment Schedule -->
                <div class="follow-up">
                    <div class="calendar-header">
                        <h2>Appointment Schedule</h2>
                    </div>
                    <div class="calendar-container">
                        <div class="calendar">
                            <div class="calendar-nav">
                                <button id="prev-month">&lt;</button>
                                <span id="month-year">May 2024</span>
                                <button id="next-month">&gt;</button>
                            </div>
                            <table class="calendar-grid">
                                <thead>
                                    <tr>
                                        <th>Su</th>
                                        <th>Mo</th>
                                        <th>Tu</th>
                                        <th>We</th>
                                        <th>Th</th>
                                        <th>Fr</th>
                                        <th>Sa</th>
                                    </tr>
                                </thead>
                                <tbody id="calendar-body">
                                    <!-- Calendar dates will populate here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="appointment-list">
                            <h3>Appointments</h3>
                            <ul id="appointments" class="info-list">
                                <!-- Appointments dynamically added here -->
                            </ul>
                        </div>
                    </div>





                </div>
            </div>
        </div>
    </div>
</body>

</html>