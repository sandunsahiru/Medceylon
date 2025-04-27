<?php

namespace App\Models;

class VPDoctor
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    /**
     * Get doctor ID from user ID
     * 
     * @param int $userId
     * @return array|false
     */
    public function getDoctorIdFromUserId($userId)
    {
        try {
            $query = "SELECT d.doctor_id 
                     FROM doctors d 
                     JOIN users u ON d.user_id = u.user_id 
                     WHERE d.user_id = ? 
                     AND d.is_active = 1 
                     AND u.role_id = 3";  // Assuming role_id 3 is for specialists
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error in getDoctorIdFromUserId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get dashboard statistics
     * 
     * @param int $doctorId
     * @return array
     */
    public function getDashboardStats($doctorId)
{
    try {
        $query = "SELECT 
                COUNT(DISTINCT a.patient_id) as total_patients,
                COUNT(CASE WHEN a.appointment_status = 'Scheduled' THEN 1 END) as scheduled_appointments,
                COUNT(CASE WHEN a.appointment_status = 'Completed' THEN 1 END) as completed_appointments,
                COUNT(CASE WHEN a.appointment_status = 'Asked' THEN 1 END) as pending_appointments,
                COUNT(CASE WHEN a.appointment_status = 'Scheduled' AND a.appointment_date >= CURDATE() THEN 1 END) as upcoming_appointments
                FROM appointments a
                WHERE a.doctor_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stats = $result->fetch_assoc();
            // Add a default value for referral_appointments since we don't have that data
            $stats['referral_appointments'] = 0;
            return $stats;
        }
        
        return [
            'total_patients' => 0,
            'scheduled_appointments' => 0,
            'completed_appointments' => 0,
            'pending_appointments' => 0,
            'upcoming_appointments' => 0,
            'referral_appointments' => 0
        ];
    } catch (\Exception $e) {
        error_log("Error in getDashboardStats: " . $e->getMessage());
        throw $e;
    }
}



    /**
 * Get all appointments for the specialist dashboard
 * 
 * @param int $doctorId
 * @return array
 */
public function getReferralAppointments($doctorId)
{
    try {
        // Modified query without referring_doctor_id
        $query = "SELECT a.*, 
                p.first_name AS patient_first_name, 
                p.last_name AS patient_last_name,
                ms.general_doctor_notes AS referral_notes,
                ms.specialist_notes,
                ms.status AS session_status,
                ms.treatment_plan_id,
                tp.travel_restrictions,
                tp.treatment_description,
                tp.estimated_budget,
                tp.estimated_duration
                FROM appointments a
                JOIN users p ON a.patient_id = p.user_id
                LEFT JOIN medical_sessions ms ON a.session_id = ms.session_id
                LEFT JOIN treatment_plans tp ON ms.treatment_plan_id = tp.plan_id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            // Add treatment plan and session status flags
            $row['specialist_booked'] = true; // Already a specialist appointment
            $row['treatment_plan_created'] = !empty($row['treatment_plan_id']);
            $row['transport_booked'] = false; // Default assumption
            
            // Set default referring doctor info since we don't have that column
            $row['referring_doctor_name'] = 'General Doctor';
            
            $appointments[] = $row;
        }
        
        return $appointments;
    } catch (\Exception $e) {
        error_log("Error in getReferralAppointments: " . $e->getMessage());
        return [];
    }
}

    /**
     * Get regular appointments (non-referrals) for specialist dashboard
     * 
     * @param int $doctorId
     * @return array
     */
    public function getRegularAppointments($doctorId)
    {
        try {
            $query = "SELECT a.*, 
                p.first_name AS patient_first_name, 
                p.last_name AS patient_last_name,
                p.email, 
                p.phone_number
                FROM appointments a
                JOIN users p ON a.patient_id = p.user_id
                WHERE a.doctor_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }

            return $appointments;
        } catch (\Exception $e) {
            error_log("Error in getRegularAppointments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get appointment details for a specific appointment ID
     * 
     * @param int $appointmentId
     * @param int $doctorId
     * @return array|false
     */
    public function getAppointmentDetails($appointmentId, $doctorId)
    {
        try {
            $query = "SELECT a.*,
                    CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                    p.email AS patient_email,
                    p.phone_number AS patient_phone
                    FROM appointments a
                    JOIN users p ON a.patient_id = p.user_id
                    WHERE a.appointment_id = ?
                    AND a.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $details = $result->fetch_assoc();

                // Format date and time for display
                $details['appointment_date_raw'] = $details['appointment_date'];
                $details['appointment_time_raw'] = $details['appointment_time'];
                $details['appointment_date'] = date('F j, Y', strtotime($details['appointment_date']));
                $details['appointment_time'] = date('g:i A', strtotime($details['appointment_time']));

                return $details;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error in getAppointmentDetails: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete an appointment
     * 
     * @param int $appointmentId
     * @param int $doctorId
     * @return bool
     */
    public function completeAppointment($appointmentId, $doctorId)
    {
        try {
            $query = "UPDATE appointments
                    SET appointment_status = 'Completed',
                    updated_at = NOW()
                    WHERE appointment_id = ?
                    AND doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $appointmentId, $doctorId);

            if (!$stmt->execute()) {
                error_log("Failed to complete appointment: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in completeAppointment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel an appointment
     * 
     * @param int $appointmentId
     * @param int $doctorId
     * @param string $reason
     * @return bool
     */
    public function cancelAppointment($appointmentId, $doctorId, $reason)
    {
        try {
            $query = "UPDATE appointments
                    SET appointment_status = 'Canceled',
                    notes = CONCAT(IFNULL(notes, ''), '\nCancellation reason: ', ?),
                    updated_at = NOW()
                    WHERE appointment_id = ?
                    AND doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $reason, $appointmentId, $doctorId);

            if (!$stmt->execute()) {
                error_log("Failed to cancel appointment: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in cancelAppointment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get new appointment requests
     * 
     * @param int $doctorId
     * @return \mysqli_result|bool
     */
    public function getNewAppointmentRequests($doctorId)
    {
        try {
            $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone_number,
                    h.name as hospital_name, a.notes as patient_notes
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                    WHERE a.doctor_id = ? AND a.appointment_status = 'Asked'
                    ORDER BY a.appointment_date, a.appointment_time";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getNewAppointmentRequests: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get scheduled appointments
     * 
     * @param int $doctorId
     * @return \mysqli_result|bool
     */
    public function getScheduledAppointments($doctorId)
    {
        try {
            $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone_number,
                    h.name as hospital_name
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                    WHERE a.doctor_id = ? AND a.appointment_status = 'Scheduled'
                    AND a.appointment_date >= CURRENT_DATE
                    ORDER BY a.appointment_date, a.appointment_time";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getScheduledAppointments: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get appointments by status
     * 
     * @param int $doctorId
     * @param string $status
     * @return \mysqli_result|bool
     */
    public function getAppointmentsByStatus($doctorId, $status)
    {
        try {
            $query = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone_number,
                    h.name as hospital_name, a.notes as patient_notes
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    LEFT JOIN doctors d ON a.doctor_id = d.doctor_id
                    LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                    WHERE a.doctor_id = ? AND a.appointment_status = ?
                    ORDER BY a.appointment_date, a.appointment_time";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("is", $doctorId, $status);
            $stmt->execute();

            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getAppointmentsByStatus: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update appointment status
     * 
     * @param int $appointmentId
     * @param string $status
     * @param string|null $newDate
     * @param string|null $newTime
     * @return bool
     */
    public function updateAppointmentStatus($appointmentId, $status, $newDate = null, $newTime = null)
    {
        try {
            $this->db->begin_transaction();

            if ($status === 'Rescheduled' && $newDate && $newTime) {
                // For rescheduled appointments, we need to update the date and time
                $query = "UPDATE appointments 
                        SET appointment_status = ?, 
                        appointment_date = ?, 
                        appointment_time = ?,
                        updated_at = NOW() 
                        WHERE appointment_id = ?";

                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sssi", $status, $newDate, $newTime, $appointmentId);
            } else {
                // For other status updates
                $query = "UPDATE appointments 
                        SET appointment_status = ?, 
                        updated_at = NOW() 
                        WHERE appointment_id = ?";

                $stmt = $this->db->prepare($query);
                $stmt->bind_param("si", $status, $appointmentId);
            }

            if (!$stmt->execute()) {
                $this->db->rollback();
                error_log("Failed to update appointment status: " . $stmt->error);
                return false;
            }

            $this->db->commit();
            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateAppointmentStatus: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get doctor's availability
     * 
     * @param int $doctorId
     * @return array
     */
    public function getDoctorAvailability($doctorId)
    {
        try {
            $query = "SELECT * FROM doctor_availability 
                    WHERE doctor_id = ? AND is_active = 1
                    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $availability = [];
            while ($row = $result->fetch_assoc()) {
                $availability[] = $row;
            }

            return $availability;
        } catch (\Exception $e) {
            error_log("Error in getDoctorAvailability: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add doctor availability
     * 
     * @param int $doctorId
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function addAvailability($doctorId, $dayOfWeek, $startTime, $endTime)
    {
        try {
            $query = "INSERT INTO doctor_availability 
                    (doctor_id, day_of_week, start_time, end_time, is_active) 
                    VALUES (?, ?, ?, ?, 1)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isss", $doctorId, $dayOfWeek, $startTime, $endTime);

            if (!$stmt->execute()) {
                error_log("Failed to add availability: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in addAvailability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Edit doctor availability
     * 
     * @param int $availabilityId
     * @param int $doctorId
     * @param string $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @return bool
     */
    public function editAvailability($availabilityId, $doctorId, $dayOfWeek, $startTime, $endTime)
    {
        try {
            $query = "UPDATE doctor_availability 
                    SET day_of_week = ?, start_time = ?, end_time = ? 
                    WHERE availability_id = ? AND doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssii", $dayOfWeek, $startTime, $endTime, $availabilityId, $doctorId);

            if (!$stmt->execute()) {
                error_log("Failed to edit availability: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in editAvailability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete doctor availability
     * 
     * @param int $availabilityId
     * @param int $doctorId
     * @return bool
     */
    public function deleteAvailability($availabilityId, $doctorId)
    {
        try {
            $query = "UPDATE doctor_availability 
                    SET is_active = 0 
                    WHERE availability_id = ? AND doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $availabilityId, $doctorId);

            if (!$stmt->execute()) {
                error_log("Failed to delete availability: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in deleteAvailability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get patient statistics
     * 
     * @param int $doctorId
     * @return array
     */
    public function getPatientStats($doctorId)
    {
        try {
            $query = "SELECT 
                    COUNT(DISTINCT a.patient_id) as total_patients,
                    COUNT(CASE WHEN a.appointment_status = 'Completed' THEN 1 END) as total_consultations,
                    COUNT(CASE WHEN YEAR(a.appointment_date) = YEAR(CURRENT_DATE) THEN 1 END) as consultations_this_year,
                    COUNT(CASE WHEN MONTH(a.appointment_date) = MONTH(CURRENT_DATE) AND YEAR(a.appointment_date) = YEAR(CURRENT_DATE) THEN 1 END) as consultations_this_month
                    FROM appointments a
                    WHERE a.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }

            return [
                'total_patients' => 0,
                'total_consultations' => 0,
                'consultations_this_year' => 0,
                'consultations_this_month' => 0
            ];
        } catch (\Exception $e) {
            error_log("Error in getPatientStats: " . $e->getMessage());
            return [
                'total_patients' => 0,
                'total_consultations' => 0,
                'consultations_this_year' => 0,
                'consultations_this_month' => 0
            ];
        }
    }

    /**
     * Get doctor's patients
     * 
     * @param int $doctorId
     * @return \mysqli_result|array
     */
    public function getDoctorPatients($doctorId)
    {
        try {
            $query = "SELECT DISTINCT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.gender,
                    MAX(a.appointment_date) as last_visit,
                    COUNT(a.appointment_id) as visit_count
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    WHERE a.doctor_id = ?
                    GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.gender
                    ORDER BY last_visit DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getDoctorPatients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get patient details
     * 
     * @param int $patientId
     * @param int $doctorId
     * @return array
     */
    public function getPatientDetails($patientId, $doctorId)
    {
        try {
            // Get patient basic info
            $query = "SELECT u.*, 
                    TIMESTAMPDIFF(YEAR, u.date_of_birth, CURDATE()) as age,
                    (SELECT COUNT(*) FROM appointments a WHERE a.patient_id = u.user_id AND a.doctor_id = ?) as total_visits,
                    (SELECT MAX(appointment_date) FROM appointments a WHERE a.patient_id = u.user_id AND a.doctor_id = ?) as last_visit
                    FROM users u
                    WHERE u.user_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iii", $doctorId, $doctorId, $patientId);
            $stmt->execute();
            $patientInfo = $stmt->get_result()->fetch_assoc();

            if (!$patientInfo) {
                return ['error' => 'Patient not found'];
            }

            // Get appointment history
            $query = "SELECT a.*, 
                    GROUP_CONCAT(DISTINCT hr.diagnosis SEPARATOR ', ') as diagnoses,
                    GROUP_CONCAT(DISTINCT hr.symptoms SEPARATOR ', ') as symptoms,
                    GROUP_CONCAT(DISTINCT hr.treatment SEPARATOR ', ') as treatments
                    FROM appointments a
                    LEFT JOIN healthrecords hr ON a.appointment_id = hr.appointment_id
                    WHERE a.patient_id = ? AND a.doctor_id = ?
                    GROUP BY a.appointment_id
                    ORDER BY a.appointment_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = [
                    'appointment_id' => $row['appointment_id'],
                    'date' => date('M d, Y', strtotime($row['appointment_date'])),
                    'time' => date('h:i A', strtotime($row['appointment_time'])),
                    'status' => $row['appointment_status'],
                    'type' => $row['consultation_type'],
                    'diagnoses' => $row['diagnoses'],
                    'symptoms' => $row['symptoms'],
                    'treatments' => $row['treatments']
                ];
            }

            return [
                'patient' => $patientInfo,
                'appointments' => $appointments
            ];
        } catch (\Exception $e) {
            error_log("Error in getPatientDetails: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get patient's basic information
     * 
     * @param int $patientId
     * @return array|false
     */
    public function getPatientBasicInfo($patientId)
    {
        try {
            $query = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone_number, u.gender, u.date_of_birth 
                    FROM users u
                    WHERE u.user_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $patientInfo = $result->fetch_assoc();

                // Calculate age if date of birth is available
                if ($patientInfo['date_of_birth']) {
                    $dob = new \DateTime($patientInfo['date_of_birth']);
                    $now = new \DateTime();
                    $patientInfo['age'] = $dob->diff($now)->y;
                }

                return $patientInfo;
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error in getPatientBasicInfo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get patient's medical reports
     * 
     * @param int $patientId
     * @param int $doctorId
     * @return array
     */
    public function getPatientMedicalReports($patientId, $doctorId)
    {
        try {
            $query = "SELECT mr.* 
                    FROM medical_reports mr
                    JOIN appointments a ON mr.patient_id = a.patient_id
                    WHERE mr.patient_id = ? AND a.doctor_id = ?
                    GROUP BY mr.report_id
                    ORDER BY mr.upload_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $reports = [];
            while ($row = $result->fetch_assoc()) {
                $reports[] = [
                    'report_id' => $row['report_id'],
                    'report_name' => $row['report_name'] ?? 'Medical Report',
                    'report_type' => $row['report_type'] ?? 'General',
                    'description' => $row['description'],
                    'upload_date' => date('F j, Y', strtotime($row['upload_date'])),
                    'file_path' => 'uploads/medical-reports/' . basename($row['file_path'])
                ];
            }

            return $reports;
        } catch (\Exception $e) {
            error_log("Error in getPatientMedicalReports: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get doctor profile
     * 
     * @param int $userId
     * @return array|false
     */
    public function getDoctorProfile($userId)
    {
        try {
            $query = "SELECT d.*, u.first_name, u.last_name, u.email, u.phone_number,
                    u.address_line1, u.address_line2, u.city_id, u.date_of_birth,
                    h.name as hospital_name
                    FROM doctors d
                    JOIN users u ON d.user_id = u.user_id
                    LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                    WHERE d.user_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }

            return false;
        } catch (\Exception $e) {
            error_log("Error in getDoctorProfile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update doctor profile
     * 
     * @param int $doctorId
     * @param array $data
     * @return bool
     */
    public function updateProfile($doctorId, $data)
    {
        try {
            $this->db->begin_transaction();

            // Update doctors table
            $query = "UPDATE doctors 
                    SET qualifications = ?, 
                    years_of_experience = ?, 
                    profile_description = ?, 
                    hospital_id = ?
                    WHERE doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "sisii",
                $data['qualifications'],
                $data['experience'],
                $data['description'],
                $data['hospital_id'],
                $doctorId
            );

            if (!$stmt->execute()) {
                $this->db->rollback();
                error_log("Failed to update doctor profile: " . $stmt->error);
                return false;
            }

            // Handle specializations update
            if (isset($data['specializations'])) {
                // Delete existing specializations
                $deleteQuery = "DELETE FROM doctorspecializations WHERE doctor_id = ?";
                $stmt = $this->db->prepare($deleteQuery);
                $stmt->bind_param("i", $doctorId);
                $stmt->execute();

                // Add new specializations
                if (!empty($data['specializations'])) {
                    $insertQuery = "INSERT INTO doctorspecializations (doctor_id, specialization_id) VALUES (?, ?)";
                    $stmt = $this->db->prepare($insertQuery);

                    foreach ($data['specializations'] as $specializationId) {
                        $stmt->bind_param("ii", $doctorId, $specializationId);
                        $stmt->execute();
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateProfile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create treatment plan
     * 
     * @param array $data
     * @return int|false Plan ID if successful, false otherwise
     */
    public function createTreatmentPlan($data)
{
    try {
        $this->db->begin_transaction();

        $query = "INSERT INTO treatment_plans 
                 (session_id, doctor_id, travel_restrictions, vehicle_type, 
                  arrival_deadline, treatment_description, 
                  estimated_budget, estimated_duration, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "iisssssis",
            $data['session_id'],
            $data['doctor_id'],
            $data['travel_restrictions'],
            $data['vehicle_type'],
            $data['arrival_deadline'],
            $data['treatment_description'],
            $data['estimated_budget'],
            $data['estimated_duration'],
            $data['created_at']
        );

        if (!$stmt->execute()) {
            $this->db->rollback();
            error_log("Failed to create treatment plan: " . $stmt->error);
            return false;
        }

        $planId = $this->db->insert_id;
        $this->db->commit();

        return $planId;
    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("Error in createTreatmentPlan: " . $e->getMessage());
        return false;
    }
}

    /**
     * Update treatment plan
     * 
     * @param array $data
     * @return bool
     */
    public function updateTreatmentPlan($data)
    {
        try {
            $query = "UPDATE treatment_plans 
                     SET travel_restrictions = ?, 
                         treatment_description = ?, 
                         estimated_budget = ?, 
                         estimated_duration = ?, 
                         updated_at = ? 
                     WHERE plan_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "sssssi",
                $data['travel_restrictions'],
                $data['treatment_description'],
                $data['estimated_budget'],
                $data['estimated_duration'],
                $data['updated_at'],
                $data['plan_id']
            );

            if (!$stmt->execute()) {
                error_log("Failed to update treatment plan: " . $stmt->error);
                return false;
            }

            return $stmt->affected_rows > 0;
        } catch (\Exception $e) {
            error_log("Error in updateTreatmentPlan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Request medical test
     * 
     * @param array $data
     * @return int|false Test ID if successful, false otherwise
     */
    public function requestMedicalTest($data)
    {
        try {
            $this->db->begin_transaction();

            $query = "INSERT INTO medical_tests 
                     (session_id, patient_id, doctor_id, test_type, 
                      test_description, requires_fasting, urgency, status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "iiissssss",
                $data['session_id'],
                $data['patient_id'],
                $data['doctor_id'],
                $data['test_type'],
                $data['test_description'],
                $data['requires_fasting'],
                $data['urgency'],
                $data['status'],
                $data['created_at']
            );

            if (!$stmt->execute()) {
                $this->db->rollback();
                error_log("Failed to request medical test: " . $stmt->error);
                return false;
            }

            $testId = $this->db->insert_id;
            $this->db->commit();

            return $testId;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in requestMedicalTest: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all specializations
     * 
     * @return array
     */
    public function getSpecializations()
    {
        try {
            $query = "SELECT specialization_id, name 
                     FROM specializations 
                     ORDER BY name";

            $result = $this->db->query($query);
            if (!$result) {
                throw new \Exception("Failed to fetch specializations");
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getSpecializations: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get doctor's specializations
     * 
     * @param int $doctorId
     * @return array
     */
    public function getDoctorSpecializations($doctorId)
    {
        try {
            $query = "SELECT s.specialization_id, s.name
                     FROM doctorspecializations ds
                     JOIN specializations s ON ds.specialization_id = s.specialization_id
                     WHERE ds.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getDoctorSpecializations: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all hospitals
     * 
     * @return array
     */
    public function getHospitals()
    {
        try {
            $query = "SELECT hospital_id, name 
                    FROM hospitals 
                    WHERE is_active = 1 
                    ORDER BY name";

            $result = $this->db->query($query);
            if (!$result) {
                throw new \Exception("Failed to fetch hospitals");
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getHospitals: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Debug appointments query
     * 
     * @param int $doctorId
     * @return array|null
     */
    public function debugAppointments($doctorId)
    {
        try {
            $query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN appointment_status = 'Asked' THEN 1 ELSE 0 END) as asked,
            SUM(CASE WHEN appointment_status = 'Scheduled' THEN 1 ELSE 0 END) as scheduled,
            SUM(CASE WHEN appointment_status = 'Rescheduled' THEN 1 ELSE 0 END) as rescheduled
            FROM appointments 
            WHERE doctor_id = ?
            AND appointment_date >= CURRENT_DATE";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in debugAppointments: " . $e->getMessage());
            return null;
        }
    }
}
