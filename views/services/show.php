<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="bg-white py-4">
<div class="container">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/services">Marketplace</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($data['service']->title); ?></li>
        </ol>
    </nav>

    <div class="row g-5 mt-2">
        <!-- Left Column: Service Details -->
        <div class="col-lg-8">
            <h1 class="display-6 fw-bold mb-3"><?php echo htmlspecialchars($data['service']->title); ?></h1>
            
            <!-- Service Image -->
            <div class="mb-4">
                <img src="<?php echo !empty($data['service']->thumbnail_url) ? htmlspecialchars(URLROOT . '/' . $data['service']->thumbnail_url) : URLROOT . '/uploads/images/thumbnails/default.png'; ?>"
                     class="img-fluid rounded-3 shadow-sm w-100"
                     alt="<?php echo htmlspecialchars($data['service']->title); ?>"
                     style="max-height: 450px; object-fit: cover;">
            </div>

            <!-- Tabs for Description, Reviews etc. -->
            <ul class="nav nav-tabs nav-fill mb-4" id="serviceTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="seller-tab" data-bs-toggle="tab" data-bs-target="#seller" type="button" role="tab" aria-controls="seller" aria-selected="false">About Seller</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews (<?php echo isset($data['reviews']) && is_array($data['reviews']) ? count($data['reviews']) : 0; ?>)</button>
                </li>
            </ul>

            <div class="tab-content" id="serviceTabContent">
                <!-- Description Pane -->
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <h4 class="mb-3">Service Details</h4>
                    <div class="service-description">
                        <?php if(isset($data['service']->role) && $data['service']->role == 'admin') :
                            echo $data['service']->description; // Admin posts show full HTML - 不过滤
                        else :
                            echo nl2br(htmlspecialchars(strip_tags($data['service']->description))); // User posts show plain text
                        endif; ?>
                    </div>
                </div>

                <!-- Seller Pane -->
                <div class="tab-pane fade" id="seller" role="tabpanel" aria-labelledby="seller-tab">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo !empty($data['service']->profile_image_url) ? (strpos($data['service']->profile_image_url, 'http') === 0 ? $data['service']->profile_image_url : URLROOT . $data['service']->profile_image_url) : '/assets/default.png'; ?>" alt="Seller Avatar" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($data['service']->username); ?></h4>
                            <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['service']->userId; ?>">View Profile</a>
                        </div>
                    </div>
                    <hr>
                    <p><?php echo !empty($data['service']->bio) ? nl2br(htmlspecialchars($data['service']->bio)) : 'No bio available for this seller.'; ?></p>

                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin' && $data['sellerFullInfo']): ?>
                        <div class="admin-user-info mt-4">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-user-shield"></i> Admin Panel - User Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Basic Information</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>User ID:</strong></td>
                                                    <td><?php echo htmlspecialchars($data['sellerFullInfo']->id); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Username:</strong></td>
                                                    <td><?php echo htmlspecialchars($data['sellerFullInfo']->username); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td><?php echo htmlspecialchars($data['sellerFullInfo']->email); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Display Name:</strong></td>
                                                    <td><?php echo htmlspecialchars($data['sellerFullInfo']->display_name ?? 'Not set'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Role:</strong></td>
                                                    <td><span class="badge bg-<?php echo ($data['sellerFullInfo']->role == 'admin') ? 'danger' : 'primary'; ?>"><?php echo htmlspecialchars($data['sellerFullInfo']->role ?? 'user'); ?></span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Account Status:</strong></td>
                                                    <td><span class="badge bg-<?php echo ($data['sellerFullInfo']->status == 'active') ? 'success' : 'secondary'; ?>"><?php echo htmlspecialchars($data['sellerFullInfo']->status ?? 'unknown'); ?></span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Member Since:</strong></td>
                                                    <td><?php echo date('M j, Y', strtotime($data['sellerFullInfo']->created_at)); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Account Information</h6>
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Profile Image:</strong></td>
                                                    <td>
                                                        <?php if(!empty($data['sellerFullInfo']->profile_image_url)): ?>
                                                            <img src="<?php echo (strpos($data['sellerFullInfo']->profile_image_url, 'http') === 0 ? $data['sellerFullInfo']->profile_image_url : URLROOT . $data['sellerFullInfo']->profile_image_url); ?>" alt="Profile" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                                        <?php else: ?>
                                                            <span class="text-muted">Default avatar</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email Verified:</strong></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($data['sellerFullInfo']->email_verified ?? 0) ? 'success' : 'warning'; ?>">
                                                            <?php echo ($data['sellerFullInfo']->email_verified ?? 0) ? 'Verified' : 'Not Verified'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php if(!empty($data['sellerFullInfo']->bio)): ?>
                                                <tr>
                                                    <td><strong>Bio:</strong></td>
                                                    <td><?php echo nl2br(htmlspecialchars(substr($data['sellerFullInfo']->bio, 0, 200))); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><strong>Account Status:</strong></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($data['sellerFullInfo']->status ?? 0) ? 'success' : 'danger'; ?>">
                                                            <?php echo ($data['sellerFullInfo']->status ?? 0) ? 'Active' : 'Inactive'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <small><i class="fas fa-info-circle"></i> This information is only visible to administrators for account management purposes.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reviews Pane -->
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <?php if(!isset($data['reviews']) || empty($data['reviews'])) : ?>
                        <div class="alert alert-light text-center">No reviews for this service yet.</div>
                    <?php else: ?>
                        <?php foreach($data['reviews'] as $review): ?>
                            <div class="d-flex mb-4">
                                <img src="<?php echo !empty($review->profile_image_url) ? (strpos($review->profile_image_url, 'http') === 0 ? $review->profile_image_url : URLROOT . $review->profile_image_url) : '/assets/default.png'; ?>" alt="Reviewer Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <strong><?php echo htmlspecialchars($review->username); ?></strong>
                                    <div class="text-warning mb-1">
                                        <?php for($i = 0; $i < 5; $i++): ?>
                                            <i class="fas fa-star<?php echo ($i < $review->rating) ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($review->comment); ?></p>
                                    <small class="text-muted"><?php echo date('F j, Y', strtotime($review->created_at)); ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Purchase Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm position-sticky" style="top: 100px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Service Package</h5>
                        <p class="h3 fw-bold text-primary mb-0">$<?php echo htmlspecialchars($data['service']->price); ?></p>
                    </div>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Delivery Time
                            <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($data['service']->delivery_time); ?> days</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Link Type
                            <span class="badge bg-<?php echo ($data['service']->link_type == 'follow') ? 'success' : 'secondary'; ?> rounded-pill"><?php echo htmlspecialchars($data['service']->link_type); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Category
                            <span class="badge bg-info text-dark rounded-pill"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($data['service']->service_category))); ?></span>
                        </li>
                        <?php if(!empty($data['service']->industry_name)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Industry
                            <span class="badge bg-light text-dark rounded-pill"><?php echo htmlspecialchars($data['service']->industry_name); ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>

                    <div class="d-grid gap-2">
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['service']->userId): ?>
                            <a href="<?php echo URLROOT; ?>/orders/create_and_pay/<?php echo $data['service']->serviceId; ?>" class="btn btn-primary btn-lg">Order Now</a>
                            <a href="<?php echo URLROOT; ?>/orders/startInquiry/<?php echo $data['service']->serviceId; ?>" class="btn btn-outline-secondary">Message Seller</a>
                        <?php elseif(!isset($_SESSION['user_id'])): ?>
                             <a href="<?php echo URLROOT; ?>/users/login" class="btn btn-primary btn-lg">Login to Order</a>
                        <?php else: ?>
                            <button class="btn btn-light btn-lg" disabled>This is your own service</button>
                        <?php endif; ?>

                        <!-- Admin Delete Button -->
                        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                            <form action="<?php echo URLROOT; ?>/services/adminDelete/<?php echo $data['service']->serviceId; ?>" method="POST" class="mt-3" onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash-alt"></i> Delete Service (Admin)
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>