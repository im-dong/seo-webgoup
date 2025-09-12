<?php
class Review {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 添加评价
    public function addReview($data){
        $this->db->query('INSERT INTO reviews (order_id, reviewer_id, seller_id, rating, comment) VALUES (:order_id, :reviewer_id, :seller_id, :rating, :comment)');
        $this->db->bind(':order_id', $data['order_id']);
        $this->db->bind(':reviewer_id', $data['reviewer_id']);
        $this->db->bind(':seller_id', $data['seller_id']);
        $this->db->bind(':rating', $data['rating']);
        $this->db->bind(':comment', $data['comment']);
        if($this->db->execute()){
            return true;
        }
        return false;
    }

    // 检查订单是否已被评价
    public function hasReviewed($order_id){
        $this->db->query('SELECT id FROM reviews WHERE order_id = :order_id');
        $this->db->bind(':order_id', $order_id);
        $this->db->single();
        return $this->db->rowCount() > 0;
    }

    // 获取卖家的所有评价
    public function getReviewsBySellerId($seller_id){
        $this->db->query('SELECT r.*, u.username as reviewer_username FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.seller_id = :seller_id ORDER BY r.created_at DESC');
        $this->db->bind(':seller_id', $seller_id);
        return $this->db->resultSet();
    }

    // 获取卖家的平均评分
    public function getAverageRatingBySellerId($seller_id){
        $this->db->query('SELECT AVG(rating) as average_rating FROM reviews WHERE seller_id = :seller_id');
        $this->db->bind(':seller_id', $seller_id);
        $result = $this->db->single();
        return $result->average_rating ? round($result->average_rating, 2) : 0;
    }

    // 获取买家的所有评价
    public function getReviewsByReviewerId($reviewer_id){
        $this->db->query('SELECT r.*, u.username as seller_username FROM reviews r JOIN users u ON r.seller_id = u.id WHERE r.reviewer_id = :reviewer_id ORDER BY r.created_at DESC');
        $this->db->bind(':reviewer_id', $reviewer_id);
        return $this->db->resultSet();
    }
}
