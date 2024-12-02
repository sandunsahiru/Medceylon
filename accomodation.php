<?php
// Include the footer
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedCeylon - Accommodation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #299d97;
            --secondary-color: #0c161c;
            --text-color: #333;
            --background-color: #f4f7f6;
            --card-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
        }

        .accommodation-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .search-section {
            background-color: white;
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }

        .search-section h2 {
            font-size: 2.25rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            text-align: center;
        }

        .search-section p {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .filter-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-select {
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(41,157,151,0.2);
        }

        .search-form {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
            gap: 1rem;
        }

        .search-input {
            flex-grow: 1;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(41,157,151,0.2);
        }

        .search-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background-color: #237f78;
        }

        .accommodations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .accommodation-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .accommodation-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .accommodation-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .accommodation-card:hover .accommodation-image {
            transform: scale(1.05);
        }

        .accommodation-details {
            padding: 1.25rem;
        }

        .accommodation-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .accommodation-distance {
            color: #6b7280;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                align-items: center;
            }

            .search-form {
                flex-direction: column;
            }

            .accommodations-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Next button in the top-right corner */
        .next-button {
            position: absolute;
            bottom: 10px;
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
</head>
<body>
    <!-- Next button -->
    <a href="transportation.php">
        <button class="next-button">Next</button>
    </a>
    <div class="accommodation-container">
        <section class="search-section">
            <h2>Stay Close to Your Care</h2>
            <p>Find the perfect accommodation near your hospital with our smart search</p>
            
            <div class="filter-container">
                <select id="distance-filter" class="filter-select">
                    <option value="">All Distances</option>
                    <option value="5">Within 5 km</option>
                    <option value="10">Within 10 km</option>
                    <option value="15">Within 15 km</option>
                </select>
            </div>

            <form class="search-form" id="accommodation-search">
                <input type="text" id="hospital-input" class="search-input" placeholder="Enter hospital name" />
                <button type="submit" class="search-button">Search</button>
            </form>
        </section>

        <div id="accommodations-grid" class="accommodations-grid">
            <!-- Accommodations will be dynamically populated -->
        </div>
    </div>

    <script>
        const accommodations = [
            {name: "Ella's Apartments", distance: 1.6, hospitalDistance: 1.6, image: 'assets/images/ellas_apartments.jpg'},
            {name: "The Kingsbury", distance: 2.3, hospitalDistance: 2.3, image: 'assets/images/the_kingsbury.jpg'},
            {name: "Seaside Suites", distance: 0.9, hospitalDistance: 0.9, image: 'assets/images/seaside_suites.jpg'},
            {name: "Urban Stay Inn", distance: 1.2, hospitalDistance: 1.2, image: 'assets/images/urban_stay_inn.jpg'},
            // ... (rest of the accommodations from the original list)
        ];

        function renderAccommodations(filteredAccommodations) {
            const grid = document.getElementById('accommodations-grid');
            grid.innerHTML = '';

            filteredAccommodations.forEach(accommodation => {
                const card = document.createElement('div');
                card.classList.add('accommodation-card');
                card.innerHTML = `
                    <img src="${accommodation.image}" alt="${accommodation.name}" class="accommodation-image">
                    <div class="accommodation-details">
                        <h3 class="accommodation-name">${accommodation.name}</h3>
                        <p class="accommodation-distance">${accommodation.distance} km from Durdans Hospital</p>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        document.getElementById('distance-filter').addEventListener('change', function() {
            const maxDistance = this.value ? parseFloat(this.value) : Infinity;
            const hospitalName = document.getElementById('hospital-input').value.toLowerCase();

            const filteredAccommodations = accommodations.filter(accommodation => 
                accommodation.hospitalDistance <= maxDistance &&
                accommodation.name.toLowerCase().includes(hospitalName)
            );

            renderAccommodations(filteredAccommodations);
        });

        document.getElementById('accommodation-search').addEventListener('submit', function(e) {
            e.preventDefault();
            const hospitalName = document.getElementById('hospital-input').value.toLowerCase();
            const maxDistance = document.getElementById('distance-filter').value 
                ? parseFloat(document.getElementById('distance-filter').value) 
                : Infinity;

            const filteredAccommodations = accommodations.filter(accommodation => 
                accommodation.hospitalDistance <= maxDistance &&
                accommodation.name.toLowerCase().includes(hospitalName)
            );

            renderAccommodations(filteredAccommodations);
        });

        // Initial render
        renderAccommodations(accommodations);
    </script>
</body>
</html>


<?php
// Include the footer
include 'footer.php';
?>