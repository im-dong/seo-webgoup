<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php
// Helper function to determine badge color based on status
if (!function_exists('get_status_badge')) {
    function get_status_badge($status) {
        switch ($status) {
            case 'completed':
            case 'active':
            case 'released':
                return 'success';
            case 'pending':
            case 'pending_approval':
            case 'paused':
                return 'warning';
            case 'in_progress':
            case 'paid':
            case 'confirmed':
                return 'info';
            case 'cancelled':
            case 'rejected':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
?>

<div class="container my-4">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h1 class="fw-bold">My Dashboard</h1>
            <p class="text-muted mb-0">Welcome back, <?php echo $_SESSION['user_name']; ?>!</p>
        </div>
        <a href="<?php echo URLROOT; ?>/users/editProfile" class="btn btn-outline-primary mt-2 mt-md-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</a>
    </div>

    <?php flash('order_message'); ?>

    <!-- Wallet Stats -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted">Total Balance</h6>
                        <h2 class="card-title fw-bold text-primary mb-0">$<?php echo number_format($data['wallet']->total_balance, 2); ?></h2>
                        <small class="text-muted">Includes funds from ongoing orders.</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                        <i class="fas fa-hand-holding-usd fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted">Withdrawable Balance</h6>
                        <h2 class="card-title fw-bold text-success mb-0">$<?php echo number_format($data['wallet']->withdrawable_balance, 2); ?></h2>
                        <a href="<?php echo URLROOT; ?>/wallets" class="btn btn-sm btn-success mt-1">Withdraw Funds</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0 p-3">
            <ul class="nav nav-pills" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases-pane" type="button" role="tab" aria-controls="purchases-pane" aria-selected="true">My Purchases</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales-pane" type="button" role="tab" aria-controls="sales-pane" aria-selected="false">My Sales</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="my-services-tab" data-bs-toggle="tab" data-bs-target="#my-services-pane" type="button" role="tab" aria-controls="my-services-pane" aria-selected="false">My Services</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="myTabContent">
                <!-- Purchases Tab -->
                <div class="tab-pane fade show active p-3" id="purchases-pane" role="tabpanel" aria-labelledby="purchases-tab">
                    <?php if(!empty($data['buyer_pagination'])): ?>
                        <?php echo showPaginationStats($data['buyer_pagination']); ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['buyer_orders'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-shopping-cart fa-3x mb-3 d-block text-muted"></i>
                                                <p class="mb-0">No purchase orders found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['buyer_orders'] as $order): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/services/show/<?php echo $order->service_id; ?>" class="fw-bold"><?php echo htmlspecialchars($order->service_title); ?></a>
                                                <br>
                                                <small class="text-muted">Sold by: <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $order->seller_id; ?>"><?php echo htmlspecialchars($order->seller_name); ?></a></small>
                                            </td>
                                            <td>$<?php echo htmlspecialchars($order->amount); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                                            <td><span class="badge rounded-pill bg-<?php echo get_status_badge($order->status); ?>"><?php echo htmlspecialchars($order->status); ?></span></td>
                                            <td class="text-end">
                                                <?php if(in_array($order->status, ['completed', 'confirmed', 'released']) && !$order->has_reviewed): ?>
                                                    <a href="<?php echo URLROOT; ?>/reviews/add/<?php echo $order->id; ?>" class="btn btn-sm btn-warning">Review</a>
                                                <?php endif; ?>
                                                <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $order->id; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(!empty($data['buyer_pagination']) && $data['buyer_pagination']['total_pages'] > 1): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo paginate($data['buyer_pagination']['current_page'], $data['buyer_pagination']['total_pages'], $data['base_url'], []); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sales Tab -->
                <div class="tab-pane fade p-3" id="sales-pane" role="tabpanel" aria-labelledby="sales-tab">
                    <?php if(!empty($data['seller_pagination'])): ?>
                        <?php echo showPaginationStats($data['seller_pagination']); ?>
                    <?php endif; ?>

                     <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Amount</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['seller_orders'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-shopping-bag fa-3x mb-3 d-block text-muted"></i>
                                                <p class="mb-0">No sales orders found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['seller_orders'] as $order): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold"><?php echo htmlspecialchars($order->service_title); ?></span>
                                                <br>
                                                <small class="text-muted">Bought by: <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $order->buyer_id; ?>"><?php echo htmlspecialchars($order->buyer_name); ?></a></small>
                                            </td>
                                            <td>$<?php echo htmlspecialchars($order->amount); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($order->created_at)); ?></td>
                                            <td><span class="badge rounded-pill bg-<?php echo get_status_badge($order->status); ?>"><?php echo htmlspecialchars($order->status); ?></span></td>
                                            <td class="text-end">
                                                <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $order->id; ?>" class="btn btn-sm btn-outline-primary">Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(!empty($data['seller_pagination']) && $data['seller_pagination']['total_pages'] > 1): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo paginate($data['seller_pagination']['current_page'], $data['seller_pagination']['total_pages'], $data['base_url'], []); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- My Services Tab -->
                <div class="tab-pane fade p-3" id="my-services-pane" role="tabpanel" aria-labelledby="my-services-tab">
                    <?php if(!empty($data['services_pagination'])): ?>
                        <?php echo showPaginationStats($data['services_pagination']); ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($data['my_services'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <div class="py-4">
                                                <i class="fas fa-briefcase fa-3x mb-3 d-block text-muted"></i>
                                                <p class="mb-0">No services found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($data['my_services'] as $service): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($service->title); ?></td>
                                            <td>$<?php echo htmlspecialchars($service->price); ?></td>
                                            <td><span class="badge rounded-pill bg-<?php echo get_status_badge($service->status); ?>"><?php echo htmlspecialchars($service->status); ?></span></td>
                                            <td><?php echo date('Y-m-d', strtotime($service->created_at)); ?></td>
                                            <td class="text-end">
                                                <a href="<?php echo URLROOT; ?>/services/edit/<?php echo $service->serviceId; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form class="d-inline" action="<?php echo URLROOT; ?>/services/delete/<?php echo $service->serviceId; ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(!empty($data['services_pagination']) && $data['services_pagination']['total_pages'] > 1): ?>
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo paginate($data['services_pagination']['current_page'], $data['services_pagination']['total_pages'], $data['base_url'], []); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>