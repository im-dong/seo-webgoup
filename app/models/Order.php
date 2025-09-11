<?php
class Order {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 创建一个新订单
    public function createOrder($data){
        $this->db->query('INSERT INTO orders (service_id, buyer_id, seller_id, amount, status) VALUES (:service_id, :buyer_id, :seller_id, :amount, \'pending_payment\')');
        
        $this->db->bind(':service_id', $data['service_id']);
        $this->db->bind(':buyer_id', $data['buyer_id']);
        $this->db->bind(':seller_id', $data['seller_id']);
        $this->db->bind(':amount', $data['amount']);

        if($this->db->execute()){
            // PDO没有内置方法直接返回刚插入的ID，这里是一种解决方法
            $this->db->query('SELECT LAST_INSERT_ID() as id');
            $result = $this->db->single();
            return $result->id;
        }
        return false;
    }

    // 捕获并确认订单支付
    public function captureOrder($order_id, $payment_id){
        $this->db->query('UPDATE orders SET status = \'paid\', payment_id = :payment_id, paid_at = CURRENT_TIMESTAMP WHERE id = :order_id');
        
        $this->db->bind(':payment_id', $payment_id);
        $this->db->bind(':order_id', $order_id);

        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 根据买家ID获取订单
    public function getOrdersByBuyerId($buyer_id){
        $this->db->query('SELECT o.*, s.title as service_title FROM orders o JOIN services s ON o.service_id = s.id WHERE o.buyer_id = :buyer_id ORDER BY o.created_at DESC');
        $this->db->bind(':buyer_id', $buyer_id);
        return $this->db->resultSet();
    }

    // 根据卖家ID获取订单
    public function getOrdersBySellerId($seller_id){
        $this->db->query('SELECT o.*, s.title as service_title FROM orders o JOIN services s ON o.service_id = s.id WHERE o.seller_id = :seller_id ORDER BY o.created_at DESC');
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->resultSet();
    }

    // 卖家标记订单为完成
    public function markAsComplete($order_id, $proof_url){
        // 首先获取服务的持续时间
        $this->db->query('SELECT duration FROM services s JOIN orders o ON s.id = o.service_id WHERE o.id = :order_id');
        $this->db->bind(':order_id', $order_id);
        $service = $this->db->single();
        $duration = (int)$service->duration;

        // 计算资金解锁日期
        $release_date = date('Y-m-d H:i:s', strtotime("+{$duration} days"));

        // 更新订单
        $this->db->query('UPDATE orders SET status = \'completed\', proof_url = :proof_url, completed_at = CURRENT_TIMESTAMP, funds_release_date = :release_date WHERE id = :order_id');
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':proof_url', $proof_url);
        $this->db->bind(':release_date', $release_date);

        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 买家确认订单
    public function confirmOrder($order_id, $user_id){
        // 增加一个 user_id 验证，确保只有买家本人才能确认
        $this->db->query('UPDATE orders SET status = \'confirmed\', confirmed_at = CURRENT_TIMESTAMP WHERE id = :order_id AND buyer_id = :user_id');
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':user_id', $user_id);

        if($this->db->execute() && $this->db->rowCount() > 0){
            return true;
        }
        return false;
    }

    // 获取单个订单
    public function getOrderById($order_id){
        $this->db->query('SELECT * FROM orders WHERE id = :order_id');
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }

    // 获取特定服务和买卖双方的最新订单
    public function getLatestOrderByServiceAndUsers($service_id, $buyer_id, $seller_id){
        $this->db->query('SELECT id FROM orders WHERE service_id = :service_id AND buyer_id = :buyer_id AND seller_id = :seller_id ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':service_id', $service_id);
        $this->db->bind(':buyer_id', $buyer_id);
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->single();
    }

    // 获取两个用户之间最新的订单
    public function getLatestOrderBetweenUsers($user1_id, $user2_id){
        $this->db->query('SELECT id FROM orders WHERE (buyer_id = :user1_id AND seller_id = :user2_id) OR (buyer_id = :user2_id AND seller_id = :user1_id) ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':user1_id', $user1_id);
        $this->db->bind(':user2_id', $user2_id);
        return $this->db->single();
    }

    // 查找一个咨询订单
    public function findInquiryOrder($service_id, $buyer_id, $seller_id){
        $this->db->query('SELECT id FROM orders WHERE service_id = :service_id AND buyer_id = :buyer_id AND seller_id = :seller_id AND status = \'inquiry\'');
        $this->db->bind(':service_id', $service_id);
        $this->db->bind(':buyer_id', $buyer_id);
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->single();
    }

    // 为售前咨询创建一个虚拟订单
    public function createInquiryOrder($service_id, $buyer_id, $seller_id){
        $this->db->query('INSERT INTO orders (service_id, buyer_id, seller_id, amount, status) VALUES (:service_id, :buyer_id, :seller_id, 0, \'inquiry\')');
        $this->db->bind(':service_id', $service_id);
        $this->db->bind(':buyer_id', $buyer_id);
        $this->db->bind(':seller_id', $seller_id);
        
        if($this->db->execute()){
            return $this->db->lastInsertId();
        }
        return false;
    }
}

