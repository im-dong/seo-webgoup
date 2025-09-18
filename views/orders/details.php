<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back to Dashboard</a>
    <h2>Order Details #<?php echo htmlspecialchars($data['order']->id); ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Service Snapshot</div>
                <div class="card-body">
                    <!-- Service Thumbnail -->
                    <?php if(!empty($data['snapshot']->thumbnail_url)): ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($data['snapshot']->thumbnail_url); ?>"
                                 class="img-fluid rounded shadow-sm"
                                 alt="<?php echo htmlspecialchars($data['snapshot']->title); ?>"
                                 style="max-height: 250px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/800x400?text=No+Image+Available'">
                        </div>
                    <?php endif; ?>

                    <h4 class="card-title"><?php echo htmlspecialchars($data['snapshot']->title); ?></h4>
                    <div class="card-text"><?php echo $data['snapshot']->description; ?></div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>Price:</b> $<?php echo number_format($data['snapshot']->price, 2); ?></li>
                        <li class="list-group-item"><b>Delivery Time:</b> <?php echo htmlspecialchars($data['snapshot']->delivery_time); ?> days</li>
                        <li class="list-group-item"><b>Link Type:</b> <?php echo htmlspecialchars($data['snapshot']->link_type); ?></li>
                        <li class="list-group-item"><b>Link Duration:</b> <?php echo htmlspecialchars($data['snapshot']->duration); ?> days</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Participants</div>
                <div class="card-body">
                    <p>
                        <strong>Seller:</strong>
                        <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['seller']->id; ?>">
                            <?php echo htmlspecialchars($data['seller']->username); ?>
                        </a>
                    </p>
                    <p>
                        <strong>Buyer:</strong>
                        <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['buyer']->id; ?>">
                            <?php echo htmlspecialchars($data['buyer']->username); ?>
                        </a>
                    </p>
                    <?php if ($data['conversation']): ?>
                        <hr>
                        <div class="d-grid">
                             <a href="<?php echo URLROOT; ?>/conversations/show/<?php echo $data['conversation']->id; ?>" class="btn btn-primary">
                                 <i class="fa fa-comments"></i> View Conversation
                             </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$data['order']->paid_at && $data['order']->buyer_id == $_SESSION['user_id']): ?>
                <div class="card mb-4">
                    <div class="card-header">Pay for Order</div>
                    <div class="card-body">
                        <p>Click the button below to pay with PayPal.</p>
                        <a href="<?php echo URLROOT; ?>/orders/pay/<?php echo $data['order']->id; ?>" class="btn btn-primary btn-block">Pay with PayPal</a>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="card-header">Order Timeline</div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php if($data['conversation']): ?>
                            <li><strong>Inquiry Started:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['conversation']->created_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->paid_at): ?>
                            <li><strong>Paid:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->paid_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->completed_at): ?>
                            <li><strong>Completed by Seller:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->completed_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->confirmed_at): ?>
                            <li><strong>Confirmed by Buyer:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->confirmed_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->funds_release_date): ?>
                            <li><strong>Funds Release Est.:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->funds_release_date)); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>