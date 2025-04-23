<?php

namespace App\Controllers;

use App\Models\Chat;

class ChatController extends BaseController
{
    private $chatModel;

    public function __construct()
    {
        parent::__construct();
        $this->chatModel = new Chat();
    }

    public function index()
    {
        try {
            error_log("=== Starting Chat Controller ===");

            // Check login status
            if (!$this->session->isLoggedIn()) {
                error_log("User not logged in");
                header("Location: " . $this->url('login'));
                exit();
            }

            $userId = $this->session->getUserId();
            $userRole = $this->session->getUserRole();

            error_log("User Details - ID: $userId, Role: $userRole");

            // Verify user exists in database
            $userQuery = "SELECT * FROM users WHERE user_id = ? AND role_id = ?";
            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param('ii', $userId, $userRole);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                error_log("User not found in database or role mismatch!");
                throw new \Exception("Invalid user credentials");
            }

            // Get chat participants based on role
            $receiverId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

            error_log("Receiver ID from GET: " . ($receiverId ?? 'null'));

            // Handle chat based on user role
            $viewData = null;
            if ($userRole == 1) { // Patient
                error_log("Handling patient chat");
                $viewData = $this->handlePatientChat($userId, $receiverId);
            } elseif ($userRole == 2 || $userRole == 3) { // Doctor or Specialist
                error_log("Handling doctor chat");
                $viewData = $this->handleDoctorChat($userId, $receiverId);
            } else {
                error_log("Invalid role: $userRole");
                throw new \Exception("Invalid user role");
            }

            // Add common view data
            $viewData = array_merge($viewData, [
                'userId' => $userId,
                'userRole' => $userRole,
                'basePath' => $this->basePath
            ]);

            // Determine the view path based on the user role
            if ($userRole == 1) {
                $viewPath = 'patient/chat';
            } elseif ($userRole == 3) {
                $viewPath = 'vpdoctor/chat'; // Use the VPDoctor chat view
            } else {
                $viewPath = 'doctor/chat';
            }
            error_log("Loading view: $viewPath");

            echo $this->view($viewPath, $viewData);
        } catch (\Exception $e) {
            error_log("Error in chat index: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    private function handlePatientChat($userId, $doctorUserId)
    {
        error_log("=== Starting handlePatientChat ===");
        error_log("User ID: $userId, Doctor User ID: " . ($doctorUserId ?? 'null'));

        try {
            // Get all doctors the patient has had appointments with
            $doctors = $this->chatModel->getDoctorsForChat($userId);
            error_log("Found doctors: " . print_r($doctors, true));

            $activeChatId = null;
            $messages = [];
            $activeDoctor = null;

            if ($doctorUserId) {
                // Verify patient-doctor relationship
                if (!$this->chatModel->verifyPatientDoctorRelationship($userId, $doctorUserId)) {
                    throw new \Exception("No valid appointments found with this doctor");
                }

                // Get or create conversation
                $activeChatId = $this->chatModel->getOrCreateConversation($userId, $doctorUserId);
                error_log("Active chat ID: $activeChatId");

                if ($activeChatId) {
                    // Get messages
                    $messages = $this->chatModel->getMessages($activeChatId);
                    error_log("Message count: " . count($messages));

                    // Mark messages as read
                    $this->chatModel->markMessagesAsRead($activeChatId, $userId);

                    // Get active doctor details
                    $activeDoctor = $this->findUserInList($doctors, $doctorUserId);
                }
            }

            return [
                'doctors' => $doctors,
                'activeChatId' => $activeChatId,
                'activeDoctor' => $activeDoctor,
                'messages' => $messages
            ];
        } catch (\Exception $e) {
            error_log("Error in handlePatientChat: " . $e->getMessage());
            throw $e;
        }
    }

    private function handleDoctorChat($userId, $patientUserId)
    {
        error_log("=== Starting handleDoctorChat ===");
        error_log("User ID: $userId, Patient User ID: " . ($patientUserId ?? 'null'));

        try {
            // Get all patients the doctor has had appointments with
            $patients = $this->chatModel->getPatientsForChat($userId);
            error_log("Found patients: " . print_r($patients, true));

            $activeChatId = null;
            $messages = [];
            $activePatient = null;

            if ($patientUserId) {
                // Get or create conversation
                $activeChatId = $this->chatModel->getOrCreateConversation($userId, $patientUserId);
                error_log("Active chat ID: $activeChatId");

                if ($activeChatId) {
                    // Get messages
                    $messages = $this->chatModel->getMessages($activeChatId);
                    error_log("Message count: " . count($messages));

                    // Mark messages as read
                    $this->chatModel->markMessagesAsRead($activeChatId, $userId);

                    // Get active patient details
                    $activePatient = $this->findUserInList($patients, $patientUserId);
                }
            }

            return [
                'patients' => $patients,
                'activeChatId' => $activeChatId,
                'activePatient' => $activePatient,
                'messages' => $messages
            ];
        } catch (\Exception $e) {
            error_log("Error in handleDoctorChat: " . $e->getMessage());
            throw $e;
        }
    }

    private function findUserInList($users, $userId)
    {
        foreach ($users as $user) {
            if ($user['user_id'] == $userId) {
                return $user;
            }
        }
        return null;
    }

    public function sendMessage()
    {
        try {
            // Verify CSRF token
            if (!$this->session->verifyCSRFToken($_POST['csrf_token'])) {
                throw new \Exception("Invalid CSRF token");
            }

            $userId = $this->session->getUserId();
            $receiverId = intval($_POST['receiver_id']);
            $message = trim($_POST['message']);

            if (empty($message)) {
                throw new \Exception("Message cannot be empty");
            }

            // Handle file upload if present
            $attachmentPath = null;
            $attachmentType = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['attachment']);
                $attachmentPath = $uploadResult['path'];
                $attachmentType = $uploadResult['type'];
            }

            // Get or create conversation and send message
            $conversationId = $this->chatModel->getOrCreateConversation($userId, $receiverId);
            $messageId = $this->chatModel->sendMessage($conversationId, $userId, $message, $attachmentPath, $attachmentType);

            // Return success response
            $this->sendJsonResponse([
                'success' => true,
                'message' => htmlspecialchars($message),
                'time' => date('H:i'),
                'messageId' => $messageId,
                'attachment' => $attachmentPath ? [
                    'path' => $attachmentPath,
                    'type' => $attachmentType
                ] : null
            ]);
        } catch (\Exception $e) {
            error_log("Error sending message: " . $e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    private function handleFileUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception("Invalid file type");
        }

        if ($file['size'] > $maxSize) {
            throw new \Exception("File size exceeds limit");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $uploadPath = ROOT_PATH . '/public/uploads/chat/' . $filename;

        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0777, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new \Exception("Failed to save file");
        }

        return [
            'path' => 'uploads/chat/' . $filename,
            'type' => $file['type']
        ];
    }

    public function getNewMessages()
    {
        try {
            $userId = $this->session->getUserId();
            $receiverId = intval($_GET['receiver_id']);
            $lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;

            // Get conversation
            $conversationId = $this->chatModel->getOrCreateConversation($userId, $receiverId);

            // Get new messages
            $messages = $this->chatModel->getNewMessages($conversationId, $lastId);

            // Mark messages as read
            $this->chatModel->markMessagesAsRead($conversationId, $userId);

            // Format messages for response
            $formattedMessages = $this->formatMessages($messages);

            $this->sendJsonResponse($formattedMessages);
        } catch (\Exception $e) {
            error_log("Error getting new messages: " . $e->getMessage());
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function downloadAttachment()
    {
        try {
            $userId = $this->session->getUserId();
            $messageId = intval($_GET['message_id']);

            // Verify user has access to the message
            if (!$this->chatModel->canAccessMessage($userId, $messageId)) {
                throw new \Exception("Unauthorized access to attachment");
            }

            $attachment = $this->chatModel->getMessageAttachment($messageId);
            if (!$attachment) {
                throw new \Exception("Attachment not found");
            }

            $filePath = ROOT_PATH . '/public/' . $attachment['path'];
            if (!file_exists($filePath)) {
                throw new \Exception("File not found");
            }

            // Send file headers
            header('Content-Type: ' . $attachment['type']);
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Content-Length: ' . filesize($filePath));

            readfile($filePath);
            exit();
        } catch (\Exception $e) {
            error_log("Error downloading attachment: " . $e->getMessage());
            throw $e;
        }
    }

    private function formatMessages($messages)
    {
        if (!is_array($messages)) {
            error_log("Invalid messages format provided to formatMessages");
            return [];
        }

        $formattedMessages = [];
        foreach ($messages as $message) {
            try {
                // Validate required fields
                if (
                    !isset($message['message_id']) || !isset($message['message_text']) ||
                    !isset($message['sent_time']) || !isset($message['sender_id'])
                ) {
                    error_log("Missing required fields in message: " . print_r($message, true));
                    continue;
                }

                $formattedMessage = [
                    'id' => $message['message_id'],
                    'message' => htmlspecialchars($message['message_text']),
                    'time' => date('H:i', strtotime($message['sent_time'])),
                    'timestamp' => $message['sent_time'],
                    'sender_id' => $message['sender_id'],
                    'sender_name' => isset($message['first_name']) && isset($message['last_name'])
                        ? $message['first_name'] . ' ' . $message['last_name']
                        : 'Unknown User',
                    'is_read' => isset($message['is_read']) ? (bool)$message['is_read'] : false,
                    'attachment' => null
                ];

                // Handle attachment if present
                if (!empty($message['attachment_path'])) {
                    $formattedMessage['attachment'] = [
                        'path' => $message['attachment_path'],
                        'type' => $message['attachment_type'] ?? 'application/octet-stream',
                        'filename' => basename($message['attachment_path'])
                    ];
                }

                // Add profile picture if available
                if (isset($message['profile_picture'])) {
                    $formattedMessage['sender_profile_picture'] = $message['profile_picture'];
                }

                $formattedMessages[] = $formattedMessage;
            } catch (\Exception $e) {
                error_log("Error formatting message: " . $e->getMessage());
                error_log("Message data: " . print_r($message, true));
                continue;
            }
        }

        return $formattedMessages;
    }

    private function sendJsonResponse($data, $statusCode = 200)
    {
        try {
            if (headers_sent($filename, $linenum)) {
                error_log("Headers already sent in $filename on line $linenum");
                throw new \Exception("Headers already sent");
            }

            // Clean and validate data before sending
            $cleanData = $this->sanitizeResponseData($data);

            // Set response headers
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');

            // Handle errors appropriately
            if ($statusCode >= 400) {
                $response = [
                    'success' => false,
                    'error' => is_string($data) ? $data : (isset($data['error']) ? $data['error'] : 'Unknown error'),
                    'status' => $statusCode
                ];
            } else {
                $response = [
                    'success' => true,
                    'data' => $cleanData,
                    'status' => $statusCode
                ];
            }

            // Log response for debugging if needed
            if ($statusCode !== 200) {
                error_log("Sending JSON response with status $statusCode: " . json_encode($response));
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            error_log("Error sending JSON response: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Internal Server Error',
                'status' => 500
            ]);
        }
        exit();
    }

    private function sanitizeResponseData($data)
    {
        if (is_array($data)) {
            $clean = [];
            foreach ($data as $key => $value) {
                // Sanitize array keys
                $cleanKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
                $clean[$cleanKey] = $this->sanitizeResponseData($value);
            }
            return $clean;
        } elseif (is_string($data)) {
            // Remove any potential XSS or malicious content from strings
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        } elseif (is_numeric($data) || is_bool($data) || is_null($data)) {
            return $data;
        } else {
            // Convert any other types to string
            return (string)$data;
        }
    }
}