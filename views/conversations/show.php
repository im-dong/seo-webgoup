<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <?php flash('conversation_message'); ?>
    <a href="<?php echo URLROOT; ?>/conversations" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back to Conversations</a>

    <h2>Conversation for Order #<?php echo htmlspecialchars($data['conversation']->order_id); ?></h2>
    <p>Service: <?php echo htmlspecialchars($data['conversation']->service_title); ?></p>
    <p>Participants: <?php echo htmlspecialchars($data['conversation']->buyer_username); ?> (Buyer) & <?php echo htmlspecialchars($data['conversation']->seller_username); ?> (Seller)</p>

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

<?php require APPROOT . '/views/layouts/footer.php'; ?>