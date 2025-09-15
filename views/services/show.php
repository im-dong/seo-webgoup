<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($data['service']->title); ?></h2>
            <hr>
            <p><strong>Sold by:</strong> <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['service']->userId; ?>"><strong><?php echo htmlspecialchars($data['service']->username); ?></strong></a></p>
            <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($data['service']->site_url); ?>" target="_blank"><?php echo htmlspecialchars($data['service']->site_url); ?></a></p>
            <hr>
            <h4>Service Description</h4>
            <div class="service-description">
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') : ?>
                    <?php echo $data['service']->description; // Admin sees rich text ?>
                <?php else : ?>
                    <?php echo nl2br(htmlspecialchars(strip_tags($data['service']->description))); // Non-admin sees plain text ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center fw-bold text-primary">$<?php echo htmlspecialchars($data['service']->price); ?></h2>
                    <hr>
                    <p class="text-center"><strong>Sold by:</strong> <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['service']->userId; ?>"><strong><?php echo htmlspecialchars($data['service']->username); ?></strong></a></p>
                    <hr>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Delivery Time:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($data['service']->delivery_time); ?> days</span></li>
                        <li class="mb-2"><strong>Link Duration:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($data['service']->duration); ?> days</span></li>
                        <li class="mb-2"><strong>Link Type:</strong> <span class="badge bg-<?php echo ($data['service']->link_type == 'follow') ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($data['service']->link_type); ?></span></li>
                        <li class="mb-2"><strong>Category:</strong> <span class="badge bg-warning text-dark"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($data['service']->service_category))); ?></span></li>
                        <?php if(!empty($data['service']->industry_name)): ?>
                            <li class="mb-2"><strong>Industry:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($data['service']->industry_name); ?></span></li>
                        <?php endif; ?>
                        <li class="mb-2"><strong>New Window:</strong> <?php echo ($data['service']->is_new_window) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></li>
                        <li class="mb-2"><strong>Adult Content:</strong> <?php echo ($data['service']->is_adult_allowed) ? '<span class="badge bg-success">Allowed</span>' : '<span class="badge bg-danger">Not Allowed</span>'; ?></li>
                    </ul>
                    <hr>
                    <div class="d-grid">
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['service']->userId): ?>
                            <a href="<?php echo URLROOT; ?>/orders/startInquiry/<?php echo $data['service']->serviceId; ?>" class="btn btn-primary btn-lg mb-2">Message Seller</a>
                            <a href="<?php echo URLROOT; ?>/orders/create_and_pay/<?php echo $data['service']->serviceId; ?>" class="btn btn-success btn-lg">Buy Now ($<?php echo htmlspecialchars($data['service']->price); ?>)</a>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                             <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-primary btn-lg">Login to Purchase</a>
                        <?php endif; ?>
                    </div>