<?php
require_once '../includes/config.php';

$doctor_id = $_GET['doctor_id'] ?? 0;

$query = "SELECT 
    d.*,
    u.email,
    u.phone_number,
    h.name as hospital_name,
    GROUP_CONCAT(s.name) as specializations
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id
    JOIN hospitals h ON d.hospital_id = h.hospital_id
    JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
    JOIN specializations s ON ds.specialization_id = s.specialization_id
    WHERE d.doctor_id = ? AND u.role_id = 3
    GROUP BY d.doctor_id";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();

    header('Content-Type: application/json');
    echo json_encode($doctor);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>