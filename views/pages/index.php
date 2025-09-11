<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php flash('service_message'); ?>

<div class="p-5 mb-4 bg-light rounded-3 text-center">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold"><?php echo $data['title']; ?></h1>
        <p class="fs-4"><?php echo $data['description']; ?></p>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
