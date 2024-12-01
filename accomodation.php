
<?php
?>
<!DOCTYPE html>
<html lang="english">
<head>
    <title>MedCeylon - Accommodation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" />
    
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.5;
            background-color: #f5f5f5;
        }

        /* Navbar styles */
        .navbar {
            background-color: #299d97;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-family: 'Work Sans', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #0c161c;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #0c161c;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .nav-button {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-button.primary {
            background-color: #13191c;
            color: #ffffff;
        }

        .nav-button.secondary {
            background-color: #ffffff;
            color: #0c161c;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .nav-buttons {
                gap: 0.5rem;
            }
            
            .nav-button {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>


<?php
?>
    
    <style>
        .footer {
            background-color: #299d97;
            padding: 4rem 2rem;
            color: #ffffff;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            gap: 2rem;
        }

        .footer-slogan h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            max-width: 500px;
            line-height: 1.2;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-links a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1rem;
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 0.8;
        }

        .footer-contact {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-contact h3 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .email-button {
            background-color: #0c161c;
            color: #ffffff;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            text-align: center;
            transition: background-color 0.3s;
        }

        .email-button:hover {
            background-color: #1a2830;
        }

        .phone {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-slogan h2 {
                font-size: 2rem;
            }

            .footer-contact {
                align-items: center;
            }
        }
    </style>
</body>
</html>


<?php include 'header.php'; ?>

<style>
    .accommodation-container {
        margin-top: 80px;
        padding: 2rem;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
    }

    .search-section {
        text-align: center;
        margin-bottom: 3rem;
    }

    .search-section h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
        color: #0c161c;
    }

    .search-section p {
        color: #4f7a93;
        margin-bottom: 2rem;
    }

    .search-form {
        max-width: 600px;
        margin: 0 auto;
    }

    .search-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    .search-button {
        background-color: #299d97;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
    }

    .search-button:hover {
        background-color: #238d87;
    }

    .accommodations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        padding: 1rem;
    }

    .accommodation-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }

    .accommodation-card:hover {
        transform: translateY(-5px);
    }

    .accommodation-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .accommodation-details {
        padding: 1rem;
    }

    .accommodation-name {
        font-size: 1.25rem;
        font-weight: 500;
        color: #0c161c;
        margin-bottom: 0.5rem;
    }

    .accommodation-distance {
        color: #4f7a93;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .accommodation-container {
            margin-top: 60px;
            padding: 1rem;
        }

        .search-section h2 {
            font-size: 1.5rem;
        }

        .accommodations-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }
    }
</style>

<div class="accommodation-container">
    <section class="search-section">
        <h2>Stay close to the care you need</h2>
        <p>Use our filters to find the perfect accommodation near your hospital of choice.</p>
        <form class="search-form">
            <input type="text" class="search-input" placeholder="Enter hospital name" />
            <button type="submit" class="search-button">Search</button>
        </form>
    </section>

    <div class="accommodations-grid">
    <?php
    $accommodations = [
        ['name' => "<a href='https://www.booking.com/hotel/lk/ellas-apartments.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Ella's Apartments</a>", 'distance' => '1.6 km from Durdans Hospital', 'image' => 'assets/images/ellas_apartments.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/the-kingsbury-colombo.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>The Kingsbury</a>", 'distance' => '2.3 km from Durdans Hospital', 'image' => 'assets/images/the_kingsbury.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/seaside-suites.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Seaside Suites</a>", 'distance' => '0.9 km from Durdans Hospital', 'image' => 'assets/images/seaside_suites.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/urban-stay-inn.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Urban Stay Inn</a>", 'distance' => '1.2 km from Durdans Hospital', 'image' => 'assets/images/urban_stay_inn.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/galle-face-colombo.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Galle Face Hotel</a>", 'distance' => '3.1 km from Durdans Hospital', 'image' => 'assets/images/cozy_haven_hotel.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/cinnamon-grand-colombo.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Cinnamon Grand Colombo</a>", 'distance' => '2.0 km from Durdans Hospital', 'image' => 'assets/images/palm_grove_retreat.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/shangri-la-colombo.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Shangri-La Colombo</a>", 'distance' => '1.5 km from Durdans Hospital', 'image' => 'assets/images/sunset_villas.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/heritage-residence.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Heritage Residence</a>", 'distance' => '4.2 km from Durdans Hospital', 'image' => 'assets/images/heritage_residence.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/movenpick-colombo.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Mövenpick Hotel Colombo</a>", 'distance' => '1.7 km from Durdans Hospital', 'image' => 'assets/images/azure_apartments.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/lakeview-lodge.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Lakeview Lodge</a>", 'distance' => '3.5 km from Durdans Hospital', 'image' => 'assets/images/lakeview_lodge.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/city-escape.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>City Escape</a>", 'distance' => '2.4 km from Durdans Hospital', 'image' => 'assets/images/city_escape.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/grand-vista.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Grand Vista</a>", 'distance' => '5.0 km from Durdans Hospital', 'image' => 'assets/images/grand_vista.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/tropical-escape.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Tropical Escape</a>", 'distance' => '1.0 km from Durdans Hospital', 'image' => 'assets/images/tropical_escape.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/harbor-heights.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Harbor Heights</a>", 'distance' => '2.7 km from Durdans Hospital', 'image' => 'assets/images/harbor_heights.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/mount-lavinia.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Mount Lavinia Hotel</a>", 'distance' => '4.5 km from Durdans Hospital', 'image' => 'assets/images/mountain_ridge_hotel.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/modern-luxe.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Modern Luxe</a>", 'distance' => '3.2 km from Durdans Hospital', 'image' => 'assets/images/modern_luxe.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/tranquil-inn.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Tranquil Inn</a>", 'distance' => '0.8 km from Durdans Hospital', 'image' => 'assets/images/tranquil_inn.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/the-horizon.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>The Horizon</a>", 'distance' => '1.9 km from Durdans Hospital', 'image' => 'assets/images/the_horizon.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/boutique-stay.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Boutique Stay</a>", 'distance' => '2.9 km from Durdans Hospital', 'image' => 'assets/images/boutique_stay.jpg'],
        ['name' => "<a href='https://www.booking.com/hotel/lk/paradise-suites.en-gb.html' target='_blank' style='color: black; text-decoration: none;'>Paradise Suites</a>", 'distance' => '3.6 km from Durdans Hospital', 'image' => 'assets/images/paradise_suites.jpg']
    ];
    
    foreach ($accommodations as $accommodation) {
        echo '
        <div class="accommodation-card">
            <img src="' . $accommodation['image'] . '" alt="' . $accommodation['name'] . '" class="accommodation-image">
            <div class="accommodation-details">
                <h3 class="accommodation-name">' . $accommodation['name'] . '</h3>
                <p class="accommodation-distance">' . $accommodation['distance'] . '</p>
            </div>
        </div>';
    }
    ?>
</div>
</div>

<?php include 'footer.php'; ?>
