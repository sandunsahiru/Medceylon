<?php
// Include the header
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Packages</title>
    <link rel="stylesheet" href="pricing.css">
</head>
<body>
    <div class="main-container">
        <div class="pricing-container">
            <h1>Pricing Packages</h1>
            <p class="intro-text">Choose the best package that fits your needs and enjoy your medical journey with us.</p>
            <div class="pricing-cards">
                <!-- Silver Package Card -->
                <div class="pricing-card silver">
                    <div class="silver-box">
                        <h2>Silver Package</h2>
                        <p class="package-desc">Perfect for those who want to handle their plans manually.</p>
                        <ul>
                            <li>Booking Assistance</li>
                            <li>Transportation Arrangement</li>
                            <li>Sightseeing Recommendations</li>
                        </ul>
                        <p class="price">$499</p>
                        <button class="choose-btn" onclick="openModal('Silver Package', '$499')">Choose Silver</button>
                    </div>
                </div>

                <!-- Gold Package Card -->
                <div class="pricing-card gold">
                    <div class="gold-box">
                        <h2>Gold Package</h2>
                        <p class="package-desc">Fully automated plans from booking to post-treatment follow-ups.</p>
                        <ul>
                            <li>Comprehensive Booking Management</li>
                            <li>All Transportation Included</li>
                            <li>Guided Sightseeing Tours</li>
                            <li>Post-Treatment Appointments Free of Charge</li>
                        </ul>
                        <p class="price">$999</p>
                        <button class="choose-btn" onclick="openModal('Gold Package', '$999')">Choose Gold</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="checkoutModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2 id="modal-title">Checkout</h2>
                <p id="modal-package">Package: </p>
                <p id="modal-price">Price: </p>
                <form>
                    <label for="card-name">Cardholder Name</label>
                    <input type="text" id="card-name" placeholder="Enter your name" required>
                    
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" placeholder="1234 5678 9012 3456" required>

                    <label for="expiry-date">Expiry Date</label>
                    <input type="text" id="expiry-date" placeholder="MM/YY" required>

                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" placeholder="123" required>

                    <button type="submit" class="submit-payment">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
<script>
    function openModal(packageName, packagePrice) {
        document.getElementById('checkoutModal').style.display = 'flex';
        document.getElementById('modal-package').textContent = `Package: ${packageName}`;
        document.getElementById('modal-price').textContent = `Price: ${packagePrice}`;
    }

    function closeModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }
</script>
</body>
</html>
