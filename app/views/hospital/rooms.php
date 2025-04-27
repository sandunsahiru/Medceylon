<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Theatres and Beds</h1>
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

    <!-- New Section: Availability Overview -->
    <section class="availability-section">
        <div class="section-header">
            <h2>Current Availability</h2>
        </div>
        <div class="availability-cards">
            <div class="availability-card">
                <h3>Theatres</h3>
                <div class="availability-count" id="theatres-available">
                    <span class="count">
                        <?php 
                        // Fetch theatre availability from database
                        // This is a placeholder - replace with actual DB query
                        $total_theatres = 10; // Total number of theatres
                        $booked_theatres = 3; // Number of booked theatres for today
                        $available_theatres = $total_theatres - $booked_theatres;
                        echo $available_theatres;
                        ?>
                    </span>
                    <span class="total">/ <?php echo $total_theatres; ?></span>
                </div>
                <p>Available Today</p>
            </div>
            <div class="availability-card">
                <h3>Beds</h3>
                <div class="availability-count" id="beds-available">
                    <span class="count">
                        <?php 
                        // Fetch bed availability from database
                        // This is a placeholder - replace with actual DB query
                        $total_beds = 50; // Total number of beds
                        $occupied_beds = 32; // Number of occupied beds
                        $available_beds = $total_beds - $occupied_beds;
                        echo $available_beds;
                        ?>
                    </span>
                    <span class="total">/ <?php echo $total_beds; ?></span>
                </div>
                <p>Available Today</p>
            </div>
        </div>
    </section>

    <section class="requests-section">
        <div class="section-header">
            <h2>Approved Theatre Bookings</h2>
        </div>
        <div class="calendar-container">
            <!-- Calendar -->
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

        // Create array for daily availability
        $availabilityData = [];
        // This is a placeholder - replace with actual DB query for daily availability
        // Example of what the data might look like:
        for ($i = -10; $i < 30; $i++) {
            $dayDate = date('Y-m-d', strtotime("+$i days"));
            $availabilityData[$dayDate] = [
                'theatres_total' => $total_theatres,
                'theatres_available' => rand(0, $total_theatres), // Randomized for example
                'beds_total' => $total_beds,
                'beds_available' => rand(0, $total_beds) // Randomized for example
            ];
        }
        ?>

        <script>
            const monthYear = document.getElementById("monthYear");
            const daysContainer = document.getElementById("days");
            const appointmentsContainer = document.getElementById("appointments");
            let date = new Date();

            const treatmentRequests = <?= json_encode($treatmentRequestsJsFormat) ?>;
            const availabilityData = <?= json_encode($availabilityData) ?>;

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

            // Calendar parse data to view
            function showTreatmentRequests(day) {
                let availabilityHTML = '';
                if (availabilityData[day]) {
                    const availability = availabilityData[day];
                    availabilityHTML = `
                        <div class="day-availability">
                            <div class="availability-item">
                                <strong>Theatres:</strong> ${availability.theatres_available} available (of ${availability.theatres_total})
                            </div>
                            <div class="availability-item">
                                <strong>Beds:</strong> ${availability.beds_available} available (of ${availability.beds_total})
                            </div>
                        </div>
                    `;
                }

                if (treatmentRequests[day]) {
                    appointmentsContainer.innerHTML = `
                        <h3>Date: ${day}</h3>
                        ${availabilityHTML}
                        <h4>Theatre Bookings</h4>
                        ${treatmentRequests[day].map(req => `
                            <div class='appointment-card'>
                                <div><strong>Patient:</strong> ${req.patient}<br></div>
                                <div><strong>Treatment:</strong> ${req.treatment}<br></div>
                                <div><strong>Status:</strong> ${req.status}</div>
                            </div>
                        `).join('')}
                    `;
                    appointmentsContainer.style.display = "block";
                } else {
                    appointmentsContainer.innerHTML = `
                        <h3>Date: ${day}</h3>
                        ${availabilityHTML}
                        <p>No treatment requests for this day.</p>
                    `;
                    appointmentsContainer.style.display = "block";
                }
            }

            // Update availability when selecting today's date
            function updateTodayAvailability() {
                const today = new Date();
                const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
                if (availabilityData[todayStr]) {
                    // You could update the counters if needed based on the selected date
                }
            }

            renderCalendar();
            updateTodayAvailability();
        </script>