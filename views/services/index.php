<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container">
    <h1 class="my-4">Link Service Marketplace</h1>

    <div class="p-4 mb-4 bg-white rounded-3 shadow-sm border">
        <div class="row g-3 align-items-center">
            <div class="col-md-auto">
                <h5 class="mb-0">Filters</h5>
            </div>
            <div class="col-md">
                <div class="btn-group w-100" role="group" aria-label="Category Filter">
                    <a href="<?php echo URLROOT; ?>/services/index?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo empty($data['current_category']) ? 'btn-primary' : 'btn-outline-primary'; ?>">All</a>
                    <a href="<?php echo URLROOT; ?>/services/index/backlink?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo (isset($data['current_category']) && $data['current_category'] == 'backlink') ? 'btn-primary' : 'btn-outline-primary'; ?>">Backlinks</a>
                    <a href="<?php echo URLROOT; ?>/services/index/guest_post?industry=<?php echo $data['current_industry']; ?>" class="btn <?php echo (isset($data['current_category']) && $data['current_category'] == 'guest_post') ? 'btn-primary' : 'btn-outline-primary'; ?>">Guest Posts</a>
                </div>
            </div>
            <div class="col-md-5">
                <form id="industry-filter-form" action="<?php echo URLROOT; ?>/services/index/<?php echo $data['current_category']; ?>" method="get">
                    <div class="input-group">
                        <label for="industry" class="input-group-text"><i class="fas fa-briefcase"></i></label>
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
    </div>

    <?php if(!empty($data['pagination'])): ?>
        <?php echo showPaginationStats($data['pagination']); ?>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if(empty($data['services'])): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4 class="alert-heading">No Services Found</h4>
                    <p>There are no services matching your current filter criteria. Try selecting a different category or industry.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($data['services'] as $service): ?>
            <div class="col">
                <div class="card h-100 service-card">
                    <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>">
                        <img src="<?php echo !empty($service->thumbnail_url) ? htmlspecialchars(URLROOT . '/' . $service->thumbnail_url) : URLROOT . '/uploads/images/thumbnails/default.png'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title flex-grow-1"><a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($service->title); ?></a></h5>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="h5 text-primary mb-0">$<?php echo htmlspecialchars($service->price); ?></p>
                            <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'success' : 'secondary'; ?>"><?php echo ucfirst(htmlspecialchars($service->link_type)); ?></span>
                        </div>
                        <small class="text-muted">Delivery in <?php echo htmlspecialchars($service->delivery_time); ?> days</small>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0">
                         <small class="text-muted">by <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $service->userId; ?>"><?php echo htmlspecialchars($service->username); ?></a></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if(!empty($data['pagination']) && $data['pagination']['total_pages'] > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo paginate($data['pagination']['current_page'], $data['pagination']['total_pages'], $data['base_url'], $data['get_params']); ?>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>