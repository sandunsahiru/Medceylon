<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $specialist_id = $_POST['specialist_id'];
   $patient_id = $_POST['patient_id'];
   $consultation_type = $_POST['consultation_type'];
   $preferred_date = $_POST['preferred_date'];
   $medical_history = $_POST['medical_history'];
   $referring_doctor_id = 1;

   try {
       $stmt = $conn->prepare("INSERT INTO appointments (
           patient_id, 
           doctor_id,
           appointment_date,
           appointment_status,
           consultation_type,
           medical_history,
           notes
       ) VALUES (?, ?, ?, 'Asked', ?, ?, ?)");
       
       $notes = "Referred by Doctor ID: " . $referring_doctor_id;
       
       $stmt->bind_param("iissss", 
           $patient_id,
           $specialist_id,
           $preferred_date,
           $consultation_type,
           $medical_history,
           $notes
       );
       
       $stmt->execute();
       header("Location: all-doctors.php?booking=success");
   } catch (Exception $e) {
       header("Location: all-doctors.php?booking=error&message=" . urlencode($e->getMessage()));
   }
}
?>