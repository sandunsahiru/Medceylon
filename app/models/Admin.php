<?php
namespace App\Models;

use Exception;

class Admin
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function getDoctors()
    {
        try {
            $query = "SELECT 
                            users.first_name, users.last_name, roles.role_name, users.gender, users.registration_date, users.last_login, FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) AS age,
                            COALESCE(specializations.name, 'No Specialization') AS specialization_name, 
                            users.user_id
                      FROM users 
                      JOIN roles ON users.role_id = roles.role_id
                      LEFT JOIN doctorspecializations ON users.user_id = doctorspecializations.doctor_id
                      LEFT JOIN specializations ON doctorspecializations.specialization_id = specializations.specialization_id
                      WHERE roles.role_name IN ('Specialist Doctor', 'General Doctor') AND users.is_active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if results exist
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return [];  // Return empty array if no results found
            }
        } catch (\Exception $e) {
            error_log("Error in getDoctors: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPatients()
    {
        try {
            $query = "SELECT 
                            users.first_name, users.last_name, users.gender, users.user_id, users.registration_date, users.last_login,
                            FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) AS age
                      FROM users
                      JOIN roles ON users.role_id = roles.role_id
                      WHERE roles.role_name = 'Patient' AND users.is_active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if results exist
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return [];  // Return empty array if no results found
            }
        } catch (\Exception $e) {
            error_log("Error in getPatients: " . $e->getMessage());
            throw $e;
        }
    }


    public function getUserById($user_id)
    {
        try {
            $query = "SELECT users.*, roles.role_name
            FROM users
            JOIN roles ON users.role_id = roles.role_id
            WHERE users.is_active = 1 AND users.user_id = ?;";


            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();  // just one user row
            } else {
                return null;
            }

        } catch (\Exception $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateUserProfile($user_id, $first_name, $last_name, $email, $phone_number, $address_line1, $city_id)
    {
        try {
            $query = "UPDATE users
                      SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address_line1 = ?, city_id = ?
                      WHERE user_id = ?;";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssssii", $first_name, $last_name, $email, $phone_number, $address_line1, $city_id, $user_id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in updateUserProfile: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteUser($user_id)
    {
        try {
            $query = "UPDATE users SET is_active = 0 WHERE user_id = ?;";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            throw $e;
        }
    }

    public function getHospitalById()
    {
        try {
            $query = "SELECT *
                      FROM hospitals
                      WHERE is_active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if results exist
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return [];  // Return empty array if no results found
            }
        } catch (\Exception $e) {
            error_log("Error in getPatients: " . $e->getMessage());
            throw $e;
        }
    }


    public function getHospitals()
    {
        try {
            $query = "SELECT 
                            hospitals.name, hospitals.contact_number, hospitals.hospital_id, hospitals.website
                      FROM hospitals WHERE is_active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if results exist
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return [];  // Return empty array if no results found
            }
        } catch (\Exception $e) {
            error_log("Error in getHospitals: " . $e->getMessage());
            throw $e;
        }
    }
    public function getPatientsCount()
    {
        try {
            $query_patients = "SELECT COUNT(*) AS total_patients FROM users WHERE role_id = 1";
            $stmt = $this->db->prepare($query_patients);
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result(); // Fetch the result set
            $patients_count = 0;

            if ($result && $row = $result->fetch_assoc()) {
                $patients_count = $row['total_patients']; // Get the count from the result
            }

            return $patients_count; // Return the count
        } catch (\Exception $e) {
            error_log("Error in getPatientsCount: " . $e->getMessage());
            throw $e; // Re-throw the exception for higher-level handling
        }
    }

    public function getDoctorsCount()
    {
        try {
            $query_doctors = "SELECT COUNT(*) AS total_doctors FROM users WHERE role_id = 2";
            $stmt = $this->db->prepare($query_doctors);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctors_count = 0;

            if ($result && $row = $result->fetch_assoc()) {
                $doctors_count = $row['total_doctors'];
            }

            return $doctors_count;
        } catch (\Exception $e) {
            error_log("Error in getDoctorsCount: " . $e->getMessage());
            throw $e;
        }
    }

    public function getHospitalsCount()
    {
        try {
            $query_hospitals = "SELECT COUNT(*) AS total_hospitals FROM hospitals";
            $stmt = $this->db->prepare($query_hospitals);
            $stmt->execute();
            $result = $stmt->get_result();
            $hospitals_count = 0;

            if ($result && $row = $result->fetch_assoc()) {
                $hospitals_count = $row['total_hospitals'];
            }

            return $hospitals_count;
        } catch (\Exception $e) {
            error_log("Error in getHospitalCount: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUpcomingAppointments($limit = 5)
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
                JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                JOIN specializations s ON ds.specialization_id = s.specialization_id
                JOIN hospitals h ON d.hospital_id = h.hospital_id
                LEFT JOIN appointments orig ON a.rescheduled_from = orig.appointment_id 
                LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            $appointments = [];

            while ($row = $result->fetch_assoc()) {
                $appointments[] = [
                    'doctor' => [
                        'id' => $row['doctor_id'],
                        'first_name' => $row['doctor_first_name'],
                        'last_name' => $row['doctor_last_name']
                    ],
                    'patient' => [
                        'first_name' => $row['patient_first_name'],
                        'last_name' => $row['patient_last_name']
                    ],
                    'specialization' => $row['specialization'],
                    'hospital' => $row['hospital_name'],
                    'appointment' => [
                        'date' => date('F j, Y', strtotime($row['appointment_date'])),
                        'time' => date('g:i A', strtotime($row['appointment_time'])),
                        'status' => $row['appointment_status'],
                        'consultation_type' => $row['consultation_type'],
                        'reason_for_visit' => $row['reason_for_visit']
                    ]
                ];
            }

            return $appointments;

        } catch (\Exception $e) {
            error_log("Error getting appointment details: " . $e->getMessage());
            throw $e;
        }
    }

    public function getHotelBookingsCount()
    {
        try {
            $query = "SELECT COUNT(*) AS totalPendingBookings FROM room_bookings WHERE status = 'Pending'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $pending_bookings = 0;
            if ($result && $row = $result->fetch_assoc()) {
                $pending_bookings = $row['totalPendingBookings'];
            }
            return $pending_bookings;
        } catch (Exception $e) {
            error_log("Error getting Hotel Booking Count" . $e->getMessage());
            throw $e;
        }
    }

    public function getStatusHotelBookings($status)
    {
        try {
            $query = "SELECT rb.*, p.first_name, p.last_name, hr.room_type,
             ap.name, ap.contact_info, ap.image_path, hr.available_room_count as room_availability 
             FROM room_bookings rb 
             JOIN rooms hr ON rb.room_id = hr.room_id 
             JOIN users p ON rb.patient_id = p.user_id 
             JOIN accommodationproviders ap ON hr.provider_id = ap.provider_id 
             WHERE status = ? ORDER BY rb.check_in_date DESC;";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
            $Hotelbookings = [];

            // Check if results exist
            if ($result->num_rows > 0) {
                return $result;
            } else {
                return [];  // Return empty array if no results found
            }
        } catch (\Exception $e) {
            error_log("Error getting hotel bookings: " . $e->getMessage());
            throw $e;
        }
    }


    public function confirmBookingById($bookingId)
    {
        try {
            $query = "UPDATE room_bookings rb
                    JOIN rooms hr ON rb.room_id = hr.room_id
                    SET rb.status = 'Successful',
                    hr.available_room_count = hr.available_room_count - 1
                    WHERE rb.booking_id = ?;";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $bookingId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error confirming booking: " . $e->getMessage());
            throw $e;
        }
    }

    public function rejectBookingById($bookingId)
    {
        try {
            $query = "UPDATE room_bookings SET status = 'Unsuccesful' WHERE booking_id = ?;";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $bookingId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error rejecting booking: " . $e->getMessage());
            throw $e;
        }
    }
   


    public function getPatientPlan($status)
    {
        try {
            $query = "SELECT t.*, u.*, p.plan_name FROM treatment_plans t
            JOIN payment_plans p ON t.payment_plan_id = p.id
            JOIN users u ON t.patient_id = u.user_id
            WHERE u.is_active = 1 AND t.payment_plan_id IS NOT NULL
            AND plan_name = ?
            ORDER BY created_at DESC;";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);



        } catch (\Exception $e) {
            error_log("Error getting treatment plan by patient ID: " . $e->getMessage());
            return false;
        }
    }

}
?>