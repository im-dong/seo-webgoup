<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <?php flash('conversation_message'); ?>
    <a href="<?php echo URLROOT; ?>/conversations" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back to Conversations</a>

    <div class="card mb-3">
        <div class="card-header">Service Details</div>
        <div class="card-body d-flex">
            <img src="<?php echo URLROOT . htmlspecialchars($data['conversation']->service_thumbnail); ?>" alt="Service Thumbnail" class="img-thumbnail me-3" style="width: 100px; height: auto;">
            <div>
                <h5><a href="<?php echo URLROOT; ?>/services/show/<?php echo $data['conversation']->service_id; ?>"><?php echo htmlspecialchars($data['conversation']->service_title); ?></a></h5>
                <p>Order #<?php echo htmlspecialchars($data['conversation']->order_id); ?></p>
                <p>
                    Chatting with:
                    <strong>
                        <a href="<?php echo URLROOT; ?>/users/profile/<?php echo ($data['conversation']->buyer_id == $_SESSION['user_id']) ? $data['conversation']->seller_id : $data['conversation']->buyer_id; ?>">
                            <?php
                            $other_user = ($data['conversation']->buyer_id == $_SESSION['user_id']) ? $data['conversation']->seller_username : $data['conversation']->buyer_username;
                            echo htmlspecialchars($other_user);
                            ?>
                        </a>
                    </strong>
                </p>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Messages</div>
        <div class="card-body message-box" style="max-height: 400px; overflow-y: auto;">
            <?php if(empty($data['conversation']->messages)): ?>
                <p class="text-center text-muted">No messages yet. Start the conversation!</p>
            <?php else: ?>
                <?php foreach($data['conversation']->messages as $message): ?>
                    <div class="message-item mb-2 <?php echo ($message->sender_id == $_SESSION['user_id']) ? 'text-end' : 'text-start'; ?>">
                        <small class="text-muted"><?php echo htmlspecialchars($message->sender_username); ?> at <?php echo date('Y-m-d H:i', strtotime($message->created_at)); ?></small>
                        <div class="p-2 rounded <?php echo ($message->sender_id == $_SESSION['user_id']) ? 'bg-primary text-white' : 'bg-light'; ?> d-inline-block">
                            <?php echo nl2br(htmlspecialchars($message->message_text)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Send a Message</div>
        <div class="card-body">
            <form action="<?php echo URLROOT; ?>/conversations/show/<?php echo $data['conversation']->id; ?>" method="post">
                <div class="form-group mb-3">
                    <textarea name="message_text" class="form-control" rows="3" placeholder="Type your message here..."></textarea>
                </div>
                <input type="submit" class="btn btn-success" value="Send Message">
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageBox = document.querySelector('.message-box');
        const conversationId = <?php echo $data['conversation']->id; ?>;
        let lastMessageId = <?php echo end($data['conversation']->messages)->id ?? 0; ?>;

        function fetchMessages() {
            fetch(`<?php echo URLROOT; ?>/conversations/getMessages/${conversationId}/${lastMessageId}`)
                .then(response => response.json())
                .then(messages => {
                    if (messages.length > 0) {
                        messages.forEach(message => {
                            appendMessage(message);
                            lastMessageId = message.id;
                        });
                        messageBox.scrollTop = messageBox.scrollHeight;
                    }
                });
        }

        function appendMessage(message) {
            const messageItem = document.createElement('div');
            const isCurrentUser = message.sender_id == <?php echo $_SESSION['user_id']; ?>;
            messageItem.className = `message-item mb-2 ${isCurrentUser ? 'text-end' : 'text-start'}`;

            const small = document.createElement('small');
            small.className = 'text-muted';
            small.textContent = `${message.sender_username} at ${new Date(message.created_at).toLocaleString()}`;

            const messageBody = document.createElement('div');
            messageBody.className = `p-2 rounded ${isCurrentUser ? 'bg-primary text-white' : 'bg-light'} d-inline-block`;
            messageBody.innerHTML = message.message_text.replace(/\n/g, '<br>');

            messageItem.appendChild(small);
            messageItem.appendChild(document.createElement('br'));
            messageItem.appendChild(messageBody);
            messageBox.appendChild(messageItem);
        }

        // Scroll to bottom initially
        messageBox.scrollTop = messageBox.scrollHeight;

        // Fetch new messages every 3 seconds
        setInterval(fetchMessages, 3000);
    });
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>