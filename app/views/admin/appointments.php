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
    // Update appointment status function
    function updateAppointmentStatus(appointmentId) {
        // Show a modal or prompt to update the status
        const newStatus = prompt('Enter new status for the appointment:');
        if (newStatus) {
            // Send request to update the status (AJAX or form submission)
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="update_appointment_status">
                <input type="hidden" name="appointment_id" value="${appointmentId}">
                <input type="hidden" name="new_status" value="${newStatus}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Cancel appointment function
    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            // Send a request to cancel the appointment (AJAX or form submission)
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="cancel_appointment">
                <input type="hidden" name="appointment_id" value="${appointmentId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    const monthYear = document.getElementById("monthYear");
    const daysContainer = document.getElementById("days");
    const appointmentsContainer = document.getElementById("appointments");
    let date = new Date();

    const appointments = {
        "2025-02-18": ["Meeting with client", "Doctor appointment"],
        "2025-02-22": ["Project deadline"],
        "2025-02-25": ["Team outing", "Birthday party"]
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
            appointmentsContainer.innerHTML = `<h3>Appointments for ${day}</h3><ul>${appointments[day].map(appt => `<li>${appt}</li>`).join('')}</ul>`;
            appointmentsContainer.style.display = "block";
        } else {
            appointmentsContainer.innerHTML = "<p>No appointments for this day.</p>";
            appointmentsContainer.style.display = "block";
        }
    }
    renderCalendar();

</script>