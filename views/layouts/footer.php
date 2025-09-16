</div> <!-- Closing container -->

<footer class="footer bg-light text-muted border-top mt-auto py-3">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-4 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">webGoup</h5>
                <p>
                    Our mission is to empower website owners and businesses by providing a decentralized platform for SEO services. We believe in the power of the collective to elevate every member's online presence.
                </p>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Links</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/about" class="text-muted">About Us</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/terms" class="text-muted">Terms</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/services" class="text-muted">Marketplace</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/official" class="text-muted">Our Services</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Connect</h5>
                <div>
                    <a href="#" class="btn btn-outline-secondary btn-floating m-1" role="button"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-floating m-1" role="button"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-floating m-1" role="button"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-floating m-1" role="button"><i class="fab fa-github"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase text-dark">Newsletter</h5>
                <p>Stay up to date with our latest news and offers.</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Enter your email" aria-label="Enter your email">
                    <button class="btn btn-primary" type="button">Subscribe</button>
                </div>
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
});
</script>

</body>
</html>
