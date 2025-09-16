<?php
class Service {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 添加新服务
    public function addService($data){
        $this->db->query('INSERT INTO services (user_id, title, description, thumbnail_url, site_url, price, delivery_time, link_type, service_category, industry_id, is_adult_allowed, is_new_window, duration, is_official) VALUES (:user_id, :title, :description, :thumbnail_url, :site_url, :price, :delivery_time, :link_type, :service_category, :industry_id, :is_adult_allowed, :is_new_window, :duration, :is_official)');
        
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
        $this->db->bind(':is_official', $data['is_official']);

        // 执行
        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 获取所有服务
    public function getServices($filters = [], $pagination = []){
        $sql = 'SELECT s.*, s.id as serviceId, u.id as userId, u.username, i.name as industry_name
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

        // 添加分页 - 确保即使值为0也应用LIMIT
        if (isset($pagination['per_page']) && isset($pagination['offset'])) {
            $per_page = intval($pagination['per_page']);
            $offset = intval($pagination['offset']);
            if ($per_page > 0) {
                $sql .= ' LIMIT ' . $per_page . ' OFFSET ' . $offset;
            }
        }

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

    // 获取服务总数（用于分页）
    public function getServicesCount($filters = []){
        $sql = 'SELECT COUNT(*) as total
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

        $this->db->query($sql);

        if(!empty($filters['category'])){
            if($filters['category'] == 'guest_post' || $filters['category'] == 'backlink'){
                $this->db->bind(':category', $filters['category']);
            }
        }

        if(!empty($filters['industry_id'])){
            $this->db->bind(':industry_id', $filters['industry_id'], PDO::PARAM_INT);
        }

        $result = $this->db->single();
        return $result->total;
    }

    // 通过ID获取单个服务
    public function getServiceById($id){
        $sql = 'SELECT s.*, s.id as serviceId, u.id as userId, u.username, u.role, i.name as industry_name
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
    public function getServicesByUserId($user_id, $pagination = []){
        $sql = 'SELECT s.*, s.id as serviceId, u.username, i.name as industry_name
                FROM services s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN industries i ON s.industry_id = i.id
                WHERE s.user_id = :user_id';

        // 添加分页 - 确保即使值为0也应用LIMIT
        if (isset($pagination['per_page']) && isset($pagination['offset'])) {
            $per_page = intval($pagination['per_page']);
            $offset = intval($pagination['offset']);
            if ($per_page > 0) {
                $sql .= ' LIMIT ' . $per_page . ' OFFSET ' . $offset;
            }
        }

        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        $results = $this->db->resultSet();
        return $results;
    }

    // 获取用户服务总数（用于分页）
    public function getServicesByUserIdCount($user_id){
        $sql = 'SELECT COUNT(*) as total
                FROM services s
                WHERE s.user_id = :user_id';
        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        $result = $this->db->single();
        return $result->total;
    }

    public function updateService($data){
        $this->db->query('UPDATE services SET title = :title, description = :description, thumbnail_url = :thumbnail_url, site_url = :site_url, price = :price, delivery_time = :delivery_time, link_type = :link_type, service_category = :service_category, industry_id = :industry_id, is_adult_allowed = :is_adult_allowed, is_new_window = :is_new_window, duration = :duration, is_official = :is_official WHERE id = :id');
        
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
        $this->db->bind(':is_official', $data['is_official']);

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