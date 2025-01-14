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

    public function getAvailableDoctors() {
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

        // Get doctor's availability
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

        // Get booked slots
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

    public function getDoctorDetails($doctorId)
    {
        $query = "SELECT d.*, u.first_name, u.last_name, h.name as hospital_name,
                       s.name as specialization
                FROM doctors d
                JOIN users u ON d.user_id = u.user_id
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                JOIN specializations s ON ds.specialization_id = s.specialization_id
                WHERE d.doctor_id = ? AND d.is_active = 1";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUpcomingAppointments($doctorId)
    {
        $query = "SELECT a.*, u.first_name, u.last_name
                FROM appointments a
                JOIN users u ON a.patient_id = u.user_id
                WHERE a.doctor_id = ?
                AND a.appointment_date >= CURDATE()
                AND a.appointment_status = 'Scheduled'
                ORDER BY a.appointment_date, a.appointment_time";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateAvailability($doctorId, $availability)
    {
        $this->db->begin_transaction();

        try {
            // Clear existing availability
            $deleteQuery = "DELETE FROM doctor_availability WHERE doctor_id = ?";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bind_param("i", $doctorId);
            $deleteStmt->execute();

            // Insert new availability
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

    public function updateProfile($doctorId, $data)
    {
        $this->db->begin_transaction();

        try {
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
            $stmt->execute();

            if (isset($data['specializations'])) {
                // Update specializations
                $deleteSpec = "DELETE FROM doctorspecializations WHERE doctor_id = ?";
                $deleteStmt = $this->db->prepare($deleteSpec);
                $deleteStmt->bind_param("i", $doctorId);
                $deleteStmt->execute();

                $insertSpec = "INSERT INTO doctorspecializations (doctor_id, specialization_id) VALUES (?, ?)";
                $insertStmt = $this->db->prepare($insertSpec);

                foreach ($data['specializations'] as $specId) {
                    $insertStmt->bind_param("ii", $doctorId, $specId);
                    $insertStmt->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
