<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Add a New Link Service</h2>
            <p>Fill out the form to publish your service</p>
            <form action="<?php echo URLROOT; ?>/services/add" method="post">
                <div class="form-group mb-3">
                    <label for="title">Service Title: <sup>*</sup></label>
                    <input type="text" name="title" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>">
                    <span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Service Description: <sup>*</sup></label>
                    <textarea name="description" class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['description']; ?></textarea>
                    <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="site_url">Your Website URL (where the link will be placed): <sup>*</sup></label>
                    <input type="text" name="site_url" class="form-control <?php echo (!empty($data['site_url_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['site_url']; ?>">
                    <span class="invalid-feedback"><?php echo $data['site_url_err']; ?></span>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="price">Price ($): <sup>*</sup></label>
                        <input type="number" name="price" class="form-control <?php echo (!empty($data['price_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['price']; ?>" step="0.01">
                        <span class="invalid-feedback"><?php echo $data['price_err']; ?></span>
                    </div>
                    <div class="col">
                        <label for="delivery_time">Delivery Time (days): <sup>*</sup></label>
                        <input type="number" name="delivery_time" class="form-control <?php echo (!empty($data['delivery_time_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['delivery_time']; ?>">
                        <span class="invalid-feedback"><?php echo $data['delivery_time_err']; ?></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="duration">Link Duration (days): <sup>*</sup></label>
                        <input type="number" name="duration" class="form-control <?php echo (!empty($data['duration_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['duration']; ?>">
                        <span class="invalid-feedback"><?php echo $data['duration_err']; ?></span>
                    </div>
                    <div class="col">
                        <label for="link_type">Link Type: <sup>*</sup></label>
                        <select name="link_type" class="form-select">
                            <option value="follow">Follow</option>
                            <option value="nofollow">Nofollow</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="is_new_window">Open in new window?: <sup>*</sup></label>
                        <select name="is_new_window" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="is_adult_allowed">Allow Adult/Gambling Content?: <sup>*</sup></label>
                        <select name="is_adult_allowed" class="form-select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input <?php echo (!empty($data['terms_err'])) ? 'is-invalid' : ''; ?>" type="checkbox" name="terms" id="terms">
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#">Terms of Service</a>.
                    </label>
                    <div class="invalid-feedback"><?php echo $data['terms_err']; ?></div>
                </div>
                <input type="submit" class="btn btn-success" value="Publish Service">
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>