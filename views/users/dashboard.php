<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>My Dashboard</h2>
    <?php flash('order_message'); ?>
    <p>Welcome, <?php echo $_SESSION['user_name']; ?>!</p>

    <div class="card mb-4">
        <div class="card-header">My Wallet</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Total Balance: <span class="text-info">$<?php echo number_format($data['wallet']->total_balance, 2); ?></span></h4>
                    <small>This includes funds from ongoing orders.</small>
                </div>
                <div class="col-md-6">
                    <h4>Withdrawable Balance: <span class="text-success">$<?php echo number_format($data['wallet']->withdrawable_balance, 2); ?></span></h4>
                    <small>Funds available for withdrawal.</small>
                    <a href="<?php echo URLROOT; ?>/wallets" class="btn btn-sm btn-success mt-2">Withdraw</a>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab">My Purchases</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">My Sales</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Purchases Tab -->
        <div class="tab-pane fade show active" id="purchases" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['buyer_orders'] as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order->service_title); ?></td>
                                <td>$<?php echo htmlspecialchars($order->amount); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($order->status); ?></span></td>
                                <td>
                                    <?php if($order->status == 'completed'): ?>
                                        <a href="<?php echo htmlspecialchars($order->proof_url); ?>" class="btn btn-sm btn-info" target="_blank">View Proof</a>
                                        <form action="<?php echo URLROOT; ?>/orders/confirm/<?php echo $order->id; ?>" method="post" class="d-inline">
                                            <input type="submit" class="btn btn-sm btn-success" value="Confirm Completion">
                                        </form>
                                    <?php endif; ?>
                                    <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $order->id; ?>" class="btn btn-sm btn-secondary">Message Seller</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sales Tab -->
        <div class="tab-pane fade" id="sales" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['seller_orders'] as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order->service_title); ?></td>
                                <td>$<?php echo htmlspecialchars($order->amount); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                                <td><span class="badge bg-info"><?php echo htmlspecialchars($order->status); ?></span></td>
                                <td>
                                    <?php if($order->status == 'paid'): ?>
                                        <a href="<?php echo URLROOT; ?>/orders/complete/<?php echo $order->id; ?>" class="btn btn-sm btn-success">Mark as Complete</a>
                                    <?php endif; ?>
                                    <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $order->id; ?>" class="btn btn-sm btn-secondary">Message Buyer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>