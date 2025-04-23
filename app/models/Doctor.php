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

    public function getAvailableDoctors()
{
    try {
        error_log("Getting available doctors");
        $query = "SELECT d.doctor_id, u.first_name, u.last_name, h.name as hospital_name 
                FROM doctors d 
                JOIN users u ON d.user_id = u.user_id 
                LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                WHERE u.role_id = 2 
                AND d.is_active = 1 
                AND u.is_active = 1
                AND d.is_verified = 1
                ORDER BY u.first_name, u.last_name";

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

// Add this method to your Doctor model class if it doesn't exist,
// or update it if it does to ensure time slots are returned as strings

public function getAvailableTimeSlots($doctorId, $date) {
    try {
        // Convert numeric day of week to day name
        $dayNumber = date('w', strtotime($date)); // Returns 0 (Sunday) to 6 (Saturday)
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $dayName = $dayNames[$dayNumber];
        
        error_log("Checking time slots for doctor ID: $doctorId on $date ($dayName)");
        
        // Get the doctor's working hours for that day of week from doctor_availability
        $query = "SELECT start_time, end_time 
                 FROM doctor_availability 
                 WHERE doctor_id = ? AND day_of_week = ? 
                 AND is_active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $doctorId, $dayName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("No schedule found for doctor $doctorId on $dayName");
            return [];
        }
        
        $schedule = $result->fetch_assoc();
        error_log("Found schedule: " . json_encode($schedule));
        
        // Get already booked appointments
        $query = "SELECT appointment_time 
                 FROM appointments 
                 WHERE doctor_id = ? AND appointment_date = ? 
                 AND appointment_status NOT IN ('Canceled', 'Rejected')";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $doctorId, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $bookedSlots = [];
        while ($row = $result->fetch_assoc()) {
            $bookedSlots[] = $row['appointment_time'];
        }
        
        error_log("Booked slots: " . json_encode($bookedSlots));
        
        // Generate time slots (30 minute intervals)
        $startTime = strtotime($schedule['start_time']);
        $endTime = strtotime($schedule['end_time']);
        
        $slots = [];
        $slotDuration = 30 * 60; // 30 minutes in seconds
        
        for ($time = $startTime; $time < $endTime; $time += $slotDuration) {
            // Format as HH:MM:SS for database comparison
            $formattedTime = date('H:i:s', $time);
            
            // Skip if already booked
            if (in_array($formattedTime, $bookedSlots)) {
                continue;
            }
            
            // Add to available slots (formatted for display)
            $displayTime = date('g:i A', $time);
            $slots[] = $displayTime;
        }
        
        error_log("Available slots: " . json_encode($slots));
        return $slots;
    } catch (\Exception $e) {
        error_log("Error getting available time slots: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        throw $e;
    }
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
        error_log("Getting patients for doctor ID: " . $doctorId); // Debug log
        
        // Get all patients who have had appointments with this doctor
        $query = "SELECT DISTINCT 
            u.user_id, 
            u.first_name, 
            u.last_name,
            u.email,
            u.phone_number,
            MAX(a.appointment_date) as last_visit,
            COUNT(DISTINCT a.appointment_id) as total_visits
            FROM appointments a
            INNER JOIN users u ON a.patient_id = u.user_id 
            WHERE a.doctor_id = ?
            AND u.is_active = 1
            AND a.appointment_status IN ('Completed', 'Scheduled', 'Asked')
            GROUP BY u.user_id, u.first_name, u.last_name, u.email, u.phone_number
            ORDER BY last_visit DESC";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Database error: " . $this->db->error);
        }

        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $patients = $result->fetch_all(MYSQLI_ASSOC);
        
        error_log("Found " . count($patients) . " patients for doctor ID: " . $doctorId);
        return $patients;
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
