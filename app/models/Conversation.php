<?php
class Conversation {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 获取用户的所有对话
    public function getConversationsByUserId($user_id){
        $this->db->query('SELECT c.*, o.service_id, s.title as service_title, 
                                    buyer.username as buyer_username, seller.username as seller_username
                             FROM conversations c
                             JOIN orders o ON c.order_id = o.id
                             JOIN services s ON o.service_id = s.id
                             JOIN users buyer ON c.buyer_id = buyer.id
                             JOIN users seller ON c.seller_id = seller.id
                             WHERE c.buyer_id = :user_id OR c.seller_id = :user_id
                             ORDER BY c.updated_at DESC');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    // 获取单个对话及其所有消息
    public function getConversationById($conversation_id){
        $this->db->query('SELECT c.*, o.service_id, s.title as service_title, s.thumbnail_url as service_thumbnail, 
                                    buyer.username as buyer_username, seller.username as seller_username
                             FROM conversations c
                             JOIN orders o ON c.order_id = o.id
                             JOIN services s ON o.service_id = s.id
                             JOIN users buyer ON c.buyer_id = buyer.id
                             JOIN users seller ON c.seller_id = seller.id
                             WHERE c.id = :conversation_id');
        $this->db->bind(':conversation_id', $conversation_id);
        $conversation = $this->db->single();

        if($conversation){
            $this->db->query('SELECT m.*, u.username as sender_username FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = :conversation_id ORDER BY m.created_at ASC');
            $this->db->bind(':conversation_id', $conversation_id);
            $messages = $this->db->resultSet();
            $conversation->messages = $messages;
        }
        return $conversation;
    }

    // 创建或获取一个对话
    public function getOrCreateConversation($order_id, $buyer_id, $seller_id){
        $this->db->query('SELECT id FROM conversations WHERE order_id = :order_id');
        $this->db->bind(':order_id', $order_id);
        $existing_conversation = $this->db->single();

        if($existing_conversation){
            return $existing_conversation->id;
        } else {
            $this->db->query('INSERT INTO conversations (order_id, buyer_id, seller_id) VALUES (:order_id, :buyer_id, :seller_id)');
            $this->db->bind(':order_id', $order_id);
            $this->db->bind(':buyer_id', $buyer_id);
            $this->db->bind(':seller_id', $seller_id);
            if($this->db->execute()){
                return $this->db->lastInsertId();
            }
            return false;
        }
    }

    // 添加一条消息
    public function addMessage($conversation_id, $sender_id, $message_text){
        $this->db->query('INSERT INTO messages (conversation_id, sender_id, message_text) VALUES (:conversation_id, :sender_id, :message_text)');
        $this->db->bind(':conversation_id', $conversation_id);
        $this->db->bind(':sender_id', $sender_id);
        $this->db->bind(':message_text', $message_text);
        return $this->db->execute();
    }

    // 标记对话中的消息为已读 (简化处理：当用户打开对话时，所有消息被视为已读)
    // 实际应用中可能需要更复杂的已读状态，例如每条消息的已读状态
    public function markMessagesAsRead($conversation_id, $user_id){
        // 这是一个简化版本，实际可能需要记录每条消息的已读状态
        // 目前只是一个占位符，表示用户已查看
        return true;
    }

    // 根据订单ID获取对话
    public function getConversationByOrderId($order_id){
        $this->db->query('SELECT * FROM conversations WHERE order_id = :order_id');
        $this->db->bind(':order_id', $order_id);
        return $this->db->single();
    }

    // 获取指定ID之后的新消息
    public function getMessagesAfter($conversation_id, $last_message_id){
        $this->db->query('SELECT m.*, u.username as sender_username FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = :conversation_id AND m.id > :last_message_id ORDER BY m.created_at ASC');
        $this->db->bind(':conversation_id', $conversation_id);
        $this->db->bind(':last_message_id', $last_message_id);
        return $this->db->resultSet();
    }

    // 获取用户的所有对话并按对方用户分组
    public function getGroupedConversationsByUserId($user_id){
        $conversations = $this->getConversationsByUserId($user_id);
        $groupedConversations = [];
        foreach ($conversations as $conversation) {
            $other_user_id = ($conversation->buyer_id == $user_id) ? $conversation->seller_id : $conversation->buyer_id;
            $other_user_username = ($conversation->buyer_id == $user_id) ? $conversation->seller_username : $conversation->buyer_username;

            if (!isset($groupedConversations[$other_user_id])) {
                $groupedConversations[$other_user_id] = [
                    'other_user_id' => $other_user_id,
                    'other_user_username' => $other_user_username,
                    'conversations' => []
                ];
            }
            $groupedConversations[$other_user_id]['conversations'][] = $conversation;
        }
        return $groupedConversations;
    }
}
