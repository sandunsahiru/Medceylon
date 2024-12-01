<?php
require_once '../includes/config.php';

if (!isset($conn)) {
    die(json_encode(['error' => 'Database connection failed']));
}

$patient_id = $_GET['patient_id'] ?? 0;
$doctor_id = $_GET['doctor_id'] ?? 0;

if (!$patient_id || !$doctor_id) {
    die(json_encode(['error' => 'Invalid parameters']));
}

try {
    // Get user details
    $user_query = "SELECT 
        first_name, last_name, email, phone_number, 
        gender, date_of_birth, address_line1, address_line2,
        nationality
        FROM users 
        WHERE user_id = ?";
    
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // Get appointments with medical history
    $appointments_query = "SELECT 
        appointment_date,
        appointment_time,
        appointment_status,
        consultation_type,
        reason_for_visit,
        notes,
        medical_history
        FROM appointments 
        WHERE patient_id = ? AND doctor_id = ?
        ORDER BY appointment_date DESC, appointment_time DESC";

    $stmt = $conn->prepare($appointments_query);
    $stmt->bind_param("ii", $patient_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode([
        'user' => $user,
        'appointments' => $appointments
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>