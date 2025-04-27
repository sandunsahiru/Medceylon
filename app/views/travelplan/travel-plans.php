<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travel-plan.css">

<h2>Your Travel Plans</h2>
<div class="travelplans-container">
<?php if (!empty($travelPlans)): ?> 
    <?php foreach ($travelPlans as $travelPlan): ?>
        <div class="travelplans-item <?= strtolower(htmlspecialchars($travelPlan['status'])) ?>">
            <div class="left-1">
                <h2 class="destination-name"><?= htmlspecialchars($travelPlan['destination_name']) ?></h2>
                <p class="province"><?= htmlspecialchars($travelPlan['province_name']) ?></p>
                
                <?php if ($travelPlan['status'] === 'Completed'): ?>
                    <div class="rating-display">
                        <?php if (isset($travelPlan['rating'])): ?>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $travelPlan['rating'] ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-rating">Not rated yet</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="left-2">
            <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($travelPlan['image_path'] ?? 'default.jpg') ?>" 
                 alt="<?= htmlspecialchars($travelPlan['destination_name'] ?? 'Unknown') ?>" 
                class="destination-image" />
            </div>
                
            <div class="right-1">
                <p><strong>Stay Duration:</strong> <?= htmlspecialchars($travelPlan['stay_duration']) ?> days</p>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($travelPlan['check_in']) ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($travelPlan['check_out']) ?></p>
                <p class="status <?= strtolower(htmlspecialchars($travelPlan['status'])) ?>"><?= htmlspecialchars($travelPlan['status']) ?></p>
            </div>
            <div class="right-2">    
                <div class="action-buttons">
                    <?php if ($travelPlan['status'] === 'Pending'): ?>
                        <button 
                            type="button" 
                            class="edit-button" 
                            data-plan-id="<?= htmlspecialchars($travelPlan['destination_id']) ?>"
                            data-plan-travelid="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>"
                            data-plan-name="<?= htmlspecialchars($travelPlan['destination_name']) ?>"
                            data-plan-checkin="<?= htmlspecialchars($travelPlan['check_in']) ?>"
                            data-plan-checkout="<?= htmlspecialchars($travelPlan['check_out']) ?>"
                            data-plan-image="<?= htmlspecialchars($travelPlan['image_path']) ?>"
                        >Edit</button>
                        <button 
                            type="button" 
                            class="delete-button" 
                            data-plan-travelid="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>"
                        >Delete</button>
                    <?php elseif ($travelPlan['status'] === 'Ongoing'): ?>
                        <button 
                            type="button" 
                            class="mark-completed-button" 
                            data-plan-travelid="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>"
                        >Mark as Completed</button>
                    <?php elseif ($travelPlan['status'] === 'Completed'): ?>
                        <button 
                            type="button" 
                            class="add-memories-button" 
                            data-plan-travelid="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>"
                            data-plan-name="<?= htmlspecialchars($travelPlan['destination_name']) ?>"
                        >Add Memories</button>
                        <?php if (!isset($travelPlan['memories'])): ?>
                            <span class="memories-indicator">No memories added yet</span>
                        <?php else: ?>
                            <button 
                                type="button" 
                                class="view-memories-button" 
                                data-plan-travelid="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>"
                            >View Memories</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No Travel Plans available. Please check back later.</p>
<?php endif; ?>
</div>

<!-- Include Modals -->
<?php include('edit-plan.php'); ?>
<?php include('delete-destination.php'); ?>

<script src="<?php echo $basePath; ?>/public/assets/js/travel.js"></script>

<!-- footer -->
<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>