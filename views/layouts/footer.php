</div> <!-- Closing container -->

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

</body>
</html>
