<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="bookingModalTitle">Book Accommodation</h2>
            <button class="close-btn">&times;</button>
        </div>
        <form id="bookingForm">
            <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
            <input type="hidden" name="accommodation_provider_id" id="accommodationProviderId">
            <input type="hidden" name="patient_id" value="<?php echo $_SESSION['user_id']; ?>">
            
            <div class="form-group">
                <label for="accommodationName">Accommodation</label>
                <input type="text" id="accommodationName" readonly>
            </div>
            
            <div class="form-group">
                <label for="checkInDate">Check-in Date</label>
                <input type="date" id="checkInDate" name="check_in_date" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label for="checkOutDate">Check-out Date</label>
                <input type="date" id="checkOutDate" name="check_out_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
            </div>
            
            <div class="form-group">
                <label for="accommodationType">Accommodation Type</label>
                <select id="accommodationType" name="accommodation_type" required>
                    <option value="">Select Type</option>
                    <option value="single">Single Room</option>
                    <option value="double">Double Room</option>
                    <option value="family">Family Room</option>
                    <option value="suite">Suite</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="specialRequests">Special Requests</label>
                <textarea id="specialRequests" name="special_requests" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="ri-check-line"></i>
                    Confirm Booking
                </button>
                <button type="button" class="cancel-btn">
                    <i class="ri-close-line"></i>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>