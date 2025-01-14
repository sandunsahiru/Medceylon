
    <footer class="footer">
        <div class="footer-tagline">
            Explore New Horizons with Our Exceptional Care Solutions!
        </div>
        <div class="footer-right">
            <div class="footer-links">
                <a href="/legal-agreements">Terms & Conditions</a>
                <a href="/faq">FAQs</a>
                <a href="/contact-us">Contact Us</a>
                <a href="/rate-doctor">Rate your doctor</a>
            </div>
            <div class="footer-contact">
                <p>Contact</p>
                <a href="mailto:info@medceylon.com">info@medceylon.com</a>
                <p>+94 769416196</p>
            </div>
        </div>
    </footer>
    <?php if (isset($extraJs)): ?>
        <?php foreach($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>