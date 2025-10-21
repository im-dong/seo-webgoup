<?php
class Conversation {
    private $db;
    private $emailHelper;

    public function __construct(){
        $this->db = new Database;
        $this->emailHelper = new EmailHelper();
    }

    // 获取用户的所有对话
    public function getConversationsByUserId($user_id, $pagination = []){
        $sql = 'SELECT c.*, o.service_id, s.title as service_title,
                                    buyer.username as buyer_username, seller.username as seller_username
                             FROM conversations c
                             JOIN orders o ON c.order_id = o.id
                             JOIN services s ON o.service_id = s.id
                             JOIN users buyer ON c.buyer_id = buyer.id
                             JOIN users seller ON c.seller_id = seller.id
                             WHERE c.buyer_id = :user_id OR c.seller_id = :user_id
                             ORDER BY c.updated_at DESC';

        // 添加分页
        if (isset($pagination['per_page']) && isset($pagination['offset'])) {
            $per_page = intval($pagination['per_page']);
            $offset = intval($pagination['offset']);
            if ($per_page > 0) {
                $sql .= ' LIMIT ' . $per_page . ' OFFSET ' . $offset;
            }
        }

        $this->db->query($sql);
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    // 获取用户对话总数（用于分页）
    public function getConversationsByUserIdCount($user_id){
        $this->db->query('SELECT COUNT(DISTINCT c.id) as total
                         FROM conversations c
                         WHERE c.buyer_id = :user_id OR c.seller_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        $result = $this->db->single();
        return $result->total;
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

        if($this->db->execute()){
            // 获取消息ID和相关信息
            $message_id = $this->db->lastInsertId();

            // 发送新消息通知给接收方
            $this->sendNewMessageNotification($conversation_id, $sender_id, $message_id, $message_text);

            return true;
        }
        return false;
    }

    // 标记对话中的消息为已读 (简化处理：当用户打开对话时，所有消息被视为已读)
    // 实际应用中可能需要更复杂的已读状态，例如每条消息的已读状态
    public function markMessagesAsRead($conversation_id, $user_id){
        $this->db->query('UPDATE messages SET is_read = 1 WHERE conversation_id = :conversation_id AND sender_id != :user_id AND is_read = 0');
        $this->db->bind(':conversation_id', $conversation_id);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
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

            // Get unread message count for this conversation
            $this->db->query('SELECT COUNT(*) as unread_count FROM messages WHERE conversation_id = :conversation_id AND sender_id != :user_id AND is_read = 0');
            $this->db->bind(':conversation_id', $conversation->id);
            $this->db->bind(':user_id', $user_id);
            $row = $this->db->single();
            $conversation->unread_count = $row ? (int)$row->unread_count : 0;

            $groupedConversations[$other_user_id]['conversations'][] = $conversation;
        }
        return $groupedConversations;
    }

    public function getUnreadMessageCount($user_id){
        $this->db->query('SELECT COUNT(*) as unread_count FROM messages m JOIN conversations c ON m.conversation_id = c.id WHERE (c.buyer_id = :user_id OR c.seller_id = :user_id) AND m.sender_id != :user_id AND m.is_read = 0');
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return $row ? (int)$row->unread_count : 0;
    }

    // 获取指定对话的未读消息数量
    public function getUnreadCountByConversation($conversation_id, $user_id){
        $this->db->query('SELECT COUNT(*) as unread_count FROM messages WHERE conversation_id = :conversation_id AND sender_id != :user_id AND is_read = 0');
        $this->db->bind(':conversation_id', $conversation_id);
        $this->db->bind(':user_id', $user_id);
        $row = $this->db->single();
        return $row ? (int)$row->unread_count : 0;
    }

    // 发送新消息通知
    private function sendNewMessageNotification($conversation_id, $sender_id, $message_id, $message_text) {
        try {
            // 获取对话详细信息
            $this->db->query('SELECT c.*, o.service_id, s.title as service_title,
                              buyer.email as buyer_email, buyer.username as buyer_name,
                              seller.email as seller_email, seller.username as seller_name,
                              sender.username as sender_name
                              FROM conversations c
                              JOIN orders o ON c.order_id = o.id
                              JOIN services s ON o.service_id = s.id
                              JOIN users buyer ON c.buyer_id = buyer.id
                              JOIN users seller ON c.seller_id = seller.id
                              JOIN users sender ON sender.id = :sender_id
                              WHERE c.id = :conversation_id');
            $this->db->bind(':conversation_id', $conversation_id);
            $this->db->bind(':sender_id', $sender_id);
            $conversationData = $this->db->single();

            if ($conversationData) {
                // 获取接收方信息
                $recipient_id = ($conversationData->buyer_id == $sender_id) ? $conversationData->seller_id : $conversationData->buyer_id;
                $recipient_email = ($conversationData->buyer_id == $sender_id) ? $conversationData->seller_email : $conversationData->buyer_email;
                $recipient_name = ($conversationData->buyer_id == $sender_id) ? $conversationData->seller_name : $conversationData->buyer_name;

                $messageInfo = array(
                    'order_id' => $conversationData->order_id,
                    'conversation_id' => $conversation_id,
                    'service_title' => $conversationData->service_title,
                    'sender_name' => $conversationData->sender_name,
                    'message_text' => $message_text,
                    'created_at' => date('Y-m-d H:i:s')
                );

                // 发送邮件通知给接收方
                if ($recipient_email) {
                    $this->emailHelper->sendNewMessageNotification(
                        $recipient_email,
                        $recipient_name,
                        $messageInfo
                    );
                }
            }
        } catch (Exception $e) {
            error_log("Failed to send new message notification: " . $e->getMessage());
        }
    }
}
