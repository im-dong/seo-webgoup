<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Review Order #<?php echo $data['order']->id; ?></h2>
            <p>Service: <?php echo htmlspecialchars($data['order']->service_title); ?></p>
            <p>Please provide your rating and comment for this service.</p>
            <form action="<?php echo URLROOT; ?>/reviews/add/<?php echo $data['order']->id; ?>" method="post">
                <div class="form-group mb-3">
                    <label for="rating">Rating: <sup>*</sup></label>
                    <select name="rating" class="form-select <?php echo (!empty($data['rating_err'])) ? 'is-invalid' : ''; ?>">
                        <option value="">Select Rating</option>
                        <option value="5" <?php echo ($data['rating'] == 5) ? 'selected' : ''; ?>>5 Stars - Excellent</option>
                        <option value="4" <?php echo ($data['rating'] == 4) ? 'selected' : ''; ?>>4 Stars - Very Good</option>
                        <option value="3" <?php echo ($data['rating'] == 3) ? 'selected' : ''; ?>>3 Stars - Good</option>
                        <option value="2" <?php echo ($data['rating'] == 2) ? 'selected' : ''; ?>>2 Stars - Fair</option>
                        <option value="1" <?php echo ($data['rating'] == 1) ? 'selected' : ''; ?>>1 Star - Poor</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $data['rating_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="comment">Comment: <sup>*</sup></label>
                    <textarea name="comment" class="form-control <?php echo (!empty($data['comment_err'])) ? 'is-invalid' : ''; ?>" rows="5"><?php echo $data['comment']; ?></textarea>
                    <span class="invalid-feedback"><?php echo $data['comment_err']; ?></span>
                </div>
                <input type="submit" class="btn btn-success" value="Submit Review">
                <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>