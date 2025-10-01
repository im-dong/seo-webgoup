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
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/admin/services">
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
                <h1 class="h2">Service Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <span class="btn btn-sm btn-outline-secondary">
                            Total: <?php echo number_format($data['pagination']['total_services']); ?> services
                        </span>
                    </div>
                </div>
            </div>

            <!-- Services Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Services List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Seller</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Created</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['services'])): ?>
                                    <?php foreach ($data['services'] as $service): ?>
                                        <tr>
                                            <td><?php echo $service->id; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if(!empty($service->thumbnail_url)): ?>
                                                        <img src="<?php echo URLROOT . '/' . $service->thumbnail_url; ?>"
                                                             alt="Thumbnail" class="me-2" style="width: 40px; height: 30px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($service->title); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars(substr($service->description, 0, 50)); ?>...</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $service->user_id; ?>"
                                                   class="text-decoration-none">
                                                    <?php echo htmlspecialchars($service->username); ?>
                                                </a>
                                            </td>
                                            <td><span class="badge bg-success">$<?php echo number_format($service->price, 2); ?></span></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($service->service_category); ?></span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($service->created_at)); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($service->status ?? 1) ? 'success' : 'secondary'; ?>">
                                                    <?php echo ($service->status ?? 1) ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->id; ?>"
                                                       class="btn btn-outline-primary" title="View Service">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $service->user_id; ?>"
                                                       class="btn btn-outline-info" title="View Seller">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-<?php echo ($service->status ?? 1) ? 'warning' : 'success'; ?> toggle-service-status"
                                                            data-service-id="<?php echo $service->id; ?>"
                                                            data-current-status="<?php echo ($service->status ?? 1); ?>"
                                                            title="<?php echo ($service->status ?? 1) ? 'Deactivate' : 'Activate'; ?>">
                                                        <i class="fas fa-<?php echo ($service->status ?? 1) ? 'pause' : 'play'; ?>"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger delete-service"
                                                            data-service-id="<?php echo $service->id; ?>"
                                                            data-title="<?php echo htmlspecialchars($service->title); ?>"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No services found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['pagination']['total_pages'] > 1): ?>
                        <nav aria-label="Services pagination">
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
                <p>Are you sure you want to delete this service?</p>
                <p><strong>Title:</strong> <span id="delete-title"></span></p>
                <p class="text-warning">This action cannot be undone and will also remove the service image.</p>
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
    // Toggle service status
    $('.toggle-service-status').on('click', function() {
        const button = $(this);
        const serviceId = button.data('service-id');
        const currentStatus = button.data('current-status') === 1;
        const newStatus = !currentStatus;

        if (confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this service?`)) {
            $.ajax({
                url: '<?php echo URLROOT; ?>/admin/toggleServiceStatus/' + serviceId,
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
                        const badge = row.find('td:nth-child(7) .badge');
                        badge.removeClass('bg-success bg-secondary')
                             .addClass(newStatus ? 'bg-success' : 'bg-secondary')
                             .text(newStatus ? 'Active' : 'Inactive');

                        // Show success message
                        const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text(`Service ${newStatus ? 'activated' : 'deactivated'} successfully.`)
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
                        $('.card-body').prepend(alert);
                        setTimeout(() => alert.fadeOut(), 3000);
                    } else {
                        alert('Failed to update service status: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });

    // Delete service
    let deleteServiceId = null;

    $('.delete-service').on('click', function() {
        deleteServiceId = $(this).data('service-id');
        const title = $(this).data('title');
        $('#delete-title').text(title);
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete').on('click', function() {
        if (deleteServiceId) {
            $.ajax({
                url: '<?php echo URLROOT; ?>/admin/deleteService/' + deleteServiceId,
                type: 'POST',
                data: {
                    csrf_token: '<?php echo generateCSRFToken(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        $('button[data-service-id="' + deleteServiceId + '"]').closest('tr').fadeOut(function() {
                            $(this).remove();
                        });

                        // Show success message
                        const alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                            .text('Service deleted successfully.')
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
                        $('.card-body').prepend(alert);
                        setTimeout(() => alert.fadeOut(), 3000);
                    } else {
                        alert('Failed to delete service: ' + response.message);
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