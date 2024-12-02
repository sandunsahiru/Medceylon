<?php
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure user authentication (replace with actual authentication mechanism)
$user_id = 1; 

// Get current date
$current_date = date('Y-m-d');

// Handle plan saving
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_plan'])) {
    $plan_name = mysqli_real_escape_string($conn, $_POST['plan_name'] ?? 'My Travel Plan');
    $selected_locations = $_POST['selected_locations'] ?? [];

    // Validate inputs
    if (empty($plan_name)) {
        $error_message = "Please provide a plan name.";
    } elseif (empty($selected_locations)) {
        $error_message = "Please select at least one location.";
    } else {
        // Start a transaction to ensure data integrity
        mysqli_begin_transaction($conn);

        try {
            // Insert travel plan
            $plan_query = "INSERT INTO travel_plans (user_id, plan_name, created_at) VALUES ('$user_id', '$plan_name', NOW())";
            $plan_result = mysqli_query($conn, $plan_query);
            
            if ($plan_result) {
                $plan_id = mysqli_insert_id($conn);
                
                // Prepare location and date insert
                $location_insert_query = "INSERT INTO plan_locations (plan_id, location_id, start_date, end_date) VALUES ";
                $location_values = [];
                
                foreach ($selected_locations as $index => $location_id) {
                    $start_date = mysqli_real_escape_string($conn, $_POST['start_date'][$index]);
                    $end_date = mysqli_real_escape_string($conn, $_POST['end_date'][$index]);
                    
                    // Validate dates
                    if (empty($start_date) || empty($end_date)) {
                        throw new Exception("Please provide both start and end dates for all selected locations.");
                    }
                    
                    if ($start_date < $current_date) {
                        throw new Exception("Start date cannot be before the current date.");
                    }
                    
                    if ($start_date > $end_date) {
                        throw new Exception("Start date cannot be after end date.");
                    }
                    
                    $location_values[] = "('$plan_id', '" . mysqli_real_escape_string($conn, $location_id) . "', '$start_date', '$end_date')";
                }
                
                $location_insert_query .= implode(',', $location_values);
                
                $location_result = mysqli_query($conn, $location_insert_query);
                
                if ($location_result) {
                    mysqli_commit($conn);
                    $success_message = "Travel plan saved successfully!";
                } else {
                    mysqli_rollback($conn);
                    $error_message = "Failed to save plan locations: " . mysqli_error($conn);
                }
            } else {
                mysqli_rollback($conn);
                $error_message = "Failed to save plan: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_message = $e->getMessage();
        }
    }
}

// Fetch locations with filtering
$sql = "SELECT * FROM travel_locations WHERE 1=1";

// Distance filter
if (isset($_GET['distance'])) {
    switch($_GET['distance']) {
        case '0-50':
            $sql .= " AND distance_from_colombo BETWEEN 0 AND 50";
            break;
        case '51-100':
            $sql .= " AND distance_from_colombo BETWEEN 51 AND 100";
            break;
        case '101-200':
            $sql .= " AND distance_from_colombo BETWEEN 101 AND 200";
            break;
        case '201+':
            $sql .= " AND distance_from_colombo > 200";
            break;
    }
}

// Budget filter
if (isset($_GET['budget'])) {
    $budget = mysqli_real_escape_string($conn, $_GET['budget']);
    $sql .= " AND budget = '$budget'";
}

// Activity type filter
if (isset($_GET['activity_type'])) {
    $activity = mysqli_real_escape_string($conn, $_GET['activity_type']);
    $sql .= " AND activity_type = '$activity'";
}

