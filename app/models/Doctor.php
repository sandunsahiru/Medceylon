<?php

namespace App\Models;

class Doctor
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Existing methods
    public function getAvailableDoctors()
    {
        try {
            error_log("Getting available doctors");
            $query = "SELECT d.doctor_id, u.first_name, u.last_name, h.name as hospital_name 
                    FROM doctors d 
                    JOIN users u ON d.user_id = u.user_id 
                    JOIN hospitals h ON d.hospital_id = h.hospital_id
                    JOIN userroles ur ON u.username = ur.username
                    WHERE ur.role_id = 2 AND d.is_active = 1";

            $result = $this->db->query($query);
            if (!$result) {
                error_log("Query error: " . $this->db->error);
                throw new \Exception("Failed to fetch doctors");
            }
            error_log("Found " . $result->num_rows . " doctors");
            return $result;
        } catch (\Exception $e) {
            error_log("Error in getAvailableDoctors: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAvailableTimeSlots($doctorId, $date)
    {
        $dayOfWeek = date('l', strtotime($date));

        $availQuery = "SELECT start_time, end_time, time_slot_duration 
                     FROM doctor_availability 
                     WHERE doctor_id = ? 
                     AND day_of_week = ? 
                     AND is_active = 1";

        $availStmt = $this->db->prepare($availQuery);
        $availStmt->bind_param("is", $doctorId, $dayOfWeek);
        $availStmt->execute();
        $availabilityResult = $availStmt->get_result();

        if ($availabilityResult->num_rows === 0) {
            return [];
        }

        $bookedQuery = "SELECT appointment_time 
                      FROM appointments 
                      WHERE doctor_id = ? 
                      AND appointment_date = ? 
                      AND appointment_status NOT IN ('Canceled', 'Rejected')";

        $bookedStmt = $this->db->prepare($bookedQuery);
        $bookedStmt->bind_param("is", $doctorId, $date);
        $bookedStmt->execute();
        $bookedResult = $bookedStmt->get_result();

        $bookedSlots = [];
        while ($row = $bookedResult->fetch_assoc()) {
            $startTime = strtotime($row['appointment_time']);
            $bookedSlots[] = date('H:i:s', $startTime);
        }

        $availableSlots = [];
        while ($availability = $availabilityResult->fetch_assoc()) {
            $start = strtotime($availability['start_time']);
            $end = strtotime($availability['end_time']);
            $slotDuration = 30 * 60; // 30 minutes

            for ($time = $start; $time < $end; $time += $slotDuration) {
                $currentSlot = date('H:i:s', $time);
                if (!in_array($currentSlot, $bookedSlots)) {
                    $availableSlots[] = date('g:i A', $time);
                }
            }
        }

        return $availableSlots;
    }

    // Enhanced Dashboard Methods
    public function getDoctorDashboardStats($doctorId)
    {
        try {
            $query = "SELECT 
                COUNT(DISTINCT a.patient_id) as total_patients,
                COUNT(CASE WHEN a.appointment_status = 'Completed' THEN 1 END) as completed_visits,
                COUNT(CASE WHEN a.appointment_date = CURRENT_DATE THEN 1 END) as today_visits,
                COUNT(CASE WHEN a.appointment_status = 'Scheduled' AND a.appointment_date >= CURRENT_DATE THEN 1 END) as upcoming_appointments
                FROM appointments a
                WHERE a.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDoctorAvailability($doctorId)
    {
        try {
            $query = "SELECT * FROM doctor_availability 
                     WHERE doctor_id = ? AND is_active = 1 
                     ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 
                     'Thursday', 'Friday', 'Saturday', 'Sunday')";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting doctor availability: " . $e->getMessage());
            throw $e;
        }
    }

    public function addAvailability($doctorId, $dayOfWeek, $startTime, $endTime)
    {
        try {
            $query = "INSERT INTO doctor_availability 
                     (doctor_id, day_of_week, start_time, end_time, is_active) 
                     VALUES (?, ?, ?, ?, 1)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("isss", $doctorId, $dayOfWeek, $startTime, $endTime);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error adding availability: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDoctorProfile($userId) {
        try {
            // First get the doctor_id for this user_id
            $doctorQuery = "SELECT doctor_id FROM doctors WHERE user_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($doctorQuery);
            if (!$stmt) {
                error_log("Prepare failed in getDoctorProfile (doctor query): " . $this->db->error);
                throw new \Exception("Database error occurred");
            }
    
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $doctorResult = $stmt->get_result();
            $doctorData = $doctorResult->fetch_assoc();
    
            if (!$doctorData) {
                error_log("No doctor found for user_id: " . $userId);
                throw new \Exception("Doctor profile not found");
            }
    
            $doctorId = $doctorData['doctor_id'];
    
            // Now get the full profile data
            $query = "SELECT 
                    d.doctor_id,
                    d.qualifications,
                    d.years_of_experience,
                    d.profile_description,
                    d.license_number,
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
                    h.name as hospital_name,
                    h.hospital_id
                    FROM doctors d
                    JOIN users u ON d.user_id = u.user_id
                    LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                    WHERE d.doctor_id = ? AND d.is_active = 1";
    
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                error_log("Prepare failed in getDoctorProfile (profile query): " . $this->db->error);
                throw new \Exception("Database error occurred");
            }
    
            $stmt->bind_param("i", $doctorId);
            if (!$stmt->execute()) {
                error_log("Execute failed in getDoctorProfile: " . $stmt->error);
                throw new \Exception("Failed to fetch doctor profile");
            }
    
            $result = $stmt->get_result();
            $profile = $result->fetch_assoc();
    
            if (!$profile) {
                error_log("No profile found for doctor_id: " . $doctorId);
                throw new \Exception("Profile not found");
            }
    
            return $profile;
    
        } catch (\Exception $e) {
            error_log("Error in getDoctorProfile: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProfile($doctorId, $data)
    {
        try {
            $this->db->begin_transaction();

            // First, get the user_id for this doctor
            $query = "SELECT user_id FROM doctors WHERE doctor_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctor = $result->fetch_assoc();

            if (!$doctor) {
                throw new \Exception("Doctor not found");
            }

            // Update doctors table
            $query = "UPDATE doctors SET
                       qualifications = ?,
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
                throw new \Exception("Failed to update doctor profile");
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateProfile: " . $e->getMessage());
            throw $e;
        }
    }

    // Existing update methods
    public function updateAvailability($doctorId, $availability)
    {
        $this->db->begin_transaction();

        try {
            $deleteQuery = "DELETE FROM doctor_availability WHERE doctor_id = ?";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $doctorId);
            $deleteStmt->execute();

            $insertQuery = "INSERT INTO doctor_availability 
                         (doctor_id, day_of_week, start_time, end_time, is_active) 
                         VALUES (?, ?, ?, ?, 1)";
            $insertStmt = $this->db->prepare($insertQuery);

            foreach ($availability as $daySchedule) {
                $insertStmt->bind_param(
                    "isss",
                    $doctorId,
                    $daySchedule['day'],
                    $daySchedule['start_time'],
                    $daySchedule['end_time']
                );
                $insertStmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function getAppointmentHistory($doctorId)
    {
        try {
            $query = "SELECT a.*, u.first_name, u.last_name
                    FROM appointments a
                    JOIN users u ON a.patient_id = u.user_id
                    WHERE a.doctor_id = ?
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting appointment history: " . $e->getMessage());
            throw $e;
        }
    }
    // Get all specialist doctors
    public function getAllSpecialists()
    {
        try {
            $query = "SELECT 
                d.doctor_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                d.qualifications,
                d.years_of_experience,
                h.name as hospital_name,
                GROUP_CONCAT(DISTINCT s.name) as specializations
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id 
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE u.role_id = 3 AND d.is_active = 1
                GROUP BY d.doctor_id, u.first_name, u.last_name, u.email, 
                         u.phone_number, d.qualifications, d.years_of_experience, h.name";

            $result = $this->db->query($query);
            if (!$result) {
                throw new \Exception("Failed to fetch specialists");
            }
            return $result;
        } catch (\Exception $e) {
            error_log("Error getting specialists: " . $e->getMessage());
            throw $e;
        }
    }

    // Get specialist statistics
    public function getSpecialistStats()
    {
        try {
            $query = "SELECT 
                COUNT(DISTINCT d.doctor_id) as total_doctors,
                COUNT(DISTINCT s.specialization_id) as total_specializations,
                COUNT(DISTINCT h.hospital_id) as total_hospitals
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                WHERE u.role_id = 3";

            $result = $this->db->query($query);
            if (!$result) {
                throw new \Exception("Failed to fetch specialist statistics");
            }
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error getting specialist stats: " . $e->getMessage());
            throw $e;
        }
    }

    // Get doctor's patients for booking form
    public function getDoctorPatients($doctorId)
    {
        try {
            $query = "SELECT DISTINCT 
                u.user_id, 
                u.first_name, 
                u.last_name 
                FROM users u 
                JOIN appointments a ON u.user_id = a.patient_id 
                WHERE a.doctor_id = ?
                ORDER BY u.first_name, u.last_name";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting doctor's patients: " . $e->getMessage());
            throw $e;
        }
    }
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
            error_log("Error getting hospitals: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSpecializations()
    {
        try {
            $query = "SELECT specialization_id, name 
                     FROM specializations 
                     WHERE is_active = 1 
                     ORDER BY name";

            $result = $this->db->query($query);
            if (!$result) {
                throw new \Exception("Failed to fetch specializations");
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting specializations: " . $e->getMessage());
            throw $e;
        }
    }

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
            error_log("Error getting doctor specializations: " . $e->getMessage());
            throw $e;
        }
    }
}
