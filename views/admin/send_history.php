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
                        <a class="nav-link" href="<?php echo URLROOT; ?>/newsletter/send">
                            <i class="fas fa-paper-plane me-2"></i> Send Newsletter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/newsletter/sendHistory">
                            <i class="fas fa-history me-2"></i> Send History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/newsletter/withdrawals">
                            <i class="fas fa-money-bill-wave me-2"></i> Withdrawal Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/orders">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Send History</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?php echo URLROOT; ?>/newsletter/send" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send New
                        </a>
                    </div>
                </div>
            </div>

            <!-- Send History Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Newsletter Send History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Sent By</th>
                                    <th>Recipients</th>
                                    <th>Success</th>
                                    <th>Failed</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['send_records'])): ?>
                                    <?php foreach ($data['send_records'] as $record): ?>
                                        <tr>
                                            <td><?php echo $record->id; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($record->subject); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($record->content, 0, 100)); ?>...</small>
                                            </td>
                                            <td><?php echo htmlspecialchars($record->sent_by_name); ?></td>
                                            <td><?php echo number_format($record->total_recipients); ?></td>
                                            <td>
                                                <span class="text-success"><?php echo number_format($record->successful_sends); ?></span>
                                            </td>
                                            <td>
                                                <span class="text-danger"><?php echo number_format($record->failed_sends); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $record->status == 'completed' ? 'success' :
                                                         ($record->status == 'sending' ? 'warning' :
                                                         ($record->status == 'failed' ? 'danger' : 'secondary'));
                                                ?>">
                                                    <?php echo ucfirst($record->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($record->sent_at): ?>
                                                    <?php echo date('M j, Y H:i', strtotime($record->sent_at)); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/newsletter/sendDetails/<?php echo $record->id; ?>" class="btn btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No newsletter send history found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Send history pagination">
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

<?php require APPROOT . '/views/layouts/footer.php'; ?>