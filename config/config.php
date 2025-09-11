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
define('URLROOT', 'http://webgoup'); // 请根据您的实际URL修改

// 网站名称
// Site Name
define('SITENAME', 'WebGoup');
