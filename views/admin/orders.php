<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/users">
                            <i class="fas fa-users me-2"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/services">
                            <i class="fas fa-cogs me-2"></i> Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/newsletter/admin">
                            <i class="fas fa-envelope me-2"></i> Newsletter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/admin/orders">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="btn btn-sm btn-outline-secondary">
                            Total: <?php echo number_format($data['pagination']['total_orders']); ?> orders
                        </span>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Orders List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Buyer</th>
                                    <th>Seller</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Paid</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['orders'])): ?>
                                    <?php foreach ($data['orders'] as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order->id; ?></td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/services/show/<?php echo $order->service_id; ?>"
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($order->service_title ?? 'Service #' . $order->service_id); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $order->buyer_id; ?>"
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($order->buyer_username); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $order->seller_id; ?>"
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($order->seller_username); ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-success">$<?php echo number_format($order->amount, 2); ?></span></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo match($order->status) {
                                                        'paid' => 'success',
                                                        'pending' => 'warning',
                                                        'completed' => 'info',
                                                        'cancelled' => 'danger',
                                                        default => 'secondary'
                                                    }; ?>">
                                                    <?php echo ucfirst($order->status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($order->created_at)); ?></td>
                                            <td>
                                                <?php if($order->paid_at): ?>
                                                    <span class="text-success">
                                                        <?php echo date('M j, Y', strtotime($order->paid_at)); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not paid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/orders/details/<?php echo $order->id; ?>"
                                                       class="btn btn-outline-primary" title="View Order Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/services/show/<?php echo $order->service_id; ?>"
                                                       class="btn btn-outline-info" title="View Service">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $order->buyer_id; ?>"
                                                       class="btn btn-outline-secondary" title="View Buyer">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No orders found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Orders pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($data['pagination']['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $data['pagination']['current_page'] - 1; ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $data['pagination']['total_pages']; $i++): ?>
                                    <li class="page-item <?php echo $i == $data['pagination']['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($data['pagination']['current_page'] < $data['pagination']['total_pages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $data['pagination']['current_page'] + 1; ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
$(document).ready(function() {
    // Add any interactive features if needed
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>