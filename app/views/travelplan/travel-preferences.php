<link rel="stylesheet" href="http://localhost/Medceylon/public/assets/css/travel-plan.css">

<!-- Travel Plan Page -->
<div class="travel-page-content">
    <h2>Create Your Customized Travel Plan</h2>

    <!-- Travel Preferences Form -->
    <form id="travelForm" action="<?php echo ROOT_PATH; ?>/controllers/TravelPlan/automatedTravelPlan" method="POST">
        <div class="form-row">
            <div class="form-column">
                <label for="travel_type">What type of travel do you prefer?</label><br>
                <div class="checkbox-group">
                    <input type="checkbox" name="travel_type[]" value="Adventure"> Adventure
                    <input type="checkbox" name="travel_type[]" value="Relaxation"> Relaxation
                    <input type="checkbox" name="travel_type[]" value="Cultural Experience"> Cultural Experience
                    <input type="checkbox" name="travel_type[]" value="Nature & Wildlife"> Nature & Wildlife
                    <input type="checkbox" name="travel_type[]" value="Historical Tour"> Historical Tour
                </div><br>

                <label for="wheelchair">Do you require wheelchair accessibility?</label><br>
                <input type="radio" name="wheelchair" value="1"> Yes
                <input type="radio" name="wheelchair" value="0" checked> No<br>

                <label for="activities">What activities would you like to include?</label><br>
                <div class="checkbox-group">
                    <input type="checkbox" name="activities[]" value="Sightseeing"> Sightseeing
                    <input type="checkbox" name="activities[]" value="Adventure Sports"> Adventure Sports
                    <input type="checkbox" name="activities[]" value="Spa and Wellness"> Spa and Wellness
                    <input type="checkbox" name="activities[]" value="Beach Activities"> Beach Activities
                </div><br>
            </div>

            <div class="form-column">
                <label for="days">How many days would you like to travel?</label><br>
                <input type="number" name="days" min="1" max="30" required><br>

                <label for="budget">What is your estimated budget range for the trip?</label><br>
                <select name="budget" required>
                    <option value="500-1000">$500 - $1,000</option>
                    <option value="1000-5000">$1,000 - $5,000</option>
                    <option value="5000-10000">$5,000 - $10,000</option>
                    <option value="10000+">$10,000+</option>
                </select><br>

                <label for="activities">More activities to include:</label><br>
                <div class="checkbox-group">
                    <input type="checkbox" name="activities[]" value="Wildlife Safari"> Wildlife Safari
                    <input type="checkbox" name="activities[]" value="Shopping"> Shopping
                    <input type="checkbox" name="activities[]" value="Cultural Festivals"> Cultural Festivals
                    <input type="checkbox" name="activities[]" value="Guided Tours"> Guided Tours
                </div><br>

                <label for="special_requests">Do you have any special requirements or requests?</label><br>
                <textarea name="special_requests" rows="4" cols="50"></textarea><br><br>
            </div>
        </div>

        <button onclick="location.href='<?php echo $basePath; ?>/travelplan/travel-plans';">Generate Travel Plan</button>
    </form>
</div>
