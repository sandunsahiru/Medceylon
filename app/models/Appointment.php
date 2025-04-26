<?php

namespace App\Models;

use App\Services\GoogleMeetService;

class Appointment
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Get doctor appointment by ID
     * 
     * @param int $appointmentId
     * @param int $doctorId
     * @return array|false
     */
    public function getDoctorAppointmentById($appointmentId, $doctorId) {
        try {
            $query = "SELECT a.*, u.first_name, u.last_name, a.session_id
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    WHERE a.appointment_id = ? AND a.doctor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error getting doctor appointment by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get session appointment data
     * 
     * @param int $sessionId
     * @param int $doctorId
     * @return array
     */
    public function getSessionAppointmentData($sessionId, $doctorId = null) {
        try {
            $query = "SELECT a.*, 
                    u.first_name AS patient_first_name,
                    u.last_name AS patient_last_name,
                    dr.first_name AS doctor_first_name,
                    dr.last_name AS doctor_last_name
                    FROM appointments a 
                    JOIN users u ON a.patient_id = u.user_id
                    JOIN doctors d ON a.doctor_id = d.doctor_id
                    JOIN users dr ON d.user_id = dr.user_id
                    WHERE a.session_id = ?";
                    
            if ($doctorId !== null) {
                $query .= " AND a.doctor_id = ?";
            }
            
            $query .= " ORDER BY a.appointment_date DESC LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            
            if ($doctorId !== null) {
                $stmt->bind_param("ii", $sessionId, $doctorId);
            } else {
                $stmt->bind_param("i", $sessionId);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return [];
        } catch (\Exception $e) {
            error_log("Error in getSessionAppointmentData: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get patient appointments specifically for a doctor
     * 
     * @param int $patientId
     * @param int $doctorId
     * @return array
     */
    public function getPatientAppointmentsForDoctor($patientId, $doctorId) {
        try {
            $query = "SELECT 
                    a.appointment_id, 
                    a.appointment_date, 
                    a.appointment_time,
                    a.appointment_status, 
                    a.consultation_type, 
                    a.reason_for_visit,
                    a.meet_link
                    FROM appointments a
                    WHERE a.patient_id = ? AND a.doctor_id = ?
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = [
                    'appointment_id' => $row['appointment_id'],
                    'appointment_date' => date('F j, Y', strtotime($row['appointment_date'])),
                    'appointment_time' => date('g:i A', strtotime($row['appointment_time'])),
                    'status' => $row['appointment_status'],
                    'consultation_type' => $row['consultation_type'],
                    'reason_for_visit' => $row['reason_for_visit'],
                    'meet_link' => $row['meet_link']
                ];
            }
            
            return $appointments;
        } catch (\Exception $e) {
            error_log("Error getting patient appointments for doctor: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get appointment details formatted for doctor
     * 
     * @param int $appointmentId
     * @param int $doctorId
     * @return array|false
     */
    public function getAppointmentDetailsForDoctor($appointmentId, $doctorId) {
        try {
            $query = "SELECT 
                    a.*, 
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.phone_number
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    WHERE a.appointment_id = ? 
                    AND a.doctor_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return false;
            }
            
            $details = $result->fetch_assoc();
            
            // Format date and time for display
            $details['appointment_date'] = date('Y-m-d', strtotime($details['appointment_date']));
            $details['appointment_time'] = date('g:i A', strtotime($details['appointment_time']));
            
            return $details;
        } catch (\Exception $e) {
            error_log("Error getting appointment details for doctor: " . $e->getMessage());
            return false;
        }
    }

    // Get upcoming appointments
    public function getUpcomingAppointments($doctorId, $limit = 10)
    {
        try {
            $query = "SELECT a.*, 
                     p.first_name, p.last_name,
                     p.email, p.phone_number
                     FROM appointments a
                     JOIN users p ON a.patient_id = p.user_id
                     WHERE a.doctor_id = ?
                     AND a.appointment_date >= CURDATE()
                     AND a.appointment_status = 'Scheduled'
                     ORDER BY a.appointment_date ASC, a.appointment_time ASC
                     LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $doctorId, $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting upcoming appointments: " . $e->getMessage());
            throw $e;
        }
    }

    // Update appointment status
    public function updateStatus($appointmentId, $newStatus)
    {
        try {
            $query = "UPDATE appointments 
                     SET appointment_status = ?,
                         updated_at = NOW()
                     WHERE appointment_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $newStatus, $appointmentId);

            if (!$stmt->execute()) {
                throw new \Exception("Failed to update appointment status");
            }

            return true;
        } catch (\Exception $e) {
            error_log("Error updating appointment status: " . $e->getMessage());
            throw $e;
        }
    }

    // Get patient appointments
    public function getPatientAppointments($patientId, $limit = 20)
    {
        try {
            $query = "SELECT 
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.reason_for_visit,
                a.session_id,
                a.meet_link,
                d.doctor_id,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                h.name as hospital_name,
                s.name as specialization
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE a.patient_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting patient appointments: " . $e->getMessage());
            throw $e;
        }
    }

    // Get appointments by doctor ID
    public function getDoctorAppointments($doctorId)
    {
        try {
            $query = "SELECT 
                a.appointment_id, 
                a.appointment_date, 
                a.appointment_time,
                a.appointment_status, 
                a.consultation_type, 
                a.reason_for_visit,
                a.meet_link,
                p.first_name as patient_first_name, 
                p.last_name as patient_last_name,
                p.email as patient_email,
                p.phone_number as patient_phone,
                d.doctor_id,
                u.first_name as doctor_first_name,
                u.last_name as doctor_last_name
                FROM appointments a
                JOIN users p ON a.patient_id = p.user_id
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error getting doctor appointments: " . $e->getMessage());
            throw $e;
        }
    }

    // Get appointment details
    public function getAppointmentDetails($appointmentId)
    {
        try {
            $query = "SELECT 
                a.*,
                d.doctor_id,
                u.first_name as doctor_first_name, 
                u.last_name as doctor_last_name,
                p.first_name as patient_first_name,
                p.last_name as patient_last_name,
                s.name as specialization, 
                h.name as hospital_name,
                orig.appointment_date as previous_date,
                orig.appointment_time as previous_time,
                orig.appointment_status as previous_status
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.doctor_id
                JOIN users u ON d.user_id = u.user_id
                JOIN users p ON a.patient_id = p.user_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN appointments orig ON a.rescheduled_from = orig.appointment_id
                WHERE a.appointment_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $appointmentId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            // Include the meet_link in the response
            return [
                'doctor' => [
                    'id' => $result['doctor_id'],
                    'first_name' => $result['doctor_first_name'],
                    'last_name' => $result['doctor_last_name']
                ],
                'patient' => [
                    'first_name' => $result['patient_first_name'],
                    'last_name' => $result['patient_last_name']
                ],
                'specialization' => $result['specialization'],
                'hospital' => $result['hospital_name'],
                'appointment' => [
                    'date' => date('F j, Y', strtotime($result['appointment_date'])),
                    'time' => date('g:i A', strtotime($result['appointment_time'])),
                    'status' => $result['appointment_status'],
                    'consultation_type' => $result['consultation_type'],
                    'reason_for_visit' => $result['reason_for_visit'],
                    'meet_link' => $result['meet_link']
                ]
            ];
        } catch (\Exception $e) {
            error_log("Error getting appointment details: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get recent appointments for dashboard display (includes all statuses and dates)
     * 
     * @param int $doctorId The doctor ID
     * @param int $limit Maximum number of appointments to return
     * @return array Array of appointment data
     */
    public function getRecentAppointments($doctorId, $limit = 10)
    {
        try {
            error_log("Getting recent appointments for doctor ID: " . $doctorId . " with limit: " . $limit);

            $query = "SELECT a.*, 
                    p.first_name AS patient_first_name, 
                    p.last_name AS patient_last_name,
                    p.email, 
                    p.phone_number
                    FROM appointments a
                    JOIN users p ON a.patient_id = p.user_id
                    WHERE a.doctor_id = ?
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC
                    LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $doctorId, $limit);
            if (!$stmt->execute()) {
                error_log("Query execution failed: " . $stmt->error);
                throw new \Exception("Failed to execute query: " . $stmt->error);
            }

            $result = $stmt->get_result();
            error_log("Found " . $result->num_rows . " recent appointments");

            // Convert to array for easier handling in the view
            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }

            return $appointments;
        } catch (\Exception $e) {
            error_log("Error getting recent appointments: " . $e->getMessage());
            throw $e;
        }
    }

    public function bookAppointment($data)
    {
        $this->db->begin_transaction();
        try {
            error_log("Starting bookAppointment with data: " . print_r($data, true));

            // Check if slot is still available (using preferred_date and appointment_time from form)
            if (isset($data['appointment_time'])) {
                $checkQuery = "SELECT appointment_id FROM appointments 
                            WHERE doctor_id = ? 
                            AND appointment_date = ? 
                            AND appointment_time = ? 
                            AND appointment_status NOT IN ('Canceled', 'Rejected')";

                $checkStmt = $this->db->prepare($checkQuery);
                if (!$checkStmt) {
                    throw new \Exception("Failed to prepare time slot check query: " . $this->db->error);
                }

                $checkDoctorId = $data['doctor_id'];
                $checkDate = $data['preferred_date'];  // From form field
                $checkTime = $data['appointment_time']; // From form field

                error_log("Checking availability for: Doctor=$checkDoctorId, Date=$checkDate, Time=$checkTime");

                $checkStmt->bind_param("iss", $checkDoctorId, $checkDate, $checkTime);
                $checkStmt->execute();

                if ($checkStmt->get_result()->num_rows > 0) {
                    throw new \Exception("This time slot is no longer available");
                }
            }

            // Insert appointment - explicitly convert date/time for DB
            $appointmentDate = $data['preferred_date'];   // From form field preferred_date
            $appointmentTime = $data['appointment_time']; // From form field appointment_time

            // Validate and format date and time
            if (!strtotime($appointmentDate)) {
                throw new \Exception("Invalid date format");
            }

            // Format date in MySQL format
            $formattedDate = date('Y-m-d', strtotime($appointmentDate));

            // Try to format time if it's not already in HH:MM:SS format
            if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $appointmentTime)) {
                // Try to parse and format the time
                $parsedTime = strtotime($appointmentTime);
                if ($parsedTime !== false) {
                    $formattedTime = date('H:i:s', $parsedTime);
                } else {
                    $formattedTime = $appointmentTime; // Keep as is if parsing fails
                }
            } else {
                $formattedTime = $appointmentTime; // Already in correct format
            }

            error_log("Formatted date and time for DB: Date=$formattedDate, Time=$formattedTime");

            // Insert appointment with explicit columns
            $query = "INSERT INTO appointments (
                patient_id, 
                doctor_id,
                appointment_date, 
                appointment_time,
                consultation_type,
                reason_for_visit,
                medical_history,
                appointment_status,
                notes,
                session_id,
                booking_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                throw new \Exception("Database prepare error: " . $this->db->error);
            }

            // Prepare all variables before binding
            $patientId = $data['patient_id'];
            $doctorId = $data['doctor_id'];
            $consultationType = $data['consultation_type'];
            $reasonForVisit = $data['reason_for_visit'] ?? '';
            $medicalHistory = $data['medical_history'] ?? '';
            $status = $data['appointment_status'] ?? 'Scheduled';
            $notes = $data['notes'] ?? null;
            $sessionId = $data['session_id'] ?? null;

            error_log("Binding final parameters: " . print_r([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'date' => $formattedDate,
                'time' => $formattedTime,
                'type' => $consultationType,
                'reason' => $reasonForVisit,
                'history' => $medicalHistory,
                'status' => $status,
                'notes' => $notes,
                'session_id' => $sessionId
            ], true));

            $stmt->bind_param(
                "iisssssssi",
                $patientId,
                $doctorId,
                $formattedDate,    // Use formatted date
                $formattedTime,    // Use formatted time
                $consultationType,
                $reasonForVisit,
                $medicalHistory,
                $status,
                $notes,
                $sessionId
            );

            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                throw new \Exception("Failed to insert appointment: " . $stmt->error);
            }

            $appointmentId = $this->db->insert_id;
            error_log("Successfully created appointment with ID: " . $appointmentId);

            // Create Google Meet link for online appointments
            if ($consultationType === 'Online') {
                error_log("Creating Google Meet link for online appointment");
                $meetLink = $this->createGoogleMeetLink($appointmentId, $patientId, $doctorId, $formattedDate, $formattedTime, $reasonForVisit);

                if ($meetLink) {
                    error_log("Updating appointment with Meet link: " . $meetLink);
                    $updateResult = $this->updateMeetLink($appointmentId, $meetLink);
                    error_log("Update result: " . ($updateResult ? "Success" : "Failed"));
                } else {
                    error_log("Failed to create Google Meet link for appointment ID: " . $appointmentId);
                }
            }

            // Handle documents if present
            if (!empty($data['documents'])) {
                $this->saveAppointmentDocuments($appointmentId, $data['documents']);
            }

            $this->db->commit();
            return $appointmentId;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in bookAppointment: " . $e->getMessage());
            throw $e;
        }
    }

    private function saveAppointmentDocuments($appointmentId, $documents)
    {
        $upload_dir = ROOT_PATH . '/public/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($documents['tmp_name'] as $key => $tmp_name) {
            if ($documents['error'][$key] === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($documents['name'][$key], PATHINFO_EXTENSION));
                $new_name = uniqid('doc_') . '.' . $file_ext;
                $file_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $file_path)) {
                    $query = "INSERT INTO appointmentdocuments 
                            (appointment_id, document_type, file_path) 
                            VALUES (?, ?, ?)";

                    $stmt = $this->db->prepare($query);
                    $db_path = 'uploads/' . $new_name;
                    $docType = $file_ext;
                    $stmt->bind_param("iss", $appointmentId, $docType, $db_path);
                    $stmt->execute();
                }
            }
        }
    }

    /**
     * Update session ID for an appointment
     * 
     * @param int $appointmentId Appointment ID
     * @param int $sessionId Session ID
     * @return bool Success status
     */
    public function updateSessionId($appointmentId, $sessionId)
    {
        try {
            $query = "UPDATE appointments SET session_id = ? WHERE appointment_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $sessionId, $appointmentId);
            
            if (!$stmt->execute()) {
                error_log("Failed to update appointment with session ID: " . $this->db->error);
                return false;
            }
            
            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in updateSessionId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get appointment by ID
     * 
     * @param int $appointmentId
     * @return array|false
     */
    public function getById($appointmentId)
    {
        try {
            $query = "SELECT * FROM appointments WHERE appointment_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $appointmentId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error getting appointment by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a Google Meet link for an appointment
     * 
     * @param int $appointmentId
     * @param int $patientId
     * @param int $doctorId
     * @param string $date
     * @param string $time
     * @param string $reason
     * @return string|false
     */
    private function createGoogleMeetLink($appointmentId, $patientId, $doctorId, $date, $time, $reason)
    {
        try {
            error_log("Creating Google Meet link for appointment ID: {$appointmentId}");
            error_log("Raw input date: {$date}, Raw input time: {$time}");

            // Get doctor and patient details
            $query = "SELECT 
                    CONCAT(d_user.first_name, ' ', d_user.last_name) AS doctor_name,
                    CONCAT(p_user.first_name, ' ', p_user.last_name) AS patient_name,
                    d_user.email AS doctor_email,
                    p_user.email AS patient_email
                    FROM doctors d 
                    JOIN users d_user ON d.user_id = d_user.user_id
                    JOIN users p_user ON p_user.user_id = ?
                    WHERE d.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                error_log("Failed to get doctor and patient details for Meet link creation");
                error_log("Patient ID: {$patientId}, Doctor ID: {$doctorId}");
                return false;
            }

            error_log("Doctor and patient details retrieved: " . json_encode($result));

            // Ensure date is in YYYY-MM-DD format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = date('Y-m-d', strtotime($date));
            }
            
            // Now properly handle the time format - this is critical
            // Check if time is in 12-hour format (with AM/PM)
            if (stripos($time, 'am') !== false || stripos($time, 'pm') !== false) {
                // Convert to 24-hour format
                $time = date('H:i:s', strtotime($time));
            } 
            // Check if time is missing seconds
            else if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                $time = $time . ':00';
            }
            // If it's not in HH:MM:SS format, try to convert it
            else if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                $time = date('H:i:s', strtotime($time));
            }
            
            error_log("Formatted date: {$date}, Formatted time: {$time}");
            
            // Format date and time for Google Calendar
            $startDateTime = $date . ' ' . $time;
            error_log("Combined start date/time: {$startDateTime}");
            
            // Default appointment duration is 30 minutes
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + 30 * 60);
            error_log("Calculated end date/time: {$endDateTime}");

            // Create event summary and description
            $summary = "Medical Appointment: Dr. {$result['doctor_name']} with {$result['patient_name']}";
            $description = "Appointment ID: {$appointmentId}\nReason for visit: {$reason}";

            // Attendee emails
            $attendees = [
                $result['doctor_email'],
                $result['patient_email']
            ];

            error_log("Creating Google Meet with attendees: " . json_encode($attendees));

            // Create Google Meet event
            $googleMeetService = new GoogleMeetService();
            $eventData = $googleMeetService->createMeetEvent(
                $summary,
                $description,
                $startDateTime,
                $endDateTime,
                $attendees
            );

            if ($eventData && isset($eventData['meet_link'])) {
                error_log("Meet link created successfully: " . $eventData['meet_link']);
                return $eventData['meet_link'];
            } else {
                error_log("No meet link returned from GoogleMeetService");
                return false;
            }
        } catch (\Exception $e) {
            error_log("Error creating Google Meet link: " . $e->getMessage());
            error_log("Error stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Update Google Meet link for an appointment
     * 
     * @param int $appointmentId
     * @param string $meetLink
     * @return bool
     */
    public function updateMeetLink($appointmentId, $meetLink) {
        try {
            error_log("Updating appointment {$appointmentId} with Meet link: {$meetLink}");
            $query = "UPDATE appointments SET meet_link = ? WHERE appointment_id = ?";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                error_log("Failed to prepare statement: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("si", $meetLink, $appointmentId);
            
            if (!$stmt->execute()) {
                error_log("Failed to update appointment with Meet link: " . $stmt->error);
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error updating Meet link: " . $e->getMessage());
            return false;
        }
    }
}