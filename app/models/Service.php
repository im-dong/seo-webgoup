<?php
class Service {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 添加新服务
    public function addService($data){
        $this->db->query('INSERT INTO services (user_id, title, description, thumbnail_url, site_url, price, delivery_time, link_type, service_category, industry_id, is_adult_allowed, is_new_window, duration) VALUES (:user_id, :title, :description, :thumbnail_url, :site_url, :price, :delivery_time, :link_type, :service_category, :industry_id, :is_adult_allowed, :is_new_window, :duration)');
        
        // 绑定数据
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':thumbnail_url', $data['thumbnail_url']);
        $this->db->bind(':site_url', $data['site_url']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        $this->db->bind(':link_type', $data['link_type']);
        $this->db->bind(':service_category', $data['service_category']);
        $this->db->bind(':industry_id', $data['industry_id']);
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
    public function getServices($filters = []){
        $sql = 'SELECT s.*, s.id as serviceId, u.username, i.name as industry_name 
                FROM services s 
                JOIN users u ON s.user_id = u.id 
                LEFT JOIN industries i ON s.industry_id = i.id 
                WHERE s.status = 1';
        
        if(!empty($filters['category'])){
            if($filters['category'] == 'guest_post' || $filters['category'] == 'backlink'){
                $sql .= ' AND s.service_category = :category';
            }
        }

        if(!empty($filters['industry_id'])){
            $sql .= ' AND s.industry_id = :industry_id';
        }
        
        $sql .= ' ORDER BY s.created_at DESC';

        $this->db->query($sql);

        if(!empty($filters['category'])){
            if($filters['category'] == 'guest_post' || $filters['category'] == 'backlink'){
                $this->db->bind(':category', $filters['category']);
            }
        }

        if(!empty($filters['industry_id'])){
            $this->db->bind(':industry_id', $filters['industry_id'], PDO::PARAM_INT);
        }

        $results = $this->db->resultSet();
        return $results;
    }

    // 通过ID获取单个服务
    public function getServiceById($id){
        $sql = 'SELECT s.*, s.id as serviceId, u.id as userId, u.username, i.name as industry_name 
                FROM services s 
                JOIN users u ON s.user_id = u.id 
                LEFT JOIN industries i ON s.industry_id = i.id 
                WHERE s.id = :id';
        $this->db->query($sql);
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
    }

    // 获取所有官方服务
    public function getOfficialServices(){
        $this->db->query('SELECT *, services.id as serviceId, users.id as userId FROM services JOIN users ON services.user_id = users.id WHERE services.is_official = 1 AND services.status = 1 ORDER BY services.created_at DESC');
        $results = $this->db->resultSet();
        return $results;
    }

    // 根据用户ID获取服务
    public function getServicesByUserId($user_id){
        $sql = 'SELECT s.*, s.id as serviceId, u.username, i.name as industry_name 
                FROM services s 
                JOIN users u ON s.user_id = u.id 
                LEFT JOIN industries i ON s.industry_id = i.id 
                WHERE s.user_id = :user_id';
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();
        return $results;
    }

    public function updateService($data){
        $this->db->query('UPDATE services SET title = :title, description = :description, thumbnail_url = :thumbnail_url, site_url = :site_url, price = :price, delivery_time = :delivery_time, link_type = :link_type, service_category = :service_category, industry_id = :industry_id, is_adult_allowed = :is_adult_allowed, is_new_window = :is_new_window, duration = :duration WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':thumbnail_url', $data['thumbnail_url']);
        $this->db->bind(':site_url', $data['site_url']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':delivery_time', $data['delivery_time']);
        $this->db->bind(':link_type', $data['link_type']);
        $this->db->bind(':service_category', $data['service_category']);
        $this->db->bind(':industry_id', $data['industry_id']);
        $this->db->bind(':is_adult_allowed', $data['is_adult_allowed']);
        $this->db->bind(':is_new_window', $data['is_new_window']);
        $this->db->bind(':duration', $data['duration']);

        if($this->db->execute()){
            return true;
        }
        return false;
    }

    public function deleteService($id){
        $this->db->query('DELETE FROM services WHERE id = :id');
        $this->db->bind(':id', $id);

        if($this->db->execute()){
            return true;
        }
        return false;
    }
}