<!-- Mark as Completed Modal -->
<div id="markCompletedModal" class="modal-container">
    <div class="modal-box">
        <div class="modal-top">
            <h3>Mark Travel as Completed</h3>
            <span class="close" id="closeMarkCompletedModal">&times;</span>
        </div>
        <div class="modal-content">
            <p>Are you sure you want to mark this travel plan as completed?</p>
            <form id="markCompletedForm" action="<?php echo $basePath; ?>/travelplan/markCompleted" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $this->session->generateCSRFToken(); ?>">
                <input type="hidden" name="travel_id" id="complete_travel_id">
                <div class="form-buttons">
                    <button type="button" id="cancelMarkCompleted" class="cancel-btn">Cancel</button>
                    <button type="submit" class="confirm-btn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>