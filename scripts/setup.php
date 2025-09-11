<?php
// 这是一个命令行脚本，用于初始化数据库和表结构
// This is a command-line script to initialize the database and tables.
// 用法: php scripts/setup.php

echo "开始数据库初始化...
";

// 定义基本路径
define('APPROOT', dirname(__DIR__));

// 引入配置文件
require_once APPROOT . '/config/config.php';

try {
    // 1. 连接到MySQL服务器 (不指定数据库名)
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "成功连接到MySQL服务器。
";

    // 2. 创建数据库
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "数据库 '" . DB_NAME . "' 已创建或已存在。
";

    // 3. 选择新创建的数据库
    $pdo->exec("USE `" . DB_NAME . "`");
    echo "已切换到数据库 '" . DB_NAME . "'。
";

    // 4. 开始创建数据表
    echo "开始创建数据表...
";

    // -- users table --
    $sql = "
    CREATE TABLE IF NOT EXISTS `users` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(50) NOT NULL UNIQUE,
      `email` VARCHAR(100) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `status` TINYINT(1) DEFAULT 1 COMMENT '用户状态: 1=激活, 0=禁用',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB COMMENT='用户表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'users' 已创建。
";

    // -- services table --
    $sql = "
    CREATE TABLE IF NOT EXISTS `services` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT NOT NULL,
      `title` VARCHAR(255) NOT NULL COMMENT '服务标题',
      `description` TEXT NOT NULL COMMENT '服务描述',
      `site_url` VARCHAR(255) NOT NULL COMMENT '提供外链的网站',
      `price` DECIMAL(10, 2) NOT NULL COMMENT '服务价格',
      `delivery_time` INT NOT NULL COMMENT '交货时间(天)',
      `link_type` ENUM('follow', 'nofollow') NOT NULL COMMENT '链接类型',
      `is_adult_allowed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否允许成人内容',
      `is_new_window` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否新窗口打开',
      `duration` INT NOT NULL COMMENT '外链持续服务时间(天)',
      `is_official` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否官方服务: 1=是, 0=否',
      `status` TINYINT(1) DEFAULT 1 COMMENT '服务状态: 1=可用, 0=下架',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB COMMENT='外链服务表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'services' 已创建。
";

    // -- orders table --
    $sql = "
    CREATE TABLE IF NOT EXISTS `orders` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `service_id` INT NOT NULL,
      `buyer_id` INT NOT NULL,
      `seller_id` INT NOT NULL,
      `amount` DECIMAL(10, 2) NOT NULL COMMENT '订单金额',
      `status` VARCHAR(50) NOT NULL DEFAULT 'pending_payment' COMMENT '订单状态: pending_payment, paid, completed, confirmed, released, cancelled',
      `payment_id` VARCHAR(255) NULL COMMENT '支付网关返回的ID',
      `proof_url` VARCHAR(255) NULL COMMENT '卖家完成工作的证明链接',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `paid_at` TIMESTAMP NULL,
      `completed_at` TIMESTAMP NULL COMMENT '卖家标记完成的时间',
      `confirmed_at` TIMESTAMP NULL COMMENT '买家确认的时间',
      `funds_release_date` DATE NULL COMMENT '资金解锁日期',
      FOREIGN KEY (`service_id`) REFERENCES `services`(`id`),
      FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`),
      FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`)
    ) ENGINE=InnoDB COMMENT='订单表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'orders' 已创建。
";

    // -- user_wallets table --
    $sql = "
    CREATE TABLE IF NOT EXISTS `user_wallets` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `user_id` INT NOT NULL UNIQUE,
      `total_balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '总余额 (包括冻结资金)',
      `withdrawable_balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '可提现余额',
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB COMMENT='用户钱包表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'user_wallets' 已创建。
";

    // -- settings table --
    $sql = "
    CREATE TABLE IF NOT EXISTS `settings` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `setting_key` VARCHAR(100) NOT NULL UNIQUE,
      `setting_value` TEXT NULL,
      `description` VARCHAR(255) NULL
    ) ENGINE=InnoDB COMMENT='网站配置表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'settings' 已创建。
";

    // -- 插入PayPal配置 --
    $sql = "
    INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
    ('paypal_client_id', '', 'PayPal Client ID'),
    ('paypal_client_secret', '', 'PayPal Client Secret'),
    ('paypal_mode', 'sandbox', 'PayPal Mode (sandbox or live)');
    ";
    $pdo->exec($sql);
    echo "  - 已插入PayPal基础配置。
";


    echo "所有数据表创建完毕。
";
    echo "数据库初始化完成！
";

} catch (PDOException $e) {
    die("数据库操作失败: " . $e->getMessage() . "
");
}
