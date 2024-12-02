<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/edit-destination.css">

<div id="editDestinationModal" class="edit-modal-container">
    <div class="editModal">
        <div class="editModal-top">
            <h1>Edit Destination in Travel Plan</h1>
            <span class="close" id="closeEditModal">&times;</span>
        </div>

        <div class="modal-form">
            <form id="editDestinationForm" action="<?php echo URLROOT; ?>/TravelPlan/editDestination" method="post">
                <div class="body">
                    <div class="left">
                        <span class="destination-name" id="modalEditDestinationName"></span><br>

                        <!-- Check-In (Start Date) -->
                        <label for="edit_check_in">Check-In</label>
                        <input type="date" id="edit_check_in" name="check_in" required>

                        <!-- Check-Out (End Date) -->
                        <label for="edit_check_out">Check-Out</label>
                        <input type="date" id="edit_check_out" name="check_out" required>
                    </div>

                    <div class="right">
                        <div class="destination-image">
                            <img id="modalEditDestinationImage" src="http://localhost/Hansika/public/images/Kandy.jpg"  alt="Destination Image">
                        </div>
                    </div>
                </div>

                <input type="hidden" id="destination_id" name="destination_id" value="<?= htmlspecialchars($travelPlan->destination_id) ?>">
                <input type="hidden" id="travel_id" name="travel_id" value="<?= htmlspecialchars($travelPlan->travel_plan_id) ?>">

                <div class="submit-btn">
                    <button type="submit" name="submit">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
