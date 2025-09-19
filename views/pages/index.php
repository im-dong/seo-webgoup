<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php flash('service_message'); ?>

<!-- Legal Notice Banner -->
<?php if(isset($_SESSION['user_id']) && !isset($_SESSION['legal_notice_shown'])): ?>
<div class="alert alert-warning alert-dismissible fade show mb-0 legal-notice" role="alert">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-10">
                <strong>⚠️ Important Legal Notice:</strong> webGoup is a technology platform connecting service providers and buyers.
                We do not provide SEO services directly. All users must comply with search engine guidelines.
                <a href="<?php echo URLROOT; ?>/pages/terms" class="alert-link">Read our Terms of Service</a> and
                <a href="<?php echo URLROOT; ?>/pages/seoGuidelines" class="alert-link">SEO Guidelines</a>.
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn-close legal-notice-close" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const legalNotice = document.querySelector('.legal-notice');
    const closeButton = document.querySelector('.legal-notice-close');

    if (closeButton && legalNotice) {
        closeButton.addEventListener('click', function() {
            // Set session variable to mark legal notice as shown for this login session
            fetch('<?php echo URLROOT; ?>/users/markLegalNoticeShown', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Immediately hide the alert
            const alert = closeButton.closest('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 150); // Wait for fade out animation
            }
        });
    }
});
</script>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero-section-new">
    <div class="container text-center">
        <div class="hero-content">
            <h1 class="display-3 fw-bold mb-4">web go up</h1>
            <p class="lead mb-5 mx-auto"><?php echo $data['description']; ?></p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-primary-new btn-lg">
                    <i class="fas fa-rocket me-2"></i>Get Started Free
                </a>
                <a href="<?php echo URLROOT; ?>/services" class="btn btn-secondary-new btn-lg">
                    <i class="fas fa-search me-2"></i>Explore Marketplace
                </a>
            </div>
            <div class="hero-stats mt-5">
                <div class="row justify-content-center g-4">
                    <div class="col-6 col-md-auto">
                        <div class="stat-item">
                            <div class="h2 fw-bold mb-0">70%</div>
                            <small class="text-white-75">Seller Revenue</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="stat-item">
                            <div class="h2 fw-bold mb-0">Global</div>
                            <small class="text-white-75">Marketplace</small>
                        </div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <div class="stat-item">
                            <div class="h2 fw-bold mb-0">Secure</div>
                            <small class="text-white-75">Payments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it Works Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2>How It Works</h2>
            <p class="lead text-muted">A simple, two-sided marketplace for SEO services.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h5 class="card-title">Buy SEO Services</h5>
                        <p class="card-text">Browse our marketplace of vetted providers and purchase high-quality SEO services to boost your website rankings.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <h5 class="card-title">Sell SEO Services</h5>
                        <p class="card-text">Monetize your digital assets by selling backlink placements to our global community. Set your own prices and earn revenue.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Professional Services Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Professional Services</h2>
            <p class="lead text-muted">Vetted providers with enhanced guarantees and refund protection.</p>
        </div>
        <div class="row g-4">
            <?php
            $serviceModel = new Service();
            $officialServices = $serviceModel->getOfficialServices();
            if(!empty($officialServices)):
                foreach(array_slice($officialServices, 0, 4) as $service):
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card service-card h-100">
                    <?php if(!empty($service->thumbnail_url)): ?>
                    <img src="<?php echo URLROOT . $service->thumbnail_url; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <img src="<?php echo URLROOT; ?>/uploads/images/thumbnails/default.png" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                        <p class="card-text"><?php echo substr(strip_tags($service->description), 0, 80); ?>...</p>
                        <div class="mt-auto">
                            <p class="h5 text-primary mb-3">$<?php echo htmlspecialchars($service->price); ?></p>
                            <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                endforeach;
            else:
            ?>
            <div class="col-12 text-center">
                <p class="text-muted">Professional services coming soon. We're currently vetting the best providers!</p>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo URLROOT; ?>/official" class="btn btn-primary btn-lg">View All Professional Services</a>
        </div>
    </div>
</section>

<!-- Community Marketplace Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2>Community Marketplace</h2>
            <p class="lead text-muted">Discover services from our trusted community providers.</p>
        </div>
        <div class="row g-4">
            <?php
            $marketplaceServices = $serviceModel->getServices();
            if(!empty($marketplaceServices)):
                foreach(array_slice($marketplaceServices, 0, 4) as $service):
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card service-card h-100">
                    <?php if(!empty($service->thumbnail_url)): ?>
                    <img src="<?php echo URLROOT . $service->thumbnail_url; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                    <img src="<?php echo URLROOT; ?>/uploads/images/thumbnails/default.png" class="card-img-top" alt="<?php echo htmlspecialchars($service->title); ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                        <div class="d-flex justify-content-between align-items-center my-2">
                            <small class="text-muted">By <?php echo htmlspecialchars($service->username); ?></small>
                            <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'success' : 'secondary'; ?>"><?php echo ucfirst($service->link_type); ?></span>
                        </div>
                        <div class="mt-auto">
                            <p class="h5 text-primary mb-3">$<?php echo htmlspecialchars($service->price); ?></p>
                            <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                endforeach;
            else:
            ?>
            <div class="col-12 text-center">
                <p class="text-muted">No marketplace services available at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="<?php echo URLROOT; ?>/services" class="btn btn-primary btn-lg">Browse Full Marketplace</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <div class="p-5 bg-light rounded-3">
            <h2 class="fw-bold mb-4">Ready to Boost Your SEO?</h2>
            <p class="lead text-muted mb-4">Join thousands of website owners who have improved their rankings with webGoup.</p>
            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-primary btn-lg">Sign Up for Free</a>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layouts/footer.php'; ?>