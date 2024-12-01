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
    a.appointment_date,
    h.diagnosis,
    h.treatment_plan
    FROM appointments a
    LEFT JOIN healthrecords h ON a.appointment_id = h.appointment_id
    WHERE a.patient_id = ? AND a.doctor_id = ?
    ORDER BY a.appointment_date DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $patient_id, $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $records = [];
    while($row = $result->fetch_assoc()) {
        $records[] = [
            'appointment_date' => date('d/m/Y', strtotime($row['appointment_date'])),
            'diagnosis' => $row['diagnosis'],
            'treatment_plan' => $row['treatment_plan']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($records);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>