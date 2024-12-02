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
    
    <style>
        /* Professional Typography and Layout */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        :root {
            --primary-color: #248c7f;
            --secondary-color: #1f7569;
            --text-dark: #1a1a2e;
            --text-light: #4a4a68;
            --background-light: #f4f4f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background-color: var(--background-light);
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 80px 20px;
        }

        .pricing-container {
            text-align: center;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .intro-text {
            max-width: 700px;
            margin: 0 auto 50px;
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .pricing-cards {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .pricing-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            width: 350px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .package-box h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--text-dark);
            font-weight: 500;
        }

        .package-desc {
            color: var(--text-light);
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .price {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .choose-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .choose-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            width: 450px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .close-btn {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            color: #888;
        }

        .modal form label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .modal form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-payment {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-payment:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Entire body content remains the same as the original -->
    <div class="main-container">
        <div class="pricing-container">
            <h1>Pricing Packages</h1>
            <p class="intro-text">Choose the best package that fits your needs and enjoy your medical journey with us.</p>
            <div class="pricing-cards">
                <!-- Monthly Package Card -->
                <div class="pricing-card monthly">
                    <div class="package-box">
                        <h2>Monthly Package</h2>
                        <p class="package-desc">Perfect for short-term medical travel plans.</p>
                        <p class="price">$100</p>
                        <button class="choose-btn" onclick="openModal('Monthly Package', '$100')">Choose Monthly</button>
                    </div>
                </div>

                <!-- Biannual Package Card -->
                <div class="pricing-card biannual">
                    <div class="package-box">
                        <h2>Biannual Package</h2>
                        <p class="package-desc">Ideal for those planning an extended stay of up to 6 months.</p>
                        <p class="price">$500</p>
                        <button class="choose-btn" onclick="openModal('Biannual Package', '$500')">Choose Biannual</button>
                    </div>
                </div>

                <!-- Annual Package Card -->
                <div class="pricing-card annual">
                    <div class="package-box">
                        <h2>Annual Package</h2>
                        <p class="package-desc">Perfect for long-term medical travel with extensive support.</p>
                        <p class="price">$1000</p>
                        <button class="choose-btn" onclick="openModal('Annual Package', '$1000')">Choose Annual</button>
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

    <script>
        function openModal(packageName, packagePrice) {
            document.getElementById('modal-title').innerText = 'Checkout for ' + packageName;
            document.getElementById('modal-package').innerText = 'Package: ' + packageName;
            document.getElementById('modal-price').innerText = 'Price: ' + packagePrice;
            document.getElementById('checkoutModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('checkoutModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('checkoutModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
// Include the footer
include 'footer.php';
?>