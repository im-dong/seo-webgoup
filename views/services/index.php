<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Link Service Marketplace</h1>

    <div class="row mb-4 align-items-center p-3 bg-light rounded">
        <div class="col-md-auto mb-2 mb-md-0">
            <strong>Category:</strong>
            <div class="btn-group">
                <a href="<?php echo URLROOT; ?>/services/index?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo empty($data['current_category']) ? 'btn-dark' : 'btn-outline-dark'; ?>">All</a>
                <a href="<?php echo URLROOT; ?>/services/index/backlink?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo (isset($data['current_category']) && $data['current_category'] == 'backlink') ? 'btn-dark' : 'btn-outline-dark'; ?>">Backlinks</a>
                <a href="<?php echo URLROOT; ?>/services/index/guest_post?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo (isset($data['current_category']) && $data['current_category'] == 'guest_post') ? 'btn-dark' : 'btn-outline-dark'; ?>">Guest Posts</a>
            </div>
        </div>
        <div class="col-md-5">
            <form id="industry-filter-form" action="<?php echo URLROOT; ?>/services/index/<?php echo $data['current_category']; ?>" method="get">
                <div class="input-group">
                    <label for="industry" class="input-group-text"><strong>Industry:</strong></label>
                    <select name="industry" class="form-select" onchange="document.getElementById('industry-filter-form').submit()">
                        <option value="">All Industries</option>
                        <?php foreach($data['industries'] as $industry): ?>
                            <option value="<?php echo $industry->id; ?>" <?php echo ($data['current_industry'] == $industry->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($industry->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if(empty($data['services'])): ?>
            <p class="text-center">No services have been published yet.</p>
        <?php else: ?>
            <?php foreach($data['services'] as $service): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 service-card">
                    <img src="<?php echo !empty($service->thumbnail_url) ? htmlspecialchars($service->thumbnail_url) : 'https://via.placeholder.com/300x200'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>">
                    <div class="card-body">
                        <h5 class="card-title card-title-clamp"><?php echo htmlspecialchars($service->title); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">$<?php echo htmlspecialchars($service->price); ?></h6>
                        <p class="card-text card-text-clamp"><?php echo substr(strip_tags($service->description), 0, 100); ?>...</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Seller: <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $service->userId; ?>"><?php echo htmlspecialchars($service->username); ?></a></li>
                        <li class="list-group-item">Delivery in: <?php echo htmlspecialchars($service->delivery_time); ?> days</li>
                        <li class="list-group-item">Link Type: <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'primary' : 'secondary'; ?>"><?php echo htmlspecialchars($service->link_type); ?></span></li>
                    </ul>
                    <div class="card-body mt-auto">
                        <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>