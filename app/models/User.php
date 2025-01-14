<?php

namespace App\Models;

class User {
    protected $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function register($userData) {
        try {
            $this->db->begin_transaction();

            $roleId = $this->getRoleIdFromUserType($userData['user_type']);
            
            // Split name into first and last name
            $nameParts = explode(' ', $userData['name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            
            // Create username from name
            $username = strtolower(str_replace(' ', '_', $userData['name']));
            
            // Hash password
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

            // Insert into users table
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, 
                                nationality, phone_number, role_id, age) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("sssssssii", 
                $username, 
                $userData['email'],
                $passwordHash,
                $firstName,
                $lastName,
                $userData['country'],
                $userData['contact_number'],
                $roleId,
                $userData['age']
            );
            
            if (!$stmt->execute()) {
                throw new \Exception("Error creating user account");
            }

            $userId = $stmt->insert_id;

            // Handle role-specific data
            switch($userData['user_type']) {
                case 'general_doctor':
                case 'special_doctor':
                    $this->insertDoctorData($userId, $userData);
                    break;
                case 'caretaker':
                    $this->insertCaretakerData($userId, $userData);
                    break;
            }

            $this->db->commit();
            return ['success' => true];

        } catch (\Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function authenticate($email, $password, $userType) {
        // Special handling for admin and travel partner
        if ($userType === 'admin' && $email === 'admin@example.com' && $password === 'admin123') {
            return [
                'success' => true,
                'user' => [
                    'user_id' => 3,
                    'name' => 'Admin User',
                    'role_id' => 4
                ]
            ];
        }

        $roleId = $this->getRoleIdFromUserType($userType);

        $stmt = $this->db->prepare("
            SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as name, 
                   u.email, u.password_hash, u.role_id
            FROM users u
            WHERE u.email = ? AND u.role_id = ? AND u.is_active = 1
        ");
        
        $stmt->bind_param("si", $email, $roleId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                return ['success' => true, 'user' => $user];
            }
        }
        
        return ['success' => false, 'error' => 'Invalid credentials'];
    }

    private function getRoleIdFromUserType($userType) {
        return match($userType) {
            'patient' => 1,
            'general_doctor', 'special_doctor' => 2,
            'caretaker' => 5,
            'admin' => 4,
            'travel_partner' => 6,
            default => 1
        };
    }

    private function insertDoctorData($userId, $userData) {
        $stmt = $this->db->prepare("
            INSERT INTO doctors (user_id, license_number) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $userId, $userData['slmc_registration_number']);
        return $stmt->execute();
    }

    private function insertCaretakerData($userId, $userData) {
        $stmt = $this->db->prepare("
            INSERT INTO caretakers (user_id, experience_years) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $userId, $userData['experience_years']);
        return $stmt->execute();
    }
}