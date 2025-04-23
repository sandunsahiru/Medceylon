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
            <div class="ongoing-appointments">
                <h2>Upcoming Appointments</h2>

                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-upper-details">
                                <div class="details">
                                    <p>Doctor: Dr.
                                        <?= htmlspecialchars($appointment['doctor']['first_name'] . ' ' . $appointment['doctor']['last_name']) ?>
                                    </p>
                                </div>
                                <div class="details">
                                    <p>Patient:
                                        <?= htmlspecialchars($appointment['patient']['first_name'] . ' ' . $appointment['patient']['last_name']) ?>
                                    </p>
                                </div>
                                <div class="details">
                                    <p>Date: <?= htmlspecialchars($appointment['appointment']['date']) ?></p>
                                </div>
                                <div class="details">
                                    <p>Time: <?= htmlspecialchars($appointment['appointment']['time']) ?></p>
                                </div>
                                <div class="details">
                                    <p>Status: <?= htmlspecialchars($appointment['appointment']['status']) ?></p>
                                </div>
                            </div>
                            <button>Reschedule</button>
                            <button>Finish Consultation</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No upcoming appointments.</p>
                <?php endif; ?>
            </div>
        </div>




    </div>
    </div>
</body>

</html>

<?php
$appointmentsJsFormat = [];
foreach ($appointments as $a) {
    $date = date('Y-m-d', strtotime($a['appointment']['date']));
    if (!isset($appointmentsJsFormat[$date])) {
        $appointmentsJsFormat[$date] = [];
    }
    $appointmentsJsFormat[$date][] = [
        'doctor' => "Dr. " . $a['doctor']['first_name'] . " " . $a['doctor']['last_name'],
        'patient' => $a['patient']['first_name'] . " " . $a['patient']['last_name'],
        'time' => $a['appointment']['time']
    ];
}
?>


<script>
    const monthYear = document.getElementById("monthYear");
    const daysContainer = document.getElementById("days");
    const appointmentsContainer = document.getElementById("appointments");
    let date = new Date();

    const appointments = <?= json_encode($appointmentsJsFormat) ?>;

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
                            <div><strong>Doctor:</strong> ${appt.doctor}<br></div>
                            <div><strong>Patient:</strong> ${appt.patient}<br></div>
                            <div><strong>Time:</strong> ${appt.time}</div>
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