<?php
class Conversations extends Controller {
    protected $conversationModel;
    protected $orderModel;
    protected $serviceModel;

    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
            exit();
        }
        $this->conversationModel = $this->model('Conversation');
        $this->orderModel = $this->model('Order');
        $this->serviceModel = $this->model('Service');
    }

    // 显示用户的所有对话列表
    public function index(){
        $conversations = $this->conversationModel->getGroupedConversationsByUserId($_SESSION['user_id']);
        $data = [
            'title' => 'Messages',
            'description' => 'View your conversations with other users.',
            'keywords' => 'messages, conversations, inbox',
            'conversations' => $conversations
        ];
        $this->view('conversations/index', $data);
    }

    // 显示单个对话及其消息，并处理发送消息
    public function show($conversation_id){
        $conversation = $this->conversationModel->getConversationById($conversation_id);

        // 确保当前用户是对话的参与者
        if(!$conversation || ($conversation->buyer_id != $_SESSION['user_id'] && $conversation->seller_id != $_SESSION['user_id'])){
            flash('conversation_message', 'You are not authorized to view this conversation.', 'alert alert-danger');
            header('location: ' . URLROOT . '/conversations');
            exit();
        }

        // 处理发送消息
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $message_text = trim($_POST['message_text']);

            if(empty($message_text)){
                flash('conversation_message', 'Message cannot be empty.', 'alert alert-danger');
                header('location: ' . URLROOT . '/conversations/show/' . $conversation_id);
                exit();
            }

            if($this->conversationModel->addMessage($conversation_id, $_SESSION['user_id'], $message_text)){
                // 消息发送成功后，刷新页面
                header('location: ' . URLROOT . '/conversations/show/' . $conversation_id);
                exit();
            } else {
                flash('conversation_message', 'Failed to send message.', 'alert alert-danger');
                header('location: ' . URLROOT . '/conversations/show/' . $conversation_id);
                exit();
            }
        }

        // 标记消息为已读 (简化处理)
        $this->conversationModel->markMessagesAsRead($conversation_id, $_SESSION['user_id']);

        $data = [
            'title' => 'Conversation',
            'description' => 'View your conversation with another user.',
            'keywords' => 'conversation, messages, chat',
            'conversation' => $conversation
        ];
        $this->view('conversations/show', $data);
    }

    // 从订单页面创建或跳转到对话
    public function start($order_id){
        $order = $this->orderModel->getOrderById($order_id); // 假设OrderModel有getOrderById方法

        if(!$order || ($order->buyer_id != $_SESSION['user_id'] && $order->seller_id != $_SESSION['user_id'])){
            flash('conversation_message', 'You are not authorized to start a conversation for this order.', 'alert alert-danger');
            header('location: ' . URLROOT . '/users/dashboard');
            exit();
        }

        $conversation_id = $this->conversationModel->getOrCreateConversation($order_id, $order->buyer_id, $order->seller_id);

        if($conversation_id){
            header('location: ' . URLROOT . '/conversations/show/' . $conversation_id);
            exit();
        } else {
            flash('conversation_message', 'Failed to start conversation.', 'alert alert-danger');
            header('location: ' . URLROOT . '/users/dashboard');
            exit();
        }
    }

    // API: 获取新消息
    public function getMessages($conversation_id, $last_message_id = 0){
        header('Content-Type: application/json');
        $messages = $this->conversationModel->getMessagesAfter($conversation_id, $last_message_id);
        echo json_encode($messages);
    }
}
