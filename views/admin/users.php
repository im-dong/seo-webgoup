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
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/admin/users">
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
                <h1 class="h2">User Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="btn btn-sm btn-outline-secondary">
                            Total: <?php echo number_format($data['pagination']['total_users']); ?> users
                        </span>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Users List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Member Since</th>
                                    <th>Email Verified</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['users'])): ?>
                                    <?php foreach ($data['users'] as $user): ?>
                                        <tr>
                                            <td><?php echo $user->id; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if(!empty($user->profile_image_url)): ?>
                                                        <img src="<?php echo (strpos($user->profile_image_url, 'http') === 0 ? $user->profile_image_url : URLROOT . $user->profile_image_url); ?>"
                                                             alt="Avatar" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($user->username); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($user->email); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($user->role == 'admin') ? 'danger' : 'primary'; ?>">
                                                    <?php echo htmlspecialchars($user->role ?? 'user'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo ($user->status ?? 1) ? 'success' : 'secondary'; ?>">
                                                    <?php echo ($user->status ?? 1) ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($user->created_at)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($user->email_verified ?? 0) ? 'success' : 'warning'; ?>">
                                                    <?php echo ($user->email_verified ?? 0) ? 'Yes' : 'No'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $user->id; ?>"
                                                       class="btn btn-outline-primary" title="View Profile">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if($user->id != $_SESSION['user_id']): ?>
                                                    <button type="button"
                                                            class="btn btn-<?php echo ($user->status ?? 1) ? 'warning' : 'success'; ?> toggle-user-status"
                                                            data-user-id="<?php echo $user->id; ?>"
                                                            data-current-status="<?php echo ($user->status ?? 1); ?>"
                                                            title="<?php echo ($user->status ?? 1) ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fas fa-<?php echo ($user->status ?? 1) ? 'pause' : 'play'; ?>"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No users found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Users pagination">
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
    // Toggle user status
    $('.toggle-user-status').on('click', function() {
        const button = $(this);
        const userId = button.data('user-id');
        const currentStatus = button.data('current-status') === 1;
        const newStatus = !currentStatus;

        if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this user?`)) {
            $.ajax({
                url: '<?php echo URLROOT; ?>/admin/toggleUserStatus/' + userId,
                type: 'POST',
                data: {
                    csrf_token: '<?php echo generateCSRFToken(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update button state
                        button.data('current-status', newStatus ? 1 : 0);
                        button.removeClass('btn-success btn-warning')
                               .addClass(newStatus ? 'btn-warning' : 'btn-success');
                        button.find('i').removeClass('fa-play fa-pause')
                                       .addClass(newStatus ? 'fa-pause' : 'fa-play');
                        button.attr('title', newStatus ? 'Deactivate' : 'Activate');

                        // Update status badge
                        const row = button.closest('tr');
                        const badge = row.find('td:nth-child(5) .badge');
                        badge.removeClass('bg-success bg-secondary')
                             .addClass(newStatus ? 'bg-success' : 'bg-secondary')
                             .text(newStatus ? 'Active' : 'Inactive');

                        // Show success message
                        const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text(`User ${newStatus ? 'activated' : 'deactivated'} successfully.`)
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
                        $('.card-body').prepend(alert);
                        setTimeout(() => alert.fadeOut(), 3000);
                    } else {
                        alert('Failed to update user status: ' + response.message);
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