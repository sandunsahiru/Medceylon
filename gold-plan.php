<?php
// Include the header
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gold Plan - Medical Tourism Journey</title>
    <link rel="stylesheet" href="gold-plan.css">
</head>
<body>
    <div class="main-container">
        <!-- Form Section -->
        <div id="formContainer" class="form-container">
            <h1>Gold Plan</h1>
            <p class="intro-text">Answer the questions below, and we'll generate a personalized medical tourism plan for you!</p>
            <form id="goldPlanForm" onsubmit="generatePlan(event)">
                <!-- Country -->
                <div class="form-group">
                    <label for="country">Your Country</label>
                    <select id="country" required>
                        <option value="" disabled selected>Select your country</option>
                        <option value="India">India</option>
                        <option value="UK">United Kingdom</option>
                        <option value="USA">United States</option>
                        <option value="Australia">Australia</option>
                        <option value="Canada">Canada</option>
                        <option value="Germany">Germany</option>
                        <option value="France">France</option>
                        <!-- Add more countries as needed -->
                    </select>
                </div>
                
                <!-- Budget -->
                <div class="form-group">
                    <label for="budget">Your Budget</label>
                    <select id="budget" required>
                        <option value="" disabled selected>Select a budget tier</option>
                        <option value="500">$500 - Basic</option>
                        <option value="1000">$1,000 - Standard</option>
                        <option value="2000">$2,000 - Premium</option>
                        <option value="3000">$3,000+ - Luxury</option>
                    </select>
                </div>
                
                <!-- Travel Dates -->
                <div class="form-group dates">
                    <label for="travel-dates">Travel Dates</label>
                    <div class="date-inputs">
                        <input type="date" id="travel-start" required>
                        <span class="to-text">to</span>
                        <input type="date" id="travel-end" required>
                    </div>
                </div>
                
                <!-- Preferences -->
                <div class="form-group">
                    <label for="preferences">What are you interested in?</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" value="Adventure"> Adventure</label>
                        <label><input type="checkbox" value="Culture"> Culture</label>
                        <label><input type="checkbox" value="Relaxation"> Relaxation</label>
                        <label><input type="checkbox" value="Nature"> Nature</label>
                        <label><input type="checkbox" value="Shopping"> Shopping</label>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Generate Plan</button>
            </form>
        </div>

        <!-- Generated Plan -->
        <div id="generatedPlan" class="plan-container hidden">
            <h1>Your Medical Tourism Plan</h1>
            <div class="plan-section">
                <h2>Pre-Arrival Checklist</h2>
                <ul>
                    <li><strong>Visa Requirement:</strong> <span id="visa-status">Yes</span></li>
                    <li>Book your flight to Colombo, Sri Lanka</li>
                    <li>Purchase international medical travel insurance</li>
                    <li>Gather all medical records and prescriptions</li>
                    <li>Confirm hotel booking at Cinnamon Grand Colombo</li>
                    <li>Arrange transportation from the airport to the hotel</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 1: Arrival</h2>
                <p><strong>Arrival Date:</strong> <span id="arrival-date">January 15, 2025</span></p>
                <ul>
                    <li><strong>Hotel:</strong> Cinnamon Grand Colombo</li>
                    <li><strong>Accommodation Cost:</strong> $200/night</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 2: Initial Consultation</h2>
                <p>Consultation with a specialist at Sri Jayawardenepura Hospital.</p>
                <ul>
                    <li><strong>Cost:</strong> $100</li>
                    <li><strong>Transportation:</strong> Private car</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 3: Treatment Day</h2>
                <p>Your treatment will be conducted by top specialists at the hospital.</p>
                <ul>
                    <li><strong>Treatment Cost:</strong> $5000</li>
                    <li><strong>Hospital Stay:</strong> Included</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 4: Recovery</h2>
                <p>Spend your recovery day at the hotel with full-time caregiver support.</p>
                <ul>
                    <li><strong>Caregiver Cost:</strong> $150</li>
                    <li><strong>Meals:</strong> Room service</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 5: Light Sightseeing</h2>
                <p>Enjoy a light sightseeing tour of Colombo.</p>
                <ul>
                    <li><strong>Destinations:</strong> Galle Face Green, Gangaramaya Temple</li>
                    <li><strong>Tour Cost:</strong> $100</li>
                </ul>
            </div>
            <div class="plan-section">
                <h2>Day 6: Departure</h2>
                <p>You will be transferred to the airport for your flight back home.</p>
                <ul>
                    <li><strong>Airport Transfer Cost:</strong> $60</li>
                </ul>
            </div>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
<script>
    function generatePlan(event) {
        event.preventDefault(); // Prevent form submission

        // Fetch input values
        const country = document.getElementById('country').value;
        const budget = document.getElementById('budget').value;
        const travelStart = document.getElementById('travel-start').value;
        const travelEnd = document.getElementById('travel-end').value;

        // Update plan details
        document.getElementById('visa-status').textContent = country === 'India' ? 'No' : 'Yes';
        document.getElementById('arrival-date').textContent = travelStart;

        // Hide form and show plan
        document.getElementById('formContainer').classList.add('hidden');
        document.getElementById('generatedPlan').classList.remove('hidden');
    }
</script>
</body>
</html>
