<?php

namespace App\Models;

class Hospital
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Dashboard Statistics
    public function getRequestStatistics()
    {
        try {
            $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN request_status = 'Pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN request_status = 'Approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN request_status = 'Completed' THEN 1 ELSE 0 END) as completed
                FROM treatment_requests 
                WHERE is_active = 1";

            $result = $this->db->query($query);
            return $result->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getRequestStatistics: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllHospitals()
    {
        try{
            $sql = "SELECT * 
                    FROM hospitals";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
        }catch (\Exception $e) {
            error_log("Error in getAllHospitals: " . $e->getMessage());
            throw $e;
        }
    }

    // Department Methoda
    public function getAllDepartments()
    {
        try {
            $query = "SELECT d.*, 
                      (SELECT COUNT(*) FROM doctors WHERE department_id = d.department_id AND is_active = 1) as doctor_count,
                      CONCAT(u.first_name, ' ', u.last_name) as head_doctor
                      FROM hospital_departments d
                      LEFT JOIN doctors doc ON d.head_doctor_id = doc.doctor_id
                      LEFT JOIN users u ON doc.user_id = u.user_id
                      WHERE doc.is_active = 1 
                      ORDER BY d.department_name ASC";
            return $this->db->query($query);
        } catch (\Exception $e) {
            error_log("Error in getAllDepartments: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDepartmentDetails($departmentId)
    {
        try {
            $query = "SELECT d.*, 
                      CONCAT(u.first_name, ' ', u.last_name) as head_doctor_name
                      FROM hospital_departments d
                      LEFT JOIN doctors doc ON d.head_doctor_id = doc.doctor_id
                      LEFT JOIN users u ON doc.user_id = u.user_id
                      WHERE d.department_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $departmentId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDepartmentDetails: " . $e->getMessage());
            throw $e;
        }
    }

    public function createDepartment($data)
    {
        try {
            $this->db->begin_transaction();

            $query = "INSERT INTO hospital_departments 
                      (department_name, description, head_doctor_id, doctor_count) 
                      VALUES (?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "ssii",
                $data['department_name'],
                $data['description'],
                $data['head_doctor'],
                $data['doctor_count']
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return $stmt->insert_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in createDepartment: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateDepartment($departmentId, $data)
    {
        try {
            $this->db->begin_transaction();

            $query = "UPDATE hospital_departments SET 
                      department_name = ?,
                      description = ?,
                      head_doctor_id = ?,
                      updated_by = ?,
                      updated_at = NOW()
                      WHERE department_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "sssii",
                $data['department_name'],
                $data['description'],
                $data['head_doctor'],
                $data['updated_by'],
                $departmentId
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateDepartment: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteDepartment($departmentId)
    {
        try {
            $this->db->begin_transaction();

            // Check if department has active doctors
            $checkQuery = "SELECT COUNT(*) as doctor_count 
                          FROM doctors 
                          WHERE department_id = ? AND is_active = 1";
            $stmt = $this->db->prepare($checkQuery);
            $stmt->bind_param("i", $departmentId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result['doctor_count'] > 0) {
                throw new \Exception("Cannot delete department with active doctors");
            }

            // Soft delete the department
            $query = "UPDATE hospital_departments SET 
                      is_active = 0,
                      updated_at = NOW()
                      WHERE department_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $departmentId);

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in deleteDepartment: " . $e->getMessage());
            throw $e;
        }
    }

    // Doctor Methoda
    public function getAllDoctors()
    {
        try {
            $query = "SELECT d.*, u.first_name, u.last_name, u.email, u.phone_number,
                      hd.department_name,
                      (SELECT COUNT(*) > 0 FROM doctor_availability da 
                       WHERE da.doctor_id = d.doctor_id 
                       AND da.day_of_week = LOWER(DAYNAME(NOW()))
                       AND da.is_active = 1) as is_available
                      FROM doctors d 
                      JOIN users u ON d.user_id = u.user_id 
                      LEFT JOIN hospital_departments hd ON d.department_id = hd.department_id
                      WHERE u.is_active = 1
                      ORDER BY u.first_name ASC";
            return $this->db->query($query);
        } catch (\Exception $e) {
            error_log("Error in getAllDoctors: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDoctorDetails($doctorId)
    {
        try {
            $query = "SELECT d.*, u.first_name, u.last_name, u.email, u.phone_number,
                      hd.department_name
                      FROM doctors d
                      JOIN users u ON d.user_id = u.user_id
                      LEFT JOIN hospital_departments hd ON d.department_id = hd.department_id
                      WHERE d.doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getDoctorDetails: " . $e->getMessage());
            throw $e;
        }
    }

    public function createDoctor($data)
    {
        try {
            $this->db->begin_transaction();

            // First create user account
            $query = "INSERT INTO users 
                      (first_name, last_name, email, phone_number, role_id, created_by) 
                      VALUES (?, ?, ?, ?, 2, ?)"; // role_id 2 for doctors

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "ssssi",
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone_number'],
                $data['updated_by']
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $userId = $stmt->insert_id;

            // Then create doctor record
            $query = "INSERT INTO doctors 
                      (user_id, specialization, license_number, department_id) 
                      VALUES (?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "issi",
                $userId,
                $data['specialization'],
                $data['license_number'],
                $data['department_id']
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return $stmt->insert_id;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in createDoctor: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateDoctor($doctorId, $data)
    {
        try {
            $this->db->begin_transaction();

            // Update doctor record
            $query = "UPDATE doctors SET 
                      specialization = ?,
                      license_number = ?,
                      department_id = ?,
                      updated_at = NOW()
                      WHERE doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "ssii",
                $data['specialization'],
                $data['license_number'],
                $data['department_id'],
                $doctorId
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            // Update user record
            $query = "UPDATE users SET 
                      first_name = ?,
                      last_name = ?,
                      email = ?,
                      phone_number = ?,
                      updated_by = ?,
                      updated_at = NOW()
                      WHERE user_id = (SELECT user_id FROM doctors WHERE doctor_id = ?)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "ssssii",
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone_number'],
                $data['updated_by'],
                $doctorId
            );

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateDoctor: " . $e->getMessage());
            throw $e;
        }
    }

    public function toggleDoctorStatus($doctorId)
    {
        try {
            $this->db->begin_transaction();

            $query = "UPDATE users SET 
                      is_active = NOT is_active,
                      updated_at = NOW()
                      WHERE user_id = (SELECT user_id FROM doctors WHERE doctor_id = ?)";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in toggleDoctorStatus: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDoctorSchedule($doctorId)
    {
        try {
            $query = "SELECT * FROM doctor_schedules WHERE doctor_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();

            $result = $stmt->get_result();
            $schedule = [];

            while ($row = $result->fetch_assoc()) {
                $schedule[$row['day']] = [
                    'start' => $row['start_time'],
                    'end' => $row['end_time'],
                    'available' => $row['is_available']
                ];
            }

            return $schedule;
        } catch (\Exception $e) {
            error_log("Error in getDoctorSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateDoctorSchedule($doctorId, $scheduleData)
    {
        try {
            $this->db->begin_transaction();

            // Delete existing schedule
            $deleteQuery = "DELETE FROM doctor_schedules WHERE doctor_id = ?";
            $stmt = $this->db->prepare($deleteQuery);
            $stmt->bind_param("i", $doctorId);
            $stmt->execute();

            // Insert new schedule
            $insertQuery = "INSERT INTO doctor_schedules 
                           (doctor_id, day, start_time, end_time, is_available) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($insertQuery);

            foreach ($scheduleData as $day => $times) {
                $stmt->bind_param(
                    "isssi",
                    $doctorId,
                    $day,
                    $times['start'],
                    $times['end'],
                    $times['available']
                );

                if (!$stmt->execute()) {
                    throw new \Exception($stmt->error);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateDoctorSchedule: " . $e->getMessage());
            throw $e;
        }
    }

    // Patient Methoda
    public function getAllPatients()
    {
        try {
            $query = "SELECT u.*, c.city_name 
                     FROM users u 
                     LEFT JOIN cities c ON u.city_id = c.city_id
                     JOIN userroles r ON u.role_id = r.role_id 
                     WHERE u.role_id = 1
                     ORDER BY u.first_name ASC";
            return $this->db->query($query);
        } catch (\Exception $e) {
            error_log("Error in getAllPatients: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPatientDetails($patientId)
    {
        try {
            $query = "SELECT u.*, c.city_name, co.country_name
                      FROM users u
                      LEFT JOIN cities c ON u.city_id = c.city_id
                      LEFT JOIN countries co ON u.nationality = co.country_code
                      WHERE u.user_id = ? AND u.role_id = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getPatientDetails: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPatientMedicalHistory($patientId)
    {
        try {
            $query = "SELECT h.*, 
                      CONCAT(d.first_name, ' ', d.last_name) as doctor_name,
                      tr.treatment_type,
                      tr.request_status, a.appointment_date
                      FROM healthrecords h
                      JOIN treatment_requests tr ON h.record_id = tr.request_id
                      LEFT JOIN doctors doc ON h.doctor_id = doc.doctor_id
                      LEFT JOIN users d ON doc.user_id = d.user_id
                      LEFT JOIN appointments a ON h.appointment_id = a.appointment_id
                      WHERE tr.patient_id = ?
                      ORDER BY h.date_created DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();

            $history = [];
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }

            return $history;
        } catch (\Exception $e) {
            error_log("Error in getPatientMedicalHistory: " . $e->getMessage());
            throw $e;
        }
    }

    // Treatment Request Methoda
    public function getAllTreatmentRequests()
    {
        try {
            $query = "SELECT 
                tr.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number
                FROM treatment_requests tr
                JOIN users u ON tr.patient_id = u.user_id
                WHERE tr.is_active = 1
                ORDER BY tr.request_date DESC";
            return $this->db->query($query);
        } catch (\Exception $e) {
            error_log("Error in getAllTreatmentRequests: " . $e->getMessage());
            throw $e;
        }
    }

    public function getLatestRequests($limit)
    {
        try {
            $query = "SELECT 
                tr.*,
                u.first_name,
                u.last_name,
                tr.preferred_date,
                tr.treatment_type,
                tr.doctor_preference,
                tr.special_requirements,
                tr.request_status,
                tr.estimated_cost,
                tr.request_date
                FROM treatment_requests tr
                JOIN users u ON tr.patient_id = u.user_id
                WHERE tr.is_active = 1
                ORDER BY tr.request_date DESC
                LIMIT ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            return $stmt->get_result();
        } catch (\Exception $e) {
            error_log("Error in getLatestRequests: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateRequest($requestId, $data)
    {
        try {
            $this->db->begin_transaction();
    
            // Fetch old status
            $statusQuery = "SELECT request_status FROM treatment_requests WHERE request_id = ?";
            $statusStmt = $this->db->prepare($statusQuery);
            $statusStmt->bind_param("i", $requestId);
            $statusStmt->execute();
            $statusResult = $statusStmt->get_result();
            $row = $statusResult->fetch_assoc();
            $oldStatus = $row['request_status'];
    
            // Update treatment_requests
            $query = "UPDATE treatment_requests SET 
                      estimated_cost = ?, 
                      response_message = ?, 
                      additional_requirements = ?, 
                      request_status = ?, 
                      last_updated = NOW()
                      WHERE request_id = ?";
    
            $stmt = $this->db->prepare($query);
            $stmt->bind_param(
                "dsssi",
                $data['estimated_cost'],
                $data['response_message'],
                $data['additional_requirements'],
                $data['new_status'], // NEW field you must pass in controller
                $requestId
            );
    
            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }
    
            // Insert into treatment_request_history
            $historyQuery = "INSERT INTO treatment_request_history 
                             (request_id, old_status, new_status, changed_by, changed_at, notes) 
                             VALUES (?, ?, ?, ?, NOW(), ?)";
    
            $histStmt = $this->db->prepare($historyQuery);
            $histStmt->bind_param(
                "issis",
                $requestId,
                $oldStatus,
                $data['new_status'],
                $data['updated_by'],
                $data['response_message'] // optional note
            );
    
            if (!$histStmt->execute()) {
                throw new \Exception($histStmt->error);
            }
    
            // Add notification
            $notificationQuery = "INSERT INTO notifications 
                                  (user_id, type, message, related_id) 
                                  SELECT patient_id, 'request_update', 
                                  'Your treatment request has been updated', request_id 
                                  FROM treatment_requests 
                                  WHERE request_id = ?";
    
            $notifStmt = $this->db->prepare($notificationQuery);
            $notifStmt->bind_param("i", $requestId);
            $notifStmt->execute();
    
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateRequest: " . $e->getMessage());
            throw $e;
        }
    }
    

    public function updateRequestStatus($requestId, $status, $updatedBy)
    {
        try {
            $this->db->begin_transaction();

            $query = "UPDATE treatment_requests SET 
                      request_status = ?,
                      updated_by = ?,
                      updated_at = NOW()
                      WHERE request_id = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $status, $updatedBy, $requestId);

            if (!$stmt->execute()) {
                throw new \Exception($stmt->error);
            }

            // Add notification
            $notificationQuery = "INSERT INTO notifications 
                                (user_id, type, message, related_id) 
                                SELECT patient_id, 'status_update', 
                                CONCAT('Your request status has been updated to ', ?), 
                                request_id 
                                FROM treatment_requests 
                                WHERE request_id = ?";

            $notifStmt = $this->db->prepare($notificationQuery);
            $notifStmt->bind_param("si", $status, $requestId);
            $notifStmt->execute();

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error in updateRequestStatus: " . $e->getMessage());
            throw $e;
        }
    }

    public function getRequestDetails($requestId)
    {
        try {
            $query = "SELECT 
                tr.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone_number,
                u.date_of_birth,
                c.city_name,
                co.country_name
                FROM treatment_requests tr
                JOIN users u ON tr.patient_id = u.user_id
                LEFT JOIN cities c ON u.city_id = c.city_id
                LEFT JOIN countries co ON u.nationality = co.country_code
                WHERE tr.request_id = ? AND tr.is_active = 1";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                throw new \Exception('Treatment request not found');
            }

            return $result;
        } catch (\Exception $e) {
            error_log("Error in getRequestDetails: " . $e->getMessage());
            throw $e;
        }
    }

}
