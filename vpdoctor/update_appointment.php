<?php
session_start();
require_once '../includes/config.php';

if(isset($_GET['id']) && isset($_GET['status'])) {
    $appointment_id = intval($_GET['id']);
    $status = $_GET['status'];
    
    if($status === 'Rescheduled') {
        $query = "UPDATE appointments 
                 SET appointment_status = ?, 
                     appointment_date = ?,
                     appointment_time = ?
                 WHERE appointment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $status, $_GET['new_date'], $_GET['new_time'], $appointment_id);
    } else {
        $query = "UPDATE appointments 
                 SET appointment_status = ? 
                 WHERE appointment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $appointment_id);
    }
    
    $stmt->execute();
    $stmt->close();
}

header('Location: appointments.php');
exit;
?>