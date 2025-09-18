<?php require APPROOT . '/views/layouts/header.php'; ?>

<!-- Hero Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Professional Services</h1>
                <p class="lead mb-4">Premium SEO services from vetted providers with webGoup quality guarantees and enhanced refund protection.</p>
                <div class="d-flex gap-3">
                    <span class="badge bg-light text-primary fs-6"><i class="fas fa-check-circle me-1"></i> Vetted Providers</span>
                    <span class="badge bg-light text-primary fs-6"><i class="fas fa-shield-alt me-1"></i> Quality Guaranteed</span>
                    <span class="badge bg-light text-primary fs-6"><i class="fas fa-money-bill-wave me-1"></i> Enhanced Refund Policy</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What Makes Professional Services Different -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-info border-0 bg-primary bg-opacity-10">
                    <h3 class="alert-heading"><i class="fas fa-star me-2"></i>What Makes Professional Services Special?</h3>
                    <p class="mb-0">Professional Services are carefully selected from our marketplace providers who demonstrate exceptional quality, reliability, and compliance with SEO best practices. When you choose Professional Services, webGoup provides enhanced protection and guarantees that aren't available in the regular marketplace.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-circle bg-success text-white mb-3">
                            <i class="fas fa-award fa-2x"></i>
                        </div>
                        <h5>Vetted Providers</h5>
                        <p>All Professional Service providers undergo rigorous screening including portfolio review, compliance checks, and performance verification.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-circle bg-primary text-white mb-3">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                        <h5>Enhanced Guarantees</h5>
                        <p>Professional Services come with specific, measurable deliverables and enhanced refund protections beyond standard marketplace offerings.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="icon-circle bg-warning text-white mb-3">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                        <h5>Priority Support</h5>
                        <p>Get priority customer support and dedicated account management for all Professional Services orders.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Real Example: DR40 Guarantee -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Real Example: Our DR40 Guarantee</h2>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-circle bg-success text-white me-3">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-1">Domain Authority Boost Service</h4>
                                <p class="text-muted mb-0">Professional Service with DR40 Guarantee</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-check text-success me-2"></i>What We Guarantee</h5>
                                <ul class="list-unstyled">
                                    <li><strong>✓ Minimum DR 40:</strong> We guarantee your website will achieve Domain Rating 40 or higher</li>
                                    <li><strong>✓ Full Refund:</strong> If DR40 isn't achieved within the specified timeframe, you get a complete refund</li>
                                    <li><strong>✓ White Hat Only:</strong> All methods comply with Google's guidelines</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-times text-danger me-2"></i>What We Don't Promise</h5>
                                <ul class="list-unstyled">
                                    <li><strong>✗ Search Rankings:</strong> We don't guarantee specific positions in search results</li>
                                    <li><strong>✗ Traffic Numbers:</strong> No promises about exact visitor counts</li>
                                    <li><strong>✗ Indexation Rates:</strong> We don't guarantee how many pages get indexed</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <h6><i class="fas fa-lightbulb me-2"></i>Why This Approach?</h6>
                            <p class="mb-0">We guarantee what we can control (quality backlinks, DR improvement) but don't make promises about search engine algorithms, which we cannot influence. This is honest, transparent, and sustainable SEO.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Professional Services List -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Available Professional Services</h2>

        <?php if(empty($data['officialServices'])): ?>
            <div class="text-center py-5">
                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                <h4>Professional Services Coming Soon</h4>
                <p class="text-muted">We're currently vetting and selecting the best providers for our Professional Services program. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach($data['officialServices'] as $service): ?>
                <div class="col-lg-6">
                    <div class="card h-100 border-primary shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title"><?php echo htmlspecialchars($service->title); ?></h5>
                                <span class="badge bg-success">Professional</span>
                            </div>
                            <p class="card-text"><?php echo $service->description; ?></p>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h5 mb-0 text-primary">$<?php echo htmlspecialchars($service->price); ?></div>
                                        <small class="text-muted">Price</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h5 mb-0 text-primary"><?php echo htmlspecialchars($service->delivery_time); ?></div>
                                        <small class="text-muted">Days Delivery</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-<?php echo ($service->link_type == 'follow') ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($service->link_type); ?>
                                </span>
                                <span class="badge bg-info">
                                    <?php echo ucfirst($service->service_category); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex gap-2">
                                <a href="<?php echo URLROOT; ?>/services/show/<?php echo $service->serviceId; ?>" class="btn btn-primary flex-fill">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="<?php echo URLROOT; ?>/services/order/<?php echo $service->serviceId; ?>" class="btn btn-success flex-fill">
                                    <i class="fas fa-shopping-cart me-1"></i>Order Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Professional Services vs Marketplace -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Professional Services vs Marketplace</h2>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-star me-2"></i>Professional Services</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Vetted and verified providers</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Enhanced refund guarantees</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Specific, measurable deliverables</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority customer support</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Quality assurance by webGoup</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Dispute resolution assistance</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0"><i class="fas fa-store me-2"></i>Regular Marketplace</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>Open provider network</li>
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>Standard refund policy</li>
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>General service descriptions</li>
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>Standard support channels</li>
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>Self-service quality checks</li>
                            <li class="mb-2"><i class="fas fa-times text-secondary me-2"></i>Direct provider communication</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require APPROOT . '/views/layouts/footer.php'; ?>