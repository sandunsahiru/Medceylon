<?php
require_once '../includes/config.php';

$appointment_id = $_GET['id'] ?? 0;

try {
    $query = "SELECT 
        a.*,
        u.first_name,
        u.last_name,
        s.name as specialization,
        h.name as hospital_name,
        orig.appointment_date as previous_date,
        orig.appointment_time as previous_time,
        orig.appointment_status as previous_status
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u ON d.user_id = u.user_id
        JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
        JOIN specializations s ON ds.specialization_id = s.specialization_id
        JOIN hospitals h ON d.hospital_id = h.hospital_id
        LEFT JOIN appointments orig ON a.rescheduled_from = orig.appointment_id
        WHERE a.appointment_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $response = [
        'doctor' => [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ],
        'specialization' => $data['specialization'],
        'hospital' => $data['hospital_name'],
        'appointment' => [
            'date' => date('F j, Y', strtotime($data['appointment_date'])),
            'time' => date('g:i A', strtotime($data['appointment_time'])),
            'status' => $data['appointment_status'],
            'consultation_type' => $data['consultation_type'],
            'reason_for_visit' => $data['reason_for_visit']
        ]
    ];

    if ($data['rescheduled_from']) {
        $response['previous_appointment'] = [
            'date' => date('F j, Y', strtotime($data['previous_date'])),
            'time' => date('g:i A', strtotime($data['previous_time'])),
            'status' => $data['previous_status']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>