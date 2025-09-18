<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <?php if ($data['title'] === 'Successfully Unsubscribed'): ?>
                            <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-warning fa-4x mb-3"></i>
                        <?php endif; ?>
                    </div>

                    <h2 class="card-title"><?php echo $data['title']; ?></h2>
                    <p class="card-text text-muted"><?php echo $data['message']; ?></p>

                    <?php if ($data['title'] === 'Successfully Unsubscribed'): ?>
                        <p class="text-muted small mt-3">
                            We're sorry to see you go. You can always resubscribe later if you change your mind.
                        </p>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="<?php echo URLROOT; ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Return to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>