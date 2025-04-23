<?php include_once 'partials/header.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Treatment Requests</h1>
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
                    <h2>All Treatment Requests</h2>
                    <div class="filter-options">
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="requests-list">
                    <?php while ($request = $requests->fetch_assoc()): ?>
                        <div class="request-card" data-status="<?php echo strtolower($request['request_status']); ?>">
                            <div class="request-info">
                                <h3><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></h3>
                                <p class="treatment-type">
                                    <i class="ri-medicine-bottle-line"></i>
                                    <?php echo htmlspecialchars($request['treatment_type']); ?>
                                </p>
                                <p class="doctor-preference">
                                    <i class="ri-user-star-line"></i>
                                    Dr. <?php echo htmlspecialchars($request['doctor_preference']); ?>
                                </p>
                                <p class="preferred-date">
                                    <i class="ri-calendar-check-line"></i>
                                    <?php echo date('d/m/Y', strtotime($request['preferred_date'])); ?>
                                </p>
                            </div>

                            <div class="request-status <?php echo strtolower($request['request_status']); ?>">
                                <?php echo $request['request_status']; ?>
                            </div>

                            <div class="request-details">
                                
                                <?php if ($request['special_requirements']): ?>
                                    <p class="requirements" title="<?php echo htmlspecialchars($request['special_requirements']); ?>">
                                        <i class="ri-file-list-3-line"></i>
                                        Special Requirements
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="request-actions">
                                <button class="action-btn view-btn" data-id="<?php echo $request['request_id']; ?>" 
                                        title="View Details">
                                    <i class="ri-eye-line"></i>
                                    View
                                </button>
                                <button class="action-btn respond-btn" data-id="<?php echo $request['request_id']; ?>"
                                        title="Respond">
                                    <i class="ri-message-2-line"></i>
                                    Respond
                                </button>
                                <?php if ($request['request_status'] === 'Pending'): ?>
                                    <button class="action-btn approve-btn" data-id="<?php echo $request['request_id']; ?>"
                                            title="Approve Request">
                                        <i class="ri-check-line"></i>
                                        Approve
                                    </button>
                                    <button class="action-btn reject-btn" data-id="<?php echo $request['request_id']; ?>"
                                            title="Reject Request">
                                            <i class="ri-close-line"></i>
                                        Reject
                                    </button>
                                <?php endif; ?>
                                <?php if ($request['request_status'] === 'Approved'): ?>
                                    <button class="action-btn complete-btn" data-id="<?php echo $request['request_id']; ?>"
                                            title="Approve Request">
                                            <i class="ri-star-line"></i>
                                        Complete
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>
    </div>

    <div id="viewDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h1>Request Details</h1>  
            </div>
            <div class="modal-body">
                <div class="details-section">
                    <h3>Patient Information</h3>
                    <p><strong>Name:</strong> <span id="patientName"></span></p>
                    <p><strong>Email:</strong> <span id="patientEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="patientPhone"></span></p>
                </div>
                <div class="details-section">
                    <h3>Treatment Information</h3>
                    <p><strong>Treatment Type:</strong> <span id="treatmentType"></span></p>
                    <p><strong>Doctor Preference:</strong> <span id="doctorPreference"></span></p>
                    <p><strong>Preferred Date:</strong> <span id="preferredDate"></span></p>
                </div>
                <div class="details-section">
                    <h3>Additional Information</h3>
                    <p><strong>Estimated Cost:</strong> $<span id="estimatedCost"></span></p>
                    <p><strong>Special Requirements:</strong> <span id="specialRequirements"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="close-btn" onclick="closeViewDetailsModal()">Close</button>
            </div>
        </div>
    </div> 

    <!-- Response Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close-btn">&times;</button>
                <h2>Respond to Treatment Request</h2>
            </div>
            <form id="responseForm">
                <input type="hidden" name="request_id" id="request_id">
                <div class="form-group">
                    <label for="estimated_cost">Estimated Cost ($)</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" 
                           min="0" step="100" required>
                </div>
                <div class="form-group">
                    <label for="response_message">Response Message</label>
                    <textarea id="response_message" name="response_message" 
                            rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="additional_requirements">Additional Requirements</label>
                    <textarea id="additional_requirements" name="additional_requirements" 
                            rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        <i class="ri-send-plane-line"></i>
                        Send Response
                    </button>
                    <button type="button" class="cancel-btn" onclick="closeModal()">
                        <i class="ri-close-line"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const basePath = "http://localhost/MedCeylon";
        const csrfToken = "<?php echo $this->session->getCSRFToken(); ?>";

        document.addEventListener('DOMContentLoaded', function() {
            // Search Functionality
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const requestCards = document.querySelectorAll('.request-card');

            function filterRequests() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusTerm = statusFilter.value.toLowerCase();

                requestCards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    const status = card.dataset.status;
                    const matchesSearch = text.includes(searchTerm);
                    const matchesStatus = !statusTerm || status === statusTerm;

                    card.style.display = matchesSearch && matchesStatus ? 'flex' : 'none';
                });
            }

            searchInput.addEventListener('input', filterRequests);
            statusFilter.addEventListener('change', filterRequests);

            // Modal Handling
            const modal = document.getElementById('responseModal');
            const closeBtn = modal.querySelector('.close-btn');
            const viewDetailsModal = document.getElementById('viewDetailsModal');

            // View button handler
            document.querySelectorAll('.action-btn.view-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    try {
                        showLoadingSpinner();
                        const response = await fetch(`${basePath}/hospital/get-request-details?id=${requestId}`);
                        hideLoadingSpinner();
                        
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const data = await response.json();
                        
                        // Populate the view details modal
                        document.getElementById('patientName').textContent = data.patient_name || 'N/A';
                        document.getElementById('patientEmail').textContent = data.patient_email || 'N/A';
                        document.getElementById('patientPhone').textContent = data.patient_phone || 'N/A';
                        document.getElementById('treatmentType').textContent = data.treatment_type || 'N/A';
                        document.getElementById('doctorPreference').textContent = data.doctor_preference || 'N/A';
                        document.getElementById('preferredDate').textContent = data.preferred_date || 'N/A';
                        document.getElementById('estimatedCost').textContent = data.estimated_cost || 'N/A';
                        document.getElementById('specialRequirements').textContent = data.special_requirements || 'N/A';
                        
                        // Show the view details modal
                        viewDetailsModal.classList.add('show');
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('Error loading request details', 'error');
                    }
                });
            });

            // Response button handler
            document.querySelectorAll('.action-btn.respond-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const requestId = this.dataset.id;
                    document.getElementById('request_id').value = requestId;
                    modal.classList.add('show');
                });
            });

            // Close modal buttons
            closeBtn.onclick = () => modal.classList.remove('show');
            window.onclick = (e) => {
                if (e.target === modal) modal.classList.remove('show');
                if (e.target === viewDetailsModal) viewDetailsModal.classList.remove('show');
            }

            // Response form submission
            document.getElementById('responseForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                try {
                    const response = await fetch(`${basePath}/hospital/process-response`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.text())
                    .then(text => {
                    console.log('RAW response:', text);
                    try {
                        const json = JSON.parse(text);
                        console.log('Parsed JSON:', json);
                    } catch (err) {
                        console.error('Failed to parse JSON:', err);
                    }
                    });

            // Handle approve, reject, and complete buttons
            document.querySelectorAll('.action-btn.approve-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to approve this request?')) {
                        try {
                            showLoadingSpinner();
                            const response = await fetch(`${basePath}/hospital/approve-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}&csrf_token=${csrfToken}`
                            });
                            hideLoadingSpinner();
                            
                            if (!response.ok) {
                                const errorData = await response.json();
                                console.error("Server error:", errorData);
                                showToast("Server error: " + (errorData.error || "Unknown error"), 'error');
                                return;
                            }

                            const data = await response.json();
                            if (data.success) {
                                showToast('Request approved successfully', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showToast(data.error || 'An error occurred', 'error');
                            }
                        } catch (error) {
                            hideLoadingSpinner();
                            console.error('Error:', error);
                            showToast('Error approving request', 'error');
                        }
                    }
                });
            });

            document.querySelectorAll('.action-btn.reject-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to reject this request?')) {
                        try {
                            showLoadingSpinner();
                            const response = await fetch(`${basePath}/hospital/reject-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}&csrf_token=${csrfToken}`
                            });
                            hideLoadingSpinner();
                            
                            const data = await response.json();
                            if (data.success) {
                                showToast('Request rejected successfully', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showToast(data.error || 'An error occurred', 'error');
                            }
                        } catch (error) {
                            hideLoadingSpinner();
                            console.error('Error:', error);
                            showToast('Error rejecting request', 'error');
                        }
                    }
                });
            });

            document.querySelectorAll('.action-btn.complete-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to mark this request as completed?')) {
                        try {
                            showLoadingSpinner();
                            const response = await fetch(`${basePath}/hospital/complete-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}&csrf_token=${csrfToken}`
                            });
                            hideLoadingSpinner();
                            
                            const data = await response.json();
                            if (data.success) {
                                showToast('Request marked as completed', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showToast(data.error || 'An error occurred', 'error');
                            }
                        } catch (error) {
                            hideLoadingSpinner();
                            console.error('Error:', error);
                            showToast('Error completing request', 'error');
                        }
                    }
                });
            });
        });

        // Utility functions
        function closeModal() {
            const modal = document.getElementById('responseModal');
            modal.classList.remove('show');
        }

        function closeViewDetailsModal() {
            const modal = document.getElementById('viewDetailsModal');
            modal.classList.remove('show');
        }

        function showLoadingSpinner() {
            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            document.body.appendChild(spinner);
        }

        function hideLoadingSpinner() {
            const spinner = document.querySelector('.loading-spinner');
            if (spinner) spinner.remove();
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            
            const container = document.querySelector('.toast-container') || createToastContainer();
            container.appendChild(toast);
            
            requestAnimationFrame(() => toast.classList.add('show'));
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }
    </script>
</body>
</html>