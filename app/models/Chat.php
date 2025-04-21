<?php

namespace App\Models;

class Chat
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function getConversations($userId)
    {
        try {
            $query = "SELECT 
                        c.*,
                        u.first_name, 
                        u.last_name, 
                        u.profile_picture,
                        u.role_id,
                        d.specialization,
                        m.message_text as last_message,
                        m.sent_time as last_message_time,
                        COUNT(CASE WHEN m2.is_read = 0 AND m2.sender_id != ? THEN 1 END) as unread_count
                    FROM conversations c
                    JOIN users u ON (CASE 
                        WHEN c.participant1_id = ? THEN c.participant2_id
                        ELSE c.participant1_id 
                    END) = u.user_id
                    LEFT JOIN doctors d ON u.user_id = d.user_id
                    LEFT JOIN messages m ON c.last_message_id = m.message_id
                    LEFT JOIN messages m2 ON c.conversation_id = m2.conversation_id
                    WHERE (c.participant1_id = ? OR c.participant2_id = ?)
                    AND c.status = 'active'
                    GROUP BY c.conversation_id
                    ORDER BY c.updated_at DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iiii', $userId, $userId, $userId, $userId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getConversations: " . $e->getMessage());
            throw $e;
        }
    }

    public function getOrCreateConversation($senderId, $receiverId)
    {
        try {
            // First try to find existing conversation
            $query = "SELECT conversation_id 
                     FROM conversations 
                     WHERE (participant1_id = ? AND participant2_id = ?)
                     OR (participant1_id = ? AND participant2_id = ?)
                     AND status = 'active'
                     LIMIT 1";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iiii', $senderId, $receiverId, $receiverId, $senderId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                return $result->fetch_assoc()['conversation_id'];
            }

            // Create new conversation
            $this->db->begin_transaction();

            try {
                $currentTime = date('Y-m-d H:i:s');
                $query = "INSERT INTO conversations 
                         (participant1_id, participant2_id, status, last_message_time, updated_at) 
                         VALUES (?, ?, 'active', ?, ?)";
                         
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('iiss', $senderId, $receiverId, $currentTime, $currentTime);

                if (!$stmt->execute()) {
                    throw new \Exception("Failed to create conversation");
                }

                $conversationId = $this->db->insert_id;
                $this->db->commit();
                return $conversationId;
            } catch (\Exception $e) {
                $this->db->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Error in getOrCreateConversation: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDoctorsForChat($patientId)
    {
        try {
            $query = "SELECT DISTINCT 
                        u.user_id,
                        u.first_name, 
                        u.last_name,
                        u.profile_picture,
                        s.name as specialization
                     FROM appointments a
                     JOIN doctors d ON a.doctor_id = d.doctor_id
                     JOIN users u ON d.user_id = u.user_id
                     LEFT JOIN doctorspecializations ds ON d.doctor_id = ds.doctor_id
                     LEFT JOIN specializations s ON ds.specialization_id = s.specialization_id
                     WHERE a.patient_id = ?
                     AND a.appointment_status IN ('Scheduled', 'Completed', 'Asked')
                     AND u.role_id IN (2, 3)
                     GROUP BY u.user_id
                     ORDER BY MAX(a.appointment_date) DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $patientId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getDoctorsForChat: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPatientsForChat($doctorUserId)
    {
        try {
            // First get the doctor_id from the doctors table
            $doctorQuery = "SELECT doctor_id FROM doctors WHERE user_id = ?";
            $stmt = $this->db->prepare($doctorQuery);
            $stmt->bind_param('i', $doctorUserId);
            $stmt->execute();
            $result = $stmt->get_result();
            $doctorData = $result->fetch_assoc();
            
            if (!$doctorData) {
                return [];
            }
            
            $doctorId = $doctorData['doctor_id'];

            // Now get the patients using doctor_id
            $query = "SELECT DISTINCT 
                        u.user_id, 
                        u.first_name, 
                        u.last_name,
                        u.profile_picture,
                        MAX(a.appointment_date) as last_appointment
                     FROM appointments a
                     JOIN users u ON a.patient_id = u.user_id
                     WHERE a.doctor_id = ?
                     AND a.appointment_status IN ('Completed', 'Scheduled')
                     AND u.is_active = 1
                     AND u.role_id = 1
                     GROUP BY u.user_id
                     ORDER BY last_appointment DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $doctorId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getPatientsForChat: " . $e->getMessage());
            throw $e;
        }
    }


    public function sendMessage($conversationId, $senderId, $messageText, $attachmentPath = null, $attachmentType = null)
    {
        try {
            $this->db->begin_transaction();

            try {
                // Insert message
                $query = "INSERT INTO messages (
                            conversation_id, 
                            sender_id, 
                            message_text, 
                            attachment_path, 
                            attachment_type, 
                            sent_time,
                            is_read
                        ) VALUES (?, ?, ?, ?, ?, NOW(), 0)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('iisss', $conversationId, $senderId, $messageText, $attachmentPath, $attachmentType);

                if (!$stmt->execute()) {
                    throw new \Exception("Failed to insert message");
                }

                $messageId = $this->db->insert_id;

                // Update conversation's last message and time
                $query = "UPDATE conversations 
                         SET last_message_id = ?,
                             last_message_time = NOW(),
                             updated_at = NOW() 
                         WHERE conversation_id = ?";
                         
                $stmt = $this->db->prepare($query);
                $stmt->bind_param('ii', $messageId, $conversationId);

                if (!$stmt->execute()) {
                    throw new \Exception("Failed to update conversation");
                }

                $this->db->commit();
                return $messageId;
            } catch (\Exception $e) {
                $this->db->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            error_log("Error in sendMessage: " . $e->getMessage());
            throw $e;
        }
    }

    public function getMessages($conversationId)
    {
        try {
            $query = "SELECT 
                        m.*,
                        u.first_name,
                        u.last_name,
                        u.profile_picture,
                        u.role_id
                    FROM messages m
                    JOIN users u ON m.sender_id = u.user_id
                    WHERE m.conversation_id = ? 
                    AND m.deleted_at IS NULL
                    ORDER BY m.sent_time ASC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $conversationId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getMessages: " . $e->getMessage());
            throw $e;
        }
    }

    public function getNewMessages($conversationId, $lastId)
    {
        try {
            $query = "SELECT 
                    m.message_id,
                    m.message_text,
                    m.sender_id,
                    m.sent_time,
                    m.attachment_path,
                    m.attachment_type,
                    u.first_name,
                    u.last_name,
                    u.role_id
                 FROM messages m
                 JOIN users u ON m.sender_id = u.user_id
                 WHERE m.conversation_id = ?
                 AND m.message_id > ?
                 ORDER BY m.sent_time ASC";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $conversationId, $lastId);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getNewMessages: " . $e->getMessage());
            throw $e;
        }
    }

    public function verifyPatientDoctorRelationship($patientId, $doctorUserId)
    {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM appointments a
                     JOIN doctors d ON a.doctor_id = d.doctor_id
                     WHERE a.patient_id = ? 
                     AND d.user_id = ? 
                     AND a.appointment_status IN ('Scheduled', 'Completed', 'Asked')";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $patientId, $doctorUserId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['count'] > 0;
        } catch (\Exception $e) {
            error_log("Error in verifyPatientDoctorRelationship: " . $e->getMessage());
            throw $e;
        }
    
    }

    public function markMessagesAsRead($conversationId, $userId)
    {
        try {
            $query = "UPDATE messages 
                     SET is_read = 1 
                     WHERE conversation_id = ? 
                     AND sender_id != ? 
                     AND is_read = 0";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ii', $conversationId, $userId);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error in markMessagesAsRead: " . $e->getMessage());
            throw $e;
        }
    }

    public function canAccessMessage($userId, $messageId)
    {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM messages m
                     JOIN conversations c ON m.conversation_id = c.conversation_id
                     WHERE m.message_id = ? 
                     AND (c.participant1_id = ? OR c.participant2_id = ?)";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iii', $messageId, $userId, $userId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['count'] > 0;
        } catch (\Exception $e) {
            error_log("Error in canAccessMessage: " . $e->getMessage());
            throw $e;
        }
    }

    public function getMessageAttachment($messageId)
    {
        try {
            $query = "SELECT attachment_path as path, attachment_type as type 
                     FROM messages 
                     WHERE message_id = ?";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('i', $messageId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (\Exception $e) {
            error_log("Error in getMessageAttachment: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUnreadCount($userId)
    {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM messages m
                     JOIN conversations c ON m.conversation_id = c.conversation_id
                     WHERE (c.participant1_id = ? OR c.participant2_id = ?)
                     AND m.sender_id != ?
                     AND m.is_read = 0
                     AND c.status = 'active'";
                     
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('iii', $userId, $userId, $userId);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc()['count'];
        } catch (\Exception $e) {
            error_log("Error in getUnreadCount: " . $e->getMessage());
            throw $e;
        }
    }
}