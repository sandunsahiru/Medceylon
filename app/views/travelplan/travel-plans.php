<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travel-plan.css">


<h2>Your Travel Plans</h2>
<div class="travelplans-container">
<?php if (!empty($travelPlans)): ?> 
    <?php foreach ($travelPlans as $travelPlan): ?>
        <div class="travelplans-item">
            <div class="left-info">

                <h2 class="destination-name"><?= htmlspecialchars($travelPlan['destination_name']) ?></h2>
                <p class="province"><?= htmlspecialchars($travelPlan['province']) ?></p>
            </div>
            <img src="<?= 'http://localhost/Medceylon/public/assets/' . htmlspecialchars($travelPlan['image_path'] ?? 'default.jpg') ?>" 
                 alt="<?= htmlspecialchars($travelPlan['destination_name'] ?? 'Unknown') ?>" 
                class="destination-image" />
            <div class="right-info">
                <p><strong>Stay Duration:</strong> <?= htmlspecialchars($travelPlan['stay_duration']) ?> days</p>
                <p><strong>Check-In:</strong> <?= htmlspecialchars($travelPlan['check_in']) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($travelPlan['check_out']) ?></p>
                <div class="action-buttons">
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
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No Travel Plans available. Please check back later.</p>
<?php endif; ?>
</div>

<!-- Include Edit and Delete Modals -->
<?php include('edit-plan.php'); ?>
<?php include('delete-destination.php'); ?>

<script src="<?php echo $basePath; ?>/public/assets/js/travel.js"></script>

<!-- footer -->
<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?> 


    
