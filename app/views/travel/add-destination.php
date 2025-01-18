<!-- Modal Structure -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/add-destination.css">

<div id="addToPlanModal" class="add-modal-container">
    <div class="addModal">
        <div class="addModal-top">
            <h1>Add Destination to Travel Plan</h1>
            <span class="close" id="closeModal">&times;</span>
        </div>

        <div class="modal-form">
            <form id="addToPlanForm" action="<?php echo URLROOT; ?>/TravelPlan/addDestination" method="post">
                <div class="body">
                    <div class="left">
                        <span class="destination-name" id ="modalDestinationName"><b><?= htmlspecialchars($destination->destination_name) ?></b></span><br>
                        
                        <!-- Check-In (Start Date) -->
                        <label for="check_in">Check-In</label>
                        <input type="date" id="check_in" name="check_in" required>

                        <!-- Check-Out (End Date) -->
                        <label for="check_out">Check-Out</label>
                        <input type="date" id="check_out" name="check_out" required>
                    </div>

                    <div class="right">
                        <div class="destination-image" id ="modalDestinationImage">
                            <img src="<?= URLROOT . '/' .  htmlspecialchars($destination->image_path) ?>" alt="<?= htmlspecialchars($destination->destination_name) ?>">
                        </div>
                    </div>
                </div>

                <input type="hidden" id="modalDestinationID" name="destination_id" value="<?= htmlspecialchars($destination->destination_id)?>">

                <div class="submit-btn">
                    <button type="submit" name="submit">Add to Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
