<?php
// models/ChatModel.php

class ChatModel {
    private $db;

    public function __construct() {
        // Initialize database connection
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=medceylon', 'root', '');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set charset to UTF-8
            $this->db->exec("SET NAMES 'utf8';");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    // Fetch conversations for a user
    public function getConversations($userId) {
        $stmt = $this->db->prepare("
            SELECT c.conversation_id, c.last_message_time,
                   u.user_id, u.first_name, u.last_name, u.profile_picture
            FROM Conversations c
            INNER JOIN Users u ON (u.user_id = IF(c.participant1_id = :userId, c.participant2_id, c.participant1_id))
            WHERE c.participant1_id = :userId OR c.participant2_id = :userId
            ORDER BY c.last_message_time DESC
        ");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch messages in a conversation
    public function getMessages($conversationId) {
        $stmt = $this->db->prepare("
            SELECT m.message_id, m.sender_id, m.message_text, m.sent_time, m.is_read,
                   u.first_name, u.last_name, u.profile_picture
            FROM Messages m
            INNER JOIN Users u ON m.sender_id = u.user_id
            WHERE m.conversation_id = :conversationId
            ORDER BY m.sent_time ASC
        ");
        $stmt->execute(['conversationId' => $conversationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Send a message
    public function sendMessage($conversationId, $senderId, $messageText) {
        $this->db->beginTransaction();
        try {
            // Insert message
            $stmt = $this->db->prepare("
                INSERT INTO Messages (conversation_id, sender_id, message_text)
                VALUES (:conversationId, :senderId, :messageText)
            ");
            $stmt->execute([
                'conversationId' => $conversationId,
                'senderId' => $senderId,
                'messageText' => $messageText
            ]);

            // Update last_message_time in Conversations
            $stmt = $this->db->prepare("
                UPDATE Conversations
                SET last_message_time = NOW()
                WHERE conversation_id = :conversationId
            ");
            $stmt->execute(['conversationId' => $conversationId]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Create a new conversation
    public function createConversation($participant1Id, $participant2Id) {
        $stmt = $this->db->prepare("
            INSERT INTO Conversations (participant1_id, participant2_id)
            VALUES (:participant1Id, :participant2Id)
        ");
        $stmt->execute([
            'participant1Id' => $participant1Id,
            'participant2Id' => $participant2Id
        ]);
        return $this->db->lastInsertId();
    }

    // Check if a conversation exists between two users
    public function getConversationByParticipants($userId, $otherUserId) {
        $stmt = $this->db->prepare("
            SELECT conversation_id
            FROM Conversations
            WHERE (participant1_id = :userId AND participant2_id = :otherUserId)
               OR (participant1_id = :otherUserId AND participant2_id = :userId)
        ");
        $stmt->execute([
            'userId' => $userId,
            'otherUserId' => $otherUserId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch user info
    public function getUserById($userId) {
        $stmt = $this->db->prepare("
            SELECT user_id, first_name, last_name, profile_picture
            FROM Users
            WHERE user_id = :userId
        ");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
