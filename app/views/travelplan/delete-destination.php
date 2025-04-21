
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/delete-destination.css">
<div id="warningModalContainer" class="confirmModal">
    <div class="wrapper">
        <div class="modalContent">
            <img src="<?php echo URLROOT;?>/Images/warning.png" alt="Warning Icon">
            <p>Are you sure you want to Delete this destination?</p>
        </div>
        <div class="bottom">
            <button id="cancelDelete" class="cancel-btn">Cancel</button>
            <button id="confirmDelete" class="delete-btn">Delete</button>
        </div>
    </div>
</div>


    <form action="<?php echo URLROOT; ?>/TravelPlan/deleteDestination" id="deleteForm" method="POST" style="display:none;">
        <input type="hidden" id="destination_id" name="destination_id" value="<?= htmlspecialchars($travelPlan->destination_id) ?>">
        <input type="hidden" id="travel_id" name="travel_id" value="<?= htmlspecialchars($travelPlan->travel_plan_id) ?>">
    </form>
    
</div>

