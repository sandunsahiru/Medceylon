<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Theatres and Beds Management</h1>
        <div class="header-right">
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <!-- New Section: Availability Overview -->
    <section class="availability-section">
        <div class="section-header">
            <h2>Current Availability</h2>
        </div>
        <div class="availability-cards">
            <div class="availability-card">
                <h3>Theatres</h3>
                <div class="availability-count" id="theatres-available">
                    <span class="count"><?= $total_theatres - $booked_theatres ?></span>
                    <span class="total">/ <?= $total_theatres ?></span>
                </div>
                <p>Available Today</p>
                <div class="availability-progress">
                    <div class="progress-bar" style="width: <?= ($total_theatres > 0) ? (($total_theatres - $booked_theatres) / $total_theatres * 100) : 0 ?>%"></div>
                </div>
            </div>
            <div class="availability-card">
                <h3>Beds</h3>
                <div class="availability-count" id="beds-available">
                    <span class="count"><?= $total_beds - $occupied_beds ?></span>
                    <span class="total">/ <?= $total_beds ?></span>
                </div>
                <p>Available Today</p>
                <div class="availability-progress">
                    <div class="progress-bar" style="width: <?= ($total_beds > 0) ? (($total_beds - $occupied_beds) / $total_beds * 100) : 0 ?>%"></div>
                </div>
            </div>
            <div class="availability-card upcoming-card">
                <h3>Upcoming Surgeries</h3>
                <div class="upcoming-count">
                    <span class="count"><?= htmlspecialchars($upcomingSurgeries ?? 0) ?></span>
                </div>
                <p>For the Next 7 Days</p>
            </div>
        </div>
    </section>

    <section class="requests-section">
        <div class="section-header">
            <h2>Approved Theatre Bookings</h2>
        </div>
        
        <div class="calendar-container">
            <!-- Calender -->
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

        </body>

        </html>

        
        <?php
        $treatmentRequestsJsFormat = [];
        foreach ($requests as $req) {
            $date = date('Y-m-d', strtotime($req['preferred_date']));
            if (!isset($treatmentRequestsJsFormat[$date])) {
                $treatmentRequestsJsFormat[$date] = [];
            }
            $treatmentRequestsJsFormat[$date][] = [
                'patient' => $req['first_name'] . " " . $req['last_name'],
                'treatment' => $req['treatment_type'],
                'status' => $req['request_status'] 
            ];
        }
        ?>


        <script>
            const monthYear = document.getElementById("monthYear");
            const daysContainer = document.getElementById("days");
            const appointmentsContainer = document.getElementById("appointments");
            let date = new Date();

            const treatmentRequests = <?= json_encode($treatmentRequestsJsFormat) ?>;

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
                    let requestDot = treatmentRequests[dayDate] ? "<div class='dot'></div>" : "";
                    daysContainer.innerHTML += `<div class='day' onclick='showTreatmentRequests("${dayDate}")'>${i}${requestDot}</div>`;
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

            // Calender parse data to view
            function showTreatmentRequests(day) {
                if (treatmentRequests[day]) {
                    appointmentsContainer.innerHTML = `<h3>Treatment Requests for ${day}</h3>` +
                        treatmentRequests[day].map(req => `
                        <div class='appointment-card'>
                            <div><strong>Patient:</strong> ${req.patient}<br></div>
                            <div><strong>Treatment:</strong> ${req.treatment}<br></div>
                            <div><strong>Status:</strong> ${req.status}</div>
                        </div>
                    `).join('');
                    appointmentsContainer.style.display = "block";
                } else {
                    appointmentsContainer.innerHTML = "<p>No treatment requests for this day.</p>";
                    appointmentsContainer.style.display = "block";
                }
            }

            renderCalendar();
        </script>
</main>