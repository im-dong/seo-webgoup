<?php require APPROOT . '/views/layouts/header.php'; ?>

<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/users/dashboard" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back to Dashboard</a>
    <h2>Order Details #<?php echo htmlspecialchars($data['order']->id); ?></h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Service Snapshot</div>
                <div class="card-body">
                    <?php if($data['snapshot']): ?>
                        <!-- Show snapshot data -->
                        <?php if(!empty($data['snapshot']->thumbnail_url)): ?>
                            <div class="mb-3">
                                <img src="<?php echo htmlspecialchars($data['snapshot']->thumbnail_url); ?>"
                                     class="img-fluid rounded shadow-sm"
                                     alt="<?php echo htmlspecialchars($data['snapshot']->title ?? 'Service Image'); ?>"
                                     style="max-height: 250px; object-fit: cover;"
                                     onerror="this.style.display='none'; if(this.parentElement.nextElementSibling) this.parentElement.nextElementSibling.style.display='block';">
                            </div>
                            <div class="alert alert-info text-center" style="display:none;">
                                <i class="fas fa-image fa-3x text-muted mb-2"></i><br>
                                <small>Service image not available</small>
                            </div>
                        <?php endif; ?>

                        <h4 class="card-title"><?php echo htmlspecialchars($data['snapshot']->title ?? 'Service Title Not Available'); ?></h4>
                        <div class="card-text"><?php echo $data['snapshot']->description ?? 'Service description not available.'; ?></div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>Price:</b> $<?php echo number_format($data['snapshot']->price ?? 0, 2); ?></li>
                            <li class="list-group-item"><b>Delivery Time:</b> <?php echo htmlspecialchars($data['snapshot']->delivery_time ?? 'N/A'); ?> days</li>
                            <li class="list-group-item"><b>Link Type:</b> <?php echo htmlspecialchars($data['snapshot']->link_type ?? 'N/A'); ?></li>
                            <li class="list-group-item"><b>Link Duration:</b> <?php echo htmlspecialchars($data['snapshot']->duration ?? 'N/A'); ?> days</li>
                        </ul>
                    <?php elseif($data['service']): ?>
                        <!-- Show current service data when no snapshot -->
                        <div class="mb-3">
                            <?php if(!empty($data['service']->thumbnail_url)): ?>
                                <img src="<?php echo htmlspecialchars(URLROOT . '/' . $data['service']->thumbnail_url); ?>"
                                     class="img-fluid rounded shadow-sm"
                                     alt="<?php echo htmlspecialchars($data['service']->title ?? 'Service Image'); ?>"
                                     style="max-height: 250px; object-fit: cover;"
                                     onerror="this.style.display='none'; if(this.parentElement.nextElementSibling) this.parentElement.nextElementSibling.style.display='block';">
                            <?php endif; ?>
                        </div>
                        <div class="alert alert-info text-center" style="display:none;">
                            <i class="fas fa-image fa-3x text-muted mb-2"></i><br>
                            <small>Service image not available</small>
                        </div>

                        <h4 class="card-title"><?php echo htmlspecialchars($data['service']->title ?? 'Service Title Not Available'); ?></h4>
                        <div class="card-text">
                            <?php
                            if(isset($data['service']->role) && $data['service']->role == 'admin') {
                                echo $data['service']->description; // Admin posts show full HTML
                            } else {
                                echo nl2br(htmlspecialchars(strip_tags($data['service']->description ?? 'Service description not available.'))); // User posts show plain text
                            }
                            ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><b>Price:</b> $<?php echo number_format($data['service']->price ?? 0, 2); ?></li>
                            <li class="list-group-item"><b>Delivery Time:</b> <?php echo htmlspecialchars($data['service']->delivery_time ?? 'N/A'); ?> days</li>
                            <li class="list-group-item"><b>Link Type:</b> <?php echo htmlspecialchars($data['service']->link_type ?? 'N/A'); ?></li>
                            <li class="list-group-item"><b>Link Duration:</b> <?php echo htmlspecialchars($data['service']->duration ?? 'N/A'); ?> days</li>
                        </ul>
                        <div class="alert alert-info mt-3">
                            <small><i class="fas fa-info-circle"></i> Showing current service details (order snapshot not available)</small>
                        </div>
                    <?php else: ?>
                        <!-- No snapshot and no service data -->
                        <div class="alert alert-warning">
                            <h4 class="card-title">Service Details Not Available</h4>
                            <p class="card-text">The service details for this order are not available. The service may have been deleted.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Participants</div>
                <div class="card-body">
                    <p>
                        <strong>Seller:</strong>
                        <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['seller']->id; ?>">
                            <?php echo htmlspecialchars($data['seller']->username); ?>
                        </a>
                    </p>
                    <p>
                        <strong>Buyer:</strong>
                        <a href="<?php echo URLROOT; ?>/users/profile/<?php echo $data['buyer']->id; ?>">
                            <?php echo htmlspecialchars($data['buyer']->username); ?>
                        </a>
                        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                            <br><small class="text-muted">
                                <i class="fas fa-envelope"></i>
                                Email: <?php echo htmlspecialchars($data['buyer']->email ?? 'Not available'); ?>
                            </small>
                        <?php endif; ?>
                    </p>
                    <?php if ($data['conversation']): ?>
                        <hr>
                        <div class="d-grid">
                             <a href="<?php echo URLROOT; ?>/conversations/show/<?php echo $data['conversation']->id; ?>" class="btn btn-primary">
                                 <i class="fa fa-comments"></i> View Conversation
                             </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$data['order']->paid_at && $data['order']->buyer_id == $_SESSION['user_id']): ?>
                <div class="card mb-4">
                    <div class="card-header">Choose Payment Method</div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h5 class="text-success"><i class="fas fa-credit-card"></i> Secure Payment Options</h5>
                            <p class="text-muted small">Choose your preferred payment method - all options are 100% secure</p>
                        </div>

                        <!-- PayPal Smart Buttons -->
                        <div class="paypal-buttons-container">
                            <!-- 支付方式说明 -->
                            <div class="payment-methods-info mb-3">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <i class="fab fa-cc-visa fa-2x text-primary"></i>
                                    </div>
                                    <div class="col-3">
                                        <i class="fab fa-cc-mastercard fa-2x text-danger"></i>
                                    </div>
                                    <div class="col-3">
                                        <i class="fab fa-cc-amex fa-2x text-info"></i>
                                    </div>
                                    <div class="col-3">
                                        <i class="fab fa-paypal fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-lock"></i> 256-bit SSL encryption<br>
                                    <i class="fas fa-shield-alt"></i> PayPal buyer protection<br>
                                    <i class="fas fa-globe"></i> Global payment acceptance
                                </p>
                            </div>

                            <!-- PayPal按钮容器 -->
                            <div class="d-grid gap-2 mb-3">
                                <!-- 信用卡支付按钮 -->
                                <div id="card-button-container" class="mb-2">
                                    <div class="text-muted small mb-2">Pay with Credit/Debit Card</div>
                                    <div id="card-button"></div>
                                </div>
                                <hr>
                                <!-- PayPal账户支付按钮 -->
                                <div id="paypal-button-container" class="mb-2">
                                    <div class="text-muted small mb-2">Pay with PayPal Account</div>
                                    <div id="paypal-button"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <strong>Total Amount:</strong> $<?php echo number_format($data['order']->amount, 2); ?> USD<br>
                                All payments are processed securely by PayPal
                            </small>
                        </div>
                    </div>
                </div>

                <!-- PayPal JavaScript -->
                <script>
                    // 设置全局变量供JS使用
                    window.URLROOT = '<?php echo URLROOT; ?>';
                    window.PAYPAL_CLIENT_ID = '<?php echo PAYPAL_CLIENT_ID; ?>';
                    window.PAYPAL_SANDBOX = <?php echo PAYPAL_SANDBOX ? 'true' : 'false'; ?>;

                    console.log('PayPal Config:');
                    console.log('URLROOT:', window.URLROOT);
                    console.log('CLIENT_ID:', window.PAYPAL_CLIENT_ID);
                    console.log('SANDBOX:', window.PAYPAL_SANDBOX);
                </script>

                <!-- 直接加载PayPal SDK -->
                <script src="https://<?php echo PAYPAL_SANDBOX ? 'www.sandbox' : 'www'; ?>.paypal.com/sdk/js?client-id=<?php echo PAYPAL_CLIENT_ID; ?>&currency=USD"></script>

                <script>
                    // 渲染分离的支付按钮
                    document.addEventListener('DOMContentLoaded', function() {
                        console.log('DOM Loaded, paypal object:', typeof window.paypal);

                        if (window.paypal) {
                            // 信用卡支付按钮 - 强制访客结账
                            paypal.Buttons({
                                fundingSource: paypal.FUNDING.CARD,
                                style: {
                                    label: 'pay',
                                    color: 'black',
                                    shape: 'rect',
                                    height: 40
                                },
                                createOrder: function(data, actions) {
                                    console.log('Creating credit card order for existing order: <?php echo $data['order']->id; ?>');
                                    return actions.order.create({
                                        intent: 'CAPTURE',
                                        purchase_units: [{
                                            amount: {
                                                value: '<?php echo number_format($data['order']->amount, 2); ?>'
                                            },
                                            description: 'Order #<?php echo $data['order']->id; ?> - Service Purchase',
                                            custom_id: 'wg_<?php echo $data['order']->id; ?>',
                                            soft_descriptor: 'Digital Service'
                                        }],
                                        application_context: {
                                            brand_name: '<?php echo SITENAME; ?>',
                                            locale: 'en-US',
                                            landing_page: 'BILLING',
                                            shipping_preference: 'NO_SHIPPING',
                                            user_action: 'PAY_NOW',
                                            payment_method_preference: 'IMMEDIATE_PAYMENT_REQUIRED',
                                            store_invoicing_preference: 'NO_STORE',
                                            user_experience_flow: 'DIRECT',
                                            no_shipping: true,
                                            allow_note: false
                                        }
                                    });
                                },
                                onApprove: function(data, actions) {
                                    console.log('Card payment approved:', data);

                                    // 直接捕获支付获取交易详情
                                    actions.order.capture().then(function(details) {
                                        console.log('Payment captured:', details);

                                        // 获取交易ID
                                        const transactionId = details.purchase_units[0].payments.captures[0].id;

                                        // 直接使用PayPal的捕获数据更新订单状态
                                        fetch(window.URLROOT + '/orders/updateFromPayPal/<?php echo $data['order']->id; ?>', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                paypalOrderId: data.orderID,
                                                transactionId: transactionId,
                                                captureData: details
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(result => {
                                            if (result.status === 'success') {
                                                alert('Payment successful! Transaction ID: ' + transactionId);
                                                window.location.reload();
                                            } else {
                                                alert('Payment successful! Transaction ID: ' + transactionId);
                                                // 即使更新失败也刷新页面，因为支付成功了
                                                window.location.reload();
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Order update error:', error);
                                            alert('Payment successful! Transaction ID: ' + transactionId);
                                            // 即使API调用失败，也显示成功消息并刷新页面
                                            window.location.reload();
                                        });
                                    });
                                },
                                onError: function(err) {
                                    console.error('Card button error:', err);
                                    alert('Payment error: ' + err.message);
                                }
                            }).render('#card-button').catch(function(err) {
                                console.error('Failed to render card button:', err);
                                document.getElementById('card-button').innerHTML = '<div class="alert alert-danger">Card payment failed to load</div>';
                            });

                            // PayPal账户支付按钮
                            paypal.Buttons({
                                fundingSource: paypal.FUNDING.PAYPAL,
                                style: {
                                    label: 'pay',
                                    color: 'blue',
                                    shape: 'rect',
                                    height: 40
                                },
                                createOrder: function(data, actions) {
                                    console.log('Creating PayPal order for existing order: <?php echo $data['order']->id; ?>');
                                    return actions.order.create({
                                        intent: 'CAPTURE',
                                        purchase_units: [{
                                            amount: {
                                                value: '<?php echo number_format($data['order']->amount, 2); ?>'
                                            },
                                            description: 'Order #<?php echo $data['order']->id; ?> - Service Purchase',
                                            custom_id: 'wg_<?php echo $data['order']->id; ?>',
                                            soft_descriptor: 'Digital Service'
                                        }],
                                        application_context: {
                                            brand_name: '<?php echo SITENAME; ?>',
                                            locale: 'en-US',
                                            landing_page: 'BILLING',
                                            shipping_preference: 'NO_SHIPPING',
                                            user_action: 'PAY_NOW'
                                        }
                                    });
                                },
                                onApprove: function(data, actions) {
                                    console.log('PayPal payment approved:', data);

                                    // 直接捕获支付获取交易详情
                                    actions.order.capture().then(function(details) {
                                        console.log('PayPal Payment captured:', details);

                                        // 获取交易ID
                                        const transactionId = details.purchase_units[0].payments.captures[0].id;

                                        // 直接使用PayPal的捕获数据更新订单状态
                                        fetch(window.URLROOT + '/orders/updateFromPayPal/<?php echo $data['order']->id; ?>', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                paypalOrderId: data.orderID,
                                                transactionId: transactionId,
                                                captureData: details
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(result => {
                                            if (result.status === 'success') {
                                                alert('Payment successful! Transaction ID: ' + transactionId);
                                                window.location.reload();
                                            } else {
                                                alert('Payment successful! Transaction ID: ' + transactionId);
                                                // 即使更新失败也刷新页面，因为支付成功了
                                                window.location.reload();
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Order update error:', error);
                                            alert('Payment successful! Transaction ID: ' + transactionId);
                                            // 即使API调用失败，也显示成功消息并刷新页面
                                            window.location.reload();
                                        });
                                    });
                                },
                                onError: function(err) {
                                    console.error('PayPal button error:', err);
                                    alert('PayPal error: ' + err.message);
                                }
                            }).render('#paypal-button').catch(function(err) {
                                console.error('Failed to render PayPal button:', err);
                                document.getElementById('paypal-button').innerHTML = '<div class="alert alert-danger">PayPal button failed to load</div>';
                            });

                        } else {
                            console.error('PayPal SDK not loaded!');
                            document.getElementById('card-button').innerHTML = '<div class="alert alert-danger">Payment system failed to load</div>';
                            document.getElementById('paypal-button').innerHTML = '<div class="alert alert-danger">Payment system failed to load</div>';
                        }
                    });
                </script>
            <?php endif; ?>
            <div class="card">
                <div class="card-header">Order Timeline</div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php if($data['conversation']): ?>
                            <li><strong>Inquiry Started:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['conversation']->created_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->paid_at): ?>
                            <li><strong>Paid:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->paid_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->completed_at): ?>
                            <li><strong>Completed by Seller:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->completed_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->confirmed_at): ?>
                            <li><strong>Confirmed by Buyer:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->confirmed_at)); ?></li>
                        <?php endif; ?>
                        <?php if($data['order']->funds_release_date): ?>
                            <li><strong>Funds Release Est.:</strong><br> <?php echo date('Y-m-d H:i', strtotime($data['order']->funds_release_date)); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/footer.php'; ?>