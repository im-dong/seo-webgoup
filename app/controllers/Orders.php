<?php
class Orders extends Controller {
    protected $orderModel;
    protected $serviceModel;
    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
            exit();
        }
        $this->orderModel = $this->model('Order');
        $this->serviceModel = $this->model('Service');
    }

    // 为售前咨询开启一个对话
    public function startInquiry($service_id){
        $service = $this->serviceModel->getServiceById($service_id);

        // 确保服务存在且用户不是服务所有者
        if(!$service || $service->userId == $_SESSION['user_id']){
            flash('service_message', 'Invalid service or you cannot message yourself.', 'alert alert-danger');
            header('location: ' . URLROOT . '/services');
            exit();
        }

        $buyer_id = $_SESSION['user_id'];
        $seller_id = $service->userId;

        // 检查是否已有咨询订单
        $inquiry_order = $this->orderModel->findInquiryOrder($service_id, $buyer_id, $seller_id);

        if($inquiry_order){
            $order_id = $inquiry_order->id;
        } else {
            // 创建一个新的咨询订单
            $order_id = $this->orderModel->createInquiryOrder($service_id, $buyer_id, $seller_id);
        }

        if($order_id){
            header('location: ' . URLROOT . '/conversations/start/' . $order_id);
            exit();
        } else {
            flash('service_message', 'Could not initiate conversation.', 'alert alert-danger');
            header('location: ' . URLROOT . '/services/show/' . $service_id);
            exit();
        }
    }

    // API端点：创建订单 (被PayPal JS SDK调用)
    public function create($service_id){
        // 设置响应头为JSON
        header('Content-Type: application/json');

        $service = $this->serviceModel->getServiceById($service_id);
        if(!$service){
            echo json_encode(['error' => 'Service not found']);
            return;
        }

        $data = [
            'service_id' => $service_id,
            'buyer_id' => $_SESSION['user_id'],
            'seller_id' => $service->userId,
            'amount' => $service->price
        ];

        $order_id = $this->orderModel->createOrder($data);

        if($order_id){
            // 创建服务快照
            $this->orderModel->createServiceSnapshot($order_id, $service);
            echo json_encode(['orderID' => $order_id]);
        } else {
            echo json_encode(['error' => 'Failed to create order']);
        }
    }

    // API端点：捕获支付 (被PayPal JS SDK调用)
    public function capture($order_id){
        header('Content-Type: application/json');
        $paypal_order_id = json_decode(file_get_contents('php://input'))->orderID;

        // 获取服务和卖家信息
        $service = $this->serviceModel->getServiceByOrderId($order_id);

        if($this->orderModel->captureOrder($order_id, $paypal_order_id)){
            // 将资金添加到卖家的总余额中
            $this->walletModel = $this->model('Wallet');
            $this->walletModel->addFundsToTotalBalance($service->user_id, $service->price);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function complete($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'order_id' => $order_id,
                'proof_url' => trim($_POST['proof_url']),
                'proof_url_err' => ''
            ];

            if(empty($data['proof_url'])){
                $data['proof_url_err'] = 'Please provide the proof URL.';
            } elseif(!filter_var($data['proof_url'], FILTER_VALIDATE_URL)){
                $data['proof_url_err'] = 'Please provide a valid URL.';
            }

            if(empty($data['proof_url_err'])){
                if($this->orderModel->markAsComplete($order_id, $data['proof_url'])){
                    flash('order_message', 'Order marked as complete. Funds will be released after the duration period.');
                    header('location: ' . URLROOT . '/users/dashboard');
                } else {
                    die('Something went wrong.');
                }
            } else {
                $this->view('orders/complete', $data);
            }

        } else {
            $data = [
                'order_id' => $order_id,
                'proof_url' => '',
                'proof_url_err' => ''
            ];
            $this->view('orders/complete', $data);
        }
    }

    public function confirm($order_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->orderModel->confirmOrder($order_id, $_SESSION['user_id'])){
                flash('order_message', 'Order has been confirmed successfully.');
                header('location: ' . URLROOT . '/users/dashboard');
            } else {
                die('Something went wrong or you are not authorized.');
            }
        } else {
            header('location: ' . URLROOT . '/users/dashboard');
        }
    }

    public function details($order_id){
        $order = $this->orderModel->getOrderById($order_id);
        $snapshot = $this->orderModel->getSnapshotByOrderId($order_id);
        $this->conversationModel = $this->model('Conversation');
        $conversation = $this->conversationModel->getConversationByOrderId($order_id);

        // Authorization check
        if(!$order || ($order->buyer_id != $_SESSION['user_id'] && $order->seller_id != $_SESSION['user_id'])){
            flash('order_message', 'You are not authorized to view this page.', 'alert alert-danger');
            header('location: ' . URLROOT . '/users/dashboard');
            exit();
        }

        $data = [
            'order' => $order,
            'snapshot' => $snapshot,
            'conversation' => $conversation
        ];

        $this->view('orders/details', $data);
    }
}
