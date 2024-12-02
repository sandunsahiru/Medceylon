<?php
// Include the header
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Doctor</title>
    <link rel="stylesheet" href="./assets/css/rateyourdoctor.css">
</head>
<body>
    <div class="main-container">
        <div class="rate-container">
            <h1>Rate Your Doctor</h1>
            <p class="intro-text">We value your feedback! Please take a moment to rate your doctor and share your experience.</p>
            <form id="rateDoctorForm" onsubmit="handleRatingSubmission(event)">
                <!-- Doctor Selection -->
                <div class="form-group">
                    <label for="doctor">Select Your Doctor</label>
                    <select id="doctor" required>
                        <option value="" disabled selected>Select a doctor</option>
                        <option value="Dr. Silva">Dr. Silva</option>
                        <option value="Dr. Nimal">Dr. Nimal</option>
                        <option value="Dr. Fernando">Dr. Fernando</option>
                        <option value="Dr. Perera">Dr. Perera</option>
                        <!-- Add more doctor options as needed -->
                    </select>
                </div>

                <!-- Star Rating -->
                <div class="form-group rating-group">
                    <label>Rate Your Experience</label>
                    <div class="stars">
                        <input type="radio" id="star5" name="rating" value="5" required>
                        <label for="star5" title="5 stars">★</label>
                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4" title="4 stars">★</label>
                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3" title="3 stars">★</label>
                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2" title="2 stars">★</label>
                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1" title="1 star">★</label>
                    </div>
                </div>

                <!-- Comments -->
                <div class="form-group">
                    <label for="comments">Comments (Optional)</label>
                    <textarea id="comments" placeholder="Share your experience..." rows="6"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Submit Rating</button>
            </form>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
<script>
    function handleRatingSubmission(event) {
        event.preventDefault();
        alert('Thank you for rating your doctor! Your feedback has been submitted.');
        document.getElementById('rateDoctorForm').reset(); // Reset the form after submission
    }
</script>
</body>
</html>
