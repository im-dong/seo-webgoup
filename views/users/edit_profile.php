<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Edit Your Profile</h2>
            <p>Update your public profile information.</p>
            <form action="<?php echo URLROOT; ?>/users/editProfile" method="post">
                <div class="form-group mb-3">
                    <label for="username">Username: <sup>*</sup></label>
                    <input type="text" name="username" class="form-control form-control-lg <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['username']; ?>" disabled>
                    <small class="form-text text-muted">Username cannot be changed.</small>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email: <sup>*</sup></label>
                    <input type="email" name="email" class="form-control form-control-lg <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>" disabled>
                    <small class="form-text text-muted">Email cannot be changed.</small>
                </div>
                <div class="form-group mb-3">
                    <label for="bio">Bio:</label>
                    <textarea name="bio" class="form-control form-control-lg" rows="5"><?php echo $data['bio']; ?></textarea>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="submit" value="Save Changes" class="btn btn-success btn-block">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>