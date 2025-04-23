<?php require_once ROOT_PATH . '/app/views/vpdoctor/partials/header.php'; ?>

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
                        <!-- Additional actions can be added here if needed -->
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
                                            <a href="<?php echo $basePath; ?>/vpdoctor/download-attachment?message_id=<?php echo $message['message_id']; ?>" target="_blank">
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
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
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

<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 140px);
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin: 20px;
        overflow: hidden;
    }

    .chat-sidebar {
        width: 300px;
        border-right: 1px solid #e0e0e0;
        overflow-y: auto;
        background-color: #f9f9f9;
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
        margin-bottom: 5px;
    }

    .chat-item:hover {
        background-color: #f1f1f1;
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
        object-fit: cover;
    }

    .chat-item-info {
        flex: 1;
    }

    .chat-item-name {
        font-weight: 600;
        margin-bottom: 3px;
    }

    .chat-item-preview {
        font-size: 0.85rem;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        background-color: #fff;
    }

    .chat-header img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }

    .chat-header-info h2 {
        margin: 0;
        font-size: 1.1rem;
    }

    .chat-header-info p {
        margin: 0;
        font-size: 0.85rem;
        color: #666;
    }

    .chat-header-actions {
        margin-left: auto;
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f5f7fa;
    }

    .message {
        display: flex;
        flex-direction: column;
        max-width: 70%;
        margin-bottom: 20px;
        clear: both;
    }

    .message.sent {
        align-items: flex-end;
        align-self: flex-end;
        margin-left: auto;
    }

    .message.received {
        align-items: flex-start;
        margin-right: auto;
    }

    .message-content {
        padding: 12px 15px;
        border-radius: 15px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        word-break: break-word;
    }

    .message.sent .message-content {
        background-color: var(--primary-color);
        color: white;
        border-bottom-right-radius: 5px;
    }

    .message.received .message-content {
        background-color: white;
        border-bottom-left-radius: 5px;
    }

    .message-time {
        font-size: 0.75rem;
        color: #999;
        margin-top: 5px;
    }

    .attachment-preview {
        margin-top: 8px;
        padding: 8px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
    }

    .attachment-preview a {
        color: inherit;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .attachment-preview i {
        margin-right: 5px;
    }

    .chat-input {
        padding: 15px;
        border-top: 1px solid #e0e0e0;
        background-color: #fff;
    }

    .chat-input-container {
        display: flex;
        align-items: center;
        background-color: #f5f5f5;
        border-radius: 25px;
        padding: 5px 15px;
    }

    .chat-input-container input[type="text"] {
        flex: 1;
        border: none;
        padding: 10px 0;
        background-color: transparent;
        outline: none;
    }

    .chat-input-container button {
        background: none;
        border: none;
        color: #666;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 8px;
        transition: color 0.3s;
    }

    .chat-input-container button:hover {
        color: var(--primary-color);
    }

    .chat-input-container .attachment-btn {
        margin-right: 5px;
    }

    .chat-input-container #sendButton {
        background-color: var(--primary-color);
        color: white;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .chat-input-container #sendButton:hover {
        background-color: var(--primary-dark);
    }

    .chat-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        font-size: 1.1rem;
        text-align: center;
        background-color: #f9f9f9;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px 15px;
        border-radius: 5px;
        margin: 10px 15px;
        display: none;
    }

    .hidden {
        display: none;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 120px);
            margin: 10px;
        }

        .chat-sidebar {
            width: 250px;
        }

        .message {
            max-width: 85%;
        }
    }

    @media (max-width: 576px) {
        .chat-container {
            flex-direction: column;
            height: calc(100vh - 100px);
        }

        .chat-sidebar {
            width: 100%;
            height: 100px;
            border-right: none;
            border-bottom: 1px solid #e0e0e0;
        }

        .chat-main {
            height: calc(100vh - 200px);
        }
    }
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
                window.location.href = `${basePath}/vpdoctor/chat?user_id=${patientId}`;
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

                if (!message && !fileInput.files.length) {
                    return; // Don't send empty messages
                }

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
                                ${fileInput.files.length ? '<div class="attachment-preview"><i class="ri-attachment-2"></i> Sending attachment...</div>' : ''}
                            </div>
                            <div class="message-time">
                                ${currentTime}
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', tempMessageHtml);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    messageForm.reset();

                    const response = await fetch(`${basePath}/vpdoctor/send-message`, {
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

                    const response = await fetch(`${basePath}/vpdoctor/get-new-messages?receiver_id=${receiverId}&last_id=${lastMessageId}`);
                    const responseData = await response.json();
                    
                    // Check if the response contains a data property
                    const newMessages = responseData.data || [];

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
                                                    <a href="${basePath}/vpdoctor/download-attachment?message_id=${message.id}" target="_blank">
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