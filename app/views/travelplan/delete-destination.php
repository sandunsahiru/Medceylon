
<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travelmodal.css">
<div id="warningModalContainer" class="confirm-modal-container">
    <div class="confirmModal">
        <div class="modalContent">
            <img src="http://localhost/Medceylon/public/assets/Images/warning.png" alt="Warning Icon">
            <p>Are you sure you want to Delete this Destination from your Travel Plan?</p>
        </div>
        <div class="bottom">
            <button id="cancelDelete" class="cancel-btn">Cancel</button>
            <button id="confirmDelete" class="delete-btn">Delete</button>
        </div>
    </div>
</div>


    <form action="http://localhost/Medceylon/TravelPlan/delete-destination" id="deleteForm" method="POST" >
        <input type="hidden" name="csrf_token" value="<?= $this->session->getCSRFToken(); ?>">
        <input type="hidden" id="destination_id" name="destination_id" value="<?= htmlspecialchars($travelPlan['destination_id']) ?>">
        <input type="hidden" id="travel_id" name="travel_id" value="<?= htmlspecialchars($travelPlan['travel_plan_id']) ?>">
    </form>
    
</div>

