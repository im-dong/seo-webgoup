</div> <!-- Closing container -->

<footer class="footer bg-light text-muted border-top mt-auto py-3">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-4 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">webGoup</h5>
                <p>
                    Connecting businesses with SEO experts through our trusted marketplace platform.
                </p>
                <p class="mb-0">
                    <i class="fas fa-envelope me-2"></i>
                    <a href="mailto:info@webgoup.com" class="text-muted text-decoration-none">info@webgoup.com</a>
                </p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Legal & Compliance</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/terms" class="text-muted">
                            <i class="fas fa-gavel me-1"></i>Terms of Service
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/seoGuidelines" class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>SEO Guidelines
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/privacy" class="text-muted">
                            <i class="fas fa-lock me-1"></i>Privacy Policy
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Platform</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/about" class="text-muted">About Us</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/services" class="text-muted">Marketplace</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/official" class="text-muted">Professional Services</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Newsletter</h5>
                <p>Latest news and offers.</p>
                <div class="input-group mb-3">
                    <input type="email" id="newsletter-email" class="form-control" placeholder="Enter your email" aria-label="Enter your email">
                    <button class="btn btn-primary" type="button" id="newsletter-subscribe">Subscribe</button>
                </div>
                <div id="newsletter-message" class="small mt-2"></div>
            </div>
        </div>
    </div>
    <div class="text-center p-3 border-top">
        © 2025 Copyright:
        <a class="text-dark" href="https://www.webgoup.com">webGoup.com</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="<?php echo URLROOT; ?>/assets/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="<?php echo URLROOT; ?>/assets/js/jquery-3.7.1.min.js"></script>
<!-- Custom JS -->
<script src="<?php echo URLROOT; ?>/js/main.js?v=1.1"></script>

<!-- Toast Initializer -->
<script>
    var toastEl = document.getElementById('flash-toast');
    if (toastEl) {
        var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }
</script>

<script>
$(document).ready(function() {
    function fetchUnreadCount() {
        $.ajax({
            url: '<?php echo URLROOT; ?>/conversations/unreadCount',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const unreadCount = response.unread_count;
                const messagesLink = $('#messages-link'); // Add an ID to the messages link
                if (unreadCount > 0) {
                    messagesLink.html(`Messages <span class="badge bg-danger">${unreadCount}</span>`);
                } else {
                    messagesLink.html('Messages');
                }
            }
        });
    }

    // Fetch count on page load
    fetchUnreadCount();

    // Fetch count every 30 seconds
    setInterval(fetchUnreadCount, 30000);

    // Newsletter subscription
    $('#newsletter-subscribe').on('click', function() {
        const email = $('#newsletter-email').val().trim();
        const messageDiv = $('#newsletter-message');
        const button = $(this);

        if (!email) {
            showMessage('Please enter your email address.', 'danger');
            return;
        }

        if (!isValidEmail(email)) {
            showMessage('Please enter a valid email address.', 'danger');
            return;
        }

        // Disable button and show loading
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Subscribing...');
        showMessage('', '');

        $.ajax({
            url: '<?php echo URLROOT; ?>/newsletter/subscribe',
            type: 'POST',
            data: {
                email: email,
                csrf_token: '<?php echo generateCSRFToken(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(response.message, 'success');
                    $('#newsletter-email').val('');
                } else {
                    showMessage(response.message, 'warning');
                }
            },
            error: function() {
                showMessage('An error occurred. Please try again.', 'danger');
            },
            complete: function() {
                button.prop('disabled', false).html('Subscribe');
            }
        });
    });

    // Enter key support for email input
    $('#newsletter-email').on('keypress', function(e) {
        if (e.which === 13) {
            $('#newsletter-subscribe').click();
        }
    });

    function showMessage(message, type) {
        const messageDiv = $('#newsletter-message');
        if (message) {
            messageDiv.html('<div class="alert alert-' + type + ' alert-sm py-1 mb-0">' + message + '</div>');
        } else {
            messageDiv.html('');
        }
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>

</body>
</html>
