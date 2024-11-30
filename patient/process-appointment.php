<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: book-appointment.php');
    exit;
}

try {
    $conn->begin_transaction();

    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['appointment_date'];
    $time = date('H:i:s', strtotime($_POST['time_slot']));
    $consultation_type = $_POST['consultation_type'];
    $reason = $_POST['reason'];
    $medical_history = $_POST['medical_history'] ?? null;

    // Check if slot is still available
    $check_query = "SELECT appointment_id FROM appointments 
                   WHERE doctor_id = ? 
                   AND appointment_date = ? 
                   AND appointment_time = ? 
                   AND appointment_status NOT IN ('Canceled', 'Rejected')";
    
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("iss", $doctor_id, $date, $time);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        throw new Exception("This time slot is no longer available.");
    }

    // Insert appointment
    $insert_query = "INSERT INTO appointments 
                    (patient_id, doctor_id, appointment_date, appointment_time, 
                     consultation_type, reason_for_visit, medical_history, 
                     appointment_status, booking_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'Scheduled', NOW())";

    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("iisssss", 
        $patient_id, $doctor_id, $date, $time, 
        $consultation_type, $reason, $medical_history
    );
    $insert_stmt->execute();
    $appointment_id = $conn->insert_id;

    // Handle document uploads
    if (!empty($_FILES['documents']['name'][0])) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = $_FILES['documents']['name'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $new_name = uniqid('doc_') . '.' . $file_ext;
                $file_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $file_path)) {
                    $doc_query = "INSERT INTO appointmentdocuments 
                                (appointment_id, document_type, file_path) 
                                VALUES (?, ?, ?)";
                    
                    $doc_stmt = $conn->prepare($doc_query);
                    $db_path = 'uploads/' . $new_name;
                    $doc_stmt->bind_param("iss", $appointment_id, $file_ext, $db_path);
                    $doc_stmt->execute();
                }
            }
        }
    }

    $conn->commit();
    $_SESSION['success_message'] = "Appointment booked successfully!";
    header('Location: index.php');

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "Error booking appointment: " . $e->getMessage();
    header('Location: book-appointment.php');
}
?>