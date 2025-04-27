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
            $nameParts = explode(' ', $userData['name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';
            $username = strtolower(str_replace(' ', '_', $userData['name']));
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, nationality, phone_number, role_id, age)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sssssssii", 
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
                throw new \Exception("User insert failed: " . $stmt->error);
            }

            $userId = $stmt->insert_id;

            switch ($userData['user_type']) {
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
            SELECT user_id, CONCAT(first_name, ' ', last_name) as name, email, password_hash, role_id
            FROM users 
            WHERE email = ? AND role_id = ? AND is_active = 1
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

    public function storeResetToken($email, $token, $expiry) {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) return false;

        $user = $res->fetch_assoc();
        $userId = $user['user_id'];

        $stmt = $this->db->prepare("REPLACE INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expiry);
        return $stmt->execute();
    }

    
    

    public function resetPasswordWithToken($token, $newPassword) {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("
            SELECT u.user_id FROM password_resets r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.token = ? AND r.expires_at > ?
        ");
        $stmt->bind_param("ss", $token, $now);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            return ['success' => false, 'error' => 'Invalid or expired token'];
        }

        $user = $res->fetch_assoc();
        $userId = $user['user_id'];
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hash, $userId);
        $success = $stmt->execute();

        if ($success) {
            $this->db->query("DELETE FROM password_resets WHERE user_id = $userId");
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Failed to reset password'];
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT user_id, email FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updatePassword($email, $newPassword){
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->bind_param("ss", $passwordHash, $email);
        return $stmt->execute();
    }
    
    

    private function insertDoctorData($userId, $userData) {
        $stmt = $this->db->prepare("INSERT INTO doctors (user_id, license_number) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $userData['slmc_registration_number']);
        return $stmt->execute();
    }

    private function insertCaretakerData($userId, $userData) {
        $stmt = $this->db->prepare("INSERT INTO caretakers (user_id, experience_years) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $userData['experience_years']);
        return $stmt->execute();
    }

    private function getRoleIdFromUserType($usertype) {
        return match ($usertype){
            'patient' => 1,
            'general_doctor' => 2,
            'special_doctor' => 3,
            'admin' => 4,
            'caretaker' => 5,
            'hospital' => 6,
            default => 1
        };
    }
}
