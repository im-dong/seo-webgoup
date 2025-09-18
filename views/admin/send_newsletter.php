<?php
require APPROOT . '/views/layouts/header.php';
require APPROOT . '/app/helpers/csrf_helper.php';
?>

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
                        <a class="nav-link active" href="<?php echo URLROOT; ?>/newsletter/send">
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
                <h1 class="h2">Send Newsletter</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="<?php echo URLROOT; ?>/newsletter/sendHistory" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-history me-1"></i> View History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Send Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title"><?php echo number_format($data['total_subscribers']); ?></h4>
                                    <p class="card-text">Active Subscribers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">HTML</h4>
                                    <p class="card-text">Rich Text Editor</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-code fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="card-title">Ready</h4>
                                    <p class="card-text">Send Now</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-paper-plane fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Compose Newsletter</h5>
                </div>
                <div class="card-body">
                    <form id="sendNewsletterForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" required placeholder="Enter newsletter subject...">
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" class="form-control" required placeholder="Write your newsletter content here..."></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Use the rich text editor above to format your content. Images, links, and formatting will be preserved in the email.
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Send Information</h6>
                            <ul class="mb-0">
                                <li>This newsletter will be sent to <strong><?php echo number_format($data['total_subscribers']); ?></strong> active subscribers</li>
                                <li>Each email will include an unsubscribe link</li>
                                <li>Send progress will be tracked and recorded</li>
                                <li>Failed sends will be logged for troubleshooting</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="location.href='<?php echo URLROOT; ?>/newsletter/admin'">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="fas fa-paper-plane me-1"></i> Send Newsletter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Send Progress Modal -->
<div class="modal fade" id="sendProgressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sending Newsletter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                </div>
                <p class="text-center mb-0" id="sendStatusText">Preparing to send...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" disabled>Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Send Result Modal -->
<div class="modal fade" id="sendResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Complete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sendResultContent">
                <!-- Result content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="viewHistoryBtn">View History</button>
            </div>
        </div>
    </div>
</div>

<link href="<?php echo URLROOT; ?>/assets/summernote/summernote-lite.min.css" rel="stylesheet">
<script src="<?php echo URLROOT; ?>/assets/summernote/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Summernote editor
    $('#content').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Handle form submission
    $('#sendNewsletterForm').on('submit', function(e) {
        e.preventDefault();

        const subject = $('#subject').val().trim();
        const content = $('#content').val().trim();

        if (!subject || !content) {
            alert('Please fill in both subject and content.');
            return;
        }

        // Show progress modal
        $('#sendProgressModal').modal('show');
        $('#sendBtn').prop('disabled', true);

        // Send AJAX request
        $.ajax({
            url: '<?php echo URLROOT; ?>/newsletter/processSend',
            type: 'POST',
            data: {
                subject: subject,
                content: content,
                csrf_token: '<?php echo generateCSRFToken(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                // Hide progress modal
                $('#sendProgressModal').modal('hide');

                if (response.success) {
                    // Show success result
                    const resultHtml = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i>Newsletter Sent Successfully!</h6>
                            <p class="mb-2">${response.message}</p>
                            <div class="row text-center mt-3">
                                <div class="col-4">
                                    <h5 class="text-success">${response.message.match(/Sent to (\d+) subscribers/)[1]}</h5>
                                    <small class="text-muted">Successfully Sent</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-danger">${response.message.match(/Failed: (\d+)/)[1]}</h5>
                                    <small class="text-muted">Failed</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-primary">100%</h5>
                                    <small class="text-muted">Complete</small>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#sendResultContent').html(resultHtml);
                } else {
                    // Show error result
                    $('#sendResultContent').html(`
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Send Failed</h6>
                            <p>${response.message}</p>
                        </div>
                    `);
                }

                // Show result modal
                $('#sendResultModal').modal('show');
            },
            error: function(xhr) {
                // Hide progress modal
                $('#sendProgressModal').modal('hide');

                // Show error
                $('#sendResultContent').html(`
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Error</h6>
                        <p>An error occurred while sending the newsletter. Please try again.</p>
                    </div>
                `);
                $('#sendResultModal').modal('show');
            },
            complete: function() {
                // Re-enable send button
                $('#sendBtn').prop('disabled', false);
            }
        });
    });

    // View history button
    $('#viewHistoryBtn').on('click', function() {
        window.location.href = '<?php echo URLROOT; ?>/newsletter/sendHistory';
    });
});
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>