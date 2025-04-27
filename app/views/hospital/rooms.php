<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Rooms</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" id="searchInput" placeholder="Search requests...">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <section class="requests-section">
        <div class="section-header">
            <h2>Rooms</h2>
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

        <!-- Calendar fetch data from DB -->
        <?php
        $treatmentRequestsJsFormat = [];
        foreach ($requests as $req) {
            $date = date('Y-m-d', strtotime($req['preferred_date']));
            if (!isset($treatmentRequestsJsFormat[$date])) {
                $treatmentRequestsJsFormat[$date] = [];
            }
            $treatmentRequestsJsFormat[$date][] = [
                'patient' => $req['first_name'] . " " . $req['last_name'],
                'treatment' => $req['treatment_type'], // make sure treatment_type is selected in your DB query
                'status' => $req['request_status'] // or any other time field you prefer
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