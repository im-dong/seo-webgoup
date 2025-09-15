<?php
// 添加邮箱验证码表
// Usage: php scripts/update_schema_11.php

echo "开始添加邮箱验证码表...
";

define('APPROOT', dirname(__DIR__));
require_once APPROOT . '/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 创建邮箱验证码表
    $sql = "
    CREATE TABLE IF NOT EXISTS `email_verifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(100) NOT NULL,
        `verification_code` VARCHAR(6) NOT NULL,
        `expires_at` TIMESTAMP NOT NULL,
        `is_used` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_email_code` (`email`, `verification_code`),
        INDEX `idx_email` (`email`),
        INDEX `idx_expires` (`expires_at`)
    ) ENGINE=InnoDB COMMENT='邮箱验证码表';
    ";
    $pdo->exec($sql);
    echo "  - 表 'email_verifications' 已创建。
";

    // 为用户表添加邮箱验证状态字段
    $sql = "
    ALTER TABLE `users`
    ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 COMMENT '邮箱是否已验证: 1=已验证, 0=未验证' AFTER `status`,
    ADD COLUMN `verification_code` VARCHAR(6) NULL COMMENT '邮箱验证码' AFTER `email_verified`;
    ";
    $pdo->exec($sql);
    echo "  - 用户表已添加邮箱验证相关字段。
";

    echo "邮箱验证码表创建完成！
";

} catch (PDOException $e) {
    die("数据库操作失败: " . $e->getMessage() . "
");
}
?>