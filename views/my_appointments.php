<!-- views/my_appointments.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'My Appointments';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/patient_sidebar.php'; ?>

    <div class="content content-full-height">
        <h1>My Appointments</h1>

        <!-- Appointment Table -->
        <div class="appointment-table-container">
            <h2>Upcoming Appointments</h2>
            <?php if (!empty($appointments)): ?>
                <table class="appointment-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?= date('h:i A', strtotime($appointment['appointment_time'])); ?></td>
                                <td>Dr. <?= htmlspecialchars($appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name']); ?></td>
                                <td><?= htmlspecialchars($appointment['specialization'] ?: 'General'); ?></td>
                                <td><?= htmlspecialchars($appointment['appointment_status']); ?></td>
                                <td><?= htmlspecialchars($appointment['reason_for_visit']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no upcoming appointments.</p>
            <?php endif; ?>
        </div>

        <!-- Calendar -->
        <div class="calendar-container">
            <h2>Appointment Calendar</h2>
            <div class="calendar">
                <div class="calendar-header">
                    <button class="prev-month">‹</button>
                    <div class="current-month-year">
                        <span id="month-name"></span> <span id="year"></span>
                    </div>
                    <button class="next-month">›</button>
                </div>
                <div class="calendar-weekdays">
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>
                    <div>Sun</div>
                </div>
                <div class="calendar-days" id="calendar-days">
                    <!-- Days will be generated by JavaScript -->
                </div>
            </div>
            <div class="appointment-details" id="appointment-details">
                <!-- Appointment details will be displayed here -->
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<script>
// JavaScript for Calendar Functionality
document.addEventListener('DOMContentLoaded', function() {
    const appointments = <?php echo json_encode($appointments); ?>;
    const calendarDays = document.getElementById('calendar-days');
    const monthNameEl = document.getElementById('month-name');
    const yearEl = document.getElementById('year');
    const appointmentDetailsEl = document.getElementById('appointment-details');

    let date = new Date();
    let selectedDate = new Date();

    const renderCalendar = () => {
        date.setDate(1);
        const monthName = date.toLocaleString('default', { month: 'long' });
        const year = date.getFullYear();

        monthNameEl.textContent = monthName;
        yearEl.textContent = year;

        const firstDayIndex = (date.getDay() + 6) % 7; // Adjust for Monday start
        const prevLastDay = new Date(date.getFullYear(), date.getMonth(), 0).getDate();
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

        calendarDays.innerHTML = '';

        // Previous Month's Days
        for (let x = firstDayIndex; x > 0; x--) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('calendar-day', 'inactive');
            dayEl.textContent = prevLastDay - x + 1;
            calendarDays.appendChild(dayEl);
        }

        // Current Month's Days
        for (let i = 1; i <= lastDay; i++) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('calendar-day');
            dayEl.textContent = i;

            const dayDate = new Date(date.getFullYear(), date.getMonth(), i);

            // Highlight today's date
            const today = new Date();
            if (
                dayDate.getDate() === today.getDate() &&
                dayDate.getMonth() === today.getMonth() &&
                dayDate.getFullYear() === today.getFullYear()
            ) {
                dayEl.classList.add('today');
            }

            // Check for appointments on this date
            const dayAppointments = appointments.filter(function(appointment) {
                return appointment.appointment_date === dayDate.toISOString().split('T')[0];
            });

            if (dayAppointments.length > 0) {
                const taskCount = document.createElement('span');
                taskCount.classList.add('task-count');
                taskCount.textContent = dayAppointments.length + ' appointment(s)';
                dayEl.appendChild(taskCount);

                // Click event to show appointments
                dayEl.addEventListener('click', function() {
                    selectedDate = dayDate;
                    displayAppointments(dayAppointments);

                    // Highlight selected day
                    document.querySelectorAll('.calendar-day').forEach(function(el) {
                        el.classList.remove('selected');
                    });
                    dayEl.classList.add('selected');
                });
            }

            calendarDays.appendChild(dayEl);
        }

        // Next Month's Days
        const nextDays = 42 - calendarDays.children.length; // Total of 6 weeks displayed
        for (let j = 1; j <= nextDays; j++) {
            const dayEl = document.createElement('div');
            dayEl.classList.add('calendar-day', 'inactive');
            dayEl.textContent = j;
            calendarDays.appendChild(dayEl);
        }
    };

    const displayAppointments = (dayAppointments) => {
        appointmentDetailsEl.innerHTML = '<h2>Appointments on ' + selectedDate.toDateString() + '</h2>';
        dayAppointments.forEach(function(appointment) {
            const appointmentEl = document.createElement('div');
            appointmentEl.classList.add('appointment-item');
            appointmentEl.innerHTML = `
                <h3>Appointment with Dr. ${appointment.doctor_first_name} ${appointment.doctor_last_name}</h3>
                <p><strong>Time:</strong> ${appointment.appointment_time}</p>
                <p><strong>Status:</strong> ${appointment.appointment_status}</p>
                <p><strong>Specialization:</strong> ${appointment.specialization || 'General'}</p>
                <p><strong>Reason:</strong> ${appointment.reason_for_visit}</p>
            `;
            appointmentDetailsEl.appendChild(appointmentEl);
        });
    };

    // Navigation Buttons
    document.querySelector('.prev-month').addEventListener('click', () => {
        date.setMonth(date.getMonth() - 1);
        renderCalendar();
    });

    document.querySelector('.next-month').addEventListener('click', () => {
        date.setMonth(date.getMonth() + 1);
        renderCalendar();
    });

    renderCalendar();
});
</script>
