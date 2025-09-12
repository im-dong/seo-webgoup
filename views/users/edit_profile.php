<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Edit Your Profile</h2>
            <p>Update your public profile information.</p>
            <form action="<?php echo URLROOT; ?>/users/editProfile" method="post" enctype="multipart/form-data">
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

                <div class="form-group mb-3">
                    <label for="profile_image">Profile Image:</label>
                    <input type="file" name="profile_image" id="profile_image_input" class="form-control form-control-lg <?php echo (!empty($data['profile_image_err'])) ? 'is-invalid' : ''; ?>">
                    <img id="profile_image_preview" src="<?php echo $data['profile_image_url'] ?? 'https://via.placeholder.com/150'; ?>" alt="Image Preview" class="img-thumbnail mt-2" style="width: 150px; height: auto;">
                    <span class="invalid-feedback"><?php echo $data['profile_image_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="website_url">Website URL:</label>
                    <input type="text" name="website_url" class="form-control form-control-lg <?php echo (!empty($data['website_url_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['website_url']; ?>">
                    <span class="invalid-feedback"><?php echo $data['website_url_err']; ?></span>
                </div>

                <div class="form-group mb-3">
                    <label for="country">Country:</label>
                    <input type="text" name="country" class="form-control form-control-lg" value="<?php echo $data['country']; ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupImagePreview('profile_image_input', 'profile_image_preview');
});
</script>