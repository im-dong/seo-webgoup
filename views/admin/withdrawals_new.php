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
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/newsletter/withdrawals">
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
                <h1 class="h2">Withdrawal Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>

            <?php flash('withdrawal_message'); ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['stats']['total']); ?></h4>
                                    <p class="card-text">Total Requests</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-list fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['stats']['pending']); ?></h4>
                                    <p class="card-text">Pending</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['stats']['approved']); ?></h4>
                                    <p class="card-text">Approved</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">$<?php echo number_format($data['stats']['total_amount'], 2); ?></h4>
                                    <p class="card-text">Total Amount</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawals Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Withdrawal Requests</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>PayPal Email</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['withdrawals'])): ?>
                                    <?php foreach ($data['withdrawals'] as $withdrawal): ?>
                                        <tr>
                                            <td><?php echo $withdrawal->id; ?></td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($withdrawal->username); ?></strong>
                                                    <br>
                                                    <small class="text-muted">ID: <?php echo $withdrawal->user_id; ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">$<?php echo number_format($withdrawal->amount, 2); ?></span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($withdrawal->paypal_email); ?>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y H:i', strtotime($withdrawal->created_at)); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $withdrawal->status == 'pending' ? 'warning' :
                                                         ($withdrawal->status == 'approved' ? 'success' : 'danger');
                                                ?>">
                                                    <?php echo ucfirst($withdrawal->status); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($withdrawal->notes)): ?>
                                                    <small class="text-muted"><?php echo htmlspecialchars($withdrawal->notes); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($withdrawal->status == 'pending') : ?>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button"
                                                                class="btn btn-success approve-withdrawal"
                                                                data-withdrawal-id="<?php echo $withdrawal->id; ?>"
                                                                title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button"
                                                                class="btn btn-danger reject-withdrawal"
                                                                data-withdrawal-id="<?php echo $withdrawal->id; ?>"
                                                                title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">
                                                        <i class="fas fa-check-circle"></i> Processed
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No withdrawal requests found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Withdrawal Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <div class="mb-3">
                        <label for="rejectNotes" class="form-label">Reason for rejection</label>
                        <textarea class="form-control" id="rejectNotes" rows="3" placeholder="Please provide a reason for rejecting this withdrawal request..."></textarea>
                    </div>
                    <input type="hidden" id="rejectWithdrawalId" name="withdrawal_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Reject Request</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Approve withdrawal
    $('.approve-withdrawal').on('click', function() {
        const withdrawalId = $(this).data('withdrawal-id');

        if (confirm('Are you sure you want to approve this withdrawal request?')) {
            $.ajax({
                url: '<?php echo URLROOT; ?>/newsletter/processWithdrawal',
                type: 'POST',
                data: {
                    withdrawal_id: withdrawalId,
                    status: 'approved',
                    notes: '',
                    csrf_token: '<?php echo generateCSRFToken(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to approve withdrawal request.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });

    // Reject withdrawal
    $('.reject-withdrawal').on('click', function() {
        const withdrawalId = $(this).data('withdrawal-id');
        $('#rejectWithdrawalId').val(withdrawalId);
        $('#rejectNotes').val('');
        $('#rejectModal').modal('show');
    });

    $('#confirmReject').on('click', function() {
        const withdrawalId = $('#rejectWithdrawalId').val();
        const notes = $('#rejectNotes').val().trim();

        if (!notes) {
            alert('Please provide a reason for rejection.');
            return;
        }

        $.ajax({
            url: '<?php echo URLROOT; ?>/newsletter/processWithdrawal',
            type: 'POST',
            data: {
                withdrawal_id: withdrawalId,
                status: 'rejected',
                notes: notes,
                csrf_token: '<?php echo generateCSRFToken(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#rejectModal').modal('hide');
                    location.reload();
                } else {
                    alert('Failed to reject withdrawal request.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>