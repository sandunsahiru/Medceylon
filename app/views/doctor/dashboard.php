<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Dashboard</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search" id="searchInput">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stats-card patients-overview">
                    <div class="stats-header">
                        <h2><?php echo $stats['total_patients'] ?? 0; ?></h2>
                        <p>Patients</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-group-line"></i>
                        <div class="stats-info">
                            <h3>All Patients</h3>
                            <p><?php echo $stats['total_patients'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>All Appointments</h3>
                            <p><?php echo $stats['upcoming_appointments'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Section -->
            <section class="appointments-section">
                <h2>All Appointments</h2>
                <div class="appointments-list">
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <div class="appointment-card">
                                <div class="appointment-time">
                                    <?php echo date('H:i', strtotime($appointment['appointment_time'])); ?>
                                </div>
                                <div class="appointment-info">
                                    <h3><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></h3>
                                    <p><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></p>
                                </div>
                                <div class="appointment-actions">
                                    <button class="action-btn" onclick="viewAppointment(<?php echo $appointment['appointment_id']; ?>)">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button class="action-btn" onclick="showAppointmentDetails(<?php echo $appointment['appointment_id']; ?>)">
                                        <i class="ri-arrow-right-s-line"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-appointments">
                            <p>No appointments scheduled</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <button onclick="closeModal()" class="close-btn">&times;</button>
            </div>
            <div id="appointmentContent"></div>
        </div>
    </div>

    <script>
        function viewAppointment(appointmentId) {
            window.location.href = `${basePath}/doctor/appointments/view/${appointmentId}`;
        }

        function showAppointmentDetails(appointmentId) {
            fetch(`${basePath}/doctor/appointments/details/${appointmentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('appointmentContent').innerHTML = `
                        <div class="appointment-details">
                            <p><strong>Patient:</strong> ${data.patient_name}</p>
                            <p><strong>Date:</strong> ${data.date}</p>
                            <p><strong>Time:</strong> ${data.time}</p>
                            <p><strong>Type:</strong> ${data.consultation_type}</p>
                            <p><strong>Status:</strong> ${data.status}</p>
                            <p><strong>Reason:</strong> ${data.reason || 'Not specified'}</p>
                        </div>
                    `;
                    document.getElementById('appointmentModal').style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }

        function closeModal() {
            document.getElementById('appointmentModal').style.display = 'none';
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.appointment-card').forEach(card => {
                const patientName = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
            });
        });
    </script>

</body>
</html>