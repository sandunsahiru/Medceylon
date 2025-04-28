<?php
// This is a standalone version of the visa guidance page that doesn't require database access
// Save this file to your app/views/home/ directory as visa_guidance.php

// Hardcoded country data
$countries = [
    ['country_code' => 'AE', 'country_name' => 'United Arab Emirates'],
    ['country_code' => 'AU', 'country_name' => 'Australia'],
    ['country_code' => 'BR', 'country_name' => 'Brazil'],
    ['country_code' => 'CA', 'country_name' => 'Canada'],
    ['country_code' => 'CN', 'country_name' => 'China'],
    ['country_code' => 'DE', 'country_name' => 'Germany'],
    ['country_code' => 'EG', 'country_name' => 'Egypt'],
    ['country_code' => 'ES', 'country_name' => 'Spain'],
    ['country_code' => 'FR', 'country_name' => 'France'],
    ['country_code' => 'GB', 'country_name' => 'United Kingdom'],
    ['country_code' => 'IN', 'country_name' => 'India'],
    ['country_code' => 'IT', 'country_name' => 'Italy'],
    ['country_code' => 'JP', 'country_name' => 'Japan'],
    ['country_code' => 'KE', 'country_name' => 'Kenya'],
    ['country_code' => 'KR', 'country_name' => 'South Korea'],
    ['country_code' => 'LK', 'country_name' => 'Sri Lanka'],
    ['country_code' => 'MX', 'country_name' => 'Mexico'],
    ['country_code' => 'MY', 'country_name' => 'Malaysia'],
    ['country_code' => 'NG', 'country_name' => 'Nigeria'],
    ['country_code' => 'NL', 'country_name' => 'Netherlands'],
    ['country_code' => 'NP', 'country_name' => 'Nepal'],
    ['country_code' => 'PH', 'country_name' => 'Philippines'],
    ['country_code' => 'PK', 'country_name' => 'Pakistan'],
    ['country_code' => 'RU', 'country_name' => 'Russia'],
    ['country_code' => 'SG', 'country_name' => 'Singapore'],
    ['country_code' => 'US', 'country_name' => 'United States']
];

// Hardcoded visa requirements data
$visaDetails = [
    'AE' => ['visa_required' => 1, 'application_steps' => '1. Complete visa form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.ae/'],
    'AU' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.sri-lanka.embassy.gov.au/'],
    'BR' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.br/'],
    'CA' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.ca/'],
    'CN' => ['visa_required' => 1, 'application_steps' => '1. Complete visa application form. 2. Submit documents.', 'embassy_link' => 'https://www.srilankaembassy.cn/'],
    'DE' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa form. 2. Submit documents and passport.', 'embassy_link' => 'https://www.srilankaembassy.de/'],
    'EG' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa application form. 2. Submit documents.', 'embassy_link' => 'https://www.srilankaembassy.eg/'],
    'ES' => ['visa_required' => 1, 'application_steps' => '1. Complete visa application form. 2. Submit documents and passport.', 'embassy_link' => 'https://www.srilankaembassy.es/'],
    'FR' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.fr/'],
    'GB' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application form. 2. Gather required documents.', 'embassy_link' => 'https://www.srilankaembassy.co.uk/'],
    'IN' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.in/'],
    'IT' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa application form. 2. Provide supporting documents.', 'embassy_link' => 'https://www.srilankaembassy.it/'],
    'JP' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.jp/'],
    'KE' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.ke/'],
    'KR' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.kr/'],
    'LK' => ['visa_required' => 0, 'application_steps' => 'No visa required for citizens of Sri Lanka.', 'embassy_link' => 'N/A'],
    'MX' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.mx/'],
    'MY' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa application. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.my/'],
    'NG' => ['visa_required' => 1, 'application_steps' => '1. Complete visa application form. 2. Submit documents.', 'embassy_link' => 'https://www.srilankaembassy.ng/'],
    'NL' => ['visa_required' => 1, 'application_steps' => '1. Complete visa form. 2. Submit documents and passport.', 'embassy_link' => 'https://www.srilankaembassy.nl/'],
    'NP' => ['visa_required' => 0, 'application_steps' => 'No visa is required for citizens of Nepal.', 'embassy_link' => 'N/A'],
    'PH' => ['visa_required' => 1, 'application_steps' => '1. Complete the visa form. 2. Submit documents and passport.', 'embassy_link' => 'https://www.srilankaembassy.ph/'],
    'PK' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.pk/'],
    'RU' => ['visa_required' => 1, 'application_steps' => '1. Fill out the visa form. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.ru/'],
    'SG' => ['visa_required' => 1, 'application_steps' => '1. Complete visa application. 2. Submit passport and documents.', 'embassy_link' => 'https://www.srilankaembassy.sg/'],
    'US' => ['visa_required' => 1, 'application_steps' => '1. Complete the online DS-160 form. 2. Pay the visa application fee. 3. Schedule an interview at the US Embassy. 4. Attend the visa interview with required documents.', 'embassy_link' => 'https://lk.usembassy.gov/']
];

