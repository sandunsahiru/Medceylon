<?php
// Include database connection
include('./includes/db_connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the delete button was clicked
    if (isset($_POST['delete_user'])) {
        $user_id = intval($_POST['user_id']); // Sanitize user ID
        echo "Received user_id: " . $user_id . "<br>";

        // Verify if the user exists and their role
        $check_user_query = "
            SELECT users.user_id, roles.role_name 
            FROM users
            JOIN roles ON users.role_id = roles.role_id
            WHERE users.user_id = $user_id
        ";
        $check_user_result = $conn->query($check_user_query);

        if ($check_user_result->num_rows > 0) {
            $user_data = $check_user_result->fetch_assoc();
            if ($user_data['role_name'] === 'Patient') {
                echo "Deleting patient with user_id: " . $user_id . "<br>";
            } elseif (in_array($user_data['role_name'], ['General Doctor', 'Specialist Doctor'])) {
                echo "Deleting doctor with user_id: " . $user_id . "<br>";
            } else {
                echo "Deleting other user type with user_id: " . $user_id . "<br>";
            }

            // Soft delete logic
            $delete_query = "UPDATE users SET is_active = 0 WHERE user_id = $user_id";
            if ($conn->query($delete_query)) {
                header("Location: index.php");
                exit;
            } else {
                echo "Error deactivating user: " . $conn->error;
                exit;
            }
        } else {
            echo "Error: User not found.<br>";
            exit;
        }


    } else {
        // Retrieve the city name from the form input
        $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);

        // Query to get the city_id based on the city name
        $city_query = "SELECT city_id FROM cities WHERE city_name = '$city_name' LIMIT 1";
        $city_result = $conn->query($city_query);

        if ($city_result->num_rows > 0) {
            // Fetch the city_id from the result
            $city_data = $city_result->fetch_assoc();
            $city_id = $city_data['city_id'];
        } else {
            $city_error = "City does not exist in the database.";
        }
        // Update logic for Both Doctors and Patients
        $user_id = intval($_POST['user_id']); // Sanitize user ID
        $first_name = $conn->real_escape_string($_POST['first_name']);
        $last_name = $conn->real_escape_string($_POST['last_name']);
        $email = $conn->real_escape_string($_POST['email']);
        $phone_number = $conn->real_escape_string($_POST['phone_number']);
        $address = $conn->real_escape_string($_POST['address']);
        $city = $city_id; // Assuming city is a numeric ID
        $nationality = $conn->real_escape_string($_POST['country']); // Primary key in countries table
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

        // Doctor-specific fields
        $license_number = isset($_POST['license_number']) ? $conn->real_escape_string($_POST['license_number']) : null;
        $years_experience = isset($_POST['years_experience']) ? intval($_POST['years_experience']) : null;
        $hospital_affiliation = isset($_POST['hospital_affiliation']) ? $conn->real_escape_string($_POST['hospital_affiliation']) : null;

        // Specializations (array of specialization IDs or names)
        $specializations = isset($_POST['specializations']) ? $_POST['specializations'] : [];

        // Determine the user's role
        $role_query = "SELECT roles.role_name FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.user_id = $user_id";
        $role_result = $conn->query($role_query);

        if ($role_result->num_rows > 0) {
            $role_row = $role_result->fetch_assoc();
            $role_name = $role_row['role_name'];
        } else {
            echo "Error: User role not found.";
            exit;
        }

        // Check if the nationality exists in the `countries` table
        $country_query = "SELECT country_name FROM countries WHERE country_code = '$nationality'";
        $country_result = $conn->query($country_query);

        if ($country_result->num_rows == 0) {
            echo "Error: The nationality '$nationality' does not exist in the countries table.";
            exit;
        }

        // Build the update query for the `users` table
        $update_query = "UPDATE users SET 
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email', 
            phone_number = '$phone_number', 
            address_line1 = '$address', 
            city_id = $city, 
            nationality = '$nationality'";

        // Include password only if it is provided
        if ($password) {
            $update_query .= ", password = '$password'";
        }

        $update_query .= " WHERE user_id = $user_id";

        if (!$conn->query($update_query)) {
            echo "Error updating user details: " . $conn->error;
            exit;
        }

        // If the user is a doctor, update doctor-specific details
        if (in_array($role_name, ['General Doctor', 'Specialist Doctor'])) {
            // Get hospital_id from the `hospitals` table based on hospital name
            $hospital_query = "SELECT hospital_id FROM hospitals WHERE name = '$hospital_affiliation'";
            $hospital_result = $conn->query($hospital_query);

            if ($hospital_result->num_rows > 0) {
                $hospital_row = $hospital_result->fetch_assoc();
                $hospital_id = $hospital_row['hospital_id'];
            } else {
                $hospital_id = null; // Null if the hospital does not exist
            }

            // Update the `doctors` table
            $doctor_query = "UPDATE doctors SET 
                license_number = '$license_number', 
                years_of_experience = $years_experience, 
                hospital_id = " . ($hospital_id ? $hospital_id : "NULL") . "
                WHERE user_id = $user_id";

            if (!$conn->query($doctor_query)) {
                echo "Error updating doctor details: " . $conn->error;
                exit;
            }

            // Update specializations for the doctor
            if (!empty($specializations)) {
                // First, delete existing specializations for the doctor
                $delete_specializations_query = "DELETE FROM doctorspecializations WHERE doctor_id = $user_id";
                if (!$conn->query($delete_specializations_query)) {
                    echo "Error deleting old specializations: " . $conn->error;
                    exit;
                }

                // Insert the updated specializations
                foreach ($specializations as $specialization) {
                    $specialization = $conn->real_escape_string($specialization);

                    // Check if the specialization exists in the `specializations` table
                    $specialization_query = "SELECT specialization_id FROM specializations WHERE name = '$specialization'";
                    $specialization_result = $conn->query($specialization_query);

                    if ($specialization_result->num_rows > 0) {
                        $specialization_row = $specialization_result->fetch_assoc();
                        $specialization_id = $specialization_row['specialization_id'];

                        // Insert the specialization for the doctor
                        $insert_specialization_query = "INSERT INTO doctorspecializations (doctor_id, specialization_id) VALUES ($user_id, $specialization_id)";
                        if (!$conn->query($insert_specialization_query)) {
                            echo "Error inserting specialization: " . $conn->error;
                            exit;
                        }
                    } else {
                        echo "Error: The specialization '$specialization' does not exist in the database.";
                        exit;
                    }
                }
            }
        }

        // Redirect to the index page after successful update
        header("Location: index.php");
        exit;
    }
} else {
    echo "Invalid request.";
}
?>