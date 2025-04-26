<?php

namespace App\Models;

class MedicalSession {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    /**
     * Get active medical session for a patient
     * 
     * @param int $patientId
     * @return array|false
     */
    public function getActiveSessionByPatient($patientId) {
        try {
            $query = "SELECT * FROM medical_sessions WHERE patient_id = ? AND status = 'Active'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error getting active session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a new medical session
     * 
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        try {
            $this->db->begin_transaction();
            
            $query = "INSERT INTO medical_sessions (patient_id, status, treatment_plan_id, created_at) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $treatmentPlanId = $data['treatment_plan_id'] ?? null;
            $stmt->bind_param("isss", 
                $data['patient_id'],
                $data['status'],
                $treatmentPlanId,
                $data['created_at']
            );
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to create medical session: " . $stmt->error);
            }
            
            $sessionId = $this->db->insert_id;
            $this->db->commit();
            
            return $sessionId;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error creating medical session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get session by ID
     * 
     * @param int $sessionId
     * @return array|false
     */
    public function getById($sessionId) {
        try {
            $query = "SELECT * FROM medical_sessions WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error getting session by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a medical session
     * 
     * @param array $data
     * @return bool
     */
    public function update($data) {
        try {
            $this->db->begin_transaction();
            
            $query = "UPDATE medical_sessions 
                      SET status = ?, 
                          treatment_plan_id = ?, 
                          updated_at = ? 
                      WHERE session_id = ?";
            
            $stmt = $this->db->prepare($query);
            $treatmentPlanId = $data['treatment_plan_id'] ?? null;
            $updatedAt = date('Y-m-d H:i:s');
            $stmt->bind_param("sisi", 
                $data['status'],
                $treatmentPlanId,
                $updatedAt,
                $data['session_id']
            );
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to update medical session: " . $stmt->error);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error updating medical session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a medical session
     * 
     * @param int $sessionId
     * @return bool
     */
    public function delete($sessionId) {
        try {
            $this->db->begin_transaction();
            
            $query = "DELETE FROM medical_sessions WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sessionId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to delete medical session: " . $stmt->error);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error deleting medical session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get treatment plan by ID
     * 
     * @param int $planId
     * @return array|false
     */
    public function getTreatmentPlanById($planId) {
        try {
            $query = "SELECT * FROM treatment_plans WHERE plan_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $planId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Error getting treatment plan: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a treatment plan
     * 
     * @param array $data
     * @return int|false
     */
    public function createTreatmentPlan($data) {
        try {
            $this->db->begin_transaction();
            
            $query = "INSERT INTO treatment_plans 
                     (session_id, doctor_id, travel_restrictions, estimated_budget, notes, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $notes = $data['notes'] ?? null;
            $stmt->bind_param("iissss", 
                $data['session_id'],
                $data['doctor_id'],
                $data['travel_restrictions'],
                $data['estimated_budget'],
                $notes,
                $data['created_at']
            );
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to create treatment plan: " . $stmt->error);
            }
            
            $planId = $this->db->insert_id;
            
            // Update the session with the treatment plan ID
            $this->update([
                'session_id' => $data['session_id'],
                'status' => 'Active',
                'treatment_plan_id' => $planId
            ]);
            
            $this->db->commit();
            return $planId;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error creating treatment plan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get formatted session data for view
     * 
     * @param int $sessionId
     * @param int $patientId
     * @return array
     */
    public function getFormattedSessionData($sessionId, $patientId) {
        try {
            // Get session data
            $session = $this->getById($sessionId);
            if (!$session) {
                throw new \Exception("Session not found");
            }
            
            // Default structure with placeholders
            $sessionData = [
                'id' => $session['session_id'],
                'status' => $session['status'],
                'treatment_plan_id' => $session['treatment_plan_id'] ?? null,
                'generalDoctorBooked' => false,
                'specialistBooked' => false,
                'treatmentPlanCreated' => false,
                'transportBooked' => false,
                'travelPlanSelected' => false,
                'generalDoctor' => null,
                'specialist' => null,
                'general_doctor_notes' => $session['general_doctor_notes'] ?? null,
                'referral_reason' => $session['referral_reason'] ?? null
            ];
            
            // If treatment plan exists, set flag and get details
            if ($session['treatment_plan_id']) {
                $sessionData['treatmentPlanCreated'] = true;
                
                // Get treatment plan details
                $treatmentPlan = $this->getTreatmentPlanById($session['treatment_plan_id']);
                
                if ($treatmentPlan) {
                    $sessionData['diagnosis'] = $treatmentPlan['diagnosis'] ?? null;
                    $sessionData['treatment_description'] = $treatmentPlan['treatment_description'] ?? null;
                    $sessionData['medications'] = $treatmentPlan['medications'] ?? null;
                    $sessionData['travelRestrictions'] = $treatmentPlan['travel_restrictions'] ?? null;
                    $sessionData['estimatedBudget'] = $treatmentPlan['estimated_budget'] ?? null;
                    $sessionData['treatment_duration'] = $treatmentPlan['treatment_duration'] ?? null;
                    $sessionData['follow_up'] = $treatmentPlan['follow_up'] ?? null;
                    $sessionData['specialist_notes'] = $treatmentPlan['specialist_notes'] ?? null;
                    
                    // Get treatment plan creator
                    $query = "SELECT 
                            CONCAT(u.first_name, ' ', u.last_name) as doctor_name
                            FROM treatment_plans tp
                            JOIN doctors d ON tp.doctor_id = d.doctor_id
                            JOIN users u ON d.user_id = u.user_id
                            WHERE tp.plan_id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bind_param("i", $session['treatment_plan_id']);
                    $stmt->execute();
                    $doctor = $stmt->get_result()->fetch_assoc();
                    
                    if ($doctor) {
                        $sessionData['treatmentPlanCreator'] = $doctor['doctor_name'];
                    }
                }
            }
            
            // Get appointments for this session
            $query = "SELECT 
                    a.*,
                    d.doctor_id,
                    u.first_name as doctor_first_name,
                    u.last_name as doctor_last_name,
                    s.name as specialization
                    FROM appointments a
                    JOIN doctors d ON a.doctor_id = d.doctor_id
                    JOIN users u ON d.user_id = u.user_id
                    LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                    LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                    WHERE a.session_id = ?
                    ORDER BY a.appointment_date ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sessionId);
            $stmt->execute();
            $appointments = $stmt->get_result();
            
            while ($appointment = $appointments->fetch_assoc()) {
                // Check if general doctor or specialist based on specialization
                $isGeneral = ($appointment['specialization'] == 'General Medicine' || 
                            $appointment['specialization'] == 'General Practitioner' ||
                            !$appointment['specialization']);
                
                if ($isGeneral && !$sessionData['generalDoctorBooked']) {
                    $sessionData['generalDoctorBooked'] = true;
                    $sessionData['generalDoctor'] = [
                        'id' => $appointment['doctor_id'],
                        'name' => $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'],
                        'specialty' => $appointment['specialization'] ?? 'General Practitioner',
                        'appointmentDate' => $appointment['appointment_date'] . ' ' . $appointment['appointment_time'],
                        'appointmentMode' => $appointment['consultation_type'],
                        'meetLink' => $appointment['meet_link'] ?? ''
                    ];
                } elseif (!$isGeneral && !$sessionData['specialistBooked']) {
                    $sessionData['specialistBooked'] = true;
                    
                    // Get hospital name
                    $query = "SELECT h.name as hospital_name
                            FROM doctors d
                            LEFT JOIN hospitals h ON d.hospital_id = h.hospital_id
                            WHERE d.doctor_id = ?";
                    $stmt2 = $this->db->prepare($query);
                    $stmt2->bind_param("i", $appointment['doctor_id']);
                    $stmt2->execute();
                    $hospital = $stmt2->get_result()->fetch_assoc();
                    
                    $sessionData['specialist'] = [
                        'id' => $appointment['doctor_id'],
                        'name' => $appointment['doctor_first_name'] . ' ' . $appointment['doctor_last_name'],
                        'specialty' => $appointment['specialization'] ?? 'Specialist',
                        'hospital' => $hospital['hospital_name'] ?? 'General Hospital',
                        'appointmentDate' => $appointment['appointment_date'] . ' ' . $appointment['appointment_time'],
                        'appointmentMode' => $appointment['consultation_type'],
                        'meetLink' => $appointment['meet_link'] ?? ''
                    ];
                }
            }
            
            // Check for transportation assistance
            $query = "SELECT COUNT(*) as count FROM transportationassistance 
                    WHERE patient_id = ? AND status != 'Canceled'";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $transportResult = $stmt->get_result()->fetch_assoc();
            $sessionData['transportBooked'] = ($transportResult['count'] > 0);
            
            // Check for travel plans
            $query = "SELECT COUNT(*) as count FROM travel_plans WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $patientId);
            $stmt->execute();
            $travelResult = $stmt->get_result()->fetch_assoc();
            $sessionData['travelPlanSelected'] = ($travelResult['count'] > 0);
            
            return $sessionData;
        } catch (\Exception $e) {
            error_log("Error in getFormattedSessionData: " . $e->getMessage());
            return [
                'id' => $sessionId,
                'status' => 'Active',
                'generalDoctorBooked' => false,
                'specialistBooked' => false,
                'treatmentPlanCreated' => false,
                'transportBooked' => false,
                'travelPlanSelected' => false
            ];
        }
    }

    /**
     * Update general doctor notes
     * 
     * @param int $sessionId
     * @param string $notes
     * @return bool
     */
    public function updateGeneralDoctorNotes($sessionId, $notes) {
        try {
            $query = "UPDATE medical_sessions SET general_doctor_notes = ? WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $notes, $sessionId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to update notes: " . $stmt->error);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error updating general doctor notes: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update specialist notes
     * 
     * @param int $sessionId
     * @param string $notes
     * @return bool
     */
    public function updateSpecialistNotes($sessionId, $notes) {
        try {
            $query = "UPDATE medical_sessions SET specialist_notes = ? WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $notes, $sessionId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to update specialist notes: " . $stmt->error);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error updating specialist notes: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create a medical request
     * 
     * @param array $data
     * @return bool
     */
    public function createMedicalRequest($data) {
        try {
            $this->db->begin_transaction();
            
            // Extract fields and values from data
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $query = "INSERT INTO medical_requests ({$fields}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($query);
            
            // Create parameter binding string (all strings)
            $types = str_repeat('s', count($data));
            $stmt->bind_param($types, ...array_values($data));
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to create medical request: " . $stmt->error);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Error creating medical request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cancel a session
     * 
     * @param int $sessionId
     * @param string $cancelReason
     * @return bool
     */
    public function cancelSession($sessionId, $cancelReason) {
        try {
            $query = "UPDATE medical_sessions SET 
                    status = 'Canceled', 
                    updated_at = ?, 
                    cancel_reason = ? 
                    WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $updatedAt = date('Y-m-d H:i:s');
            $stmt->bind_param("ssi", $updatedAt, $cancelReason, $sessionId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to cancel session: " . $stmt->error);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error canceling session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Complete a session
     * 
     * @param int $sessionId
     * @return bool
     */
    public function completeSession($sessionId) {
        try {
            $query = "UPDATE medical_sessions SET 
                    status = 'Completed', 
                    updated_at = ? 
                    WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            $updatedAt = date('Y-m-d H:i:s');
            $stmt->bind_param("si", $updatedAt, $sessionId);
            
            if (!$stmt->execute()) {
                throw new \Exception("Failed to complete session: " . $stmt->error);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Error completing session: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get medical requests for a session
     * 
     * @param int $sessionId
     * @return array
     */
    public function getMedicalRequests($sessionId) {
        try {
            $query = "SELECT mr.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as doctor_name
                    FROM medical_requests mr
                    JOIN doctors d ON mr.doctor_id = d.doctor_id
                    JOIN users u ON d.user_id = u.user_id
                    WHERE mr.session_id = ?
                    ORDER BY mr.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $requests = [];
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
            
            return $requests;
        } catch (\Exception $e) {
            error_log("Error getting medical requests: " . $e->getMessage());
            return [];
        }
    }
}