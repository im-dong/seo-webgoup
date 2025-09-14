</div> <!-- Closing container -->

<footer class="footer bg-dark text-white mt-5">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">webGoup</h5>
                <p>
                    Our mission is to empower website owners and businesses by providing a decentralized platform for SEO services. We believe in the power of the collective, the "webGroup", to elevate every member's online presence, helping your "web go up".
                </p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Links</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="<?php echo URLROOT; ?>/pages/about" class="text-white">About Us</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/services" class="text-white">Marketplace</a>
                    </li>
                    <li>
                        <a href="<?php echo URLROOT; ?>/official" class="text-white">Our Services</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <h5 class="text-uppercase">Connect</h5>
                <ul class="list-unstyled mb-0">
                    <li>
                        <a href="#!" class="text-white"><i class="fab fa-facebook-f"></i> Facebook</a>
                    </li>
                    <li>
                        <a href="#!" class="text-white"><i class="fab fa-twitter"></i> Twitter</a>
                    </li>
                    <li>
                        <a href="#!" class="text-white"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2025 webGoup:
        <a class="text-white" href="https://www.webgoup.com">webGoup.com</a>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
