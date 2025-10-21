<?php
// PayPal API测试页面
require_once 'app/bootstrap.php';

// 检查API配置
$api_configured = defined('PAYPAL_CLIENT_ID') && defined('PAYPAL_CLIENT_SECRET');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal API Test - <?php echo SITENAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/assets/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fab fa-paypal"></i> PayPal API Integration Test
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- API配置状态 -->
                        <div class="alert <?php echo $api_configured ? 'alert-success' : 'alert-danger'; ?> mb-4">
                            <h6><i class="fas fa-cog"></i> API Configuration Status</h6>
                            <ul class="mb-0">
                                <li><strong>Environment:</strong> <?php echo PAYPAL_SANDBOX ? 'Sandbox' : 'Live'; ?></li>
                                <li><strong>Client ID:</strong> <?php echo PAYPAL_CLIENT_ID ? substr(PAYPAL_CLIENT_ID, 0, 20) . '...' : 'Not Configured'; ?></li>
                                <li><strong>Client Secret:</strong> <?php echo PAYPAL_CLIENT_SECRET ? 'Configured' : 'Not Configured'; ?></li>
                                <li><strong>API Base URL:</strong> <?php echo PAYPAL_API_BASE_URL; ?></li>
                            </ul>
                        </div>

                        <!-- API连接测试 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">API Connection Test</h6>
                                    </div>
                                    <div class="card-body">
                                        <button onclick="testAPIConnection()" class="btn btn-outline-primary">
                                            <i class="fas fa-plug"></i> Test API Connection
                                        </button>
                                        <div id="api-test-result" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Order Creation Test</h6>
                                    </div>
                                    <div class="card-body">
                                        <button onclick="testOrderCreation()" class="btn btn-outline-success">
                                            <i class="fas fa-shopping-cart"></i> Test Order Creation
                                        </button>
                                        <div id="order-test-result" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PayPal按钮测试 -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-credit-card"></i> PayPal Smart Buttons Test
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>PayPal Account Button</h6>
                                        <div id="paypal-button-container"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Direct Credit Card Button</h6>
                                        <div id="card-button-container"></div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>Test Details:</strong><br>
                                        • Amount: $1.00 USD<br>
                                        • Service: Test Service - PayPal API Integration<br>
                                        • Click buttons to test the complete payment flow
                                    </small>
                                </div>

                                <div id="payment-status" class="mt-3"></div>
                            </div>
                        </div>

                        <hr>

                        <!-- 开发信息 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">API Request Log</h6>
                                    </div>
                                    <div class="card-body">
                                        <pre id="api-log" style="font-size: 12px; max-height: 200px; overflow-y: auto;">Waiting for API calls...</pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Test Instructions</h6>
                                    </div>
                                    <div class="card-body">
                                        <ol>
                                            <li>Test API connection first</li>
                                            <li>Test order creation</li>
                                            <li>Test PayPal buttons</li>
                                            <li>Use sandbox test credentials</li>
                                            <li>Check browser console for errors</li>
                                        </ol>
                                        <div class="alert alert-info mt-3">
                                            <strong>Sandbox Test Cards:</strong><br>
                                            Visa: 4111111111111111<br>
                                            MasterCard: 5555555555554444
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PayPal JavaScript -->
    <script>
        // 设置全局变量
        window.URLROOT = '<?php echo URLROOT; ?>';
        window.PAYPAL_CLIENT_ID = '<?php echo PAYPAL_CLIENT_ID; ?>';
        window.PAYPAL_SANDBOX = <?php echo PAYPAL_SANDBOX ? 'true' : 'false'; ?>;

        // 日志函数
        function log(message) {
            const logElement = document.getElementById('api-log');
            const timestamp = new Date().toLocaleTimeString();
            logElement.textContent = `[${timestamp}] ${message}\n` + logElement.textContent;
        }

        // API连接测试
        async function testAPIConnection() {
            const resultDiv = document.getElementById('api-test-result');
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Testing...';

            try {
                log('Testing PayPal API connection...');

                // 直接测试获取访问令牌
                const response = await fetch('<?php echo PAYPAL_API_BASE_URL; ?>/v1/oauth2/token', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Basic ' + btoa('<?php echo PAYPAL_CLIENT_ID; ?>:<?php echo PAYPAL_CLIENT_SECRET; ?>'),
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'grant_type=client_credentials'
                });

                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Connection successful! Token expires in ' + data.expires_in + ' seconds</div>';
                    log('API Connection Test: SUCCESS - Token received');
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times"></i> Connection failed: ' + (data.error_description || data.message) + '</div>';
                    log('API Connection Test: FAILED - ' + JSON.stringify(data));
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times"></i> Network error: ' + error.message + '</div>';
                log('API Connection Test: NETWORK ERROR - ' + error.message);
            }
        }

        // 订单创建测试
        async function testOrderCreation() {
            const resultDiv = document.getElementById('order-test-result');
            resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Creating test order...';

            try {
                log('Testing order creation...');

                const response = await fetch('<?php echo URLROOT; ?>/orders/create/1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Order created! PayPal Order ID: ' + data.id + '</div>';
                    log('Order Creation Test: SUCCESS - PayPal Order ID: ' + data.id);
                    window.testPaypalOrderId = data.id; // 保存供按钮测试使用
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times"></i> Order creation failed: ' + (data.error || 'Unknown error') + '</div>';
                    log('Order Creation Test: FAILED - ' + JSON.stringify(data));
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times"></i> Network error: ' + error.message + '</div>';
                log('Order Creation Test: NETWORK ERROR - ' + error.message);
            }
        }

        // 支付状态更新
        function updatePaymentStatus(message, type) {
            const statusDiv = document.getElementById('payment-status');
            statusDiv.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
            log('Payment Status: ' + message);
        }
    </script>

    <script src="<?php echo URLROOT; ?>/public/js/paypal-buttons.js"></script>

    <script>
        // 初始化PayPal按钮
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                log('Initializing PayPal Smart Buttons...');

                // 渲染PayPal按钮
                await paypalButtons.renderPayPalButton('#paypal-button-container', 1, {
                    style: {
                        layout: 'vertical',
                        color: 'blue',
                        shape: 'rect',
                        label: 'paypal',
                        height: 45
                    }
                });

                log('PayPal button rendered successfully');

                // 渲染信用卡按钮
                await paypalButtons.renderCardButton('#card-button-container', 1, {
                    style: {
                        layout: 'vertical',
                        color: 'gold',
                        shape: 'rect',
                        label: 'pay',
                        height: 45
                    }
                });

                log('Credit card button rendered successfully');

            } catch (error) {
                log('PayPal button initialization failed: ' + error.message);
                console.error('PayPal buttons error:', error);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>