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
}
?>