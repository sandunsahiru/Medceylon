<link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/travelmodal.css">

<div id="editPlanModal" class="edit-modal-container" data-base-url="<?= $this->url('travelplan/destinations') ?>">
    <div class="editModal">
        <div class="editModal-top">
            <h1>Edit Destination Dates</h1>
            <span class="close" id="closeEditModal">&times;</span>
        </div>

        <div class="modal-form">
            <form id="editPlanForm" action="<?= $this->url('travelplan/edit-destination') ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= $this->session->getCSRFToken() ?>">
                <input type="hidden" name="travel_id" id="modalTravelID" value="">
                <input type="hidden" name="destination_id" id="modalDestinationID" value="">
                <div class="body">
                    <div class="left">
                        <span class="destination-name" id="modalDestinationName"></span><br>
                        
                        <label for="check_in">Start Date</label>
                        <input type="date" id="check_in" name="check_in" required>

                        <label for="check_out">End Date</label>
                        <input type="date" id="check_out" name="check_out" required>
                    </div>

                    <div class="right">
                        <div class="destination-info2">
                            <p><strong>Travel Time:</strong> <span id="TravelTime"></span> hours</p>
                            <p><strong>Minimum Visit Time:</strong> <span id="MinHours"></span> hours</p>
                        </div>
                    </div>
                </div>

                <button type="submit" name="submit">Save Changes</button>
            </form>
        </div>
    </div>
</div>