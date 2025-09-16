<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="d-flex align-items-center justify-content-center" style="min-height: 75vh;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Please sign in to continue.</p>
                </div>
                
                <?php flash('register_success'); ?>

                <form action="<?php echo URLROOT; ?>/users/login" method="post">
                    <div class="form-floating mb-3">
                        <input type="text" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" placeholder="Email or Username">
                        <label for="email">Email or Username</label>
                        <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" placeholder="Password">
                        <label for="password">Password</label>
                        <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted">Don't have an account? <a href="<?php echo URLROOT; ?>/users/register">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>