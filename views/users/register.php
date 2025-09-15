<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <?php if($data['step'] == '1'): ?>
                <h2>Create An Account</h2>
                <p>Please fill out this form to register with us</p>
                <form action="<?php echo URLROOT; ?>/users/register" method="post">
                    <input type="hidden" name="step" value="1">
                    <div class="form-group mb-3">
                        <label for="username">Username: <sup>*</sup></label>
                        <input type="text" name="username" class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>">
                        <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email: <sup>*</sup></label>
                        <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                        <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password: <sup>*</sup></label>
                        <input type="password" name="password" class="form-control form-control-lg <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['password']; ?>">
                        <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirm_password">Confirm Password: <sup>*</sup></label>
                        <input type="password" name="confirm_password" class="form-control form-control-lg <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['confirm_password']; ?>">
                        <span class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></span>
                    </div>

                    <div class="row">
                        <div class="col">
                            <input type="submit" value="Next Step - Send Verification Code" class="btn btn-primary btn-block">
                        </div>
                        <div class="col">
                            <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-light btn-block">Have an account? Login</a>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <h2>Verify Your Email</h2>
                <p>We've sent a 6-digit verification code to your email. Please enter it below to complete your registration.</p>
                <div class="alert alert-info">
                    <strong>📧 Check your inbox!</strong><br>
                    The verification code has been sent to <strong><?php echo htmlspecialchars($data['email']); ?></strong><br>
                    <small>⏱️ The code will expire in 10 minutes.</small>
                </div>

                <form action="<?php echo URLROOT; ?>/users/register" method="post">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($data['username']); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">

                    <?php if(!empty($data['username_err'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $data['username_err']; ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($data['email_err'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $data['email_err']; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mb-3">
                        <label for="verification_code">Verification Code: <sup>*</sup></label>
                        <input type="text" name="verification_code" class="form-control form-control-lg <?php echo (!empty($data['verification_code_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['verification_code']; ?>" placeholder="Enter 6-digit code" maxlength="6" pattern="[0-9]{6}">
                        <span class="invalid-feedback"><?php echo $data['verification_code_err']; ?></span>
                        <small class="form-text text-muted">Enter the 6-digit code sent to your email</small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" value="1" <?php echo (!empty($data['terms'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="<?php echo URLROOT; ?>/pages/terms" target="_blank">Terms of Service</a> and understand the 30% platform commission
                        </label>
                        <?php if(!empty($data['terms_err'])): ?>
                            <div class="invalid-feedback d-block"><?php echo $data['terms_err']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Complete Registration
                            </button>
                        </div>
                        <div class="col">
                            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <small class="text-muted">Didn't receive the code? <a href="<?php echo URLROOT; ?>/users/register">Try again</a></small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
