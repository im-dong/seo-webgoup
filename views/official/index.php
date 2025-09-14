<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Our Official SEO Services</h1>
    <p>These are the premium SEO services offered directly by WebGoup.</p>

    <div class="row">
        <?php if(empty($data['officialServices'])): ?>
            <p class="text-center">No official services are currently available.</p>
        <?php else: ?>
            <?php foreach($data['officialServices'] as $service): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 official-service-card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">$<?php echo htmlspecialchars($service->price); ?></h6>
                        <div class="card-text"><?php echo $service->description; ?></div>
                    </div>
                    <ul class="list-group list-group-flush">
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