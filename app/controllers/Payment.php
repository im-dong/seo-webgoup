<?php
/*
 * 支付回调控制器
 * 处理各种支付方式的回调逻辑
 */
class Payment {
    private $payment_password = 'test123456';
    private $order;

    public function __construct() {
        require_once APPROOT . '/app/models/Order.php';
        $this->order = new Order();
    }

    // 默认方法
    public function index() {
        // 获取参数
        $password = $_GET['password'] ?? '';
        $orderid = $_GET['orderid'] ?? '';
        $email = $_GET['email'] ?? '';
        $payment_id = $_GET['payment_id'] ?? '';

        // 验证密码
        if ($password !== $this->payment_password) {
            http_response_code(403);
            echo 'Invalid password';
            return;
        }

        // 验证订单ID
        if (empty($orderid) || !is_numeric($orderid)) {
            http_response_code(400);
            echo 'Invalid order ID';
            return;
        }

        // 验证支付ID
        if (empty($payment_id)) {
            http_response_code(400);
            echo 'Invalid payment ID';
            return;
        }

        // 使用email作为备注
        $remark = !empty($email) ? $email : null;

        // 更新订单状态为已支付
        if ($this->order->captureOrder($orderid, $payment_id, $remark)) {
            echo 'Payment updated successfully';
        } else {
            http_response_code(500);
            echo 'Failed to update payment';
        }
    }
}