<?php
// 数据库清空脚本 - 清空所有业务数据表，保留必要的设置表
require_once 'config/config.php';

try {
    // 连接数据库
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    echo "开始清空数据库...\n";

    // 需要保留的表（设置和必要数据）
    $preserve_tables = [
        'settings',
        'industries'
    ];

    // 需要清空的业务数据表
    $truncate_tables = [
        'users',
        'services',
        'orders',
        'order_service_snapshots',
        'reviews',
        'conversations',
        'messages',
        'user_wallets',
        'wallet_transactions',
        'withdrawals',
        'email_verifications'
    ];

    // 先关闭外键检查
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "已关闭外键检查\n";

    // 清空业务数据表
    foreach ($truncate_tables as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
        echo "已清空表: $table\n";
    }

    // 重新启用外键检查
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "已启用外键检查\n";

    // 创建默认管理员用户
    $admin_email = 'admin@webgoup.com';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_name = 'Administrator';

    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, role, created_at)
        VALUES (?, ?, ?, 'admin', NOW())
    ");
    $stmt->execute([$admin_name, $admin_email, $admin_password]);
    echo "已创建默认管理员用户: $admin_email (密码: admin123)\n";

    // 创建测试用户
    $test_email = 'test@webgoup.com';
    $test_password = password_hash('test123', PASSWORD_DEFAULT);
    $test_name = 'Test User';

    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, role, created_at)
        VALUES (?, ?, ?, 'user', NOW())
    ");
    $stmt->execute([$test_name, $test_email, $test_password]);
    echo "已创建测试用户: $test_email (密码: test123)\n";

    // 为管理员和测试用户创建钱包
    $stmt = $pdo->prepare("
        INSERT INTO user_wallets (user_id, total_balance, withdrawable_balance)
        VALUES (?, 0.00, 0.00)
    ");
    $stmt->execute([1]); // 管理员用户的ID是1
    echo "已为管理员用户创建钱包\n";

    $stmt->execute([2]); // 测试用户的ID是2
    echo "已为测试用户创建钱包\n";

    echo "\n数据库清空完成！\n";
    echo "保留的表: " . implode(', ', $preserve_tables) . "\n";
    echo "清空的表: " . implode(', ', $truncate_tables) . "\n";

} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
    exit(1);
}
?>