$result = mysqli_query($conn, $sql);
$locations = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Travel Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleDateModal(locationId) {
            const modal = document.getElementById(`date-modal-${locationId}`);
            modal.classList.toggle('hidden');
        }

        function validateDates(locationId) {
            const startDate = document.getElementById(`start-date-${locationId}`);
            const endDate = document.getElementById(`end-date-${locationId}`);
            const checkbox = document.getElementById(`location-${locationId}`);
            const dateError = document.getElementById(`date-error-${locationId}`);

            const today = new Date().toISOString().split('T')[0];

            if (startDate.value < today) {
                dateError.textContent = "Start date cannot be before today.";
                return false;
            }

            if (startDate.value > endDate.value) {
                dateError.textContent = "Start date cannot be after end date.";
                return false;
            }

            dateError.textContent = "";
            checkbox.checked = true;
            toggleDateModal(locationId);
            return true;
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">Create Your Travel Plan</h1>

        <?php if(isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex space-x-4">
                <select name="distance" class="flex-1 p-2 border rounded">
                    <option value="">Distance from Colombo</option>
                    <option value="0-50">0-50 km</option>
                    <option value="51-100">51-100 km</option>
                    <option value="101-200">101-200 km</option>
                    <option value="201+">201+ km</option>
                </select>

                <select name="budget" class="flex-1 p-2 border rounded">
                    <option value="">Budget</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>

                <select name="activity_type" class="flex-1 p-2 border rounded">
                    <option value="">Activity Type</option>
                    <option value="Adventure">Adventure</option>
                    <option value="Culture">Culture</option>
                    <option value="Nature">Nature</option>
                    <option value="Wellness">Wellness</option>
                </select>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Apply Filters
                </button>
            </div>
        </form>

        <form method="POST">
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($locations as $location): ?>
                    <div class="bg-white p-4 rounded shadow relative">
                        <img src="<?= htmlspecialchars($location['image_url']) ?>" 
                             alt="<?= htmlspecialchars($location['name']) ?>" 
                             class="w-full h-48 object-cover mb-4 rounded">
                        <h3 class="text-xl font-semibold"><?= htmlspecialchars($location['name']) ?></h3>
                        <p>Distance: <?= htmlspecialchars($location['distance_from_colombo']) ?> km</p>
                        <p>Budget: <?= htmlspecialchars($location['budget']) ?></p>
                        <p>Activity: <?= htmlspecialchars($location['activity_type']) ?></p>
                        
                        <input type="checkbox" 
                               id="location-<?= $location['id'] ?>"
                               name="selected_locations[]" 
                               value="<?= htmlspecialchars($location['id']) ?>" 
                               class="hidden"
                               required>
                        
                        <button type="button" 
                                onclick="toggleDateModal(<?= $location['id'] ?>)" 
                                class="w-full bg-blue-500 text-white p-2 rounded mt-2">
                            Add to Plan
                        </button>

                        <!-- Date Selection Modal -->
                        <div id="date-modal-<?= $location['id'] ?>" 
                             class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-xl">
                                <h2 class="text-xl font-bold mb-4">Select Dates for <?= htmlspecialchars($location['name']) ?></h2>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">Start Date:</label>
                                    <input type="date" 
                                           id="start-date-<?= $location['id'] ?>" 
                                           name="start_date[]"
                                           min="<?= $current_date ?>"
                                           class="w-full p-2 border rounded"
                                           required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block mb-2">End Date:</label>
                                    <input type="date" 
                                           id="end-date-<?= $location['id'] ?>" 
                                           name="end_date[]"
                                           min="<?= $current_date ?>"
                                           class="w-full p-2 border rounded"
                                           required>
                                </div>
                                
                                <p id="date-error-<?= $location['id'] ?>" class="text-red-500 mb-4"></p>
                                
                                <div class="flex justify-between">
                                    <button type="button" 
                                            onclick="toggleDateModal(<?= $location['id'] ?>)" 
                                            class="bg-gray-500 text-white px-4 py-2 rounded">
                                        Cancel
                                    </button>
                                    <button type="button" 
                                            onclick="validateDates(<?= $location['id'] ?>)" 
                                            class="bg-green-500 text-white px-4 py-2 rounded">
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6">
                <input type="text" 
                       name="plan_name" 
                       placeholder="Plan Name" 
                       class="w-full p-2 border rounded mb-4"
                       required>
                <button type="submit" 
                        name="save_plan" 
                        class="w-full bg-green-500 text-white p-2 rounded">
                    Save Travel Plan
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <a href="view_plan.php" class="bg-blue-600 text-white px-6 py-3 rounded">
                View My Plans
            </a>
        </div>
    </div>
</body>
</html>