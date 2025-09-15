<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold">About webGoup</h1>
                <p class="lead text-muted">Elevating the web, together.</p>
            </div>

            <div class="mb-5">
                <h2 class="fw-bold">Our Mission</h2>
                <p>At webGoup, our mission is to democratize the SEO industry. We believe that every website, regardless of its size or budget, deserves to be visible. We are the "webGroup" – a collective of passionate SEO experts, developers, and entrepreneurs – and we are here to help your "web go up".</p>
                <p>We've created a decentralized <strong>two-sided marketplace</strong> where website owners can both buy and sell SEO services. Our platform is built on transparency, trust, and the shared goal of achieving exceptional results for everyone.</p>
            </div>

            <div class="mb-5">
                <h2 class="fw-bold">Our Two-Sided Marketplace</h2>
                <p>webGoup empowers both buyers and sellers in the SEO ecosystem:</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                                <h5 class="card-title">For Buyers</h5>
                                <p class="card-text">Access a curated marketplace of quality backlinks from reputable websites. Boost your SEO rankings with safe, effective link building strategies.</p>
                                <ul class="list-unstyled text-start mt-3">
                                    <li><i class="fas fa-check text-success me-2"></i>Vetted service providers</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Secure payment escrow</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Quality guarantee</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Transparent pricing</li>
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
                                <h5 class="card-title">For Sellers</h5>
                                <p class="card-text">Monetize your website by selling backlink placements. Connect with buyers worldwide and earn recurring revenue from your digital assets.</p>
                                <ul class="list-unstyled text-start mt-3">
                                    <li><i class="fas fa-check text-primary me-2"></i>Set your own prices</li>
                                    <li><i class="fas fa-check text-primary me-2"></i>Earn 70% revenue</li>
                                    <li><i class="fas fa-check text-primary me-2"></i>Secure payment system</li>
                                    <li><i class="fas fa-check text-primary me-2"></i>Build your reputation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <h2 class="fw-bold">Transparent Fee Structure</h2>
                <p>We believe in complete transparency. Here's how our platform fees work:</p>
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="text-primary">Service Providers Earn</h5>
                                <p class="display-4 fw-bold text-success">70%</p>
                                <p>Of the total service price</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary">Platform Fee</h5>
                                <p class="display-4 fw-bold text-info">30%</p>
                                <p>Covers payment processing, support, and platform maintenance</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <strong>Example:</strong> For a $100 service, the provider earns $70 and webGoup retains $30.
                </div>
            </div>

            <div class="mb-5">
                <h2 class="fw-bold">The Dual Meaning of webGoup</h2>
                <div class="accordion" id="accordionPanelsStayOpenExample">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                        <strong>webGroup: A Community of Experts</strong>
                      </button>
                    </h2>
                    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                      <div class="accordion-body">
                        We are a "webGroup," a vibrant community of SEO professionals, content creators, and digital strategists. By joining webGoup, you become part of a powerful network dedicated to sharing knowledge, best practices, and opportunities. We believe that by working together, we can achieve more than we ever could alone.
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                        <strong>web go up: Elevating Your Online Presence</strong>
                      </button>
                    </h2>
                    <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                      <div class="accordion-body">
                        Our ultimate goal is to make your "web go up." We provide the tools, resources, and connections you need to improve your website's ranking, increase traffic, and grow your business. Whether you're looking to buy or sell SEO services, webGoup is your partner in success.
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <div class="text-center">
                <a href="<?php echo URLROOT; ?>/services" class="btn btn-primary btn-lg">Explore the Marketplace</a>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>