<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Email System Test
                    </h3>
                </div>
                <div class="card-body">
                    <?php if ($data['result'] == 'success'): ?>
                        <div class="alert alert-success">
                            <h5>Success!</h5>
                            Test email sent to: <?php echo htmlspecialchars($data['test_email']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($data['result'] == 'error'): ?>
                        <div class="alert alert-danger">
                            <h5>Error</h5>
                            <?php echo htmlspecialchars($data['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo URLROOT; ?>/users/emailTest">
                        <div class="mb-3">
                            <label class="form-label">Test Email Address:</label>
                            <input type="email" name="test_email" class="form-control"
                                   value="<?php echo htmlspecialchars($data['test_email']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Send Test Email
                        </button>
                    </form>

                    <hr>
                    <h5>Current Configuration:</h5>
                    <ul class="list-unstyled">
                        <li><strong>SMTP Host:</strong> <?php echo htmlspecialchars(EMAIL_HOST); ?></li>
                        <li><strong>SMTP Port:</strong> <?php echo htmlspecialchars(EMAIL_PORT); ?></li>
                        <li><strong>From Email:</strong> <?php echo htmlspecialchars(EMAIL_FROM_EMAIL); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>