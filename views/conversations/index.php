<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <?php flash('conversation_message'); ?>
    <h2>My Conversations</h2>
    <p>Here you can view all your conversations with buyers and sellers.</p>

    <?php if(empty($data['conversations'])): ?>
        <div class="alert alert-info">You have no conversations yet.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach($data['conversations'] as $conversation): ?>
                <a href="<?php echo URLROOT; ?>/conversations/show/<?php echo $conversation->id; ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Order #<?php echo htmlspecialchars($conversation->order_id); ?>: <?php echo htmlspecialchars($conversation->service_title); ?></h5>
                        <small><?php echo date('Y-m-d H:i', strtotime($conversation->updated_at)); ?></small>
                    </div>
                    <p class="mb-1">Participants: <?php echo htmlspecialchars($conversation->buyer_username); ?> (Buyer) & <?php echo htmlspecialchars($conversation->seller_username); ?> (Seller)</p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>