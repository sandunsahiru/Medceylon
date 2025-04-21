<?php include_once 'partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Patients</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" id="searchInput" placeholder="Search patients...">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <section class="patients-section">
                <div class="section-header">
                    <h2>All Patients</h2>
                    <div class="filter-options">
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="patients-list">
                    <?php while ($patient = $patients->fetch_assoc()): ?>
                        <div class="patient-card" data-status="<?php echo $patient['is_active'] ? 'active' : 'inactive'; ?>">
                            <div class="patient-info">
                                <h3><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h3>
                                <p>
                                    <i class="ri-mail-line"></i>
                                    <?php echo htmlspecialchars($patient['email']); ?>
                                </p>
                                <?php if (!empty($patient['phone_number'])): ?>
                                    <p>
                                        <i class="ri-phone-line"></i>
                                        <?php echo htmlspecialchars($patient['phone_number']); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($patient['date_of_birth'])): ?>
                                    <p>
                                        <i class="ri-calendar-line"></i>
                                        <?php echo date('d/m/Y', strtotime($patient['date_of_birth'])); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($patient['city_name'])): ?>
                                    <p>
                                        <i class="ri-map-pin-line"></i>
                                        <?php echo htmlspecialchars($patient['city_name']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="patient-actions">
                                <button class="action-btn view-btn" data-id="<?php echo $patient['user_id']; ?>" 
                                        title="View Details">
                                    <i class="ri-eye-line"></i>
                                    View Details
                                </button>
                                <button class="action-btn history-btn" data-id="<?php echo $patient['user_id']; ?>"
                                        title="Medical History">
                                    <i class="ri-file-list-line"></i>
                                    Medical History
                                </button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Patient Details Modal -->
    <div id="patientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Patient Details</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div id="patientDetails" class="patient-details">
                <!-- Patient details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Medical History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Medical History</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div id="medicalHistory" class="medical-history">
                <!-- Medical history will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';
        document.addEventListener('DOMContentLoaded', function() {
            // Search and Filter Functionality
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const patientCards = document.querySelectorAll('.patient-card');

            function filterPatients() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusTerm = statusFilter.value.toLowerCase();

                patientCards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    const status = card.dataset.status;
                    const matchesSearch = text.includes(searchTerm);
                    const matchesStatus = !statusTerm || status === statusTerm;

                    card.style.display = matchesSearch && matchesStatus ? 'flex' : 'none';
                });
            }

            searchInput.addEventListener('input', filterPatients);
            statusFilter.addEventListener('change', filterPatients);

            // Modal Handling
            const patientModal = document.getElementById('patientModal');
            const historyModal = document.getElementById('historyModal');
            const closeBtns = document.querySelectorAll('.close-btn');

            // View Patient Details
            document.querySelectorAll('.action-btn.view-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const patientId = this.dataset.id;
                    try {
                        const response = await fetch(`${basePath}/hospital/get-patient-details?id=${patientId}`);
                        const data = await response.json();
                        
                        document.getElementById('patientDetails').innerHTML = `
                            <div class="detail-section">
                                <h3>Personal Information</h3>
                                <p><strong>Name:</strong> ${data.first_name} ${data.last_name}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Phone:</strong> ${data.phone_number || 'Not provided'}</p>
                                <p><strong>Date of Birth:</strong> ${data.date_of_birth ? new Date(data.date_of_birth).toLocaleDateString() : 'Not provided'}</p>
                            </div>
                            <div class="detail-section">
                                <h3>Address Information</h3>
                                <p><strong>Address:</strong> ${data.address_line1 || 'Not provided'}</p>
                                <p><strong>City:</strong> ${data.city_name || 'Not provided'}</p>
                                <p><strong>Country:</strong> ${data.country_name || 'Not provided'}</p>
                            </div>
                        `;
                        patientModal.style.display = 'flex';
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });

            // View Medical History
            document.querySelectorAll('.action-btn.history-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const patientId = this.dataset.id;
                    try {
                        const response = await fetch(`${basePath}/hospital/get-medical-history?id=${patientId}`);
                        const data = await response.json();
                        
                        let historyHtml = '<div class="history-timeline">';
                        if (data.length === 0) {
                            historyHtml += '<p>No medical history records found.</p>';
                        } else {
                            data.forEach(record => {
                                historyHtml += `
                                    <div class="history-item">
                                        <div class="history-date">${new Date(record.appointment_date).toLocaleDateString()}</div>
                                        <div class="history-content">
                                            <h4>${record.treatment_type}</h4>
                                            <p><strong>Doctor:</strong> Dr. ${record.doctor_name}</p>
                                            <p><strong>Diagnosis:</strong> ${record.diagnosis || 'Not provided'}</p>
                                            <p><strong>Treatment:</strong> ${record.treatment_plan || 'Not provided'}</p>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        historyHtml += '</div>';
                        
                        document.getElementById('medicalHistory').innerHTML = historyHtml;
                        historyModal.style.display = 'flex';
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Close Modal Functionality
            closeBtns.forEach(btn => {
                btn.onclick = function() {
                    patientModal.style.display = 'none';
                    historyModal.style.display = 'none';
                }
            });

            window.onclick = function(event) {
                if (event.target === patientModal) {
                    patientModal.style.display = 'none';
                }
                if (event.target === historyModal) {
                    historyModal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>