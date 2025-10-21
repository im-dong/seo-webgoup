<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php flash('email_test_result'); ?>

<!-- Email Test Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-envelope me-2"></i>Email System Test
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center mb-4">
                            Test your email configuration by sending a test email to any address.
                        </p>

                        <?php if ($data['test_result'] == 'success'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading">
                                    <i class="fas fa-check-circle me-2"></i>Success!
                                </h5>
                                Test email sent successfully to <strong><?php echo htmlspecialchars($data['test_email']); ?></strong>
                                <hr>
                                <p class="mb-0">Please check your inbox (and spam folder) to confirm receipt.</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($data['test_result'] == 'error'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Email Sending Failed
                                </h5>
                                <?php if (isset($data['error_message'])): ?>
                                    <p class="mb-2"><?php echo htmlspecialchars($data['error_message']); ?></p>
                                <?php endif; ?>
                                <p class="mb-0">Please check your email configuration and server error logs.</p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo URLROOT; ?>/testemail">
                            <div class="mb-4">
                                <label for="test_email" class="form-label">
                                    <i class="fas fa-at me-1"></i>Test Email Address
                                </label>
                                <input type="email"
                                       class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                                       id="test_email"
                                       name="test_email"
                                       value="<?php echo htmlspecialchars($data['test_email']); ?>"
                                       placeholder="Enter email address to test"
                                       required>
                                <?php if (!empty($data['email_err'])): ?>
                                    <div class="invalid-feedback">
                                        <?php echo $data['email_err']; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    The test email will be sent to this address. You can use your own email or a test account.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Send Test Email
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <!-- Current Configuration Display -->
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-cog me-1"></i>Current Email Configuration
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <small class="text-muted">SMTP Host:</small><br>
                                        <strong><?php echo htmlspecialchars(EMAIL_HOST); ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted">SMTP Port:</small><br>
                                        <strong><?php echo htmlspecialchars(EMAIL_PORT); ?></strong>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <small class="text-muted">From Email:</small><br>
                                        <strong><?php echo htmlspecialchars(EMAIL_FROM_EMAIL); ?></strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted">From Name:</small><br>
                                        <strong><?php echo htmlspecialchars(EMAIL_FROM_NAME); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test Information -->
                        <div class="mt-4">
                            <h6>
                                <i class="fas fa-info-circle me-1"></i>What This Test Checks:
                            </h6>
                            <ul class="small text-muted mb-0">
                                <li>SMTP server connection and authentication</li>
                                <li>PHPMailer library functionality</li>
                                <li>HTML email content rendering</li>
                                <li>Email delivery to recipient inbox</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions Section -->
<section class="py-3 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                    <a href="<?php echo URLROOT; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layouts/footer.php'; ?>