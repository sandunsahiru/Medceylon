<?php
// Debug session information
error_log("Session information in chat view:");
error_log("Is logged in: " . ($this->session->isLoggedIn() ? 'Yes' : 'No'));
error_log("User ID: " . $this->session->getUserId());
error_log("User Role: " . $this->session->getUserRole());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - MediCare</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/public/assets/css/patients.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 140px);
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        .chat-sidebar {
            width: 300px;
            border-right: 1px solid #e0e0e0;
            overflow-y: auto;
        }

        .chat-list {
            padding: 10px;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-item:hover {
            background-color: #f5f5f5;
        }

        .chat-item.active {
            background-color: var(--primary-color);
            color: white;
        }

        .chat-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .chat-item-info {
            flex: 1;
        }

        .chat-item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .chat-item-preview {
            font-size: 0.9em;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
        }

        .chat-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }

        .message {
            margin-bottom: 20px;
            max-width: 70%;
            display: flex;
            flex-direction: column;
        }

        .message.sent {
            align-self: flex-end;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px 12px 12px 2px;
            background-color: #fff;
            display: inline-block;
            word-break: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .message.sent .message-content {
            background-color: var(--primary-color);
            color: white;
            border-radius: 12px 12px 2px 12px;
        }

        .message-time {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
            text-align: left;
        }

        .message.sent .message-time {
            text-align: right;
        }

        .chat-input {
            padding: 20px;
            border-top: 1px solid #e0e0e0;
            background-color: #fff;
        }

        .chat-input form {
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            outline: none;
        }

        .chat-input button {
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .chat-input button:hover {
            background-color: var(--primary-dark);
        }

        .chat-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
            width: 100%;
        }

        .attachment-btn {
            background: none;
            border: none;
            padding: 8px;
            cursor: pointer;
            color: var(--primary-color);
        }

        .hidden {
            display: none;
        }

        .attachment-preview {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .message.sent .attachment-preview {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .error-message {
            color: #dc3545;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #fbe7e9;
            display: none;
        }

        .chat-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #666;
            font-size: 1.1em;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <a href="<?php echo $basePath; ?>" style="text-decoration: none; color: var(--primary-color);">
                    <h1>Medceylon</h1>
                </a>
            </div>

            <nav class="nav-menu">
                <a href="<?php echo $basePath; ?>/patient/dashboard" class="nav-item">
                    <i class="ri-dashboard-line"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/book-appointment" class="nav-item">
                    <i class="ri-calendar-line"></i>
                    <span>Book Appointment</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/chat" class="nav-item active">
                    <i class="ri-message-3-line"></i>
                    <span>Chat</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/medical-history" class="nav-item">
                    <i class="ri-file-list-line"></i>
                    <span>Medical History</span>
                </a>
                <a href="<?php echo $basePath; ?>/patient/profile" class="nav-item">
                    <i class="ri-user-line"></i>
                    <span>Profile</span>
                </a>
            </nav>

            <a href="<?php echo $basePath; ?>/logout" class="exit-button">
                <i class="ri-logout-box-line"></i>
                <span>Exit</span>
            </a>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Chat</h1>
                <div class="header-right">
                    <div class="search-box">
                        <i class="ri-search-line"></i>
                        <input type="text" placeholder="Search conversations">
                    </div>
                    <div class="date">
                        <i class="ri-calendar-line"></i>
                        <?php echo date('l, d.m.Y'); ?>
                    </div>
                </div>
            </header>

            <div class="chat-container">
                <div class="chat-sidebar">
                    <div class="chat-list">
                        <?php if (isset($doctors) && is_array($doctors) && count($doctors) > 0): ?>
                            <?php foreach ($doctors as $doctor): ?>
                                <div class="chat-item <?php echo ($activeChatId && $activeDoctor && $activeDoctor['user_id'] == $doctor['user_id']) ? 'active' : ''; ?>"
                                    data-doctor-id="<?php echo htmlspecialchars($doctor['user_id']); ?>">
                                    <img src="<?php echo $basePath; ?>/public/assets/images/doctor-avatar.png" alt="Doctor avatar">
                                    <div class="chat-item-info">
                                        <div class="chat-item-name">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></div>
                                        <div class="chat-item-preview"><?php echo htmlspecialchars($doctor['specialization'] ?? ''); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="chat-item">
                                <div class="chat-item-info">
                                    <div class="chat-item-name">No doctors available</div>
                                    <div class="chat-item-preview">Book an appointment to start chatting</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="chat-main">
                    <?php if ($activeChatId): ?>
                        <div class="chat-header">
                            <img src="<?php echo $basePath; ?>/public/assets/images/doctor-avatar.png" alt="Doctor avatar">
                            <div class="chat-header-info">
                                <h2>Dr. <?php echo htmlspecialchars($activeDoctor['first_name'] . ' ' . $activeDoctor['last_name']); ?></h2>
                                <p><?php echo htmlspecialchars($activeDoctor['specialization'] ?? ''); ?></p>
                            </div>
                        </div>

                        <div class="chat-messages" id="chatMessages">
                            <?php if (isset($messages) && is_array($messages)): ?>
                                <?php foreach ($messages as $message): ?>
                                    <div class="message <?php echo $message['sender_id'] == $userId ? 'sent' : 'received'; ?>"
                                        data-message-id="<?php echo htmlspecialchars($message['message_id']); ?>">
                                        <div class="message-content">
                                            <?php echo htmlspecialchars($message['message_text']); ?>
                                            <?php if (!empty($message['attachment_path'])): ?>
                                                <div class="attachment-preview">
                                                    <a href="<?php echo $basePath; ?>/patient/download-attachment?message_id=<?php echo $message['message_id']; ?>" target="_blank">
                                                        <i class="ri-attachment-2"></i> View Attachment
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="message-time">
                                            <?php echo date('H:i', strtotime($message['sent_time'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="error-message" id="errorMessage"></div>

                        <div class="chat-input">
                            <form id="messageForm" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $this->session->getCSRFToken(); ?>">
                                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($activeDoctor['user_id']); ?>">
                                <div class="chat-input-container">
                                    <input type="text" name="message" placeholder="Type your message..." required>
                                    <input type="file" name="attachment" id="attachment" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <button type="button" class="attachment-btn" onclick="document.getElementById('attachment').click()">
                                        <i class="ri-attachment-2"></i>
                                    </button>
                                    <button type="submit" id="sendButton">Send</button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="chat-placeholder">
                            <p>Select a conversation to start chatting</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        const basePath = '<?php echo $basePath; ?>';
        const userId = <?php echo $userId; ?>;
        let lastMessageId = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const messageForm = document.getElementById('messageForm');
            const chatMessages = document.getElementById('chatMessages');
            const chatItems = document.querySelectorAll('.chat-item');
            const errorMessage = document.getElementById('errorMessage');
            const fileInput = document.getElementById('attachment');
            let pollingInterval;

            // Initialize last message ID
            if (chatMessages) {
                const messages = chatMessages.querySelectorAll('.message');
                if (messages.length > 0) {
                    const lastMessage = messages[messages.length - 1];
                    lastMessageId = parseInt(lastMessage.dataset.messageId) || 0;
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // File input change handler
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        if (file.size > 5 * 1024 * 1024) {
                            showError('File size must be less than 5MB');
                            this.value = '';
                        }
                    }
                });
            }

            // Handle message submission
            if (messageForm) {
                messageForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(messageForm);
                    const submitButton = messageForm.querySelector('button[type="submit"]');
                    const messageInput = messageForm.querySelector('input[name="message"]');
                    const message = messageInput.value.trim();

                    try {
                        submitButton.disabled = true;

                        // Get current time
                        const currentTime = new Date();
                        const formattedTime = currentTime.toLocaleTimeString('en-US', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: false
                        });

                        // Add message to UI immediately
                        const tempMessageHtml = `
                            <div class="message sent temp-message">
                                <div class="message-content">
                                    ${message}
                                </div>
                                <div class="message-time">
                                    ${formattedTime}
                                </div>
                            </div>
                        `;
                        chatMessages.insertAdjacentHTML('beforeend', tempMessageHtml);
                        chatMessages.scrollTop = chatMessages.scrollHeight;

                        // Clear form
                        messageForm.reset();

                        // Send to server
                        const response = await fetch(`${basePath}/patient/send-message`, {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            throw new Error(result.error || 'Failed to send message');
                        }

                    } catch (error) {
                        showError(error.message);
                        // Remove the temporary message if sending failed
                        const tempMessages = chatMessages.querySelectorAll('.temp-message');
                        if (tempMessages.length > 0) {
                            tempMessages[tempMessages.length - 1].remove();
                        }
                        // Restore the message in the input
                        messageInput.value = message;
                    } finally {
                        submitButton.disabled = false;
                    }
                });
            }

            // Handle chat selection
            chatItems.forEach(item => {
                item.addEventListener('click', function() {
                    const doctorId = this.dataset.doctorId;
                    window.location.href = `${basePath}/patient/chat?user_id=${doctorId}`; // Changed to user_id
                });
            });

            // Poll for new messages
            if (chatMessages) {
                pollingInterval = setInterval(async function() {
                    try {
                        const receiverInput = document.querySelector('input[name="receiver_id"]');
                        if (!receiverInput) {
                            clearInterval(pollingInterval);
                            return;
                        }

                        const receiverId = receiverInput.value;

                        const response = await fetch(`${basePath}/patient/get-new-messages?receiver_id=${receiverId}&last_id=${lastMessageId}`);
                        const newMessages = await response.json();

                        if (newMessages.length > 0) {
                            // Remove any temporary messages first
                            const tempMessages = chatMessages.querySelectorAll('.temp-message');
                            tempMessages.forEach(msg => msg.remove());

                            newMessages.forEach(message => {
                                // Check if message already exists
                                const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                                if (!existingMessage && message.id > lastMessageId) {
                                    const messageHtml = `
                                        <div class="message ${message.sender_id === userId ? 'sent' : 'received'}" 
                                             data-message-id="${message.id}">
                                            <div class="message-content">
                                                ${message.message}
                                                ${message.attachment ? `
                                                    <div class="attachment-preview">
                                                        <a href="${basePath}/patient/download-attachment?message_id=${message.id}" target="_blank">
                                                            <i class="ri-attachment-2"></i> View Attachment
                                                        </a>
                                                    </div>
                                                ` : ''}
                                            </div>
                                            <div class="message-time">
                                                ${message.time}
                                            </div>
                                        </div>
                                    `;
                                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                                    lastMessageId = message.id;
                                }
                            });
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                }, 3000); // Poll every 3 seconds
            }

            // Error handling function
            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 5000);
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });
        });
    </script>
</body>

</html>