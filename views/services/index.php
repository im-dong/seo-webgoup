<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Link Service Marketplace</h1>

    <div class="row">
        <?php if(empty($data['services'])): ?>
            <p class="text-center">No services have been published yet.</p>
        <?php else: ?>
            <?php foreach($data['services'] as $service): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo !empty($service->thumbnail_url) ? htmlspecialchars($service->thumbnail_url) : 'https://via.placeholder.com/300x200'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">$<?php echo htmlspecialchars($service->price); ?></h6>
                        <p class="card-text"><?php echo substr(htmlspecialchars($service->description), 0, 100); ?>...</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Seller: <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $service->userId; ?>"><?php echo htmlspecialchars($service->username); ?></a></li>
                        <li class="list-group-item">Delivery in: <?php echo htmlspecialchars($service->delivery_time); ?> days</li>
                        <li class="list-group-item">Link Type: <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'primary' : 'secondary'; ?>"><?php echo htmlspecialchars($service->link_type); ?></span></li>
                    </ul>
                    <div class="card-body">
                        <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>