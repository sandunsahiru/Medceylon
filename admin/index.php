<?php
$page = "user_management";
include "./includes/header.php";
?>

<body>
    <link rel="stylesheet" href="./css/index.css">
    <?php include "./includes/navbar.php" ?>

    <div class="main-content">
        <header>
            <h1>User Management</h1>
        </header>

        <div class="content">
            <div class="tabs">
                <button onclick="navigate('doctors')">Doctors</button>
                <button onclick="navigate('patients')">Patients</button>
                <button onclick="navigate('hospitals')">Hospitals</button>
                <button onclick="navigate('caretakers')">Caretakers</button>
            </div>

            <div class="search-and-actions">
                <form method="GET">
                    <input type="text" placeholder="Search here...">
                    <button type="submit">Search</button>
                </form>
            </div>

            <hr style="margin: 40px;">

            <div class="actions">
                <button class="add-btn" onclick="navigateToAddUser()">New User</button>
            </div>


            <div class="user-list">
                <?php
                // Determine the table to query based on the page parameter
                $page = $_GET['page'] ?? 'doctors'; // Default to 'doctors' if no page is specified
                $tableMap = [
                    'doctors' => 'doctors_table',
                    'patients' => 'users',
                    'hospitals' => 'hospitals',
                    'caretakers' => 'caretakers_table'
                ];

                $table = $tableMap[$page] ?? 'doctors_table'; // Default to 'doctors_table' if invalid page
                
                // Query to get patients
                if ($page === 'patients') {
                    $query = "SELECT users.first_name, users.last_name, users.gender, users.user_id, 
                  FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) AS age
                  FROM users
                  JOIN roles ON users.role_id = roles.role_id
                  WHERE roles.role_name = 'Patient' AND users.is_active = 1;";
                }

                // Query to get doctors
                elseif ($page === 'doctors') {
                    $query = "SELECT 
                            users.first_name, users.last_name, roles.role_name, 
                            COALESCE(specializations.name, 'No Specialization') AS specialization_name, 
                            users.user_id
                            FROM users JOIN roles 
                            ON users.role_id = roles.role_id
                            LEFT JOIN doctorspecializations 
                            ON users.user_id = doctorspecializations.doctor_id
                            LEFT JOIN specializations 
                            ON doctorspecializations.specialization_id = specializations.specialization_id
                            WHERE roles.role_name IN ('Specialist Doctor', 'General Doctor') AND users.is_active = 1;";
                }

                // Query to get hospitals (example)
                elseif ($page === 'hospitals') {
                    $query = "SELECT hospitals.hospital_id,
                            hospitals.name AS hospital_name, 
                            cities.city_name AS city_name
                            FROM 
                            hospitals JOIN cities ON hospitals.city_id = cities.city_id;";
                }

                /*// Query to get caretakers (example)
                elseif ($page === 'caretakers') {
                    $query = "SELECT first_name, last_name, role_name, caretaker_id 
                  FROM caretakers_table";
                } */

                // Execute the query
                $result = $conn->query($query);

                // Check if there are any results
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Display user information
                        echo '<div class="profile-card">';
                        echo '<div class="profile-header">';


                        // If it's a patient, show age and gender
                        if ($page === 'patients') {
                            echo '<img src="' . $row['profile_picture'] . '" alt="' . $row['first_name'] . '" class="profile-img">';
                            echo '<h2>' . $row['first_name'] . ' ' . $row['last_name'] . '</h2>';
                            echo '</div>';
                            echo '<div class="profile-details">';
                            echo '<div class="detail">';
                            echo '<h3>' . $row['gender'] . '</h3>';
                            echo '<p>Patient Gender</p>';
                            echo '</div>';
                            echo '<div class="detail">';
                            echo '<h3>' . $row['age'] . '</h3>';
                            echo '<p>Years Old</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<button class="profile-button" onclick="navigateprofile(' . $row['user_id'] . ')">View Profile</button>';
                            echo '</div>';
                        }
                        // If it's a doctor, show role and specialization
                        elseif ($page === 'doctors') {
                            echo '<img src="' . $row['profile_picture'] . '" alt="' . $row['first_name'] . '" class="profile-img">';
                            echo '<h2>' . $row['first_name'] . ' ' . $row['last_name'] . '</h2>';
                            echo '</div>';
                            echo '<div class="profile-details">';
                            echo '<div class="detail">';
                            echo '<h3>' . $row['role_name'] . '</h3>';
                            echo '<p>Role</p>';
                            echo '</div>';
                            echo '<div class="detail">';
                            echo '<h3>' . $row['specialization_name'] . '</h3>';
                            echo '<p>Specialization</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<button class="profile-button" onclick="navigateprofile(' . $row['user_id'] . ')">View Profile</button>';
                            echo '</div>';
                        }
                        // If it's a hospital, show hospital name and city
                        elseif ($page === 'hospitals') {
                            echo '<img src="' . $row['profile_picture'] . '" alt="' . $row['hospital_name'] . '" class="profile-img">';
                            echo '<h2>' . $row['hospital_name'] . '</h2>';
                            echo '</div>';
                            echo '<div class="profile-details">';
                            echo '<div class="detail">';
                            echo '<h3>' . $row['city_name'] . '</h3>';
                            echo '<p>City</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<button class="profile-button" onclick="navigateprofile(' . $row['hospital_id'] . ')">View Profile</button>';
                            echo '</div>';
                        }

                    }
                } else {
                    echo '<p>No records found.</p>';
                }

                $conn->close();
                ?>
            </div>

        </div>
    </div>
    
    </div>
</body>

</html>

<script>

    function navigate(page) {
        window.location.href = `?page=${page}`;
    }

    function navigateprofile(user_id) {
        window.location.href = `edit_user.php?user_id=${user_id}`;
        console.log(user_id);
    }

    function navigateToAddUser() {
        window.location.href = 'register.php';
    }
</script>