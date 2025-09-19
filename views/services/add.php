<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
<link href="<?php echo URLROOT; ?>/assets/summernote/summernote-lite.min.css" rel="stylesheet">
<?php endif; ?>

<div class="row">
    <?php flash('service_message'); ?>
    <div class="col-md-8 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Add a New Link Service</h2>
            <p>Fill out the form to publish your service</p>
            <form action="<?php echo URLROOT; ?>/services/add" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="title">Service Title: <sup>*</sup></label>
                    <input type="text" name="title" class="form-control <?php echo (!empty($data['title_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['title']; ?>">
                    <span class="invalid-feedback"><?php echo $data['title_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Service Description: <sup>*</sup></label>
                    <textarea id="description" name="description" class="form-control <?php echo (!empty($data['description_err'])) ? 'is-invalid' : ''; ?>"><?php echo $data['description']; ?></textarea>
                    <span class="invalid-feedback"><?php echo $data['description_err']; ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="thumbnail_image">Thumbnail Image:</label>
                    <input type="file" name="thumbnail_image" id="thumbnail_image_input" class="form-control <?php echo (!empty($data['thumbnail_err'])) ? 'is-invalid' : ''; ?>">
                    <img id="thumbnail_image_preview" src="<?php echo $data['thumbnail_url'] ?? 'https://via.placeholder.com/300x200'; ?>" alt="Image Preview" class="img-thumbnail mt-2" style="width: 300px; height: 200px; object-fit: cover;">
                    <span class="invalid-feedback"><?php echo $data['thumbnail_err']; ?></span>
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
                    <div class="col-md-6">
                        <label for="service_category">Service Category: <sup>*</sup></label>
                        <select name="service_category" class="form-select <?php echo (!empty($data['service_category_err'])) ? 'is-invalid' : ''; ?>">
                            <option value="backlink">Backlink</option>
                            <option value="guest_post">Guest Post</option>
                        </select>
                        <span class="invalid-feedback"><?php echo $data['service_category_err']; ?></span>
                    </div>
                    <div class="col-md-6">
                        <label for="industry_id">Industry: <sup>*</sup></label>
                        <select name="industry_id" class="form-select <?php echo (!empty($data['industry_id_err'])) ? 'is-invalid' : ''; ?>">
                            <?php foreach($data['industries'] as $industry): ?>
                                <option value="<?php echo $industry->id; ?>" <?php echo (!isset($data['industry_id']) || $data['industry_id'] == $industry->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($industry->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="invalid-feedback"><?php echo $data['industry_id_err']; ?></span>
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
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_official" id="is_official">
                    <label class="form-check-label" for="is_official">
                        Professional Service (will appear in Professional Services section)
                    </label>
                </div>
                <?php endif; ?>

                <div class="card border-danger mb-4">
                    <div class="card-body">
                        <h6 class="card-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Service Provider Legal Agreement</h6>
                        <p class="card-text small">
                            As a service provider, you must comply with all applicable laws and search engine guidelines. By publishing a service, you agree to:
                        </p>
                        <ul class="small mb-3">
                            <li>Follow Google's Search Essentials and all search engine policies</li>
                            <li>Not engage in link schemes or black hat SEO practices</li>
                            <li>Provide accurate service descriptions without false guarantees</li>
                            <li>Accept full responsibility for your services</li>
                            <li>Indemnify webGoup from any claims related to your services</li>
                        </ul>
                        <div class="form-check mb-3">
                            <input class="form-check-input <?php echo (!empty($data['terms_err'])) ? 'is-invalid' : ''; ?>" type="checkbox" name="terms" id="terms" required>
                            <label class="form-check-label" for="terms">
                                <strong>I have read the <a href="<?php echo URLROOT; ?>/pages/terms" target="_blank">Terms of Service</a> and <a href="<?php echo URLROOT; ?>/pages/seoGuidelines" target="_blank">SEO Guidelines</a>. I certify that my service complies with all applicable laws and search engine policies.</strong>
                            </label>
                            <div class="invalid-feedback"><?php echo $data['terms_err']; ?></div>
                        </div>
                        <p class="card-text small text-muted mb-0">
                            <i class="fas fa-gavel me-1"></i>
                            Violation of these terms may result in service removal, account suspension, or legal action.
                        </p>
                    </div>
                </div>
                <input type="submit" class="btn btn-success" value="Publish Service">
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>

<?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
<script src="<?php echo URLROOT; ?>/assets/summernote/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
  $('#description').summernote({
    height: 300,
    dialogsInBody: true,
    dialogsFade: false,
    disableDragAndDrop: false,
    followingToolbar: false,
    toolbar: [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      ['table', ['table']],
      ['insert', ['link', 'picture', 'video']],
      ['view', ['fullscreen', 'codeview', 'help']]
    ]
  });
});
</script>
<?php else: ?>
<script>
// Add more rows to the textarea for non-admins
document.getElementById('description').rows = 10;
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setupImagePreview('thumbnail_image_input', 'thumbnail_image_preview');

    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Check if the input has an error
            if (this.classList.contains('is-invalid')) {
                // Basic validation: check if the value is not empty
                if (this.value.trim() !== '') {
                    this.classList.remove('is-invalid');
                    const errorSpan = this.nextElementSibling;
                    if (errorSpan && errorSpan.classList.contains('invalid-feedback')) {
                        errorSpan.textContent = '';
                    }
                }
            }
        });
    });
});
</script>