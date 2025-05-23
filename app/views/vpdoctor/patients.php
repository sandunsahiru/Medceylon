<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

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
                            <p><?php echo $stats['total_patients']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-check-line"></i>
                        <div class="stats-info">
                            <h3>Completed Visits</h3>
                            <p><?php echo $stats['completed_visits']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="stats-content">
                        <i class="ri-calendar-todo-line"></i>
                        <div class="stats-info">
                            <h3>Upcoming</h3>
                            <p><?php echo $stats['upcoming_appointments']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patients List Section -->
            <section class="patients-wrapper">
                <?php if ($patients->num_rows > 0): ?>
                    <?php while ($patient = $patients->fetch_assoc()): ?>
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
                                <button class="view-btn" onclick="viewPatient(<?php echo $patient['user_id']; ?>)">
                                    <i class="ri-eye-line"></i>
                                    View
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-results">
                        <p>No patients found.</p>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Patient Details Modal -->
            <div id="patientModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Patient Details</h2>
            <button onclick="closePatientModal()" class="close-btn">&times;</button>
        </div>
        <div id="patientContent" class="patient-details">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<!-- Medical Reports Modal -->
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

    <script>
        const basePath = '<?php echo $basePath; ?>';
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            initializeModals();
        });

        function initializeSearch() {
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.patient-row').forEach(row => {
                    const patientName = row.dataset.patientName.toLowerCase();
                    row.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        }

        function initializeModals() {
            // Close modal when clicking outside
            window.onclick = function(event) {
        const patientModal = document.getElementById('patientModal');
        const medicalReportsModal = document.getElementById('medicalReportsModal');
        
        if (event.target === patientModal) {
            patientModal.style.display = 'none';
        }
        if (event.target === medicalReportsModal) {
            medicalReportsModal.style.display = 'none';
        }
    }
        }

        function viewPatient(patientId) {
    fetch(`${basePath}/vpdoctor/get-patient-details?patient_id=${patientId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            const patientContent = document.getElementById('patientContent');
            
            // Check if age can be calculated
            let age = '';
            if (data.user && data.user.date_of_birth) {
                age = calculateAge(data.user.date_of_birth);
                age = `${age} years old | `;
            }
            
            patientContent.innerHTML = `
                <div class="patient-profile">
                    <div class="profile-header">
                        <h3>${data.user.first_name} ${data.user.last_name}</h3>
                        <p class="patient-meta">
                            ${age}${data.user.gender || ''} ${data.user.nationality ? '| ' + data.user.nationality : ''}
                        </p>
                    </div>
                    
                    <div class="contact-info">
                        <div class="info-item">
                            <i class="ri-mail-line"></i>
                            <span>${data.user.email || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <i class="ri-phone-line"></i>
                            <span>${data.user.phone_number || 'N/A'}</span>
                        </div>
                        <div class="info-item">
                            <i class="ri-map-pin-line"></i>
                            <span>${data.user.address_line1 || 'N/A'} ${data.user.address_line2 || ''}</span>
                        </div>
                    </div>

                    <div class="patient-actions">
                        <button class="view-btn" onclick="viewMedicalReports(${patientId})">
                            <i class="ri-file-list-3-line"></i>
                            View Medical Reports
                        </button>
                    </div>

                    <div class="medical-history">
                        <h4>Medical History</h4>
                        ${data.appointments && data.appointments.length > 0 ? `
                            <div class="appointment-timeline">
                                ${data.appointments.map(app => `
                                    <div class="timeline-item">
                                        <div class="timeline-date">
                                            ${formatDate(app.appointment_date)}
                                        </div>
                                        <div class="timeline-content">
                                            <div class="appointment-header">
                                                <span class="appointment-type">${app.consultation_type || 'Consultation'}</span>
                                                <span class="appointment-status status-${app.appointment_status ? app.appointment_status.toLowerCase() : 'unknown'}">
                                                    ${app.appointment_status || 'Unknown'}
                                                </span>
                                            </div>
                                            <p class="appointment-reason">${app.reason_for_visit || 'No reason specified'}</p>
                                            <p class="appointment-notes">${app.notes || 'No notes available'}</p>
                                            ${app.medical_history ? `
                                                <div class="medical-details">
                                                    <p><strong>Medical History:</strong> ${app.medical_history}</p>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : '<p class="no-history">No appointment history available.</p>'}
                    </div>
                </div>
            `;

            document.getElementById('patientModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load patient details. Please try again.');
        });
}

    function viewMedicalReports(patientId) {
    const modalContent = document.getElementById('medicalReportsContent');
    modalContent.innerHTML = '<div class="loading-spinner">Loading medical reports...</div>';
    document.getElementById('medicalReportsModal').style.display = 'block';

    fetch(`${basePath}/vpdoctor/getPatientMedicalReports?patient_id=${patientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
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
                        <button onclick="window.open('${basePath}/uploads/medical-reports/${report.file_path}', '_blank')" class="view-btn">
                            <i class="ri-eye-line"></i> View
                        </button>
                        
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

// Add the delete function
function deleteReport(reportId) {
    if (confirm('Are you sure you want to delete this report?')) {
        fetch(`${basePath}/vpdoctor/deleteMedicalReport`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `report_id=${reportId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Get the current patient ID from the URL or store it in a variable when opening the modal
                const patientId = getCurrentPatientId(); // You'll need to implement this function
                viewMedicalReports(patientId); // Refresh the reports list
            } else {
                alert(data.error || 'Failed to delete report');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete report. Please try again.');
        });
    }
}


    function calculateAge(dateOfBirth) {
        const today = new Date();
        const birthDate = new Date(dateOfBirth);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

        function closeMedicalReportsModal() {
        document.getElementById('medicalReportsModal').style.display = 'none';
    }

    function closePatientModal() {
        document.getElementById('patientModal').style.display = 'none';
    }

    </script>
</body>
</html>