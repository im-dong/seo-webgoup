<?php
// PayPal支付测试页面
require_once 'app/bootstrap.php';

// 模拟创建一个测试订单
$test_order = [
    'id' => 'TEST_' . time(),
    'amount' => 1.00,
    'service_title' => 'Test Service - PayPal Guest Checkout'
];

// 构建PayPal支付URL - 复用Orders控制器中的逻辑
$amount = number_format($test_order['amount'], 2, '.', '');
$return_url = URLROOT . '/test_paypal_payment.php?success=1';
$cancel_url = URLROOT . '/test_paypal_payment.php?cancelled=1';

// PayPal支付参数 - 启用访客结账功能
$paypal_params = [
    'cmd' => '_xclick',
    'business' => PAYPAL_RECEIVER_EMAIL,
    'item_name' => 'Test Order #' . $test_order['id'] . ' - ' . $test_order['service_title'],
    'item_number' => $test_order['id'],
    'amount' => $amount,
    'currency_code' => 'USD',
    'quantity' => '1',
    'no_shipping' => '1',  // 不需要送货地址
    'no_note' => '1',      // 不需要备注
    'cn' => 'Optional Note', // 备注字段标签
    'rm' => '2',           // Return method: 2 = POST
    'return' => $return_url,
    'cancel_return' => $cancel_url,
    'notify_url' => URLROOT . '/orders/ipn',
    'custom' => 'wg_' . $test_order['id'],
    'lc' => 'US',          // 地区设置
    'page_style' => 'primary', // 页面样式
    'cbt' => 'Return to ' . SITENAME, // 按钮文本
    // 启用访客结账的关键参数
    'allow_note' => '0',   // 不允许买家备注
    'charset' => 'utf-8',  // 字符编码
    'address_override' => '0', // 不覆盖地址
    'discount_amount' => '0', // 折扣金额
    'discount_amount_cart' => '0', // 购物车折扣
    'shipping' => '0',     // 运费
    'tax' => '0',         // 税费
    'handling' => '0',     // 手续费
];

$paypal_query = http_build_query($paypal_params);
$paypal_url = PAYPAL_URL . '?' . $paypal_query;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Guest Checkout Test - <?php echo SITENAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/assets/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card"></i> PayPal Guest Checkout Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Payment Test Completed!</h5>
                                <p>You have successfully returned from PayPal. This confirms the payment flow is working correctly.</p>
                                <p><strong>Note:</strong> This is just a test of the payment flow. No actual order was created.</p>
                            </div>
                        <?php elseif (isset($_GET['cancelled'])): ?>
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-times-circle"></i> Payment Cancelled</h5>
                                <p>You cancelled the payment. This is normal for testing purposes.</p>
                            </div>
                        <?php endif; ?>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Test Details</h6>
                                    <ul class="mb-0">
                                        <li><strong>Test Order ID:</strong> <?php echo $test_order['id']; ?></li>
                                        <li><strong>Amount:</strong> $<?php echo $amount; ?> USD</li>
                                        <li><strong>Environment:</strong> <?php echo PAYPAL_SANDBOX ? 'Sandbox' : 'Live'; ?></li>
                                        <li><strong>PayPal Email:</strong> <?php echo PAYPAL_RECEIVER_EMAIL; ?></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <h6><i class="fas fa-credit-card"></i> Payment Methods</h6>
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
                                            <i class="fab fa-cc-discover fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                    <small class="d-block mt-2">
                                        <strong>Guest Checkout Enabled</strong><br>
                                        No PayPal account required
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Test Instructions</h6>
                            <ol>
                                <li>Click the "Test PayPal Payment" button below</li>
                                <li>You'll be redirected to PayPal's secure checkout page</li>
                                <li>Choose to pay as guest or with PayPal account</li>
                                <li>Use test credentials if in sandbox mode</li>
                                <li>Complete or cancel the payment to test the return flow</li>
                            </ol>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="<?php echo htmlspecialchars($paypal_url); ?>"
                               class="btn btn-success btn-lg"
                               target="_blank">
                                <i class="fas fa-lock"></i> Test PayPal Payment - $<?php echo $amount; ?>
                            </a>

                            <small class="text-muted text-center">
                                This will open PayPal in a new tab/window<br>
                                You can use PayPal's sandbox test credentials for testing
                            </small>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">PayPal Parameters Sent</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre class="small"><?php echo htmlspecialchars(print_r($paypal_params, true)); ?></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">PayPal URL Generated</h6>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-break"><?php echo htmlspecialchars($paypal_url); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>