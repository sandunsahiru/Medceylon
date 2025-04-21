<?php require_once ROOT_PATH . '/app/views/admin/layouts/header.php'; ?>

<body>
    <?php require_once ROOT_PATH . '/app/views/admin/layouts/navbar.php'; ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/admin/appointment.css">

    <div class="main-content">
        <header>
            <h1>Appointments</h1>
        </header>

        <div class="container">

            <!-- Appointment Schedule -->
            <!-- <div class="follow-up">
                    <div class="calendar-header">
                        <h2>Appointment Schedule</h2>
                    </div> -->
            <div class="calendar">
                <header>
                    <button onclick="prevMonth()">◀</button>
                    <h2 id="monthYear"></h2>
                    <button onclick="nextMonth()">▶</button>
                </header>
                <div class="days" id="days"></div>
            </div>
            <div class="appointments" id="appointments"></div>

        </div>

        <div class="upper">
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

    </div>
    </div>
</body>

</html>

<script>
        const monthYear = document.getElementById("monthYear");
        const daysContainer = document.getElementById("days");
        const appointmentsContainer = document.getElementById("appointments");
        let date = new Date();
        
        const appointments = {
            "2025-02-18": [
                { doctor: "Dr. Smith", patient: "John Doe", time: "10:00 AM" },
                { doctor: "Dr. Brown", patient: "Jane Doe", time: "2:00 PM" }
            ],
            "2025-02-22": [
                { doctor: "Dr. Adams", patient: "Alice Green", time: "1:00 PM" }
            ],
            "2025-02-25": [
                { doctor: "Dr. White", patient: "Bob Black", time: "3:30 PM" }
            ]
        };

        function renderCalendar() {
            daysContainer.innerHTML = "";
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).getDay();
            const lastDate = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
            monthYear.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });
            for (let i = 0; i < firstDay; i++) {
                daysContainer.innerHTML += "<div></div>";
            }
            for (let i = 1; i <= lastDate; i++) {
                const dayDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                let appointmentDot = appointments[dayDate] ? "<div class='dot'></div>" : "";
                daysContainer.innerHTML += `<div class='day' onclick='showAppointments("${dayDate}")'>${i}${appointmentDot}</div>`;
            }
        }

        function prevMonth() {
            date.setMonth(date.getMonth() - 1);
            renderCalendar();
        }
        function nextMonth() {
            date.setMonth(date.getMonth() + 1);
            renderCalendar();
        }
        function showAppointments(day) {
            if (appointments[day]) {
                appointmentsContainer.innerHTML = `<h3>Appointments for ${day}</h3>` +
                    appointments[day].map(appt => `
                        <div class='appointment-card'>
                            <strong>Doctor:</strong> ${appt.doctor}<br>
                            <strong>Patient:</strong> ${appt.patient}<br>
                            <strong>Time:</strong> ${appt.time}
                        </div>
                    `).join('');
                appointmentsContainer.style.display = "block";
            } else {
                appointmentsContainer.innerHTML = "<p>No appointments for this day.</p>";
                appointmentsContainer.style.display = "block";
            }
        }
        renderCalendar();
    </script>
