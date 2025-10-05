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
    public function captureOrder($order_id, $payment_id, $remark = null){
        $sql = 'UPDATE orders SET status = \'paid\', payment_id = :payment_id, paid_at = CURRENT_TIMESTAMP';
        if ($remark !== null) {
            $sql .= ', remark = :remark';
        }
        $sql .= ' WHERE id = :order_id';

        $this->db->query($sql);
        $this->db->bind(':payment_id', $payment_id);
        $this->db->bind(':order_id', $order_id);
        if ($remark !== null) {
            $this->db->bind(':remark', $remark);
        }

        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 根据买家ID获取订单
    public function getOrdersByBuyerId($buyer_id, $pagination = []){
        $sql = 'SELECT o.*, s.title as service_title, u.username as seller_name, CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as has_reviewed FROM orders o JOIN services s ON o.service_id = s.id JOIN users u ON o.seller_id = u.id LEFT JOIN reviews r ON o.id = r.order_id WHERE o.buyer_id = :buyer_id ORDER BY o.created_at DESC';

        // 添加分页
        if (isset($pagination['per_page']) && isset($pagination['offset'])) {
            $per_page = intval($pagination['per_page']);
            $offset = intval($pagination['offset']);
            if ($per_page > 0) {
                $sql .= ' LIMIT ' . $per_page . ' OFFSET ' . $offset;
            }
        }

        $this->db->query($sql);
        $this->db->bind(':buyer_id', $buyer_id);
        return $this->db->resultSet();
    }

    // 根据卖家ID获取订单
    public function getOrdersBySellerId($seller_id, $pagination = []){
        $sql = 'SELECT o.*, s.title as service_title, CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as has_reviewed FROM orders o JOIN services s ON o.service_id = s.id LEFT JOIN reviews r ON o.id = r.order_id WHERE o.seller_id = :seller_id ORDER BY o.created_at DESC';

        // 添加分页
        if (isset($pagination['per_page']) && isset($pagination['offset'])) {
            $per_page = intval($pagination['per_page']);
            $offset = intval($pagination['offset']);
            if ($per_page > 0) {
                $sql .= ' LIMIT ' . $per_page . ' OFFSET ' . $offset;
            }
        }

        $this->db->query($sql);
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->resultSet();
    }

    // 获取买家订单总数（用于分页）
    public function getOrdersByBuyerIdCount($buyer_id){
        $this->db->query('SELECT COUNT(*) as total FROM orders o WHERE o.buyer_id = :buyer_id');
        $this->db->bind(':buyer_id', $buyer_id);
        $result = $this->db->single();
        return $result->total;
    }

    // 获取卖家订单总数（用于分页）
    public function getOrdersBySellerIdCount($seller_id){
        $this->db->query('SELECT COUNT(*) as total FROM orders o WHERE o.seller_id = :seller_id');
        $this->db->bind(':seller_id', $seller_id);
        $result = $this->db->single();
        return $result->total;
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

    // 根据支付ID获取订单
    public function getOrderByPaymentId($payment_id){
        $this->db->query('SELECT * FROM orders WHERE payment_id = :payment_id');
        $this->db->bind(':payment_id', $payment_id);
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

    // 创建服务快照
    public function createServiceSnapshot($order_id, $service_data){
        $this->db->query('INSERT INTO order_service_snapshots (order_id, title, description, thumbnail_url, price, delivery_time, link_type, duration) VALUES (:order_id, :title, :description, :thumbnail_url, :price, :delivery_time, :link_type, :duration)');
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':title', $service_data->title);
        $this->db->bind(':description', $service_data->description);
        $this->db->bind(':thumbnail_url', $service_data->thumbnail_url ?? null);
        $this->db->bind(':price', $service_data->price);
        $this->db->bind(':delivery_time', $service_data->delivery_time);
        $this->db->bind(':link_type', $service_data->link_type);
        $this->db->bind(':duration', $service_data->duration);

        return $this->db->execute();
    }

    // 根据订单ID获取服务快照
    public function getSnapshotByOrderId($order_id){
        $this->db->query('SELECT * FROM order_service_snapshots WHERE order_id = :order_id');
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }

    public function getTotalOrdersCount(){
        $this->db->query('SELECT COUNT(*) as count FROM orders');
        $row = $this->db->single();
        return $row->count;
    }

    public function getAllOrdersWithPagination($per_page = 20, $offset = 0){
        $this->db->query('SELECT o.*,
                                 s.title as service_title,
                                 buyer.username as buyer_username,
                                 seller.username as seller_username
                          FROM orders o
                          LEFT JOIN services s ON o.service_id = s.id
                          LEFT JOIN users buyer ON o.buyer_id = buyer.id
                          LEFT JOIN users seller ON o.seller_id = seller.id
                          ORDER BY o.created_at DESC
                          LIMIT :limit OFFSET :offset');
        $this->db->bind(':limit', $per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
}

