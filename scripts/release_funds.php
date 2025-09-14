<?php
// 这是一个需要通过 Cron Job 定时执行的脚本
// 用法: php /path/to/your/project/scripts/release_funds.php

// 引入基础环境
require_once dirname(__DIR__) . '/app/bootstrap.php';

echo "开始执行资金解锁任务...\n";

$db = new Database();

// 查找所有已由买家确认，并且资金解锁日期已到或已过的订单
$db->query("SELECT * FROM orders WHERE status = 'confirmed' AND funds_release_date <= NOW()");
$orders_to_release = $db->resultSet();

if(empty($orders_to_release)){
    echo "没有需要解锁的资金。任务结束。\n";
    exit();
}

echo "找到 " . count($orders_to_release) . " 个待解锁的订单。\n";

$walletModel = new Wallet();

foreach($orders_to_release as $order){
    echo "处理订单 #{$order->id}... ";

    $seller_earnings = $order->amount * (1 - PLATFORM_FEE_PERCENTAGE / 100);
    $platform_fee = $order->amount * (PLATFORM_FEE_PERCENTAGE / 100);

    // 1. 将资金从总余额移动到可提现余额
    if ($walletModel->moveFundsToWithdrawable($order->seller_id, $seller_earnings, $order->id)) {
        // 2. 更新订单状态为 released
        $db->query("UPDATE orders SET status = 'released' WHERE id = :order_id");
        $db->bind(':order_id', $order->id);
        $db->execute();

        // 3. Log platform fee transaction (for now, just a placeholder)
        // In a real application, you would have a user for the platform
        // $walletModel->logTransaction(PLATFORM_USER_ID, 'fee', $platform_fee, 'Platform fee for order #' . $order->id, $order->id);

        echo "完成.\n";
    } else {
        echo "失败.\n";
    }
}

echo "所有资金解锁任务已完成。\n";

