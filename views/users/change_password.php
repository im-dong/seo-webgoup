<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-key me-2"></i>Change Password</h4>
                </div>
                <div class="card-body">
                    <?php flash('user_message'); ?>

                    <form action="<?php echo URLROOT; ?>/users/changePassword" method="POST">
                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" name="current_password"
                                   class="form-control <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>"
                                   id="current_password" value="<?php echo $data['current_password']; ?>" required>
                            <div class="invalid-feedback"><?php echo $data['current_password_err']; ?></div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" name="new_password"
                                   class="form-control <?php echo (!empty($data['new_password_err'])) ? 'is-invalid' : ''; ?>"
                                   id="new_password" value="<?php echo $data['new_password']; ?>" required>
                            <div class="invalid-feedback"><?php echo $data['new_password_err']; ?></div>
                            <div class="form-text">Password must be at least 6 characters long.</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password"
                                   class="form-control <?php echo (!empty($data['confirm_password_err'])) ? 'is-invalid' : ''; ?>"
                                   id="confirm_password" value="<?php echo $data['confirm_password']; ?>" required>
                            <div class="invalid-feedback"><?php echo $data['confirm_password_err']; ?></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                            <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4 border-info">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-shield-alt me-2"></i>Password Security Tips</h5>
                    <ul class="mb-0">
                        <li>Use a strong password with at least 8 characters</li>
                        <li>Include uppercase letters, lowercase letters, numbers, and symbols</li>
                        <li>Avoid using personal information or common words</li>
                        <li>Use a unique password for each website</li>
                        <li>Consider using a password manager</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>