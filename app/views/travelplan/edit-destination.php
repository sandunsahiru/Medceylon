<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travelmodal.css">

<div id="editPlanModal" class="edit-modal-container">
    <div class="editModal">
        <div class="editModal-top">
            <h1>Edit Destination Stay</h1>
            <span class="close" id="closeEditModal">&times;</span>
        </div>
        
        <div class="modal-form">
            <form id="editPlanForm">
            <input type="hidden" id="modalTravelID" name="travel_id">
            <input type="hidden" id="modalDestinationID" name="destination_id">
            <input type="date" id="check_in" name="check_in" required>
            <input type="date" id="check_out" name="check_out" required>
            <input type="hidden" id="travel_time" name="travel_time">
            <input type="hidden" id="min_hours" name="min_hours">
            <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $this->session->getCSRFToken() ?>">

                
                <div class="form-group">
                    <label>Destination</label>
                    <span id="modalDestinationName" class="destination-name"></span>
                </div>
                
                <div class="form-group">
                    <label for="editStartDate">Start Date</label>
                    <input type="date" id="editStartDate" readonly>
                </div>
                
                <div class="form-group">
                    <label for="travelTime">Travel Time (hours)</label>
                    <input type="number" id="travelTime" readonly>
                </div>
                
                <div class="form-group">
                    <label for="editStayDuration">Stay Duration (hours)</label>
                    <input type="number" id="editStayDuration" min="1" step="1" required>
                    <small>Minimum: <span id="minStayDuration"></span> hours</small>
                </div>
                
                <div class="form-group">
                    <label for="editEndDate">End Date</label>
                    <input type="date" id="editEndDate" readonly>
                </div>
                
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>
</div>