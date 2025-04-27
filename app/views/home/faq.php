<?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

<style>
/* FAQ Container Styling */
.faq-container {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Title Styling */
.faq-title {
    font-size: 36px;
    font-weight: 600;
    color: #333;
    text-align: center;
    margin-bottom: 40px;
}

/* FAQ Item Styling */
.faq-item {
    background-color: #fff;
    margin-bottom: 10px;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
}

/* Hover effect for FAQ items */
.faq-item:hover {
    background-color: #f1f1f1;
}

/* Question Styling */
.faq-question {
    font-size: 22px;
    font-weight: 500;
    color: #333;
    margin-bottom: 10px;
    flex-grow: 1;  /* Take up available space */
}

/* Dropdown Icon Styling */
.faq-icon {
    font-size: 24px;
    color: #333;
    margin-left: 15px;
    transition: transform 0.3s ease;
}

/* Answer Styling (Initially hidden) */
.faq-answer {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
    padding-top: 10px;
    display: none;  /* Hide answers by default */
}

/* Small screen responsiveness */
@media screen and (max-width: 768px) {
    .faq-container {
        padding: 10px;
    }

    .faq-title {
        font-size: 30px;
    }

    .faq-question {
        font-size: 20px;
    }

    .faq-answer {
        font-size: 14px;
    }
}
</style>

<!-- FAQ Container -->
<div class="faq-container">
    <h1 class="faq-title">Frequently Asked Questions (FAQ)</h1>

    <!-- FAQ Items with Collapsible Functionality -->
    <div class="faq-item">
        <h2 class="faq-question">What is Medical Tourism?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">Medical tourism refers to traveling to a different country for medical treatment. It often includes seeking affordable, high-quality care and combining the treatment with a vacation experience.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">How do I book an appointment with a doctor?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">You can book an appointment by using our platform, selecting a doctor, and choosing a date that fits your schedule. After booking, you will receive a confirmation email with all the details.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">What types of medical services can I find in Sri Lanka?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">Sri Lanka offers a wide range of medical services including surgeries, dental care, fertility treatments, cosmetic surgeries, and alternative therapies like Ayurveda.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">Are there any special packages for medical tourists?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">Yes, we offer various medical tourism packages that include medical consultation, treatment, accommodation, and transportation. You can select a package that best suits your needs.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">Is medical tourism in Sri Lanka safe?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">Yes, Sri Lanka has highly qualified doctors and advanced medical facilities. All medical institutions are internationally accredited, ensuring the highest standards of safety and quality care.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">How long does it take to get an appointment?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">The waiting time for an appointment varies depending on the doctor and your preferred time. However, we try to schedule appointments as soon as possible to meet your needs.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">Do I need a referral to book an appointment?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">Most doctors do not require a referral to book an appointment, but for certain specialties, a referral from your primary care physician may be requested.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">What payment methods are accepted?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">We accept a variety of payment methods, including credit cards, debit cards, and bank transfers. You can choose the most convenient option for you when booking your appointment.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">Do I need to bring anything to my appointment?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">It is helpful to bring any relevant medical records, identification, and your payment method. If you are traveling from abroad, please bring any necessary travel documents.</p>
    </div>

    <div class="faq-item">
        <h2 class="faq-question">What is the average cost of treatment in Sri Lanka?</h2>
        <span class="faq-icon">+</span>
        <p class="faq-answer">The cost of medical treatment in Sri Lanka is generally lower than in many Western countries, but it depends on the type of treatment. We offer cost-effective packages for various medical services.</p>
    </div>
</div>

<script>
// Get all FAQ items
const faqItems = document.querySelectorAll('.faq-item');

// Add event listener to each FAQ item to toggle the answer and icon
faqItems.forEach(item => {
    item.addEventListener('click', () => {
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon');
        
        // Toggle the answer visibility
        if (answer.style.display === 'block') {
            answer.style.display = 'none';
            icon.textContent = '+';  // Change icon to plus
            icon.style.transform = 'rotate(0deg)';
        } else {
            answer.style.display = 'block';
            icon.textContent = '−';  // Change icon to minus
            icon.style.transform = 'rotate(180deg)';
        }
    });
});
</script>

<?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>