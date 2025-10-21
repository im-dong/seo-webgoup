<?php
class Users extends Controller {
    protected $userModel;
    protected $orderModel;
    protected $walletModel;
    protected $reviewModel;
    protected $serviceModel;
    protected $emailHelper;
    public function __construct(){
        $this->userModel = $this->model('User');
        $this->emailHelper = new EmailHelper();
    }

    public function register(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $data =[
                'title' => 'Register',
                'description' => 'Create an account to join our community of SEO professionals and start improving your website\'s ranking.',
                'keywords' => 'register, signup, create account, SEO community',
                'username' => trim($_POST['username']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'verification_code' => isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '',
                'terms' => isset($_POST['terms']) ? trim($_POST['terms']) : '',
                'username_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'verification_code_err' => '',
                'terms_err' => '',
                'step' => isset($_POST['step']) ? $_POST['step'] : '1'
            ];

            // 第一步：验证基本信息
            if($data['step'] == '1'){
                if(empty($data['email'])){ $data['email_err'] = 'Please enter email'; }
                else { if($this->userModel->findUserByEmail($data['email'])){ $data['email_err'] = 'Email is already taken'; }}
                if(empty($data['username'])){ $data['username_err'] = 'Please enter username'; }
                else { if($this->userModel->findUserByUsername($data['username'])){ $data['username_err'] = 'Username is already taken'; }}
                if(empty($data['password'])){ $data['password_err'] = 'Please enter password'; }
                elseif(strlen($data['password']) < 6){ $data['password_err'] = 'Password must be at least 6 characters'; }
                if(empty($data['confirm_password'])){ $data['confirm_password_err'] = 'Please confirm password'; }
                else { if($data['password'] != $data['confirm_password']){ $data['confirm_password_err'] = 'Passwords do not match'; }}
                if(empty($data['terms'])){ $data['terms_err'] = 'You must agree to the Terms of Service to register'; }

                if(empty($data['email_err']) && empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err']) && empty($data['terms_err'])){
                    // 检查是否需要邮箱验证
                    if(EMAIL_VERIFICATION_ENABLED){
                        // 生成验证码
                        $verificationCode = sprintf('%06d', mt_rand(100000, 999999));
                        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                        // 保存验证码到数据库
                        if($this->userModel->saveVerificationCode($data['email'], $verificationCode, $expiresAt)){
                            // 发送验证邮件
                            if($this->emailHelper->sendVerificationEmail($data['email'], $verificationCode)){
                                // 临时存储用户信息在session中
                                $_SESSION['temp_registration'] = [
                                    'username' => $data['username'],
                                    'email' => $data['email'],
                                    'password' => password_hash($data['password'], PASSWORD_DEFAULT)
                                ];

                                $data['step'] = '2';
                                $this->view('users/register', $data);
                            } else {
                                $data['email_err'] = 'Failed to send verification email. Please try again.';
                                $this->view('users/register', $data);
                            }
                        } else {
                            die('Something went wrong with verification code generation');
                        }
                    } else {
                        // 无需验证，直接注册
                        $userData = [
                            'username' => $data['username'],
                            'email' => $data['email'],
                            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
                        ];

                        if($user_id = $this->userModel->register($userData)){
                            // 创建钱包
                            $this->walletModel = $this->model('Wallet');
                            $this->walletModel->createWallet($user_id);

                            // 在后台异步发送欢迎邮件
                            register_shutdown_function(function() use ($data, $user_id) {
                                set_time_limit(30);
                                ignore_user_abort(true);

                                try {
                                    // 发送欢迎邮件给新用户
                                    $this->emailHelper->sendWelcomeEmail($data['email'], $data['username'], array(
                                        'user_id' => $user_id,
                                        'username' => $data['username'],
                                        'email' => $data['email']
                                    ));
                                } catch (Exception $e) {
                                    error_log("Failed to send welcome email: " . $e->getMessage());
                                }
                            });

                            flash('register_success', 'Registration successful! You can now log in.');
                            header('location: ' . URLROOT . '/users/login');
                        } else {
                            die('Something went wrong during registration');
                        }
                    }
                } else { $this->view('users/register', $data); }
            }
            // 第二步：验证邮箱验证码
            elseif($data['step'] == '2'){
                // 从POST数据中获取用户名和邮箱（如果表单重新提交）
                if(isset($_POST['username']) && isset($_POST['email'])){
                    $data['username'] = trim($_POST['username']);
                    $data['email'] = trim($_POST['email']);
                }

                if(empty($data['verification_code'])){
                    $data['verification_code_err'] = 'Please enter verification code';
                } else {
                    // 验证验证码
                    $tempRegistration = $_SESSION['temp_registration'] ?? null;
                    if($tempRegistration && $this->userModel->verifyCode($tempRegistration['email'], $data['verification_code'])){
                        // 再次检查用户名和邮箱是否仍然可用（防止在验证过程中被其他人注册）
                        if($this->userModel->findUserByUsername($tempRegistration['username'])){
                            $data['username_err'] = 'Username is already taken. Please choose a different username.';
                            $data['step'] = '2';
                            $this->view('users/register', $data);
                            return;
                        }
                        if($this->userModel->findUserByEmail($tempRegistration['email'])){
                            $data['email_err'] = 'Email is already taken. Please use a different email.';
                            $data['step'] = '2';
                            $this->view('users/register', $data);
                            return;
                        }

                        // 注册用户
                        $userData = [
                            'username' => $tempRegistration['username'],
                            'email' => $tempRegistration['email'],
                            'password' => $tempRegistration['password']
                        ];

                        if($user_id = $this->userModel->register($userData)){
                            // 创建钱包
                            $this->walletModel = $this->model('Wallet');
                            $this->walletModel->createWallet($user_id);

                            // 清除临时数据
                            unset($_SESSION['temp_registration']);

                            // 在后台异步发送欢迎邮件
                            register_shutdown_function(function() use ($tempRegistration, $user_id) {
                                set_time_limit(30);
                                ignore_user_abort(true);

                                try {
                                    // 发送欢迎邮件给新用户
                                    $this->emailHelper->sendWelcomeEmail($tempRegistration['email'], $tempRegistration['username'], array(
                                        'user_id' => $user_id,
                                        'username' => $tempRegistration['username'],
                                        'email' => $tempRegistration['email']
                                    ));
                                } catch (Exception $e) {
                                    error_log("Failed to send welcome email after verification: " . $e->getMessage());
                                }
                            });

                            flash('register_success', 'Registration successful! You can now log in.');
                            header('location: ' . URLROOT . '/users/login');
                        } else { die('Something went wrong during registration'); }
                    } else {
                        $data['verification_code_err'] = 'Invalid or expired verification code';
                        $data['step'] = '2';
                        $this->view('users/register', $data);
                    }
                }
            }
        } else {
            $data =[
                'title' => 'Register',
                'description' => 'Create an account to join our community of SEO professionals and start improving your website\'s ranking.',
                'keywords' => 'register, signup, create account, SEO community',
                'username' => '','email' => '','password' => '','confirm_password' => '','verification_code' => '', 'terms' => '',
                'username_err' => '','email_err' => '','password_err' => '','confirm_password_err' => '','verification_code_err' => '', 'terms_err' => '',
                'step' => '1'
            ];
            $this->view('users/register', $data);
        }
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $data =[
                'title' => 'Login',
                'description' => 'Login to your webGoup account to access the marketplace and your dashboard.',
                'keywords' => 'login, signin, access account, SEO marketplace',
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
            ];

            if(empty($data['email'])){ $data['email_err'] = 'Please enter email or username'; }
            if(empty($data['password'])){ $data['password_err'] = 'Please enter password'; }

            // 检查用户是否存在（支持用户名或邮箱）
            $user = $this->userModel->findUserByUsernameOrEmail($data['email']);
            if(!$user){
                $data['email_err'] = 'No user found with this email or username';
            }

            if(empty($data['email_err']) && empty($data['password_err'])){
                $loggedInUser = $this->userModel->loginByUsernameOrEmail($data['email'], $data['password']);
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
            $data =[
                'title' => 'Login',
                'description' => 'Login to your webGoup account to access the marketplace and your dashboard.',
                'keywords' => 'login, signin, access account, SEO marketplace',
                'email' => '','password' => '','email_err' => '','password_err' => ''
            ];
            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->username;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_avatar'] = $user->profile_image_url ?? 'default.png'; // Store avatar URL
        unset($_SESSION['legal_notice_shown']); // Reset legal notice flag for new login
        header('location: ' . URLROOT);
    }

    public function markLegalNoticeShown(){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_SESSION['legal_notice_shown'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

public function logout(){
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_avatar']);
    unset($_SESSION['legal_notice_shown']);
    session_destroy();
    header('location: ' . URLROOT . '/users/login');
    }

    public function dashboard(){
        $this->orderModel = $this->model('Order');
        $this->walletModel = $this->model('Wallet');
        $this->reviewModel = $this->model('Review'); // 加载Review模型
        $this->serviceModel = $this->model('Service'); // 加载Service模型

        // 分页设置
        $current_page = getCurrentPage();
        $per_page = 10; // 每页显示10个订单

        // 获取买家订单分页信息
        $total_buyer_orders = $this->orderModel->getOrdersByBuyerIdCount($_SESSION['user_id']);
        $buyer_pagination = calculatePagination($total_buyer_orders, $current_page, $per_page);

        // 获取分页后的买家订单
        $buyer_orders = $this->orderModel->getOrdersByBuyerId($_SESSION['user_id'], [
            'per_page' => $buyer_pagination['per_page'],
            'offset' => $buyer_pagination['offset']
        ]);
        foreach($buyer_orders as $order){
            $order->has_reviewed = $this->reviewModel->hasReviewed($order->id);
        }

        // 获取卖家订单分页信息
        $total_seller_orders = $this->orderModel->getOrdersBySellerIdCount($_SESSION['user_id']);
        $seller_pagination = calculatePagination($total_seller_orders, $current_page, $per_page);

        // 获取分页后的卖家订单
        $seller_orders = $this->orderModel->getOrdersBySellerId($_SESSION['user_id'], [
            'per_page' => $seller_pagination['per_page'],
            'offset' => $seller_pagination['offset']
        ]);
        foreach($seller_orders as $order){
            $order->has_reviewed = $this->reviewModel->hasReviewed($order->id);
        }

        $wallet = $this->walletModel->getWalletByUserId($_SESSION['user_id']);

        // 获取用户服务分页信息
        $total_services = $this->serviceModel->getServicesByUserIdCount($_SESSION['user_id']);
        $services_pagination = calculatePagination($total_services, $current_page, $per_page);

        // 获取分页后的用户服务
        $my_services = $this->serviceModel->getServicesByUserId($_SESSION['user_id'], [
            'per_page' => $services_pagination['per_page'],
            'offset' => $services_pagination['offset']
        ]);

        $data = [
            'title' => 'Dashboard',
            'description' => 'Manage your orders, services, and wallet from your personal dashboard.',
            'keywords' => 'dashboard, my account, orders, services, wallet',
            'buyer_orders' => $buyer_orders,
            'seller_orders' => $seller_orders,
            'wallet' => $wallet,
            'my_services' => $my_services,
            'buyer_pagination' => $buyer_pagination,
            'seller_pagination' => $seller_pagination,
            'services_pagination' => $services_pagination,
            'base_url' => URLROOT . '/users/dashboard'
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
                'title' => 'Edit Profile',
                'description' => 'Update your profile information, including your bio, website URL, and profile picture.',
                'keywords' => 'edit profile, update profile, user settings',
                'id' => $_SESSION['user_id'],
                'profile_image_err' => '',
                'website_url_err' => ''
            ];

            // Only add fields to data if they are submitted
            if(isset($_POST['bio'])){
                $data['bio'] = trim($_POST['bio']);
            }
            if(isset($_POST['website_url'])){
                $data['website_url'] = trim($_POST['website_url']);
            }
            if(isset($_POST['country'])){
                $data['country'] = trim($_POST['country']);
            }
            if(isset($_POST['contact_method'])){
                $data['contact_method'] = trim($_POST['contact_method']);
            }

            // Handle file upload
            if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
                $upload_dir = 'uploads/images/avatars/';
                $file_name = uniqid() . '-' . basename($_FILES['profile_image']['name']);
                $target_file = $upload_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Validation
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
                unset($data['profile_image_err']);
                unset($data['website_url_err']);
                // 移除不属于用户表的字段，避免SQL错误
                unset($data['title']);
                unset($data['description']);
                unset($data['keywords']);

                if($this->userModel->updateProfile($data)){
                    // Update session avatar if it was changed
                    if(isset($data['profile_image_url'])){
                        $_SESSION['user_avatar'] = $data['profile_image_url'];
                    }
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
                'title' => 'Edit Profile',
                'description' => 'Update your profile information, including your bio, website URL, and profile picture.',
                'keywords' => 'edit profile, update profile, user settings',
                'username' => $user->username,
                'email' => $user->email,
                'bio' => $user->bio,
                'profile_image_url' => $user->profile_image_url,
                'website_url' => $user->website_url,
                'country' => $user->country,
                'contact_method' => $user->contact_method,
                'profile_image_err' => '',
                'website_url_err' => ''
            ];
            $this->view('users/edit_profile', $data);
        }
    }

    public function profile($user_id){
        $this->reviewModel = $this->model('Review');
        $user = $this->userModel->getUserById($user_id);

        $seller_reviews = $this->reviewModel->getReviewsBySellerId($user_id);
        $buyer_reviews = $this->reviewModel->getReviewsByReviewerId($user_id);

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

        // Check if the logged-in user is an admin
        $is_admin_viewing = (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin');

        $data = [
            'title' => $user->username . "'s Profile",
            'description' => 'View the profile, reviews, and services of ' . $user->username . ' on webGoup.',
            'keywords' => $user->username . ', user profile, reviews, services, SEO expert',
            'user' => $user,
            'seller_reviews' => $seller_reviews,
            'buyer_reviews' => $buyer_reviews,
            'average_rating' => $average_rating,
            'order_id_for_chat' => $order_id_for_chat,
            'is_admin_viewing' => $is_admin_viewing
        ];
        $this->view('users/profile', $data);
    }

    public function changePassword(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data = [
                'title' => 'Change Password',
                'description' => 'Change your account password for better security.',
                'keywords' => 'change password, update password, account security',
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];

            // 验证当前密码
            if(empty($data['current_password'])){
                $data['current_password_err'] = 'Please enter your current password';
            } else {
                // 获取当前用户数据验证密码
                $currentUser = $this->userModel->getUserById($_SESSION['user_id']);
                if(!$currentUser || !password_verify($data['current_password'], $currentUser->password)){
                    $data['current_password_err'] = 'Current password is incorrect';
                }
            }

            // 验证新密码
            if(empty($data['new_password'])){
                $data['new_password_err'] = 'Please enter a new password';
            } elseif(strlen($data['new_password']) < 6){
                $data['new_password_err'] = 'Password must be at least 6 characters';
            }

            // 验证确认密码
            if(empty($data['confirm_password'])){
                $data['confirm_password_err'] = 'Please confirm your new password';
            } elseif($data['new_password'] != $data['confirm_password']){
                $data['confirm_password_err'] = 'Passwords do not match';
            }

            // 如果没有错误，更新密码
            if(empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])){
                // 生成新密码哈希
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);

                // 更新密码
                if($this->userModel->updatePassword($_SESSION['user_id'], $hashedPassword)){
                    flash('user_message', 'Password updated successfully!', 'alert alert-success');
                    header('location: ' . URLROOT . '/users/dashboard');
                    exit();
                } else {
                    die('Something went wrong. Please try again.');
                }
            }

            // 如果有错误，重新显示表单
            $this->view('users/change_password', $data);

        } else {
            // GET请求，显示表单
            $data = [
                'title' => 'Change Password',
                'description' => 'Change your account password for better security.',
                'keywords' => 'change password, update password, account security',
                'current_password' => '',
                'new_password' => '',
                'confirm_password' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_password_err' => ''
            ];
            $this->view('users/change_password', $data);
        }
    }

    public function emailTest() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $testEmail = trim($_POST['test_email']);
            $result = '';
            $error = '';

            if (empty($testEmail)) {
                $error = 'Please enter an email address';
            } elseif (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address';
            } else {
                try {
                    $subject = 'Test Email from WebGoup - ' . date('Y-m-d H:i:s');
                    $message = '<h1>Test Email</h1><p>This is a test email from WebGoup system.</p><p>Test time: ' . date('Y-m-d H:i:s') . '</p>';

                    if ($this->emailHelper->sendEmail($testEmail, $subject, $message, true)) {
                        $result = 'success';
                    } else {
                        $result = 'error';
                        $error = 'Failed to send email. Please check server logs.';
                    }
                } catch (Exception $e) {
                    $result = 'error';
                    $error = 'Email sending error: ' . $e->getMessage();
                }
            }

            $data = array(
                'title' => 'Email Test Result',
                'result' => $result,
                'error' => $error,
                'test_email' => $testEmail
            );
        } else {
            $data = array(
                'title' => 'Email Test',
                'result' => '',
                'error' => '',
                'test_email' => ''
            );
        }

        $this->view('users/email_test', $data);
    }
}
