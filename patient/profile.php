<?php
session_start();
require_once '../includes/config.php';

$user_id = 9;

$query = "SELECT u.*, c.city_name, co.country_name
          FROM users u
          LEFT JOIN cities c ON u.city_id = c.city_id
          LEFT JOIN countries co ON u.nationality = co.country_code
          WHERE u.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get all cities for dropdown
$cities_query = "SELECT * FROM cities ORDER BY city_name";
$cities = $conn->query($cities_query);

// Get all countries for dropdown
$countries_query = "SELECT * FROM countries ORDER BY country_name";
$countries = $conn->query($countries_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - MediCare</title>
    <link rel="stylesheet" href="../assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar same as other pages -->
        <aside class="sidebar">
            <div class="logo">
                <h1>Medceylon</h1>
            </div>

            <nav class="nav-menu">
                <a href="index.php" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="book-appointment.php" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="medical-history.php" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="profile.php" class="nav-item active">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>
            
            <a href="../logout.php" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Profile Settings</h1>
            </header>

            <section class="profile-section">
                <form id="profileForm" action="update-profile.php" method="POST" class="profile-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo $user['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $user['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $user['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Address Line 1</label>
                            <input type="text" name="address_line1" value="<?php echo htmlspecialchars($user['address_line1']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" name="address_line2" value="<?php echo htmlspecialchars($user['address_line2']); ?>">
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <select name="city_id">
                                <option value="">Select City</option>
                                <?php while($city = $cities->fetch_assoc()): ?>
                                    <option value="<?php echo $city['city_id']; ?>" 
                                            <?php echo $user['city_id'] == $city['city_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($city['city_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Nationality</label>
                            <select name="nationality">
                                <option value="">Select Country</option>
                                <?php while($country = $countries->fetch_assoc()): ?>
                                    <option value="<?php echo $country['country_code']; ?>" 
                                            <?php echo $user['nationality'] === $country['country_code'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($country['country_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Passport Number</label>
                            <input type="text" name="passport_number" value="<?php echo htmlspecialchars($user['passport_number']); ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                    </div>
                </form>

                <div class="danger-zone">
                    <h2>Delete Account</h2>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button id="deleteAccount" class="delete-btn">Delete Account</button>
                </div>

                <div id="deleteModal" class="modal">
                    <div class="modal-content">
                        <h2>Are you sure?</h2>
                        <p>This action cannot be undone. All your data will be permanently deleted.</p>
                        <div class="modal-actions">
                            <form action="delete-profile.php" method="POST">
                                <button type="submit" class="confirm-delete-btn">Yes, Delete My Account</button>
                                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        const deleteBtn = document.getElementById('deleteAccount');
        const modal = document.getElementById('deleteModal');

        deleteBtn.onclick = function() {
            modal.style.display = "flex";
        }

        function closeModal() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>