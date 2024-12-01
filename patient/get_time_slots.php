<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

$doctor_id = $_POST['doctor_id'];
$date = $_POST['date'];
$day_of_week = date('l', strtotime($date));

// Get doctor's availability for the selected day
$avail_query = "SELECT start_time, end_time, time_slot_duration 
                FROM doctor_availability 
                WHERE doctor_id = ? 
                AND day_of_week = ? 
                AND is_active = 1";

$avail_stmt = $conn->prepare($avail_query);
$avail_stmt->bind_param("is", $doctor_id, $day_of_week);
$avail_stmt->execute();
$availability_result = $avail_stmt->get_result();

if ($availability_result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

// Get booked appointments for the date
$booked_query = "SELECT appointment_time 
                 FROM appointments 
                 WHERE doctor_id = ? 
                 AND appointment_date = ? 
                 AND appointment_status NOT IN ('Canceled', 'Rejected')";

$booked_stmt = $conn->prepare($booked_query);
$booked_stmt->bind_param("is", $doctor_id, $date);
$booked_stmt->execute();
$booked_result = $booked_stmt->get_result();

$booked_slots = [];
while ($row = $booked_result->fetch_assoc()) {
    // Add the booked time and next 30 minutes
    $start_time = strtotime($row['appointment_time']);
    $booked_slots[] = date('H:i:s', $start_time);
}

$available_slots = [];

while ($availability = $availability_result->fetch_assoc()) {
    $start = strtotime($availability['start_time']);
    $end = strtotime($availability['end_time']);
    $slot_duration = 30 * 60; // 30 minutes in seconds

    for ($time = $start; $time < $end; $time += $slot_duration) {
        $current_slot = date('H:i:s', $time);
        
        // Check if this slot is not booked
        if (!in_array($current_slot, $booked_slots)) {
            $available_slots[] = date('g:i A', $time);
        }
    }
}

header('Content-Type: application/json');
echo json_encode($available_slots);
?>
