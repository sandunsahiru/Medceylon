<?php require APPROOT . '/views/includes/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/travel-plans.css">

<br>
<h2>Your Travel Plans</h2>
<div class="travelplans-container">
    <?php foreach ($travelPlans as $travelPlan): ?>
        <div class="travelplans-item">
            <div class="left-info">

                <h2 class="destination-name"><?= htmlspecialchars($travelPlan->destination_name) ?></h2>
                <p class="province"><?= htmlspecialchars($travelPlan->province) ?></p>
            </div>
            <img 
                src="<?= URLROOT . '/' .  htmlspecialchars($travelPlan->image_path) ?>" 
                alt="Image of <?= htmlspecialchars($travelPlan->destination_name) ?>" 
                class="destination-image"
            />
            <div class="right-info">
                <p><strong>Stay Duration:</strong> <?= htmlspecialchars($travelPlan->stay_duration) ?> days</p>
                <p><strong>Check-In:</strong> <?= htmlspecialchars($travelPlan->check_in) ?></p>
                <p><strong>Check-Out:</strong> <?= htmlspecialchars($travelPlan->check_out) ?></p>
                <div class="action-buttons">
                    <button 
                        type="button" 
                        class="edit-button" 
                        data-plan-id="<?= htmlspecialchars($travelPlan->destination_id) ?>"
                        data-plan-travelid="<?= htmlspecialchars($travelPlan->travel_plan_id) ?>"
                        data-plan-name="<?= htmlspecialchars($travelPlan->destination_name) ?>"
                        data-plan-checkin="<?= htmlspecialchars($travelPlan->check_in) ?>"
                        data-plan-checkout="<?= htmlspecialchars($travelPlan->check_out) ?>"
                        data-plan-image="<?= htmlspecialchars($travelPlan->image_path) ?>"
                    >Edit</button>
                    <button 
                        type="button" 
                        class="delete-button" 
                        data-plan-travelid="<?= htmlspecialchars($travelPlan->travel_plan_id) ?>"
                    >Delete</button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Include Edit and Delete Modals -->
<?php include('edit-plan.php'); ?>
<?php include('delete-destination.php'); ?>

<script src="<?php echo URLROOT; ?>/js/edit-destination.js"></script>
<script src="<?php echo URLROOT; ?>/js/delete-destination.js"></script>

<!-- Footer -->
<?php require APPROOT . '/views/includes/footer.php'; ?>
