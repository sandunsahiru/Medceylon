<?php
// controllers/ChatController.php

class ChatController {
    private $chatModel;

    public function __construct() {
        $this->chatModel = new ChatModel();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login if not logged in
            header('Location: index.php?page=login');
            exit();
        }

        $userId = $_SESSION['user_id'];

        // Get selected conversation ID from GET or default to first conversation
        $conversationId = isset($_GET['conversation_id']) ? $_GET['conversation_id'] : null;

        // Get conversations
        $conversations = $this->chatModel->getConversations($userId);

        if (!$conversationId && !empty($conversations)) {
            $conversationId = $conversations[0]['conversation_id'];
        }

        // Get messages if a conversation is selected
        $messages = [];
        $contact = null;
        if ($conversationId) {
            $messages = $this->chatModel->getMessages($conversationId);

            // Get contact info
            foreach ($conversations as $conv) {
                if ($conv['conversation_id'] == $conversationId) {
                    $contact = $conv;
                    break;
                }
            }
        }

        // Pass data to the view
        require_once 'views/chat.php';
    }

    public function sendMessage() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            exit();
        }

        $senderId = $_SESSION['user_id'];
        $conversationId = $_POST['conversation_id'];
        $messageText = $_POST['message'];

        // Sanitize message
        $messageText = htmlspecialchars($messageText, ENT_QUOTES, 'UTF-8');

        // Save message
        $success = $this->chatModel->sendMessage($conversationId, $senderId, $messageText);

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
        }
    }

    public function fetchMessages() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
            exit();
        }

        $conversationId = $_GET['conversation_id'];

        $messages = $this->chatModel->getMessages($conversationId);

        // Return messages as JSON
        echo json_encode(['status' => 'success', 'messages' => $messages]);
    }
    public function startConversation() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            // Redirect to login if not logged in
            header('Location: index.php?page=login');
            exit();
        }
    
        $userId = $_SESSION['user_id'];
        $otherUserId = $_GET['user_id'];
    
        // Check if conversation already exists
        $conversation = $this->chatModel->getConversationByParticipants($userId, $otherUserId);
    
        if ($conversation) {
            // Redirect to existing conversation
            header('Location: index.php?page=chat&conversation_id=' . $conversation['conversation_id']);
        } else {
            // Create new conversation
            $conversationId = $this->chatModel->createConversation($userId, $otherUserId);
            header('Location: index.php?page=chat&conversation_id=' . $conversationId);
        }
    }
    
}
?>
