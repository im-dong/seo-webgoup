<?php
class Users extends Controller {
    protected $userModel;
    protected $orderModel;
    protected $walletModel;
    public function __construct(){
        $this->userModel = $this->model('User');
    }

    public function register(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $data =[
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            if(empty($data['email'])){ $data['email_err'] = 'Please enter email'; }
            else { if($this->userModel->findUserByEmail($data['email'])){ $data['email_err'] = 'Email is already taken'; }}
            if(empty($data['username'])){ $data['username_err'] = 'Please enter username'; }
            if(empty($data['password'])){ $data['password_err'] = 'Please enter password'; }
            elseif(strlen($data['password']) < 6){ $data['password_err'] = 'Password must be at least 6 characters'; }
            if(empty($data['confirm_password'])){ $data['confirm_password_err'] = 'Please confirm password'; }
            else { if($data['password'] != $data['confirm_password']){ $data['confirm_password_err'] = 'Passwords do not match'; }}

            if(empty($data['email_err']) && empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if($user_id = $this->userModel->register($data)){
                    // 创建钱包
                    $this->walletModel = $this->model('Wallet');
                    $this->walletModel->createWallet($user_id);

                    flash('register_success', 'You are now registered and can log in');
                    header('location: ' . URLROOT . '/users/login');
                } else { die('Something went wrong'); }
            } else { $this->view('users/register', $data); }
        } else {
            $data =['username' => '','email' => '','password' => '','confirm_password' => '','username_err' => '','email_err' => '','password_err' => '','confirm_password_err' => ''];
            $this->view('users/register', $data);
        }
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $data =[
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',      
            ];

            if(empty($data['email'])){ $data['email_err'] = 'Please enter email'; }
            if(empty($data['password'])){ $data['password_err'] = 'Please enter password'; }

            if($this->userModel->findUserByEmail($data['email'])){
                // 用户找到
            } else {
                $data['email_err'] = 'No user found';
            }

            if(empty($data['email_err']) && empty($data['password_err'])){
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                if($loggedInUser){
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Password incorrect';
                    $this->view('users/login', $data);
                }
            } else {
                $this->view('users/login', $data);
            }
        } else {
            $data =['email' => '','password' => '','email_err' => '','password_err' => ''];
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->username;
        header('location: ' . URLROOT);
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        header('location: ' . URLROOT . '/users/login');
    }

    public function dashboard(){
        $this->orderModel = $this->model('Order');
        $this->walletModel = $this->model('Wallet');
        $this->reviewModel = $this->model('Review'); // 加载Review模型

        $buyer_orders = $this->orderModel->getOrdersByBuyerId($_SESSION['user_id']);
        foreach($buyer_orders as $order){
            $order->has_reviewed = $this->reviewModel->hasReviewed($order->id);
        }

        $seller_orders = $this->orderModel->getOrdersBySellerId($_SESSION['user_id']);
        foreach($seller_orders as $order){
            $order->has_reviewed = $this->reviewModel->hasReviewed($order->id);
        }
        
        $wallet = $this->walletModel->getWalletByUserId($_SESSION['user_id']);

        $data = [
            'buyer_orders' => $buyer_orders,
            'seller_orders' => $seller_orders,
            'wallet' => $wallet
        ];

        $this->view('users/dashboard', $data);
    }

    public function editProfile(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data = [
                'id' => $_SESSION['user_id'],
                'bio' => trim($_POST['bio']),
                'website_url' => trim($_POST['website_url']),
                'country' => trim($_POST['country']),
                'profile_image_url' => trim($_POST['current_profile_image']), // 保留当前图片
                'profile_image_err' => '',
                'website_url_err' => ''
            ];

            // 处理文件上传
            if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
                $upload_dir = 'uploads/images/avatars/';
                $file_name = uniqid() . '-' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // 验证
                if(getimagesize($_FILES['profile_image']['tmp_name']) === false) {
                    $data['profile_image_err'] = 'File is not an image.';
                } elseif ($_FILES['profile_image']['size'] > 500000) { // 500kb
                    $data['profile_image_err'] = 'Sorry, your file is too large.';
                } elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $data['profile_image_err'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
                }

                if (empty($data['profile_image_err'])) {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $data['profile_image_url'] = '/' . $target_file;
                    } else {
                        $data['profile_image_err'] = 'Sorry, there was an error uploading your file.';
                    }
                }
            }

            if(!empty($data['website_url']) && !filter_var($data['website_url'], FILTER_VALIDATE_URL)){
                $data['website_url_err'] = 'Please enter a valid website URL.';
            }

            if(empty($data['profile_image_err']) && empty($data['website_url_err'])){
                if($this->userModel->updateProfile($data)){
                    flash('profile_message', 'Profile updated successfully.');
                    header('location: ' . URLROOT . '/users/dashboard');
                } else {
                    die('Something went wrong.');
                }
            } else {
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                $data['username'] = $user->username;
                $data['email'] = $user->email;
                $this->view('users/edit_profile', $data);
            }

        } else {
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            $data = [
                'username' => $user->username,
                'email' => $user->email,
                'bio' => $user->bio,
                'profile_image_url' => $user->profile_image_url,
                'website_url' => $user->website_url,
                'country' => $user->country,
                'profile_image_err' => '',
                'website_url_err' => ''
            ];
            $this->view('users/edit_profile', $data);
        }
    }

    public function profile($user_id){
        $this->reviewModel = $this->model('Review');
        $user = $this->userModel->getUserById($user_id);
        $reviews = $this->reviewModel->getReviewsBySellerId($user_id);
        $average_rating = $this->reviewModel->getAverageRatingBySellerId($user_id);

        // 获取一个订单ID用于聊天按钮，如果当前用户是买家或卖家
        $order_id_for_chat = null;
        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id){
            $this->orderModel = $this->model('Order');
            // 尝试找到当前用户和被查看用户之间的一个订单，用于聊天
            $latest_order = $this->orderModel->getLatestOrderBetweenUsers($_SESSION['user_id'], $user_id);
            if($latest_order) {
                $order_id_for_chat = $latest_order->id;
            }
        }

        $data = [
            'user' => $user,
            'reviews' => $reviews,
            'average_rating' => $average_rating,
            'order_id_for_chat' => $order_id_for_chat
        ];
        $this->view('users/profile', $data);
    }
}
