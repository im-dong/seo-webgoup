<?php

// 错误报告设置
// Error Reporting Settings
error_reporting( E_PARSE); // 仅报告致命错误和解析错误
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
define('URLROOT', 'https://58e1b68ecff3.ngrok-free.app'); // 调试paypal时的地址

// Site Name
define('SITENAME', 'WebGoup');

// Platform Fee
define('PLATFORM_FEE_PERCENTAGE', 30);

// PayPal Settings
define('PAYPAL_RECEIVER_EMAIL', 'sb-gjh0d46332348@personal.example.com'); // TODO: 请将此替换为您的PayPal沙箱收款邮箱
//define('PAYPAL_RECEIVER_EMAIL', 'zhongshan@126.com'); // TODO: 请将此替换为您的PayPal沙箱收款邮箱
define('PAYPAL_SANDBOX', true); // 设置为 false 以进行真实交易
define('PAYPAL_URL', PAYPAL_SANDBOX ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');
define('PAYPAL_NOTIFY_URL', 'https://paypal.webgoup.com');
//define('PAYPAL_NOTIFY_URL', 'https://seo-tip.com/test2.php');
//define('PAYPAL_NOTIFY_URL', 'https://58e1b68ecff3.ngrok-free.app/orders/ipn');

// Email Settings
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', 'webgoup.service@gmail.com');
define('EMAIL_PASSWORD', 'pnla cdab hjpp lblb');
define('EMAIL_FROM_NAME', 'WebGoup Team');
define('EMAIL_FROM_EMAIL', 'webgoup.service@gmail.com');

