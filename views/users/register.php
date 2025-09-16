<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-7 col-lg-6 col-xl-5">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <?php if($data['step'] == '1'): ?>
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Create Your Account</h2>
                        <p class="text-muted">Join our community to buy or sell SEO services.</p>
                    </div>
                    <form action="<?php echo URLROOT; ?>/users/register" method="post">
                        <input type="hidden" name="step" value="1">
                        
                        <div class="form-floating mb-3">
                            <input type="text" name="username" id="username" class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>" placeholder="Username">
                            <label for="username">Username</label>
                            <div class="invalid-feedback"><?php echo $data['username_err']; ?></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="email" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" placeholder="Email">
                            <label for="email">Email</label>
                            <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" placeholder="Password">
                            <label for="password">Password</label>
                            <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" placeholder="Confirm Password">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Send Verification Code</button>
                        </div>
                    </form>
                    <div class="text-center mt-4">
                        <p class="text-muted">Already have an account? <a href="<?php echo URLROOT; ?>/users/login">Log in</a></p>
                    </div>

                <?php else: // Step 2: Verification ?>

                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Verify Your Email</h2>
                        <p class="text-muted">A 6-digit code was sent to <strong><?php echo htmlspecialchars($data['email']); ?></strong>.</p>
                    </div>

                    <?php if(!empty($data['username_err']) || !empty($data['email_err'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $data['username_err'] ?? ''; ?>
                            <?php echo $data['email_err'] ?? ''; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo URLROOT; ?>/users/register" method="post">
                        <input type="hidden" name="step" value="2">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($data['username']); ?>">
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">

                        <div class="form-floating mb-3">
                            <input type="text" name="verification_code" id="verification_code" class="form-control <?php echo (!empty($data['verification_code_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['verification_code']; ?>" placeholder="6-digit code" maxlength="6">
                            <label for="verification_code">Verification Code</label>
                            <div class="invalid-feedback"><?php echo $data['verification_code_err']; ?></div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input <?php echo (!empty($data['terms_err'])) ? 'is-invalid' : ''; ?>" type="checkbox" name="terms" id="terms" value="1" <?php echo (!empty($data['terms'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="<?php echo URLROOT; ?>/pages/terms" target="_blank">Terms of Service</a>.
                            </label>
                            <div class="invalid-feedback d-block"><?php echo $data['terms_err']; ?></div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">Complete Registration</button>
                            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light btn-lg">Back</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
