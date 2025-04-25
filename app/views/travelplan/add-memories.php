<!-- Add Memories Modal -->
<div id="addMemoriesModal" class="modal-container">
    <div class="modal-box memories-modal">
        <div class="modal-top">
            <h3>Add Memories for <span id="memoriesDestinationName"></span></h3>
            <span class="close" id="closeAddMemoriesModal">&times;</span>
        </div>
        <div class="modal-content">
            <form id="addMemoriesForm" action="<?php echo $basePath; ?>/travelplan/addMemories" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $this->session->generateCSRFToken(); ?>">
                <input type="hidden" name="travel_id" id="memories_travel_id">
                
                <div class="form-group">
                    <label for="memory_photos">Upload Photos (Max 5)</label>
                    <input type="file" id="memory_photos" name="memory_photos[]" accept="image/*" multiple>
                    <div class="photo-preview-container" id="photoPreviewContainer"></div>
                </div>
                
                <div class="form-group">
                    <label for="memory_note">Your Experience (Max 500 characters)</label>
                    <textarea id="memory_note" name="memory_note" maxlength="500" rows="4" placeholder="Share your experience..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Rate Your Experience</label>
                    <div class="rating-input">
                        <input type="radio" id="star5" name="rating" value="5">
                        <label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1">★</label>
                    </div>
                </div>
                
                <div class="form-buttons">
                    <button type="button" id="cancelAddMemories" class="cancel-btn">Cancel</button>
                    <button type="submit" class="save-btn">Save Memories</button>
                </div>
            </form>
        </div>
    </div>
</div>