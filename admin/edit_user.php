<?php
// Include database connection
include('./includes/db_connection.php');

// Check if user ID is passed via GET
if (isset($_GET['user_id'])) {

    // Get user_id from the GET request and sanitize
    $user_id = intval($_GET['user_id']); // Sanitize inputs

    // First query to fetch role_name only
    $role_query = "
    SELECT roles.role_name
    FROM users
    LEFT JOIN roles ON users.role_id = roles.role_id
    WHERE users.user_id = $user_id";

    $role_result = $conn->query($role_query);

    if ($role_result->num_rows > 0) {
        $role_data = $role_result->fetch_assoc();
        $role_name = $role_data['role_name']; // Get the role name

        // Now decide which query to run based on the role
        if (in_array($role_name, ['General Doctor', 'Specialist Doctor'])) {
            // Query for doctor (including doctor and hospital related info)
            $query = "
            SELECT users.*, cities.city_name, countries.country_name, roles.role_name, doctors.*, hospitals.name AS hospital_name
            FROM users
            LEFT JOIN cities ON users.city_id = cities.city_id
            LEFT JOIN countries ON users.nationality = countries.country_code
            LEFT JOIN roles ON users.role_id = roles.role_id
            LEFT JOIN doctors ON users.user_id = doctors.user_id
            LEFT JOIN hospitals ON doctors.hospital_id = hospitals.hospital_id
            WHERE users.user_id = $user_id";
        } else {
            // Query for non-doctors (patients, no doctor or hospital related info)
            $query = "
            SELECT users.*, cities.city_name, countries.country_name, roles.role_name
            FROM users
            LEFT JOIN cities ON users.city_id = cities.city_id
            LEFT JOIN countries ON users.nationality = countries.country_code
            LEFT JOIN roles ON users.role_id = roles.role_id
            WHERE users.user_id = $user_id";
        }

        // Execute the appropriate query
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Process the user data here
        } else {
            echo "User not found.";
            exit;
        }
    } else {
        echo "User role not found.";
        exit;
    }


    // Check if the user is a doctor (General Doctor or Specialist Doctor)
    if (in_array($user['role_name'], ['General Doctor', 'Specialist Doctor'])) {
        // Fetch specialization details for doctors
        $specializations_query = "
            SELECT ds.specialization_id, s.name AS specialization_name 
            FROM doctorspecializations ds
            LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
            WHERE ds.doctor_id = $user_id
        ";
        $specializations_result = $conn->query($specializations_query);
        $specializations = [];
        while ($row = $specializations_result->fetch_assoc()) {
            $specializations[] = $row; // Collect all specializations
        }
    }
} else {
    echo "No user ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="./css/edit_profile.css">
</head>

<body>
    <div class="profile-edit-container">
        <h1>Edit Profile</h1>
        <div class="profile-pic-container">
            <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture" class="profile-pic">
            <!-- Edit Icon -->
            <label for="profile_picture" class="edit-icon">
                <i class="fa fa-pencil-alt"></i>
            </label>
            <!-- Hidden File Input -->
            <input type="file" name="profile_picture" id="profile_picture">
        </div>

        <form method="POST" action="edit_profile.php">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">    

            <!-- General User Details -->
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name"
                    value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name"
                    value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    required>
            </div>
            <div>
                <label for="phone_number">Contact Number</label>
                <input type="text" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            </div>
            <div>
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                    value="<?php echo htmlspecialchars($user['address_line1']); ?>">
            </div>
            <div>
                <label for="city">City</label>
                <input type="text" id="city_name" name="city_name"
                    value="<?php echo htmlspecialchars($user['city_name']); ?>">
                <input type="hidden" id="city" name="city" value="<?php echo htmlspecialchars($user['city_id']); ?>">
            </div>
            <div>
                <label for="country">Country</label>
                <input type="text" id="country_name" name="country_name"
                    value="<?php echo htmlspecialchars($user['country_name']); ?>">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="">
            </div>

            <!-- Specialization Details -->
            <?php if (in_array($user['role_name'], ['General Doctor', 'Specialist Doctor'])): ?>

                <!-- Doctor Details -->
                <div class="doctor-details-box">

                    <h2>Doctor Details</h2>
                    <?php if (!empty($specializations)): ?>
                        <?php foreach ($specializations as $specialization): ?>
                            <div>
                                <label
                                    for="specialization_<?php echo $specialization['specialization_id']; ?>">Specialization</label>
                                <input type="text" id="specialization_<?php echo $specialization['specialization_id']; ?>"
                                    name="specializations[]"
                                    value="<?php echo htmlspecialchars($specialization['specialization_name']); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No specialization details found.</p>
                    <?php endif; ?>
                    <div>
                        <label for="license_number">License Number</label>
                        <input type="text" id="license_number" name="license_number"
                            value="<?php echo htmlspecialchars($user['license_number']); ?>">
                    </div>
                    <div>
                        <label for="years_experience">Years of Experience</label>
                        <input type="text" id="years_experience" name="years_experience"
                            value="<?php echo htmlspecialchars($user['years_of_experience']); ?>">
                    </div>

                    <div>
                        <label for="hospital_affiliation">Hospital Affiliation</label>
                        <input type="text" id="hospital_affiliation" name="hospital_affiliation"
                            value="<?php echo htmlspecialchars($user['hospital_name']); ?>" readonly>
                    </div>
                </div>
            <?php endif; ?>

            <div class="last-container">
                <button type="submit" class="save-button">Update Profile</button>
                <button type="submit" name="delete_user" class="delete-button">Delete User</button>
            </div>
        </form>
        <!-- Modal Popup for Error Message -->
        <?php if (isset($city_error)): ?>
            <div id="errorModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <p><?php echo $city_error; ?></p>
                </div>
            </div>
        <?php endif; ?>

        <script>
            // Show the modal if there is a city error
            <?php if (isset($city_error)): ?>
                document.getElementById('errorModal').style.display = "block";
            <?php endif; ?>

            // Function to close the modal
            function closeModal() {
                document.getElementById('errorModal').style.display = "none";
            }
        </script>
    </div>
</body>

</html>