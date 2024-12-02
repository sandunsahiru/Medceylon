<?php include('header.php'); ?>
<!-- Next button -->
<a href="Travel/destination/destinations">
        <button class="next-button">Next</button>
    </a>
<div class="container">
    <h2>Select Caregivers for Medical Tourism in Sri Lanka</h2>

    <div class="filters">
        <form method="POST" class="filter-form">
            <label for="experience">Experience:</label>
            <select name="experience" id="experience">
                <option value="all">All</option>
                <option value="1-5">1-5 Years</option>
                <option value="5-10">5-10 Years</option>
                <option value="10+">10+ Years</option>
            </select>

            <label for="city">City:</label>
            <select name="city" id="city">
                <option value="all">All</option>
                <option value="Colombo">Colombo</option>
                <option value="Kandy">Kandy</option>
                <option value="Galle">Galle</option>
                <option value="Negombo">Negombo</option>
                <option value="Jaffna">Jaffna</option>
            </select>

            <button type="submit" class="filter-button">Apply Filters</button>
        </form>
    </div>

    <div class="caregivers-cards">
        <?php
        // Sample caregiver data
        $caregivers = [
            ['name' => 'Saman Perera', 'city' => 'Colombo', 'experience' => '7 years', 'specialty' => 'Nursing', 'description' => 'Saman specializes in adult nursing and post-surgery care. He has been a caregiver for over 7 years and works in Colombo.', 'contact' => 'saman@example.com'],
            ['name' => 'Anjali Silva', 'city' => 'Kandy', 'experience' => '3 years', 'specialty' => 'Physiotherapy', 'description' => 'Anjali is an experienced physiotherapist who helps patients regain mobility post-injury. She works primarily in Kandy.', 'contact' => 'anjali@example.com'],
            ['name' => 'Chamara Fernando', 'city' => 'Galle', 'experience' => '12 years', 'specialty' => 'Caregiver Assistant', 'description' => 'Chamara has been assisting caregivers and supporting patients for over 12 years, with expertise in elderly care.', 'contact' => 'chamara@example.com'],
            ['name' => 'Dilani Perera', 'city' => 'Colombo', 'experience' => '5 years', 'specialty' => 'Home Care', 'description' => 'Dilani specializes in home care for elderly patients, ensuring they are comfortable and safe in their own homes.', 'contact' => 'dilani@example.com'],
            ['name' => 'Rajitha Kumarasinghe', 'city' => 'Colombo', 'experience' => '6 years', 'specialty' => 'Geriatric Care', 'description' => 'Rajitha has extensive experience in geriatric care, providing daily assistance to elderly patients.', 'contact' => 'rajitha@example.com'],
            ['name' => 'Nisha Perera', 'city' => 'Kandy', 'experience' => '8 years', 'specialty' => 'Nursing', 'description' => 'Nisha is known for her compassionate care and expert nursing skills, particularly in post-operative care.', 'contact' => 'nisha@example.com'],
            ['name' => 'Kamal Jayasinghe', 'city' => 'Galle', 'experience' => '15 years', 'specialty' => 'Caregiver Assistant', 'description' => 'Kamal has a passion for providing care to patients and assisting medical professionals in hospitals for over 15 years.', 'contact' => 'kamal@example.com'],
            ['name' => 'Pooja Gunawardena', 'city' => 'Negombo', 'experience' => '4 years', 'specialty' => 'Physiotherapy', 'description' => 'Pooja has worked with patients recovering from physical trauma and specializes in musculoskeletal therapy.', 'contact' => 'pooja@example.com'],
            ['name' => 'Tharindu Perera', 'city' => 'Colombo', 'experience' => '10 years', 'specialty' => 'Pediatric Care', 'description' => 'Tharindu specializes in pediatric care, working with children in critical and non-critical conditions for the last decade.', 'contact' => 'tharindu@example.com'],
            ['name' => 'Samitha Dissanayake', 'city' => 'Kandy', 'experience' => '3 years', 'specialty' => 'Nursing', 'description' => 'Samitha is a skilled nurse with a special focus on wound care and elderly care, working in Kandy.', 'contact' => 'samitha@example.com'],
            ['name' => 'Kumari Rajapaksha', 'city' => 'Galle', 'experience' => '11 years', 'specialty' => 'Elderly Care', 'description' => 'Kumari has worked extensively in providing daily care and companionship to elderly patients for over 11 years.', 'contact' => 'kumari@example.com'],
            ['name' => 'Amal Rajapakse', 'city' => 'Colombo', 'experience' => '7 years', 'specialty' => 'Nursing', 'description' => 'Amal specializes in providing intensive care for patients in critical conditions at hospitals in Colombo.', 'contact' => 'amal@example.com'],
        ];

        // Loop through caregivers and assign images
        foreach ($caregivers as $index => $caregiver) {
            $imageIndex = $index + 1; // image index starts from 1 (not 0)
            echo '
            <div class="card">
                <div class="card-inner">
                    <div class="card-front">
                        <img src="assets/images/' . $imageIndex . '.jpg" alt="Caregiver Image" class="caregiver-image" />
                        <h3>' . $caregiver['name'] . '</h3>
                        <p><strong>City:</strong> ' . $caregiver['city'] . '</p>
                        <p><strong>Experience:</strong> ' . $caregiver['experience'] . '</p>
                        <p><strong>Specialty:</strong> ' . $caregiver['specialty'] . '</p>
                    </div>
                    <div class="card-back">
                        <p><strong>Description:</strong> ' . $caregiver['description'] . '</p>
                        <div class="message-btn-container">
                            <a href="mailto:' . $caregiver['contact'] . '" class="message-btn">Send a Message</a>
                        </div>
                    </div>
                </div>
            </div>';
        }
        ?>
    </div>
</div>

<?php include('footer.php'); ?>

<!-- Embedded CSS -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 20px;
    }

    h2 {
        text-align: center;
        font-size: 36px;
        color: #333;
        margin-bottom: 40px;
    }

    .filters {
        margin-bottom: 30px;
        text-align: center;
    }

    .filter-form select {
        padding: 10px;
        margin: 5px;
        font-size: 16px;
    }

    .filter-form button {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #248c7f;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .caregivers-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .card {
        position: relative;
        width: 100%;
        height: 350px;
        perspective: 1000px;
    }

    .card-inner {
        position: absolute;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 0.6s;
    }

    .card:hover .card-inner {
        transform: rotateY(180deg);
    }

    .card-front, .card-back {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        padding: 20px;
        box-sizing: border-box;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .card-front {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .caregiver-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    .card-back {
        transform: rotateY(180deg);
        padding-top: 20px;
    }

    .message-btn-container {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    .message-btn {
        padding: 10px 20px;
        background-color: #248c7f;
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .message-btn:hover {
        background-color: #1b6c60;
    }

        /* Next button in the top-right corner */
        .next-button {
            position: absolute;
            bottom: 250px;
            right: 20px;
            padding: 10px 20px;
            background-color: #299d97;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .next-button:hover {
            background-color: #247f7a;
        }
</style>
