<?php
class Orders extends Controller {
    protected $orderModel;
    protected $serviceModel;
    protected $walletModel;
    protected $conversationModel;
    protected $userModel;

    public function __construct(){
        // Models are loaded here, but authentication checks are moved to specific methods.
        $this->orderModel = $this->model('Order');
        $this->serviceModel = $this->model('Service');
        $this->walletModel = $this->model('Wallet');
    }

    // 为售前咨询开启一个对话
    public function startInquiry($service_id){
        if(!isLoggedIn()){ header('location: ' . URLROOT . '/users/login'); exit(); }

        $service = $this->serviceModel->getServiceById($service_id);

        if(!$service || $service->userId == $_SESSION['user_id']){
            flash('service_message', 'Invalid service or you cannot message yourself.', 'alert alert-danger');
            header('location: ' . URLROOT . '/services');
            exit();
        }

        $buyer_id = $_SESSION['user_id'];
        $seller_id = $service->userId;

        $inquiry_order = $this->orderModel->findInquiryOrder($service_id, $buyer_id, $seller_id);

        if($inquiry_order){
            $order_id = $inquiry_order->id;
        } else {
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

    // 从服务页面直接创建订单并跳转到PayPal
    public function create_and_pay($service_id) {
        if (!isLoggedIn()) { header('Location: ' . URLROOT . '/users/login'); exit(); }

        $service = $this->serviceModel->getServiceById($service_id);
        if (!$service || $service->userId == $_SESSION['user_id']) {
            flash('service_message', 'Invalid service or you cannot purchase your own service.', 'alert alert-danger');
            header('Location: ' . URLROOT . '/services');
            exit();
        }

        $data = [
            'service_id' => $service_id,
            'buyer_id'   => $_SESSION['user_id'],
            'seller_id'  => $service->userId,
            'amount'     => $service->price
        ];

        $order_id = $this->orderModel->createOrder($data);

        if ($order_id) {
            $this->orderModel->createServiceSnapshot($order_id, $service);
            // 重定向到统一的支付方法
            header('Location: ' . URLROOT . '/orders/pay/' . $order_id);
            exit();
        } else {
            flash('service_message', 'Could not create order. Please try again.', 'alert alert-danger');
            header('Location: ' . URLROOT . '/services/show/' . $service_id);
            exit();
        }
    }

    // 统一的PayPal支付方法 - 可以被任何地方调用
    public function pay($order_id) {
        if (!isLoggedIn()) {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $order = $this->orderModel->getOrderById($order_id);

        // 检查订单是否存在且属于当前用户
        if (!$order || $order->buyer_id != $_SESSION['user_id']) {
            flash('order_message', 'Invalid order or you are not authorized.', 'alert alert-danger');
            header('Location: ' . URLROOT . '/users/dashboard');
            exit();
        }

        // 检查订单是否已经支付
        if ($order->status == 'paid' || $order->paid_at) {
            flash('order_message', 'This order has already been paid.', 'alert alert-warning');
            header('Location: ' . URLROOT . '/orders/details/' . $order_id);
            exit();
        }

        // 获取服务信息用于PayPal描述
        $service = $this->serviceModel->getServiceById($order->service_id);
        if (!$service) {
            flash('order_message', 'Service not found.', 'alert alert-danger');
            header('Location: ' . URLROOT . '/users/dashboard');
            exit();
        }

        // 构建PayPal支付URL
        $amount = number_format($order->amount, 2, '.', '');
        $return_url = URLROOT . '/orders/details/' . $order_id;
        $paypal_url = PAYPAL_URL . '?notify_url=' . PAYPAL_NOTIFY_URL . '&cmd=_xclick&business=' . PAYPAL_RECEIVER_EMAIL . '&item_name=' . $order_id . '&amount=' . $amount . '&currency_code=USD&return=' . $return_url . '&custom=wg_' . $order_id;

        // 记录发送到PayPal的请求
        $paypal_data = [
            'notify_url'    => PAYPAL_NOTIFY_URL,
            'cmd'           => '_xclick',
            'business'      => PAYPAL_RECEIVER_EMAIL,
            'item_name'     => $order_id,
            'amount'        => $amount,
            'currency_code' => 'USD',
            'return'        => $return_url,
            'custom'        => 'wg_' . $order_id
        ];
        $this->log_paypal_request($order_id, $paypal_data, $paypal_url);

        header('Location: ' . $paypal_url);
        exit();
    }

    // API端点：创建订单 (被PayPal JS SDK调用) - 保留但现在需要登录检查
    public function create($service_id){
        if(!isLoggedIn()){ http_response_code(401); echo json_encode(['error' => 'Unauthorized']); return; }

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
            $this->orderModel->createServiceSnapshot($order_id, $service);
            echo json_encode(['orderID' => $order_id]);
        } else {
            echo json_encode(['error' => 'Failed to create order']);
        }
    }

    // API端点：捕获支付 (被PayPal JS SDK调用) - 保留但现在需要登录检查
    public function capture($order_id){
        if(!isLoggedIn()){ http_response_code(401); echo json_encode(['error' => 'Unauthorized']); return; }

        header('Content-Type: application/json');
        $paypal_order_id = json_decode(file_get_contents('php://input'))->orderID;
        $service = $this->serviceModel->getServiceByOrderId($order_id);

        if($this->orderModel->captureOrder($order_id, $paypal_order_id)){
            $this->walletModel->addFundsToTotalBalance($service->user_id, $service->price, $order_id);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }

    public function complete($order_id){
        if(!isLoggedIn()){ header('location: ' . URLROOT . '/users/login'); exit(); }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
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
        if(!isLoggedIn()){ header('location: ' . URLROOT . '/users/login'); exit(); }

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
        if(!isLoggedIn()){ header('location: ' . URLROOT . '/users/login'); exit(); }

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

        // Get seller and buyer info
        $this->userModel = $this->model('User');
        $seller = $this->userModel->getUserById($order->seller_id);
        $buyer = $this->userModel->getUserById($order->buyer_id);
        $currentUser = $this->userModel->getUserById($_SESSION['user_id']);

        // Get service creator info
        $service = $this->serviceModel->getServiceById($order->service_id);
        $serviceCreator = $service ? $this->userModel->getUserById($service->userId) : null;

        $data = [
            'title' => 'Order Details',
            'description' => 'View the details of your order.',
            'keywords' => 'order details, order information, transaction',
            'order' => $order,
            'snapshot' => $snapshot,
            'service' => $service,
            'conversation' => $conversation,
            'seller' => $seller,
            'buyer' => $buyer,
            'currentUser' => $currentUser,
            'serviceCreator' => $serviceCreator
        ];

        $this->view('orders/details', $data);
    }

    public function ipn() {
        // No login check here - this is the entry point for PayPal
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

        $this->log_ipn('IPN Received. Sending validation request to PayPal.');

        $ch = curl_init(PAYPAL_URL);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        if (!($res = curl_exec($ch))) {
            $this->log_ipn("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        $this->log_ipn('IPN Validation Response: ' . $res);

        if (strcmp($res, "VERIFIED") == 0) {
            $this->log_ipn('IPN VERIFIED. Processing payment...');

            $order_id = isset($_POST['custom']) ? (int)$_POST['custom'] : 0;
            $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : '';
            $receiver_email = isset($_POST['receiver_email']) ? $_POST['receiver_email'] : '';
            $txn_id = isset($_POST['txn_id']) ? $_POST['txn_id'] : '';
            $mc_gross = isset($_POST['mc_gross']) ? (float)$_POST['mc_gross'] : 0.0;

            $order = $this->orderModel->getOrderById($order_id);

            if ($order && $order->status != 'paid' && $payment_status == 'Completed' && $receiver_email == PAYPAL_RECEIVER_EMAIL && $mc_gross == (float)$order->amount) {
                $existing_order = $this->orderModel->getOrderByPaymentId($txn_id);
                if ($existing_order) {
                    $this->log_ipn("Duplicate transaction ID: $txn_id. Aborting.");
                    http_response_code(200);
                    exit();
                }

                if ($this->orderModel->captureOrder($order_id, $txn_id)) {
                    $this->log_ipn("Order $order_id successfully updated.");

                    if ($this->walletModel->addFundsToTotalBalance($order->seller_id, $order->amount, $order_id)) {
                        $this->log_ipn("Funds for order $order_id added to seller $order->seller_id.");
                    } else {
                        $this->log_ipn("Failed to add funds to seller wallet for order $order_id.");
                    }
                } else {
                    $this->log_ipn("Failed to update order $order_id.");
                }
            } else {
                $this->log_ipn('Payment data validation failed or order already paid.');
                $log_data = [
                    'order_id' => $order_id,
                    'order_status' => $order ? $order->status : 'Order not found',
                    'payment_status' => $payment_status,
                    'receiver_email' => $receiver_email,
                    'expected_email' => PAYPAL_RECEIVER_EMAIL,
                    'mc_gross' => $mc_gross,
                    'order_amount' => $order ? $order->amount : 'Order not found'
                ];
                $this->log_ipn(print_r($log_data, true));
            }
        } else if (strcmp($res, "INVALID") == 0) {
            $this->log_ipn("IPN INVALID.");
        }

        http_response_code(200);
    }

    private function log_ipn($message) {
        $log_file = APPROOT . '/doc/ipn.log';
        $log_message = "[" . date("Y-m-d H:i:s") . "] " . $message . "\n";
        error_log($log_message, 3, $log_file);
    }

    private function log_paypal_request($order_id, $paypal_data, $paypal_url) {
        $log_file = APPROOT . '/doc/paypal_requests.log';
        $log_message = "[" . date("Y-m-d H:i:s") . "] PayPal Request Sent for Order #$order_id\n";
        $log_message .= "=====================================================================\n";
        $log_message .= "Order ID: $order_id\n";
        $log_message .= "User ID: " . $_SESSION['user_id'] . "\n";
        $log_message .= "Username: " . $_SESSION['user_name'] . "\n";
        $log_message .= "User Email: " . ($_SESSION['user_email'] ?? 'N/A') . "\n";
        $log_message .= "User IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "\n";
        $log_message .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . "\n";
        $log_message .= "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
        $log_message .= "HTTP Referer: " . ($_SERVER['HTTP_REFERER'] ?? 'N/A') . "\n";
        $log_message .= "\n";
        $log_message .= "PayPal Data Parameters:\n";
        $log_message .= "---------------------------------------------------------------------\n";
        foreach ($paypal_data as $key => $value) {
            $log_message .= "$key: $value\n";
        }
        $log_message .= "\n";
        $log_message .= "Complete PayPal URL:\n";
        $log_message .= "---------------------------------------------------------------------\n";
        $log_message .= $paypal_url . "\n";
        $log_message .= "=====================================================================\n\n";

        error_log($log_message, 3, $log_file);
    }
}
