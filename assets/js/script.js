// assets/js/script.js

document.addEventListener('DOMContentLoaded', function () {
    const messageForm = document.getElementById('message-form');
    const messagesBody = document.getElementById('messages-body');
    const conversationId = <?php echo json_encode($conversationId); ?>;
    const fetchInterval = 3000; // Fetch messages every 3 seconds

    function fetchMessages() {
        fetch(`index.php?page=chat&action=fetchMessages&conversation_id=${conversationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderMessages(data.messages);
                }
            });
    }

    function renderMessages(messages) {
        messagesBody.innerHTML = '';
        messages.forEach(message => {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            if (message.sender_id == <?php echo $_SESSION['user_id']; ?>) {
                messageDiv.classList.add('sent');
            } else {
                messageDiv.classList.add('received');
            }

            const contentDiv = document.createElement('div');
            contentDiv.classList.add('message-content');
            contentDiv.textContent = message.message_text;

            const timestampDiv = document.createElement('div');
            timestampDiv.classList.add('message-timestamp');
            timestampDiv.textContent = new Date(message.sent_time).toLocaleString();

            messageDiv.appendChild(contentDiv);
            messageDiv.appendChild(timestampDiv);
            messagesBody.appendChild(messageDiv);
        });

        // Scroll to the bottom
        messagesBody.scrollTop = messagesBody.scrollHeight;
    }

    if (messagesBody) {
        // Fetch messages initially
        fetchMessages();

        // Fetch messages periodically
        setInterval(fetchMessages, fetchInterval);

        // Handle message form submission
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(messageForm);

            fetch('index.php?page=chat', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageForm.reset();
                    fetchMessages();
                }
            });
        });
    }
});
