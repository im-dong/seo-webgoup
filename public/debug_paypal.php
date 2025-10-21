<?php
require_once '../app/bootstrap.php';

// 设置变量
$urlroot = URLROOT;
$clientId = defined('PAYPAL_CLIENT_ID') ? PAYPAL_CLIENT_ID : 'NOT_DEFINED';
$isSandbox = defined('PAYPAL_SANDBOX') ? PAYPAL_SANDBOX : true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Debug - <?php echo SITENAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning">
                <h4>PayPal Debug Information</h4>
            </div>
            <div class="card-body">
                <h5>Configuration Check:</h5>
                <ul>
                    <li><strong>URLROOT:</strong> <?php echo $urlroot; ?></li>
                    <li><strong>Client ID:</strong> <?php echo substr($clientId, 0, 30) . '...'; ?></li>
                    <li><strong>Sandbox:</strong> <?php echo $isSandbox ? 'Yes' : 'No'; ?></li>
                </ul>

                <hr>

                <h5>JavaScript Console Output:</h5>
                <pre id="console-output" style="background: #f8f9fa; padding: 10px; max-height: 300px; overflow-y: auto;"></pre>

                <hr>

                <h5>PayPal Button Test:</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="border p-3">
                            <h6>PayPal Button</h6>
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3">
                            <h6>Credit Card Button</h6>
                            <div id="card-button-container"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button onclick="testDirectAPI()" class="btn btn-primary">Test Direct PayPal API</button>
                    <button onclick="checkPayPalSDK()" class="btn btn-info">Check PayPal SDK</button>
                </div>

                <div id="test-results" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script>
        // 捕获控制台输出
        const originalLog = console.log;
        const originalError = console.error;
        const originalWarn = console.warn;
        const outputDiv = document.getElementById('console-output');

        function addToConsole(type, ...args) {
            const timestamp = new Date().toLocaleTimeString();
            const message = args.map(arg =>
                typeof arg === 'object' ? JSON.stringify(arg, null, 2) : String(arg)
            ).join(' ');

            outputDiv.innerHTML += `[${timestamp}] ${type.toUpperCase()}: ${message}\n`;
            outputDiv.scrollTop = outputDiv.scrollHeight;
        }

        console.log = function(...args) {
            addToConsole('log', ...args);
            originalLog.apply(console, args);
        };

        console.error = function(...args) {
            addToConsole('error', ...args);
            originalError.apply(console, args);
        };

        console.warn = function(...args) {
            addToConsole('warn', ...args);
            originalWarn.apply(console, args);
        };

        // 设置全局变量
        window.URLROOT = '<?php echo $urlroot; ?>';
        window.PAYPAL_CLIENT_ID = '<?php echo $clientId; ?>';
        window.PAYPAL_SANDBOX = <?php echo $isSandbox ? 'true' : 'false'; ?>;

        console.log('Variables set:');
        console.log('URLROOT:', window.URLROOT);
        console.log('PAYPAL_CLIENT_ID:', window.PAYPAL_CLIENT_ID);
        console.log('PAYPAL_SANDBOX:', window.PAYPAL_SANDBOX);

        // 测试函数
        function testDirectAPI() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<div class="spinner-border"></div> Testing...';

            fetch('<?php echo PAYPAL_API_BASE_URL; ?>/v1/oauth2/token', {
                method: 'POST',
                headers: {
                    'Authorization': 'Basic ' + btoa('<?php echo PAYPAL_CLIENT_ID; ?>:<?php echo PAYPAL_CLIENT_SECRET; ?>'),
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'grant_type=client_credentials'
            })
            .then(response => response.json())
            .then(data => {
                if (data.access_token) {
                    results.innerHTML = '<div class="alert alert-success">API Connection SUCCESS!</div>';
                    console.log('API Test Success:', data);
                } else {
                    results.innerHTML = '<div class="alert alert-danger">API Connection FAILED: ' + JSON.stringify(data) + '</div>';
                    console.error('API Test Failed:', data);
                }
            })
            .catch(error => {
                results.innerHTML = '<div class="alert alert-danger">Network Error: ' + error.message + '</div>';
                console.error('Network Error:', error);
            });
        }

        function checkPayPalSDK() {
            console.log('Checking PayPal SDK...');
            console.log('window.paypal exists:', typeof window.paypal !== 'undefined');
            if (window.paypal) {
                console.log('PayPal version:', window.paypal.version);
                console.log('Available funding sources:', Object.keys(window.paypal.FUNDING));
            }
        }
    </script>

    <!-- 加载PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $clientId; ?>&currency=USD&buyer-country=US"></script>

    <script>
        console.log('PayPal SDK script loaded');
        console.log('window.paypal after SDK load:', typeof window.paypal);

        // 手动创建简单按钮测试
        if (window.paypal) {
            console.log('Creating PayPal button...');
            paypal.Buttons({
                createOrder: function(data, actions) {
                    console.log('Creating PayPal order...');
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '1.00'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    console.log('Payment approved:', data);
                    return actions.order.capture().then(function(details) {
                        console.log('Capture result:', details);
                        alert('Payment completed!');
                    });
                },
                onError: function(err) {
                    console.error('PayPal button error:', err);
                }
            }).render('#paypal-button-container');

            // 信用卡按钮
            if (paypal.FUNDING && paypal.FUNDING.CARD) {
                console.log('Creating Card button...');
                paypal.Buttons({
                    fundingSource: paypal.FUNDING.CARD,
                    createOrder: function(data, actions) {
                        console.log('Creating card order...');
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '1.00'
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        console.log('Card payment approved:', data);
                        return actions.order.capture().then(function(details) {
                            console.log('Card capture result:', details);
                            alert('Card payment completed!');
                        });
                    },
                    onError: function(err) {
                        console.error('Card button error:', err);
                    }
                }).render('#card-button-container');
            } else {
                console.error('paypal.FUNDING.CARD not available');
                document.getElementById('card-button-container').innerHTML = '<div class="alert alert-warning">Card funding not available</div>';
            }
        } else {
            console.error('PayPal SDK not loaded!');
            document.getElementById('paypal-button-container').innerHTML = '<div class="alert alert-danger">PayPal SDK failed to load</div>';
            document.getElementById('card-button-container').innerHTML = '<div class="alert alert-danger">PayPal SDK failed to load</div>';
        }
    </script>
</body>
</html>