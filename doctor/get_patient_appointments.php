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

$query = "SELECT 
    appointment_date,
    appointment_time,
    appointment_status,
    consultation_type,
    reason_for_visit
    FROM appointments 
    WHERE patient_id = ? AND doctor_id = ?
    ORDER BY appointment_date DESC, appointment_time DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $patient_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while($row = $result->fetch_assoc()) {
        $row['appointment_date'] = date('d/m/Y', strtotime($row['appointment_date']));
        $row['appointment_time'] = date('H:i', strtotime($row['appointment_time']));
        $appointments[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($appointments);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>