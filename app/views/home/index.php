
<?php include('../app/views/layouts/header.php'); ?>

<div class="hero">
    <h1>Your Health, Your Journey, Your Choice</h1>
    <p>Explore world-class healthcare, affordable treatment plans, and unforgettable travel experiences.</p>
    <a href="/login" class="cta-btn">Book Now!</a>
</div>

<section class="services">
    <h2>Our Services</h2>
    <div class="services-grid">
        <!-- First Row -->
        <div class="service">
            <img src="<?php echo $basePath; ?>/public/assets/images/Rectangle181523.jpg" alt="Book expert doctors">
            <h3>Book expert doctors</h3>
        </div>
        <div class="service">
            <img src="<?php echo $basePath; ?>/public/assets/images/Rectangle201523.jpg" alt="Check visa details">
            <h3>Check visa details</h3>
        </div>
        <div class="service">
            <img src="/assets/images/Rectangle211523.jpg" alt="Find your perfect stay">
            <h3>Find your perfect stay</h3>
        </div>
        <div class="service">
            <img src="/assets/images/Rectangle221523.jpg" alt="Explore transport options">
            <h3>Explore transport options</h3>
        </div>
        <div class="service">
            <img src="/assets/images/Rectangle231573.jpg" alt="Connect with caregivers">
            <h3>Connect with caregivers</h3>
        </div>

        <!-- Second Row -->
        <div class="service">
            <img src="/assets/images/Rectangle241573.jpg" alt="Discover Sri Lanka's gems">
            <h3>Discover Sri Lanka's gems</h3>
        </div>
        <div class="service">
            <img src="/assets/images/Rectangle251573.jpg" alt="Get personalized hospital support">
            <h3>Get personalized hospital support</h3>
        </div>
        <div class="service">
            <img src="/assets/images/Rectangle261573.jpg" alt="Manage follow-ups effortlessly">
            <h3>Manage follow-ups effortlessly</h3>
        </div>
    </div>
</section>

<section class="testimonials">
    <h2>What Our Clients Say</h2>
    <div class="testimonials-row">
        <div class="testimonial">
            <img src="/assets/images/testimonial1.jpg" alt="Client 1">
            <p>"The team at MedCeylon helped me find the best healthcare options and provided exceptional service throughout my stay in Sri Lanka. Highly recommended!"</p>
            <p class="name">Sarah W.</p>
        </div>

        <div class="testimonial">
            <img src="/assets/images/testimonial2.jpg" alt="Client 2">
            <p>"Thanks to MedCeylon, my treatment journey was seamless. They took care of all the details, and I felt completely supported the entire time."</p>
            <p class="name">John D.</p>
        </div>

        <div class="testimonial">
            <img src="/assets/images/testimonial3.jpg" alt="Client 3">
            <p>"Amazing experience! MedCeylon made sure I had everything I needed from accommodation to medical care. I couldn't have asked for better service."</p>
            <p class="name">Emma R.</p>
        </div>
    </div>
</section>

<?php include('../app/views/layouts/footer.php'); ?>