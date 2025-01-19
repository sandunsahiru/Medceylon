<?php

class Hospital
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new Database;
    }

    public function getAllRequests()
    {
        $total_query = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN request_status = 'Pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN request_status = 'Approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN request_status = 'Completed' THEN 1 ELSE 0 END) as completed
            FROM treatment_requests WHERE is_active = 1";
        $total_result = $conn->query($total_query);
        $total_data = $total_result->fetch_assoc();
    }

    public function getLatestRequests()
    {
        $requests_query = "SELECT 
            tr.request_id,
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
            ORDER BY tr.request_date DESC
            LIMIT 5";
        $requests = $conn->query($requests_query);
    }

    public function getDepartments(){
        $departments_query = "SELECT * FROM hospital_departments ORDER BY department_name ASC";
        $departments = $conn->query($departments_query);
    }

    public function getDoctors(){
        $doctors_query = "SELECT * FROM doctors d JOIN users u ON d.doctor_id = u.user_id ORDER BY u.first_name ASC";
        $doctors = $conn->query($doctors_query);
    }

    public function getPatients(){
        $patients_query = "SELECT * FROM users u JOIN userroles r ON u.role_id = r.role_id WHERE u.role_id = 1 ORDER BY first_name ASC";
        $patients = $conn->query($patients_query);
    }

    public function getRequests(){
        $request_query = "SELECT * FROM treatment_requests t JOIN users u ON t.patient_id = u.user_id ORDER BY request_date DESC";
        $requests = $conn->query($request_query);
    }
    
}
?>