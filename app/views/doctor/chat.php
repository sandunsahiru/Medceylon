<?php require_once ROOT_PATH . '/app/views/doctor/partials/header.php'; ?>

<!-- Main Content -->
<main class="main-content">
    <header class="top-bar">
        <h1>Chat</h1>
        <div class="header-right">
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="Search conversations" id="searchInput">
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
    <?php if (isset($patients) && is_array($patients) && count($patients) > 0): ?>
    <?php foreach ($patients as $patient): ?>
        <div class="chat-item <?php echo ($activeChatId && $activePatient && $activePatient['user_id'] == $patient['user_id']) ? 'active' : ''; ?>"
            data-patient-id="<?php echo htmlspecialchars($patient['user_id']); ?>">
            <img src="<?php echo $basePath; ?>/public/<?php echo htmlspecialchars($patient['profile_picture'] ?? 'assets/images/patient-avatar.png'); ?>"
                alt="Patient avatar">
            <div class="chat-item-info">
                <div class="chat-item-name"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></div>
                <div class="chat-item-preview">Patient</div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="chat-item">
        <div class="chat-item-info">
            <div class="chat-item-name">No conversations</div>
            <div class="chat-item-preview">No active patients found</div>
        </div>
    </div>
<?php endif; ?>
    </div>
</div>

        <div class="chat-main">
            <?php if ($activeChatId): ?>
                <div class="chat-header">
                    <img src="<?php echo $basePath; ?>/public/<?php echo htmlspecialchars($activePatient['profile_picture'] ?? 'assets/images/patient-avatar.png'); ?>"
                        alt="Patient avatar">
                    <div class="chat-header-info">
                        <h2><?php echo htmlspecialchars($activePatient['first_name'] . ' ' . $activePatient['last_name']); ?></h2>
                        <p>Patient</p>
                    </div>
                    <div class="chat-header-actions">
                        
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
                                            <a href="<?php echo $basePath; ?>/doctor/download-attachment?message_id=<?php echo $message['message_id']; ?>" target="_blank">
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
                        <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($activePatient['user_id']); ?>">
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

    .chat-header-actions {
        margin-left: auto;
        padding-right: 20px;
    }

    .archive-btn {
        padding: 8px 16px;
        background-color: #f0f0f0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .archive-btn:hover {
        background-color: #e0e0e0;
    }

    /* Rest of the styles remain the same as in the patient chat view */
</style>

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
        const searchInput = document.getElementById('searchInput');
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

        // Search functionality
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                chatItems.forEach(item => {
                    const patientName = item.querySelector('.chat-item-name').textContent.toLowerCase();
                    item.style.display = patientName.includes(searchTerm) ? 'flex' : 'none';
                });
            });
        }

        // Handle chat selection
chatItems.forEach(item => {
    item.addEventListener('click', function() {
        const patientId = this.dataset.patientId;
        window.location.href = `${basePath}/doctor/chat?user_id=${patientId}`; // Changed to user_id
    });
});

        // File input handler
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

        // Message form handler
        if (messageForm) {
            messageForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(messageForm);
                const submitButton = messageForm.querySelector('button[type="submit"]');
                const messageInput = messageForm.querySelector('input[name="message"]');
                const message = messageInput.value.trim();

                try {
                    submitButton.disabled = true;

                    const currentTime = new Date().toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });

                    const tempMessageHtml = `
                            <div class="message sent temp-message">
                                <div class="message-content">
                                    ${message}
                                </div>
                                <div class="message-time">
                                    ${currentTime}
                                </div>
                            </div>
                        `;
                    chatMessages.insertAdjacentHTML('beforeend', tempMessageHtml);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    messageForm.reset();

                    const response = await fetch(`${basePath}/doctor/send-message`, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        throw new Error(result.error || 'Failed to send message');
                    }

                } catch (error) {
                    showError(error.message);
                    const tempMessages = chatMessages.querySelectorAll('.temp-message');
                    if (tempMessages.length > 0) {
                        tempMessages[tempMessages.length - 1].remove();
                    }
                    messageInput.value = message;
                } finally {
                    submitButton.disabled = false;
                }
            });
        }

        // Poll for new messages
        if (chatMessages) {
            pollingInterval = setInterval(async function() {
                try {
                    const receiverInput = document.querySelector('input[name="receiver_id"]');
                    if (!receiverInput) return;

                    const receiverId = receiverInput.value;

                    const response = await fetch(`${basePath}/doctor/get-new-messages?receiver_id=${receiverId}&last_id=${lastMessageId}`);
                    const newMessages = await response.json();

                    if (newMessages.length > 0) {
                        const tempMessages = chatMessages.querySelectorAll('.temp-message');
                        tempMessages.forEach(msg => msg.remove());

                        newMessages.forEach(message => {
                            const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                            if (!existingMessage && message.id > lastMessageId) {
                                const messageHtml = `
                                        <div class="message ${message.sender_id === userId ? 'sent' : 'received'}" 
                                             data-message-id="${message.id}">
                                            <div class="message-content">
                                                ${message.message}
                                                ${message.attachment ? `
                                                    <div class="attachment-preview">
                                                        <a href="${basePath}/doctor/download-attachment?message_id=${message.id}" target="_blank">
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
            }, 3000);
        }

        // Archive conversation handler
        window.archiveConversation = async function(conversationId) {
            try {
                const response = await fetch(`${basePath}/doctor/archive-conversation`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `conversation_id=${conversationId}&csrf_token=${document.querySelector('input[name="csrf_token"]').value}`
                });

                if (response.ok) {
                    window.location.href = `${basePath}/doctor/chat`;
                } else {
                    throw new Error('Failed to archive conversation');
                }
            } catch (error) {
                showError(error.message);
            }
        };

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