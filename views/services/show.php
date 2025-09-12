<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo htmlspecialchars($data['service']->title); ?></h2>
            <hr>
            <p><strong>Sold by:</strong> <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['service']->userId; ?>"><?php echo htmlspecialchars($data['service']->username); ?></a></p>
            <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($data['service']->site_url); ?>" target="_blank"><?php echo htmlspecialchars($data['service']->site_url); ?></a></p>
            <hr>
            <h4>Service Description</h4>
            <p class="service-description"><?php echo nl2br(htmlspecialchars($data['service']->description)); ?></p>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center fw-bold text-primary">$<?php echo htmlspecialchars($data['service']->price); ?></h2>
                    <hr>
                    <p class="text-center"><strong>Sold by:</strong> <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['service']->userId; ?>"><?php echo htmlspecialchars($data['service']->username); ?></a></p>
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
                        <?php endif; ?>
                        <div id="paypal-button-container"></div>
                    <p id="payment-message" class="text-center"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PayPal JS SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script> <!-- 请将 client-id=sb 替换为您的真实ID -->

<script>
    paypal.Buttons({
        // 创建订单
        createOrder: function(data, actions) {
            return fetch('<?php echo URLROOT; ?>/orders/create/<?php echo $data['service']->serviceId; ?>', {
                method: 'post'
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                return orderData.orderID; // 返回我们自己数据库的订单ID
            });
        },

        // 捕获支付
        onApprove: function(data, actions) {
            return fetch('<?php echo URLROOT; ?>/orders/capture/' + data.orderID, {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ orderID: data.orderID })
            }).then(function(res) {
                return res.json();
            }).then(function(orderData) {
                var messageContainer = document.getElementById('payment-message');
                if (orderData.status === 'success') {
                    messageContainer.className = 'alert alert-success';
                    messageContainer.innerText = 'Payment successful! Redirecting...';
                    setTimeout(() => { window.location.href = '<?php echo URLROOT; ?>/users/dashboard'; }, 3000); // 稍后创建dashboard
                } else {
                    messageContainer.className = 'alert alert-danger';
                    messageContainer.innerText = 'Payment failed. Please try again.';
                }
            });
        }
    }).render('#paypal-button-container');
</script>

<?php require APPROOT . '/views/layouts/footer.php'; ?>