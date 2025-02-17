<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Patients</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search patients..." id="searchInput">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-user-heart-line"></i>
                <div class="stats-info">
                    <h3>Total Patients</h3>
                    <p><?php echo $stats['total_patients'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-calendar-check-line"></i>
                <div class="stats-info">
                    <h3>Completed Visits</h3>
                    <p><?php echo $stats['completed_visits'] ?? 0; ?></p>
                </div>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-calendar-todo-line"></i>
                <div class="stats-info">
                    <h3>Upcoming</h3>
                    <p><?php echo $stats['upcoming_appointments'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients List Section -->
    <section class="patients-wrapper">
        <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $patient): ?>
                <div class="patient-row" data-patient-name="<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>">
                    <div class="patient-info">
                        <div class="avatar">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="info-details">
                            <h3><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h3>
                            <span><?php echo htmlspecialchars($patient['email']); ?></span>
                        </div>
                    </div>

                    <div class="patient-stats">
                        <div class="stat-item">
                            <i class="ri-phone-line"></i>
                            <span><?php echo htmlspecialchars($patient['phone_number'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="stat-item">
                            <i class="ri-calendar-check-line"></i>
                            <span><?php echo $patient['total_visits']; ?> Schedule/s</span>
                        </div>
                        <div class="stat-item">
                            <i class="ri-time-line"></i>
                            <span>Last: <?php echo date('d/m/Y', strtotime($patient['last_visit'])); ?></span>
                        </div>
                    </div>

                    <div class="row-actions">
                        <button class="view-btn" onclick="viewPatientHistory(<?php echo $patient['user_id']; ?>)">
                            <i class="ri-calendar-line"></i>
                            History
                        </button>
                        <button class="view-btn" onclick="viewMedicalReports(<?php echo $patient['user_id']; ?>)">
                            <i class="ri-file-list-3-line"></i>
                            Medical Reports
                        </button>
                    </div>

                    
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No patients found.</p>
                </div>
            <?php endif; ?>
    </section>
    <div id="medicalReportsModal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 90%; max-width: 900px; height: 80vh;">
        <div class="modal-header">
            <h2>Medical Reports</h2>
            <button onclick="closeMedicalReportsModal()" class="close-btn">&times;</button>
        </div>
        <div id="medicalReportsContent" class="modal-body">
            <!-- Reports will be loaded here dynamically -->
        </div>
    </div>
</div>


</main>
</div>

<!-- History Modal -->
<div id="historyModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Patient History</h2>
            <button onclick="closeHistoryModal()" class="close-btn">&times;</button>
        </div>
        <div id="historyContent"></div>
    </div>
</div>

<!-- Appointment History Modal -->
<div id="scheduleModal" class="modal" style="display: none;">
    <div class="modal-content schedule-modal">
        <div class="modal-header">
            <h2>Appointment History</h2>
            <button onclick="closeScheduleModal()" class="close-btn">&times;</button>
        </div>
        <div id="appointmentContent" class="appointment-list"></div>
    </div>
</div>

<script>

const basePath = '<?php echo $basePath; ?>';
function viewMedicalReports(patientId) {
    const modalContent = document.getElementById('medicalReportsContent');
    modalContent.innerHTML = '<div class="loading-spinner">Loading medical reports...</div>';
    document.getElementById('medicalReportsModal').style.display = 'block';

    // Log the URL being called for debugging
    console.log(`Making request to: ${basePath}/doctor/getPatientMedicalReports?patient_id=${patientId}`);

    fetch(`${basePath}/doctor/getPatientMedicalReports?patient_id=${patientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.error) {
                throw new Error(data.error);
            }

            if (!Array.isArray(data) || data.length === 0) {
                modalContent.innerHTML = `
                    <div class="no-data">
                        <i class="ri-file-list-3-line"></i>
                        <p>No medical reports found for this patient.</p>
                    </div>`;
                return;
            }

            const reportsHtml = data.map(report => `
                <div class="report-item">
                    <div class="report-info">
                        <h4>${report.report_name || 'Medical Report'}</h4>
                        <p><strong>Type:</strong> ${report.report_type || 'General'}</p>
                        <p><strong>Upload Date:</strong> ${report.upload_date}</p>
                        ${report.description ? `<p><strong>Description:</strong> ${report.description}</p>` : ''}
                    </div>
                    <div class="report-actions">
                        ${report.file_path ? `
                            <a href="${basePath}/${report.file_path}" target="_blank" class="view-btn">
                                <i class="ri-eye-line"></i>
                                View Report
                            </a>
                        ` : '<span class="text-muted">No file attached</span>'}
                    </div>
                </div>
            `).join('');

            modalContent.innerHTML = reportsHtml;
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            modalContent.innerHTML = `
                <div class="error-message">
                    <i class="ri-error-warning-line"></i>
                    <p>Error loading medical reports: ${error.message}</p>
                    <p>Please try again or contact support if the problem persists.</p>
                </div>
            `;
        });
}

    function closeMedicalReportsModal() {
        document.getElementById('medicalReportsModal').style.display = 'none';
    }

    // Update the window.onclick event handler
    window.onclick = function(event) {
        const historyModal = document.getElementById('historyModal');
        const scheduleModal = document.getElementById('scheduleModal');
        const medicalReportsModal = document.getElementById('medicalReportsModal');

        if (event.target === historyModal) {
            historyModal.style.display = 'none';
        }
        if (event.target === scheduleModal) {
            scheduleModal.style.display = 'none';
        }
        if (event.target === medicalReportsModal) {
            medicalReportsModal.style.display = 'none';
        }
    }
    // Search Functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.patient-row').forEach(row => {
            const patientName = row.dataset.patientName.toLowerCase();
            row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
        });
    });

    // Patient History Functions
    function viewPatientHistory(patientId) {
        fetch(`${basePath}/doctor/get-patient-appointments?patient_id=${patientId}&doctor_id=<?php echo $doctorId; ?>`)
            .then(response => response.json())
            .then(data => {
                const appointmentContent = document.getElementById('appointmentContent');
                appointmentContent.innerHTML = '';

                if (data.length === 0) {
                    appointmentContent.innerHTML = '<p class="no-data">No appointment history found.</p>';
                } else {
                    data.forEach(appointment => {
                        const statusClass = `status-${appointment.appointment_status.toLowerCase()}`;
                        appointmentContent.innerHTML += `
                                <div class="appointment-item">
                                    <div class="appointment-header">
                                        <span class="appointment-date">
                                            ${appointment.appointment_date} at ${appointment.appointment_time}
                                        </span>
                                        <span class="appointment-status ${statusClass}">
                                            ${appointment.appointment_status}
                                        </span>
                                    </div>
                                    <div class="appointment-details">
                                        <p>Type: ${appointment.consultation_type}</p>
                                        <p>Reason: ${appointment.reason_for_visit || 'Not specified'}</p>
                                    </div>
                                </div>
                            `;
                    });
                }

                document.getElementById('scheduleModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error fetching patient history');
            });

        // Also fetch medical history if available
        fetch(`${basePath}/doctor/get-patient-history?patient_id=${patientId}&doctor_id=<?php echo $doctorId; ?>`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const historyContent = document.getElementById('historyContent');
                    historyContent.innerHTML = data.map(record => `
                            <div class="history-record">
                                <div class="record-date">${record.appointment_date}</div>
                                <div class="record-details">
                                    <p><strong>Diagnosis:</strong> ${record.diagnosis || 'None recorded'}</p>
                                    <p><strong>Treatment Plan:</strong> ${record.treatment_plan || 'None recorded'}</p>
                                </div>
                            </div>
                        `).join('');
                    document.getElementById('historyModal').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').style.display = 'none';
    }

    function closeScheduleModal() {
        document.getElementById('scheduleModal').style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const historyModal = document.getElementById('historyModal');
        const scheduleModal = document.getElementById('scheduleModal');
        if (event.target === historyModal) {
            historyModal.style.display = 'none';
        }
        if (event.target === scheduleModal) {
            scheduleModal.style.display = 'none';
        }
    }
</script>

</body>

</html>