<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <?php flash('conversation_message'); ?>
    <h2>My Conversations</h2>
    <p>Here you can view all your conversations with buyers and sellers.</p>

    <?php if(empty($data['conversations'])): ?>
        <div class="alert alert-info">You have no conversations yet.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach($data['conversations'] as $groupedConversation): ?>
                <div class="list-group-item">
                    <h5 class="mb-1">
                        Conversation with 
                        <strong>
                            <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $groupedConversation['other_user_id']; ?>">
                                <?php echo htmlspecialchars($groupedConversation['other_user_username']); ?>
                            </a>
                        </strong>
                    </h5>
                    <?php foreach($groupedConversation['conversations'] as $conversation): ?>
                        <a href="<?php echo URLROOT; ?>/conversations/show/<?php echo $conversation->id; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <p class="mb-1">Order #<?php echo htmlspecialchars($conversation->order_id); ?>: <?php echo htmlspecialchars($conversation->service_title); ?></p>
                                <small><?php echo date('Y-m-d H:i', strtotime($conversation->updated_at)); ?></small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
