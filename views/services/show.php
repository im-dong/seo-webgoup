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
            <p><?php echo nl2br(htmlspecialchars($data['service']->description)); ?></p>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">$<?php echo htmlspecialchars($data['service']->price); ?></h4>
                    <hr>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Delivery Time:</strong> <?php echo htmlspecialchars($data['service']->delivery_time); ?> days</li>
                        <li class="mb-2"><strong>Link Duration:</strong> <?php echo htmlspecialchars($data['service']->duration); ?> days</li>
                        <li class="mb-2"><strong>Link Type:</strong> <span class="badge bg-<?php echo ($data['service']->link_type == 'follow') ? 'primary' : 'secondary'; ?>"><?php echo htmlspecialchars($data['service']->link_type); ?></span></li>
                        <li class="mb-2"><strong>New Window:</strong> <?php echo ($data['service']->is_new_window) ? 'Yes' : 'No'; ?></li>
                        <li class="mb-2"><strong>Adult Content:</strong> <?php echo ($data['service']->is_adult_allowed) ? 'Allowed' : 'Not Allowed'; ?></li>
                    </ul>
                    <hr>
                    <div class="d-grid">
                        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $data['service']->userId && $data['order_id_for_chat']): ?>
                            <a href="<?php echo URLROOT; ?>/conversations/start/<?php echo $data['order_id_for_chat']; ?>" class="btn btn-primary btn-lg mb-2">Message Seller</a>
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