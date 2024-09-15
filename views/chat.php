<!-- views/chat.php -->
<?php include 'templates/header.php'; 
$pageTitle = 'Chat';
?>
<?php include 'templates/topbar.php'; ?>

<div class="main-container">
    <?php include 'templates/sidebar.php'; ?>

    <div class="content chat-content">
        <div class="chat-container">
            <!-- Left Sidebar: Conversations -->
            <div class="chat-contacts">
                <div class="contacts-header">
                    <h2>Chats</h2>
                </div>
                <!-- Search Bar -->
                <div class="chat-search">
                    <input type="text" id="contact-search" placeholder="Search doctor or medical department">
                </div>
                <ul class="contacts-list">
                    <?php foreach ($conversations as $conversation): ?>
                        <li class="contact-item <?php echo ($conversation['conversation_id'] == $conversationId) ? 'active' : ''; ?>">
                            <a href="?page=chat&conversation_id=<?php echo $conversation['conversation_id']; ?>">
                                <img src="assets/images/<?php echo $conversation['profile_picture'] ?: 'default_avatar.png'; ?>" alt="<?php echo $conversation['first_name']; ?>" class="contact-avatar">
                                <div class="contact-info">
                                    <span class="contact-name"><?php echo $conversation['first_name'] . ' ' . $conversation['last_name']; ?></span>
                                    <!-- Optional: Last message preview or specialty -->
                                </div>
                                <!-- Time since last message -->
                                <span class="message-time"><?php echo date('H:i', strtotime($conversation['last_message_time'])); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Right Panel: Chat Messages -->
            <div class="chat-messages">
                <?php if ($conversationId && $contact): ?>
                    <div class="messages-header">
                        <img src="assets/images/<?php echo $contact['profile_picture'] ?: 'default_avatar.png'; ?>" alt="<?php echo $contact['first_name']; ?>" class="contact-avatar">
                        <div class="contact-info">
                            <span class="contact-name"><?php echo $contact['first_name'] . ' ' . $contact['last_name']; ?></span>
                        </div>
                        <span class="more-options">⋮</span>
                    </div>
                    <div class="messages-body" id="messages-body">
                        <!-- Messages will be loaded here via AJAX -->
                    </div>
                    <div class="message-input">
                        <form id="message-form">
                            <input type="hidden" name="conversation_id" value="<?php echo $conversationId; ?>">
                            <input type="text" name="message" id="message-input" placeholder="Start typing here" autocomplete="off">
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                <?php else: ?>
                    <p>Please select a conversation to start chatting.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
<!-- Include your JavaScript file -->
<script src="assets/js/script.js"></script>
