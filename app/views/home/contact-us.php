<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>


<body>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/contactus.css">
    <div class="main-container">
        <div class="contact-container">
            <h1>Contact Us</h1>
            <p class="intro-text">Have any questions or inquiries? Feel free to reach out to us using the form below,
                and we'll get back to you as soon as possible!</p>
            <form id="contactForm" onsubmit="handleFormSubmission(event)">
                <!-- Name -->
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" placeholder="Enter your name" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" placeholder="Enter your email address" required>
                </div>

                <!-- Subject -->
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" placeholder="Enter the subject" required>
                </div>

                <!-- Message -->
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" placeholder="Write your message here..." rows="6" required></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </div>


    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>


    <script>
        function handleFormSubmission(event) {
            event.preventDefault();
            alert('Thank you for reaching out to us! We will respond to your inquiry shortly.');
            document.getElementById('contactForm').reset(); // Reset the form after submission
        }
    </script>
</body>

</html>