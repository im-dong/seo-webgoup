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
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/newsletter/admin">
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
                <h1 class="h2">Newsletter Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?php echo URLROOT; ?>/newsletter/export" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <?php if (isset($data['error'])): ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Setup Required</h5>
                    <p><?php echo $data['error']; ?></p>
                    <p>Please run the following SQL command to create the required database table:</p>
                    <pre class="bg-light p-3 rounded">CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(100) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    unsubscribe_token VARCHAR(255) NULL,
    last_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);</pre>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['stats']['total']); ?></h4>
                                    <p class="card-text">Total Subscribers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
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
                                    <h4 class="card-title"><?php echo number_format($data['stats']['active']); ?></h4>
                                    <p class="card-text">Active Subscribers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['stats']['today']); ?></h4>
                                    <p class="card-text">New Today</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-day fa-2x"></i>
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
                                    <h4 class="card-title"><?php echo number_format($data['stats']['month']); ?></h4>
                                    <p class="card-text">New This Month</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscribers Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subscribers List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Subscribed</th>
                                    <th>Status</th>
                                    <th>IP Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['subscribers'])): ?>
                                    <?php foreach ($data['subscribers'] as $subscriber): ?>
                                        <tr>
                                            <td><?php echo $subscriber->id; ?></td>
                                            <td><?php echo htmlspecialchars($subscriber->email); ?></td>
                                            <td><?php echo htmlspecialchars($subscriber->name ?? 'N/A'); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($subscriber->subscribed_at)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $subscriber->is_active ? 'success' : 'secondary'; ?>">
                                                    <?php echo $subscriber->is_active ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo htmlspecialchars($subscriber->ip_address); ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button"
                                                            class="btn btn-<?php echo $subscriber->is_active ? 'warning' : 'success'; ?> toggle-status"
                                                            data-subscriber-id="<?php echo $subscriber->id; ?>"
                                                            data-is-active="<?php echo $subscriber->is_active ? '1' : '0'; ?>"
                                                            title="<?php echo $subscriber->is_active ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fas fa-<?php echo $subscriber->is_active ? 'pause' : 'play'; ?>"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger delete-subscriber"
                                                            data-subscriber-id="<?php echo $subscriber->id; ?>"
                                                            data-email="<?php echo htmlspecialchars($subscriber->email); ?>"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No subscribers found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Subscribers pagination">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this subscriber?</p>
                <p><strong>Email:</strong> <span id="delete-email"></span></p>
                <p class="text-warning">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle subscriber status
    $('.toggle-status').on('click', function() {
        const button = $(this);
        const subscriberId = button.data('subscriber-id');
        const isActive = button.data('is-active') === '1';
        const newStatus = !isActive;

        $.ajax({
            url: '<?php echo URLROOT; ?>/newsletter/updateSubscriber',
            type: 'POST',
            data: {
                subscriber_id: subscriberId,
                is_active: newStatus ? 1 : 0,
                csrf_token: '<?php echo generateCSRFToken(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update button state
                    button.data('is-active', newStatus ? '1' : '0');
                    button.removeClass('btn-success btn-warning')
                           .addClass(newStatus ? 'btn-warning' : 'btn-success');
                    button.find('i').removeClass('fa-play fa-pause')
                                   .addClass(newStatus ? 'fa-pause' : 'fa-play');
                    button.attr('title', newStatus ? 'Deactivate' : 'Activate');

                    // Update status badge
                    const row = button.closest('tr');
                    const badge = row.find('.badge');
                    badge.removeClass('bg-success bg-secondary')
                         .addClass(newStatus ? 'bg-success' : 'bg-secondary')
                         .text(newStatus ? 'Active' : 'Inactive');

                    // Update statistics
                    location.reload(); // Simple way to refresh stats
                } else {
                    alert('Failed to update subscriber status.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Delete subscriber
    let deleteSubscriberId = null;

    $('.delete-subscriber').on('click', function() {
        deleteSubscriberId = $(this).data('subscriber-id');
        const email = $(this).data('email');
        $('#delete-email').text(email);
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete').on('click', function() {
        if (deleteSubscriberId) {
            $.ajax({
                url: '<?php echo URLROOT; ?>/newsletter/deleteSubscriber',
                type: 'POST',
                data: {
                    subscriber_id: deleteSubscriberId,
                    csrf_token: '<?php echo generateCSRFToken(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        $('button[data-subscriber-id="' + deleteSubscriberId + '"]').closest('tr').fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Failed to delete subscriber.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>