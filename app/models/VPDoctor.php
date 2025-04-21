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

    // Get dashboard statistics for specialist doctor
    public function getDashboardStats($doctorId)
    {
        try {
            $query = "SELECT 
                COUNT(DISTINCT a.patient_id) as total,
                SUM(CASE WHEN u.gender = 'Female' THEN 1 ELSE 0 END) as women,
                SUM(CASE WHEN u.gender = 'Male' THEN 1 ELSE 0 END) as men,
                (SELECT COUNT(*) FROM appointments 
                 WHERE doctor_id = ? AND appointment_status = 'Scheduled') as scheduled,
                (SELECT COUNT(*) FROM appointments 
                 WHERE doctor_id = ? AND appointment_status = 'Asked') as pending
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id 
                WHERE a.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("iii", $doctorId, $doctorId, $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDashboardStats: " . $e->getMessage());
            throw $e;
        }
    }

    // Get new appointment requests
    public function getNewAppointmentRequests($doctorId)
    {
        try {
            $query = "SELECT 
                u.first_name, 
                u.last_name,
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.reason_for_visit as patient_notes,
                a.consultation_type
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ? 
                AND a.appointment_status = 'Asked'
                ORDER BY a.appointment_date, a.appointment_time
                LIMIT 5";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getNewAppointmentRequests: " . $e->getMessage());
            throw $e;
        }
    }

    // Get scheduled appointments
    public function getScheduledAppointments($doctorId)
    {
        try {
            $query = "SELECT 
                u.first_name, 
                u.last_name,
                a.appointment_date,
                a.appointment_time,
                a.consultation_type
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ?
                AND a.appointment_status = 'Scheduled'
                ORDER BY a.appointment_date, a.appointment_time
                LIMIT 5";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getScheduledAppointments: " . $e->getMessage());
            throw $e;
        }
    }

    // Get appointments by status
    // In VPDoctor.php - Updated getAppointmentsByStatus method

    public function getAppointmentsByStatus($doctorId, $status)
    {
        try {
            // First verify the doctor exists
            $checkDoctor = "SELECT doctor_id FROM doctors WHERE doctor_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($checkDoctor);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $doctorExists = $stmt->get_result()->fetch_assoc();

            if (!$doctorExists) {
                error_log("No active doctor found with ID: " . $doctorId);
                throw new \Exception("Invalid doctor ID");
            }

            $query = "SELECT 
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.appointment_status,
                a.consultation_type,
                a.reason_for_visit,
                u.first_name,
                u.last_name,
                u.phone_number,
                u.email
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ? 
                AND a.appointment_status = ?
                AND a.appointment_date >= CURRENT_DATE
                ORDER BY a.appointment_date ASC, a.appointment_time ASC";

            error_log("Executing appointment query for doctor_id: " . $doctorId . " and status: " . $status);

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("is", $doctorId, $status);
            $stmt->execute();

            $result = $stmt->get_result();
            error_log("Found " . $result->num_rows . " appointments");

            return $result;
        } catch (\Exception $e) {
            error_log("Error in getAppointmentsByStatus: " . $e->getMessage());
            throw $e;
        }
    }

    // Add debugging function
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

    // Get doctor availability slots
    public function getDoctorAvailability($doctorId)
    {
        try {
            $query = "SELECT * FROM doctor_availability 
                     WHERE doctor_id = ?
                     ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 
                                'Thursday', 'Friday', 'Saturday', 'Sunday')";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getDoctorAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    // Add availability slot
    public function addAvailability($doctorId, $dayOfWeek, $startTime, $endTime)
    {
        try {
            $query = "INSERT INTO doctor_availability 
                     (doctor_id, day_of_week, start_time, end_time) 
                     VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isss", $doctorId, $dayOfWeek, $startTime, $endTime);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in addAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    // Edit availability slot
    public function editAvailability($availabilityId, $doctorId, $dayOfWeek, $startTime, $endTime)
    {
        try {
            $query = "UPDATE doctor_availability 
                     SET day_of_week = ?, start_time = ?, end_time = ? 
                     WHERE availability_id = ? AND doctor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssii", $dayOfWeek, $startTime, $endTime, $availabilityId, $doctorId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in editAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    // Delete availability slot
    public function deleteAvailability($availabilityId, $doctorId)
    {
        try {
            $query = "DELETE FROM doctor_availability 
                     WHERE availability_id = ? AND doctor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $availabilityId, $doctorId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in deleteAvailability: " . $e->getMessage());
            throw $e;
        }
    }

    // Get patient statistics
    public function getPatientStats($doctorId)
    {
        try {
            $query = "SELECT 
             COUNT(DISTINCT patient_id) as total_patients,
             SUM(CASE WHEN appointment_status = 'Completed' THEN 1 ELSE 0 END) as completed_visits,
             SUM(CASE WHEN appointment_date = CURRENT_DATE THEN 1 ELSE 0 END) as today_visits,
             SUM(CASE WHEN appointment_status = 'Scheduled' THEN 1 ELSE 0 END) as upcoming_appointments
             FROM appointments 
             WHERE doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getPatientStats: " . $e->getMessage());
            return [
                'total_patients' => 0,
                'completed_visits' => 0,
                'today_visits' => 0,
                'upcoming_appointments' => 0
            ];
        }
    }

    // Get doctor's patients list
    public function getDoctorPatients($doctorId)
    {
        try {
            $query = "SELECT 
             u.user_id,
             u.first_name,
             u.last_name,
             u.email,
             u.phone_number,
             u.gender,
             u.date_of_birth,
             u.address_line1,
             u.address_line2,
             u.nationality,
             COUNT(a.appointment_id) as total_visits,
             MAX(a.appointment_date) as last_visit
             FROM appointments a
             JOIN users u ON a.patient_id = u.user_id
             WHERE a.doctor_id = ?
             GROUP BY u.user_id
             ORDER BY last_visit DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getDoctorPatients: " . $e->getMessage());
            throw $e;
        }
    }

    // Get patient details
    public function getPatientDetails($patientId, $doctorId)
    {
        try {
            $userQuery = "SELECT 
             first_name, last_name, email, phone_number, 
             gender, date_of_birth, address_line1, address_line2,
             nationality
             FROM users 
             WHERE user_id = ?";

            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            $appointmentsQuery = "SELECT 
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

            $stmt = $this->db->prepare($appointmentsQuery);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();

            $appointments = [];
            while ($row = $result->fetch_assoc()) {
                $appointments[] = $row;
            }

            return [
                'user' => $user,
                'appointments' => $appointments
            ];
        } catch (\Exception $e) {
            error_log("Error in getPatientDetails: " . $e->getMessage());
            throw $e;
        }
    }

    // Get doctor profile with active user check
    public function getDoctorProfile($userId)
    {
        try {
            // Get doctor_id with active user check
            $doctorQuery = "SELECT d.doctor_id 
                          FROM doctors d
                          JOIN users u ON d.user_id = u.user_id
                          WHERE d.user_id = ? AND u.is_active = 1";

            $stmt = $this->db->prepare($doctorQuery);
            if (!$stmt) {
                error_log("Prepare failed in getDoctorProfile: " . $this->db->error);
                throw new \Exception("Database error occurred");
            }

            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $doctorResult = $stmt->get_result();
            $doctorData = $doctorResult->fetch_assoc();

            if (!$doctorData) {
                throw new \Exception("Doctor profile not found");
            }

            $doctorId = $doctorData['doctor_id'];

            // Get full profile data
            $query = "SELECT 
                d.doctor_id,
                d.qualifications,
                d.years_of_experience,
                d.profile_description,
                d.license_number,
                d.hospital_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.username,
                u.address_line1,
                u.address_line2,
                u.city_id,
                u.nationality,
                u.gender,
                u.date_of_birth,
                h.name as hospital_name
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                WHERE d.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDoctorProfile: " . $e->getMessage());
            throw $e;
        }
    }

    // Get doctor's specializations
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

    // Get all specializations
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

    // Update doctor's specializations
    public function updateSpecializations($doctorId, $specializations)
    {
        try {
            $this->db->begin_transaction();

            // Delete existing specializations
            $deleteQuery = "DELETE FROM doctorspecializations WHERE doctor_id = ?";
            $stmt = $this->db->prepare($deleteQuery);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();

            // Add new specializations
            if (!empty($specializations)) {
                $insertQuery = "INSERT INTO doctorspecializations (doctor_id, specialization_id) VALUES (?, ?)";
                $stmt = $this->db->prepare($insertQuery);

                foreach ($specializations as $specializationId) {
                    $stmt->bind_param("ii", $doctorId, $specializationId);
                    $stmt->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateSpecializations: " . $e->getMessage());
            throw $e;
        }
    }

    // Update doctor profile with specializations
    public function updateProfile($doctorId, $data)
    {
        try {
            $this->db->begin_transaction();

            // Update doctors table
            $query = "UPDATE doctors SET
                      qualifications = ?,
                      years_of_experience = ?,
                      profile_description = ?,
                      hospital_id = ?
                   WHERE doctor_id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new \Exception("Failed to prepare update statement");
            }

            $stmt->bind_param(
                "sisii",
                $data['qualifications'],
                $data['experience'],
                $data['description'],
                $data['hospital_id'],
                $doctorId
            );

            if (!$stmt->execute()) {
                throw new \Exception("Failed to update doctor profile");
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
            throw $e;
        }
    }

    // Get hospitals list
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

    // Update appointment status
    public function updateAppointmentStatus($appointmentId, $status, $newDate = null, $newTime = null)
    {
        try {
            $this->db->begin_transaction();

            if ($status === 'Rescheduled' && $newDate && $newTime) {
                $query = "UPDATE appointments 
                        SET appointment_status = ?, 
                            appointment_date = ?,
                            appointment_time = ?
                        WHERE appointment_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sssi", $status, $newDate, $newTime, $appointmentId);
            } else {
                $query = "UPDATE appointments 
                        SET appointment_status = ? 
                        WHERE appointment_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("si", $status, $appointmentId);
            }

            $result = $stmt->execute();
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateAppointmentStatus: " . $e->getMessage());
            throw $e;
        }
    }
    public function getDoctorIdFromUserId($userId)
    {
        try {
            $query = "SELECT doctor_id 
                 FROM doctors 
                 WHERE user_id = ? 
                 AND is_active = 1";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                error_log("Prepare failed in getDoctorIdFromUserId: " . $this->db->error);
                throw new \Exception("Database error occurred");
            }

            $stmt->bind_param("i", $userId);
            if (!$stmt->execute()) {
                error_log("Execute failed in getDoctorIdFromUserId: " . $stmt->error);
                throw new \Exception("Database error occurred");
            }

            $result = $stmt->get_result();
            if (!$result) {
                error_log("Result failed in getDoctorIdFromUserId: " . $stmt->error);
                throw new \Exception("Database error occurred");
            }

            return $result->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDoctorIdFromUserId: " . $e->getMessage());
            return null;
        }
    }

    public function getPatientMedicalReports($patientId, $doctorId)
    {
        try {
            // First verify if this patient has appointments with this doctor
            $checkAccessQuery = "SELECT 1 FROM appointments 
                           WHERE patient_id = ? AND doctor_id = ? 
                           LIMIT 1";

            $stmt = $this->db->prepare($checkAccessQuery);
            $stmt->bind_param("ii", $patientId, $doctorId);
            $stmt->execute();
            $hasAccess = $stmt->get_result()->num_rows > 0;

            if (!$hasAccess) {
                throw new \Exception("Access denied");
            }

            // Get medical reports
            $query = "SELECT 
            report_id,
            report_name,
            report_type,
            file_path,
            upload_date,
            description
            FROM medical_reports 
            WHERE patient_id = ?
            ORDER BY upload_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getPatientMedicalReports: " . $e->getMessage());
            throw $e;
        }
    }
}
