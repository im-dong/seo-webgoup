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
                        <a class="nav-link" href="<?php echo URLROOT; ?>/newsletter/sendHistory">
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
                <h1 class="h2">Send Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?php echo URLROOT; ?>/newsletter/sendHistory" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Send Record Information -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Newsletter Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Subject:</strong><br><?php echo htmlspecialchars($data['send_record']->subject); ?></p>
                                    <p><strong>Sent By:</strong><br><?php echo htmlspecialchars($data['send_record']->sent_by_name); ?></p>
                                    <p><strong>Status:</strong><br>
                                        <span class="badge bg-<?php
                                            echo $data['send_record']->status == 'completed' ? 'success' :
                                                 ($data['send_record']->status == 'sending' ? 'warning' :
                                                 ($data['send_record']->status == 'failed' ? 'danger' : 'secondary'));
                                        ?>">
                                            <?php echo ucfirst($data['send_record']->status); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Total Recipients:</strong><br><?php echo number_format($data['send_record']->total_recipients); ?></p>
                                    <p><strong>Successful Sends:</strong><br><span class="text-success"><?php echo number_format($data['send_record']->successful_sends); ?></span></p>
                                    <p><strong>Failed Sends:</strong><br><span class="text-danger"><?php echo number_format($data['send_record']->failed_sends); ?></span></p>
                                </div>
                            </div>
                            <?php if ($data['send_record']->sent_at): ?>
                                <p><strong>Sent At:</strong><br><?php echo date('F j, Y \a\t g:i A', strtotime($data['send_record']->sent_at)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Success Rate</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="progress-circle mb-3">
                                <?php
                                $success_rate = $data['send_record']->total_recipients > 0
                                    ? ($data['send_record']->successful_sends / $data['send_record']->total_recipients) * 100
                                    : 0;
                                ?>
                                <div class="progress" style="height: 150px; width: 150px; margin: 0 auto;">
                                    <div class="progress-bar bg-<?php echo $success_rate >= 90 ? 'success' : ($success_rate >= 70 ? 'warning' : 'danger'); ?>"
                                         role="progressbar"
                                         style="width: <?php echo $success_rate; ?>%; transform: rotate(-90deg); transform-origin: center;"
                                         aria-valuenow="<?php echo $success_rate; ?>"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <h3 class="mt-2"><?php echo round($success_rate); ?>%</h3>
                                <p class="text-muted">Success Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Content Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Newsletter Content</h5>
                </div>
                <div class="card-body">
                    <div class="border rounded p-3" style="background-color: #f8f9fa;">
                        <?php echo $data['send_record']->content; ?>
                    </div>
                </div>
            </div>

            <!-- Send Details Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recipient Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Subscriber Name</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                    <th>Error Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['send_details'])): ?>
                                    <?php foreach ($data['send_details'] as $detail): ?>
                                        <tr>
                                            <td><?php echo $detail->id; ?></td>
                                            <td><?php echo htmlspecialchars($detail->email); ?></td>
                                            <td><?php echo htmlspecialchars($detail->subscriber_name ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $detail->status == 'sent' ? 'success' :
                                                         ($detail->status == 'failed' ? 'danger' :
                                                         ($detail->status == 'pending' ? 'warning' : 'secondary'));
                                                ?>">
                                                    <?php echo ucfirst($detail->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($detail->sent_at): ?>
                                                    <?php echo date('M j, Y H:i', strtotime($detail->sent_at)); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($detail->error_message)): ?>
                                                    <small class="text-danger"><?php echo htmlspecialchars($detail->error_message); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No recipient details found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Send details pagination">
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