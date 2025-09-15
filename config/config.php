<?php

// 错误报告设置
// Error Reporting Settings
error_reporting(E_ERROR | E_PARSE); // 仅报告致命错误和解析错误
ini_set('display_errors', 'On'); // 在开发环境中显示错误

// 数据库配置
// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'webgoup');

// URL根目录
// URL Root
//define('URLROOT', 'http://webgoup'); // 请根据您的实际URL修改
define('URLROOT', 'https://b149d569771d.ngrok-free.app'); // 调试paypal时的地址

// Site Name
define('SITENAME', 'WebGoup');

// Platform Fee
define('PLATFORM_FEE_PERCENTAGE', 30);

// PayPal Settings
define('PAYPAL_RECEIVER_EMAIL', 'sb-skb47i38000699@personal.example.com'); // TODO: 请将此替换为您的PayPal沙箱收款邮箱
define('PAYPAL_SANDBOX', true); // 设置为 false 以进行真实交易
define('PAYPAL_URL', PAYPAL_SANDBOX ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');

