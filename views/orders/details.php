<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back to Dashboard</a>
    <h2>Order Details #<?php echo htmlspecialchars($data['order']->id); ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Service Snapshot</div>
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($data['snapshot']->title); ?></h4>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($data['snapshot']->description)); ?></p>
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