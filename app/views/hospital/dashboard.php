<?php include_once 'partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <!-- Top Bar -->
    <header class="top-bar">
        <h1>Hospital Dashboard</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search requests, patients...">
            </div>
            <div class="date">
                <i class="ri-calendar-line"></i>
                <?php echo date('l, d.m.Y'); ?>
            </div>
        </div>
    </header>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stats-card primary">
            <div class="stats-header">
                <h2><?php echo $totalData['total']; ?></h2>
                <p>Total Requests</p>
            </div>
            <div class="stats-details">
                <div class="request-stat">
                    <i class="ri-loader-4-line"></i>
                    <span>Pending: <?php echo $totalData['pending']; ?></span>
                </div>
                <div class="request-stat">
                    <i class="ri-check-line"></i>
                    <span>Approved: <?php echo $totalData['approved']; ?></span>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-heart-pulse-line"></i>
                <div class="stats-info">
                    <h3>Active Treatments</h3>
                    <p><?php echo $totalData['approved']; ?></p>
                </div>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-content">
                <i class="ri-loader-4-line"></i>
                <div class="stats-info">
                    <h3>Pending</h3>
                    <p><?php echo $totalData['pending']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Treatment Requests Section -->
    <section class="requests-section">
        <div class="section-header">
            <h2>Recent Treatment Requests</h2>
            <a href="<?php echo $basePath; ?>/hospital/treatment-requests" class="view-all">View All</a>
        </div>

        <div class="requests-list-dashboard">
            <?php foreach ($requests as $request): ?>
                <div class="request-card">
                    <div class="request-status <?php echo strtolower($request['request_status']); ?>">
                        <?php echo $request['request_status']; ?>
                    </div>
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
                    <div class="request-details">
                        <?php if ($request['estimated_cost']): ?>
                            <p class="cost">
                                <i class="ri-money-dollar-circle-line"></i>
                                Est. Cost: $<?php echo number_format($request['estimated_cost'], 2); ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($request['special_requirements']): ?>
                            <p class="requirements" title="<?php echo htmlspecialchars($request['special_requirements']); ?>">
                                <i class="ri-file-list-3-line"></i>
                                Special Requirements
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="request-actions">
                        <button class="action-btn view-btn" data-id="<?php echo $request['request_id']; ?>" title="View Details">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="action-btn respond-btn" data-id="<?php echo $request['request_id']; ?>" title="Respond">
                            <i class="ri-message-2-line"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<!-- Response Modal -->
<div id="responseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Respond to Treatment Request</h2>
        </div>
        <form id="responseForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="request_id" id="request_id">

            <div class="form-group">
                <label for="estimated_cost">Estimated Cost ($)</label>
                <input type="number" id="estimated_cost" name="estimated_cost" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="response_message">Response Message</label>
                <textarea id="response_message" name="response_message" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="additional_requirements">Additional Requirements</label>
                <textarea id="additional_requirements" name="additional_requirements" rows="3"></textarea>
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

<script src="<?php echo $basePath; ?>/public/assets/js/hospital.js"></script>
</body>
</html>
