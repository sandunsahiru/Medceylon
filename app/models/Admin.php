<?php
namespace App\Models;

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
                            users.first_name, users.last_name, roles.role_name, users.gender, FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) AS age,
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
                            users.first_name, users.last_name, users.gender, users.user_id, 
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



    public function getPatientById($user_id)
    {
        try {
            $query = "SELECT *
                      FROM users
                      JOIN roles ON users.role_id = roles.role_id
                      WHERE roles.role_name = 'Patient' AND users.is_active = 1 AND users.user_id=?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_id);
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

    public function getDoctorById($user_id)
    {
        try {
            $query = "SELECT *
                      FROM users
                      JOIN roles ON users.role_id = roles.role_id
                      WHERE roles.role_name = 'Doctor' 
                      AND users.is_active = 1 
                      AND users.user_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $user_id); // "i" = integer
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc(); // return single doctor as array
            } else {
                return [];  // No doctor found
            }
        } catch (\Exception $e) {
            error_log("Error in getDoctorById: " . $e->getMessage());
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
            $stmt = $this->db->prepare($query_patients); // Prepare the SQL query
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

    public function getUpcomingAppointments($limit = 10)
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
}
?>