<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <h2>My Dashboard</h2>
    <?php flash('order_message'); ?>
    <p>Welcome, <?php echo $_SESSION['user_name']; ?>! <a href="<?php echo URLROOT; ?>/users/editProfile" class="btn btn-sm btn-outline-secondary">Edit Profile</a></p>

    <div class="card mb-4 service-card">
        <div class="card-header">My Wallet</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h3>Total Balance: <span class="text-primary fw-bold">$<?php echo number_format($data['wallet']->total_balance, 2); ?></span></h3>
                    <small>This includes funds from ongoing orders.</small>
                </div>
                <div class="col-md-6">
                    <h3>Withdrawable Balance: <span class="text-success fw-bold">$<?php echo number_format($data['wallet']->withdrawable_balance, 2); ?></span></h3>
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
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="my-services-tab" data-bs-toggle="tab" data-bs-target="#my-services" type="button" role="tab">My Services</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <!-- Purchases Tab -->
        <div class="tab-pane fade show active" id="purchases" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover">
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
                                <td>
                                    <?php
                                    $status_badge_class = 'bg-secondary';
                                    switch ($order->status) {
                                        case 'completed':
                                            $status_badge_class = 'bg-success';
                                            break;
                                        case 'pending':
                                            $status_badge_class = 'bg-warning text-dark';
                                            break;
                                        case 'in_progress':
                                            $status_badge_class = 'bg-info text-dark';
                                            break;
                                        case 'cancelled':
                                            $status_badge_class = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars($order->status); ?></span>
                                </td>
                                <td>
                                    <?php if(($order->status == 'paid' || $order->status == 'completed' || $order->status == 'confirmed' || $order->status == 'released') && !$order->has_reviewed): ?>
                                        <a href="<?php echo URLROOT; ?>/reviews/add/<?php echo $order->id; ?>" class="btn btn-sm btn-warning">Review</a>
                                    <?php endif; ?>
                                    <?php if($order->status == 'inquiry'): ?>
                                        <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $order->id; ?>" class="btn btn-sm btn-primary">View</a>
                                    <?php else: ?>
                                        <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $order->id; ?>" class="btn btn-sm btn-info">View Details</a>
                                    <?php endif; ?>
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
                <table class="table table-striped table-hover">
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
                                <td>
                                    <?php
                                    $status_badge_class = 'bg-secondary';
                                    switch ($order->status) {
                                        case 'completed':
                                            $status_badge_class = 'bg-success';
                                            break;
                                        case 'pending':
                                            $status_badge_class = 'bg-warning text-dark';
                                            break;
                                        case 'in_progress':
                                            $status_badge_class = 'bg-info text-dark';
                                            break;
                                        case 'cancelled':
                                            $status_badge_class = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars($order->status); ?></span>
                                </td>
                                <td>
                                    <?php if(($order->status == 'paid' || $order->status == 'completed' || $order->status == 'confirmed' || $order->status == 'released') && !$order->has_reviewed): ?>
                                        <a href="<?php echo URLROOT; ?>/reviews/add/<?php echo $order->id; ?>" class="btn btn-sm btn-warning">Review</a>
                                    <?php endif; ?>
                                    <?php if($order->status == 'inquiry'): ?>
                                        <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $order->id; ?>" class="btn btn-sm btn-primary">View</a>
                                    <?php else: ?>
                                        <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $order->id; ?>" class="btn btn-sm btn-info">View Details</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Services Tab -->
        <div class="tab-pane fade" id="my-services" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Industry</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['my_services'] as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service->title); ?></td>
                                <td>$<?php echo htmlspecialchars($service->price); ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($service->service_category))); ?></td>
                                <td><?php echo htmlspecialchars($service->industry_name); ?></td>
                                <td>
                                    <?php
                                    $status_badge_class = 'bg-secondary';
                                    switch ($service->status) {
                                        case 'active':
                                            $status_badge_class = 'bg-success';
                                            break;
                                        case 'paused':
                                            $status_badge_class = 'bg-warning text-dark';
                                            break;
                                        case 'pending_approval':
                                            $status_badge_class = 'bg-info text-dark';
                                            break;
                                        case 'rejected':
                                            $status_badge_class = 'bg-danger';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_badge_class; ?>"><?php echo htmlspecialchars($service->status); ?></span>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($service->created_at)); ?></td>
                                <td>
                                    <a href="<?php echo URLROOT; ?>/services/edit/<?php echo $service->serviceId; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form class="d-inline" action="<?php echo URLROOT; ?>/services/delete/<?php echo $service->serviceId; ?>" method="post">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?');">Delete</button>
                                    </form>
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