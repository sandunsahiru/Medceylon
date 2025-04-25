<!-- Modal Structure -->
<link rel="stylesheet" href="http://localhost/Medceylon/public/assets/css/travelmodal.css">

<div id="addToPlanModal" class="add-modal-container">
    <div class="addModal">
        <div class="addModal-top">
            <h1>Add Destination to Travel Plan</h1>
            <span class="close" id="closeModal">&times;</span>
        </div>

        <div class="modal-form">
            <form id="addToPlanForm" action="http://localhost/Medceylon/travelplan/add-destination" method="post">
                <input type="hidden" name="csrf_token" value="<?= $this->session->getCSRFToken(); ?>">


                <div class="body">
                    <div class="left">
                        <span class="destination-name" id ="modalDestinationName"><b><?= htmlspecialchars($destination['destination_name']) ?></b></span><br>
                        
                        <!-- Check-In (Start Date) -->
                        <label for="check_in">Check-In</label>
                        <input type="date" id="check_in" name="check_in" required>

                        <!-- Check-Out (End Date) -->
                        <label for="check_out">Check-Out</label>
                        <input type="date" id="check_out" name="check_out" required>
                    </div>

                    <div class="right">
                        <div class="destination-image-modal">
                            <img id="modalDestinationImage" src="" alt=""><br>
                        </div>
                        <div class="destination-info2">
                        <p><strong>Opening Time:</strong> <span id="OpeningTime"><?= htmlspecialchars($destination['opening_time']) ?></span></p>

                        <p><strong>Closing Time:</strong> <span id="ClosingTime"><?= htmlspecialchars($destination['closing_time']) ?></span></p>

                        <p><strong>Entry Fee (LKR):</strong> <span id="EntryFee"><?= htmlspecialchars($destination['entry_fee']) ?></span></p>

                        </div>
                    </div>
                </div>

                <input type="hidden" id="modalDestinationID" name="destination_id" value="<?= htmlspecialchars($destination['destination_id'])?>">

                
                    <button type="submit" name="submit">Add to Plan</button>
                
            </form>
        </div>
    </div>
</div>
