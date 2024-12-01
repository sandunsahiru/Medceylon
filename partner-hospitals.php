<?php
// Include the header
include 'header.php';

// List of Partner Hospitals with Local Images and Descriptions
$partner_hospitals = [
    [
        "name" => "Durdans Hospital",
        "image" => "./assets/images/durdans_hospital.jpg",
        "description" => "Located in Colombo, known for advanced cardiology care."
    ],
    [
        "name" => "Asiri Medical Hospital",
        "image" => "./assets/images/asiri_medical_hospital.jpg",
        "description" => "Based in Colombo, specializing in multi-specialty treatments."
    ],
    [
        "name" => "Nawaloka Hospital",
        "image" => "./assets/images/nawaloka_hospital.jpg",
        "description" => "A leading hospital in Colombo offering state-of-the-art facilities."
    ],
    [
        "name" => "Lanka Hospitals",
        "image" => "./assets/images/lanka_hospitals.jpg",
        "description" => "Situated in Colombo, well-known for international patient care."
    ],
    [
        "name" => "Golden Key Hospital",
        "image" => "./assets/images/golden_key_hospital.jpg",
        "description" => "Located in Rajagiriya, specializes in eye care and ENT services."
    ],
    [
        "name" => "Central Hospital",
        "image" => "./assets/images/central_hospital.jpg",
        "description" => "A top-notch hospital in Kandy, offering diverse medical services."
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Partner Hospitals</title>
    <link rel="stylesheet" href="./assets/css/partner-hospitals.css">
</head>
<body>
    <div class="hospitals-container">
        <h1>Our Partner Hospitals</h1>
        <p>We proudly partner with Sri Lanka's leading private hospitals to bring you the best in healthcare and medical tourism.</p>
        <div class="hospitals-grid">
            <?php foreach ($partner_hospitals as $hospital): ?>
                <div class="hospital-card">
                    <img src="<?php echo $hospital['image']; ?>" alt="<?php echo $hospital['name']; ?>">
                    <h2><?php echo $hospital['name']; ?></h2>
                    <p><?php echo $hospital['description']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php
// Include the footer
include 'footer.php';
?>
</body>
</html>
