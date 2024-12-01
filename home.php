<?php
// Enable error reporting to catch any potential issues
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Including header
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - Your Health, Your Journey, Your Choice</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            line-height: 1.6;
        }

        h1, h2, h3 {
            font-weight: 700;
        }

        a {
            font-size: 1.1em;
            color: black; /* Change link color to black */
            text-decoration: none; /* Remove underline */
        }

        a:hover {
            color: #248c7f; /* Optional: Change color on hover */
            text-decoration: underline; /* Optional: Add underline on hover */
        }

        /* Hero Section */
        .hero {
            background: url('assets/images/hero.jpg') no-repeat center center/cover;
            color: black; 
            padding: 100px 20px;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-bottom: 5px solid #f9f9f9;
        }

        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.5em;
            margin-top: 20px;
        }

        .cta-btn {
            background-color: #fff;
            color: #299d97;
            padding: 20px 40px;
            font-size: 1.5em;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 30px;
            transition: all 0.4s ease;
            border: 2px solid white;
        }

        .cta-btn:hover {
            background-color: #fff;
            color: #248c7f;
            border: 2px solid #248c7f;
        }

        /* Services Section */
        .services {
            padding: 40px 20px;
            text-align: center;
            background-color: #fff;
            margin-bottom: 40px;
        }

        .services h2 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: #299d97;
        }

        .services-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .service {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 18%; /* 5 items in a row */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
        }

        .service:hover {
            transform: scale(1.1); /* Makes the box slightly larger */
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2); /* Adds a deeper shadow */
        }

        .service img {
            width: 100%; /* Ensures the image scales within its container */
            height: 150px; /* Sets a uniform height for all images */
            object-fit: cover; /* Ensures the image fills the set dimensions */
            border-radius: 8px;
        }

        .service h3 {
            font-size: 1.2em;
            margin-top: 10px;
        }

        /* Testimonials Section */
        .testimonials {
            padding: 60px 20px;
            background-color: #fff;
            text-align: center;
        }

        .testimonials h2 {
            font-size: 2.8em;
            color: #299d97;
            margin-bottom: 40px;
        }

        .testimonials-row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            margin-top: 40px;
        }

        .testimonial {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .testimonial:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .testimonial img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .testimonial p {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 15px;
        }

        .testimonial .name {
            font-weight: bold;
            font-size: 1.1em;
        }

        /* Mobile view for Testimonials */
        @media (max-width: 768px) {
            .testimonials-row {
                flex-direction: column;
                gap: 20px;
            }

            .testimonial {
                width: 80%;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>

    <div class="hero">
        <h1>Your Health, Your Journey, Your Choice</h1>
        <p>Explore world-class healthcare, affordable treatment plans, and unforgettable travel experiences.</p>
        <a href="#book-now" class="cta-btn">Book Now!</a>
    </div>

    <section class="services">
        <h2>Our Services</h2>
        <div class="services-grid">
            <!-- First Row -->
            <div class="service">
                <img src="assets/images/Rectangle181523.jpg" alt="Book expert doctors">
                <h3>Book expert doctors</h3>
            </div>
            <div class="service">
                <a href="visa_guidance.php">
                    <img src="assets/images/Rectangle201523.jpg" alt="Check visa details">
                    <h3>Check visa details</h3>
                </a>
            </div>
            <div class="service">
                <a href="accomodation.php">
                    <img src="assets/images/Rectangle211523.jpg" alt="Find your perfect stay">
                    <h3>Find your perfect stay</h3>
                </a>
            </div>
            <div class="service">
                <a href="transportation.php">
                    <img src="assets/images/Rectangle221523.jpg" alt="Explore transport options">
                    <h3>Explore transport options</h3>
                </a>
            </div>
            <div class="service">
                <a href="caregivers.php">
                    <img src="assets/images/Rectangle231573.jpg" alt="Connect with caregivers">
                    <h3>Connect with caregivers</h3>
                </a>
            </div>

            <!-- Second Row -->
            <div class="service">
                <img src="assets/images/Rectangle241573.jpg" alt="Discover Sri Lanka’s gems">
                <h3>Discover Sri Lanka’s gems</h3>

            </div>
            <div class="service">
                <a href="hospital-requests.php">
                    <img src="assets/images/Rectangle251573.jpg" alt="Get personalized hospital support">
                    <h3>Get personalized hospital support</h3>
                </a>
            </div>
            <div class="service">
            <a href="followups.php">
                <img src="assets/images/Rectangle261573.jpg" alt="Manage follow-ups effortlessly">
                <h3>Manage follow-ups effortlessly</h3>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <h2>What Our Clients Say</h2>

        <div class="testimonials-row">
            <div class="testimonial">
                <img src="assets/images/testimonial1.jpg" alt="Client 1">
                <p>"The team at MedCeylon helped me find the best healthcare options and provided exceptional service throughout my stay in Sri Lanka. Highly recommended!"</p>
                <p class="name">Sarah W.</p>
            </div>

            <div class="testimonial">
                <img src="assets/images/testimonial2.jpg" alt="Client 2">
                <p>"Thanks to MedCeylon, my treatment journey was seamless. They took care of all the details, and I felt completely supported the entire time."</p>
                <p class="name">John D.</p>
            </div>

            <div class="testimonial">
                <img src="assets/images/testimonial3.jpg" alt="Client 3">
                <p>"Amazing experience! MedCeylon made sure I had everything I needed from accommodation to medical care. I couldn’t have asked for better service."</p>
                <p class="name">Emma R.</p>
            </div>
        </div>
    </section>

</body>
</html>

<?php
// Including footer
include('footer.php');
?>