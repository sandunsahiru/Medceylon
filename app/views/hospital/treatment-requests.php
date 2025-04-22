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

    <!-- Response Modal -->
    <div id="responseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Respond to Treatment Request</h2>
                <button class="close-btn">&times;</button>
            </div>
            <form id="responseForm">
                <input type="hidden" name="request_id" id="request_id">
                <div class="form-group">
                    <label for="estimated_cost">Estimated Cost ($)</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" 
                           min="0" step="0.01" required>
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

            document.querySelectorAll('.action-btn.view-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    try {
                        const response = await fetch(`${basePath}/hospital/get-request-details?id=${requestId}`);
                        const data = await response.json();
                        // Handle view details
                        console.log(data);
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            });

            document.querySelectorAll('.action-btn.respond-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const requestId = this.dataset.id;
                    document.getElementById('request_id').value = requestId;
                    modal.style.display = 'flex';
                });
            });

            document.querySelectorAll('.action-btn.approve-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to approve this request?')) {
                        try {
                            const response = await fetch(`${basePath}/hospital/approve-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}`
                            });
                            const data = await response.json();
                            if (data.success) {
                                location.reload();
                            }
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }
                });
            });

            document.querySelectorAll('.action-btn.reject-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to reject this request?')) {
                        try {
                            const response = await fetch(`${basePath}/hospital/reject-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}`
                            });
                            const data = await response.json();
                            if (data.success) {
                                location.reload();
                            }
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }
                });
            });

            document.querySelectorAll('.action-btn.complete-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const requestId = this.dataset.id;
                    if (confirm('Are you sure you want to mark this request as completed?')) {
                        try {
                            const response = await fetch(`${basePath}/hospital/complete-request`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `request_id=${requestId}`
                            });
                            const data = await response.json();
                            if (data.success) {
                                location.reload();
                            }
                        } catch (error) {
                            console.error('Error:', error);
                        }
                    }
                });
            });

            // Response form submission
            document.getElementById('responseForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                try {
                    const response = await fetch(`${basePath}/hospital/process-response`, {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.success) {
                        modal.style.display = 'none';
                        location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });

            closeBtn.onclick = () => modal.style.display = 'none';
            window.onclick = (e) => {
                if (e.target === modal) modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>