// Initialize country_details
$country_details = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['country_code'])) {
    $country_code = $_POST['country_code'];
    
    // Find the country in our data
    foreach ($countries as $country) {
        if ($country['country_code'] === $country_code) {
            // Create country details from hardcoded data
            if (isset($visaDetails[$country_code])) {
                $country_details = array_merge(
                    $country,
                    $visaDetails[$country_code]
                );
            }
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sri Lanka Visa Information</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9f8;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header,
        footer {
            width: 100%;
            flex-shrink: 0;
            background-color: #299d97;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .container {
            flex-grow: 1;
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #299d97;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            color: #299d97;
        }

        select {
            padding: 10px;
            font-size: 1rem;
            border: 2px solid #299d97;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #299d97;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #247f7a;
        }

        .details {
            margin-top: 20px;
        }

        .details h2 {
            color: #299d97;
            margin-bottom: 10px;
        }

        a {
            color: #299d97;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        pre {
            background: #f1f9f9;
            border-left: 4px solid #299d97;
            padding: 10px;
            border-radius: 5px;
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .steps-container {
            background: #f1f9f9;
            border-left: 4px solid #299d97;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .steps-container ol {
            margin: 0;
            padding-left: 20px;
        }

        .steps-container li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <?php include ROOT_PATH . '/app/views/layouts/header.php'; ?>

    <div class="container">
        <h1>Sri Lanka Visa Information</h1>
        <form method="POST">
            <label for="country_code">Select Country:</label>
            <select name="country_code" id="country_code" required>
                <option value="">-- Choose a Country --</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?= htmlspecialchars($country['country_code']); ?>"
                        <?= isset($_POST['country_code']) && $_POST['country_code'] == $country['country_code'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($country['country_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Show Visa Details</button>
        </form>
        
        <?php if ($country_details): ?>
            <div class="details">
                <h2>Visa Details for <?= htmlspecialchars($country_details['country_name']); ?></h2>
                <?php if ($country_details['visa_required']): ?>
                    <p><strong>Visa Required:</strong> Yes</p>
                    <p><strong>Steps to Apply for Visa:</strong></p>
                    
                    <?php 
                    // Check if application steps follow the numbered format (1. Step one. 2. Step two.)
                    $steps = $country_details['application_steps'];
                    if (preg_match('/^\d+\.\s/', $steps)) {
                        // Format as a proper list
                        $stepsList = preg_split('/(\d+\.\s)/', $steps, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                        echo '<div class="steps-container"><ol>';
                        
                        for ($i = 0; $i < count($stepsList); $i += 2) {
                            if (isset($stepsList[$i+1])) {
                                echo '<li>' . htmlspecialchars($stepsList[$i+1]) . '</li>';
                            }
                        }
                        
                        echo '</ol></div>';
                    } else {
                        // Just display as text if it doesn't follow the numbered format
                        echo '<pre>' . htmlspecialchars($steps) . '</pre>';
                    }
                    ?>
                <?php else: ?>
                    <p><strong>No visa is required for citizens of
                            <?= htmlspecialchars($country_details['country_name']); ?>.</strong></p>
                <?php endif; ?>
                <?php if (!empty($country_details['embassy_link']) && $country_details['embassy_link'] != 'N/A'): ?>
                    <p><strong>Embassy Link:</strong> <a href="<?= htmlspecialchars($country_details['embassy_link']); ?>"
                            target="_blank"><?= htmlspecialchars($country_details['embassy_link']); ?></a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include ROOT_PATH . '/app/views/layouts/footer.php'; ?>
</body>

</html>