<?php require APPROOT . '/views/layouts/header.php'; ?>

<?php flash('service_message'); ?>

<!-- Hero Section -->
<section class="hero-section-new">
    <div class="container text-center">
        <div class="hero-content">
            <h1 class="display-3 fw-bold mb-4"><?php echo $data['title']; ?></h1>
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

<!-- How webGoup Works -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How webGoup Works</h2>
            <p class="text-muted">Your two-sided marketplace for SEO services</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h5 class="card-title">Buy SEO Services</h5>
                        <p class="card-text">Need quality backlinks? Browse our marketplace of vetted providers and purchase SEO services to boost your website rankings.</p>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Browse verified services</li>
                            <li><i class="fas fa-check text-success me-2"></i>Secure payment escrow</li>
                            <li><i class="fas fa-check text-success me-2"></i>Quality guarantee</li>
                            <li><i class="fas fa-check text-success me-2"></i>30-day delivery timeline</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <h5 class="card-title">Sell SEO Services</h5>
                        <p class="card-text">Have quality websites? Monetize your digital assets by selling backlink placements to our global community.</p>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-primary me-2"></i>Set your own prices</li>
                            <li><i class="fas fa-check text-primary me-2"></i>Earn 70% revenue</li>
                            <li><i class="fas fa-check text-primary me-2"></i>Secure payments</li>
                            <li><i class="fas fa-check text-primary me-2"></i>Build reputation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <div class="alert alert-info d-inline-block">
                <strong>Complete Freedom:</strong> Participate as buyer, seller, or both - webGoup empowers you to choose your role in the SEO marketplace
            </div>
        </div>
    </div>
</section>

<!-- Our Services -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="text-muted">Premium SEO services offered directly by webGoup</p>
        </div>

        <div class="row g-4" id="officialServicesContainer">
            <?php
            // Load official services directly via PHP
            $serviceModel = new Service();
            $officialServices = $serviceModel->getOfficialServices();

            if(!empty($officialServices)):
                foreach(array_slice($officialServices, 0, 4) as $service):
            ?>
                <div class="col-md-3">
                    <div class="card service-card h-100 border-primary">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                            <p class="text-primary fw-bold mb-2">$<?php echo htmlspecialchars($service->price); ?></p>
                            <p class="card-text text-muted"><?php echo substr(strip_tags($service->description), 0, 80); ?>...</p>
                            <div class="mt-3">
                                <span class="badge bg-warning text-dark">Official</span>
                                <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($service->link_type); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($service->delivery_time); ?> days
                            </small>
                        </div>
                        <div class="card-body pt-0">
                            <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-primary btn-sm w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                endforeach;
            else:
            ?>
                <div class="col-12 text-center">
                    <p class="text-muted">No official services available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?php echo URLROOT; ?>/official" class="btn btn-primary btn-lg">
                <i class="fas fa-star me-2"></i>View All Official Services
            </a>
        </div>
    </div>
</section>

<!-- Community Marketplace -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Community Marketplace</h3>
                <p class="text-muted mb-0">Services from our trusted community providers</p>
            </div>
            <a href="<?php echo URLROOT; ?>/services" class="btn btn-outline-primary btn-sm">
                Browse All <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-3" id="marketplaceContainer">
            <?php
            // Load marketplace services directly via PHP
            $marketplaceServices = $serviceModel->getServices();

            if(!empty($marketplaceServices)):
                foreach(array_slice($marketplaceServices, 0, 4) as $service):
            ?>
                <div class="col-md-3">
                    <div class="card service-card h-100">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars($service->title); ?></h6>
                            <p class="text-primary fw-bold mb-2">$<?php echo htmlspecialchars($service->price); ?></p>
                            <small class="text-muted">by <?php echo htmlspecialchars($service->username); ?></small>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($service->delivery_time); ?>d
                                </small>
                                <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($service->link_type); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-outline-primary btn-sm w-100">
                                View Details
                            </a>
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
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Ready to Boost Your SEO?</h2>
        <p class="lead mb-4">Join thousands of website owners who have improved their rankings with webGoup</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="<?php echo URLROOT; ?>/users/register" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Get Started Free
            </a>
            <a href="<?php echo URLROOT; ?>/services" class="btn btn-outline-light btn-lg">
                <i class="fas fa-search me-2"></i>Browse Services
            </a>
        </div>
    </div>
</section>

<style>
@keyframes gradientAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.hero-section-new {
    color: #fff;
    background: linear-gradient(-45deg, #4158D0, #C850C0, #FFCC70, #23D5AB);
    background-size: 400% 400%;
    animation: gradientAnimation 15s ease infinite;
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.hero-section-new::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
    opacity: 0.5;
}

.hero-content {
    position: relative;
    z-index: 1;
    animation: fadeIn 1s ease-out;
}

.hero-content .lead {
    max-width: 700px;
    font-weight: 300;
    color: rgba(255, 255, 255, 0.9);
}

.btn-primary-new, .btn-secondary-new {
    border-radius: 50px;
    padding: 14px 36px;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.btn-primary-new {
    background-color: #fff;
    color: #333;
}

.btn-primary-new:hover {
    background-color: #f0f0f0;
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    color: #000;
}

.btn-secondary-new {
    background-color: rgba(255, 255, 255, 0.15);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.btn-secondary-new:hover {
    background-color: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    color: #fff;
}

.hero-stats {
    margin-top: 60px;
}

.stat-item {
    padding: 10px 20px;
    border-left: 2px solid rgba(255, 255, 255, 0.3);
}

.stat-item:first-child {
    border-left: none;
}

.stat-item .h2 {
    color: #fff;
}

.text-white-75 {
    color: rgba(255,255,255,0.75) !important;
}

/* Keep other styles for other sections */
.service-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.service-card .card-title {
    font-weight: 600;
    line-height: 1.4;
}

.service-card.border-primary {
    border: 2px solid #007bff;
}

.service-card .badge {
    font-size: 0.75em;
    padding: 0.4em 0.6em;
}

/* Official Services Cards */
.border-primary .card-title {
    min-height: 48px;
    font-size: 1.1rem;
}

/* Community Marketplace Cards */
.community-card .card-title {
    font-size: 0.9rem;
    min-height: 40px;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .hero-section-new {
        padding: 80px 0;
    }

    .hero-section-new h1 {
        font-size: 2.5rem !important;
    }
    
    .stat-item {
        border-left: none;
        text-align: center;
    }
}
</style>

<?php require APPROOT . '/views/layouts/footer.php'; ?>
