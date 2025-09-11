<?php
class Service {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 添加新服务
    public function addService($data){
        $this->db->query('INSERT INTO services (user_id, title, description, site_url, price, delivery_time, link_type, is_adult_allowed, is_new_window, duration) VALUES (:user_id, :title, :description, :site_url, :price, :delivery_time, :link_type, :is_adult_allowed, :is_new_window, :duration)');
        
        // 绑定数据
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':site_url', $data['site_url']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        $this->db->bind(':link_type', $data['link_type']);
        $this->db->bind(':is_adult_allowed', $data['is_adult_allowed']);
        $this->db->bind(':is_new_window', $data['is_new_window']);
        $this->db->bind(':duration', $data['duration']);

        // 执行
        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 获取所有服务
    public function getServices(){
        $this->db->query('SELECT *, services.id as serviceId, users.id as userId FROM services JOIN users ON services.user_id = users.id WHERE services.status = 1 ORDER BY services.created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // 通过ID获取单个服务
    public function getServiceById($id){
        $this->db->query('SELECT *, services.id as serviceId, users.id as userId FROM services JOIN users ON services.user_id = users.id WHERE services.id = :id');
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }

    // 通过订单ID获取服务信息
    public function getServiceByOrderId($order_id){
        $this->db->query('SELECT s.* FROM services s JOIN orders o ON s.id = o.service_id WHERE o.id = :order_id');
        $this->db->bind(':order_id', $order_id);
        $row = $this->db->single();
        return $row;
    return $this->db->single();
    }

    // 获取所有官方服务
    public function getOfficialServices(){
        $this->db->query('SELECT *, services.id as serviceId, users.id as userId FROM services JOIN users ON services.user_id = users.id WHERE services.is_official = 1 AND services.status = 1 ORDER BY services.created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }
}
