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
                if($this->userModel->register($data)){
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

        $buyer_orders = $this->orderModel->getOrdersByBuyerId($_SESSION['user_id']);
        $seller_orders = $this->orderModel->getOrdersBySellerId($_SESSION['user_id']);
        $wallet = $this->walletModel->getWalletByUserId($_SESSION['user_id']);

        $data = [
            'buyer_orders' => $buyer_orders,
            'seller_orders' => $seller_orders,
            'wallet' => $wallet
        ];

        $this->view('users/dashboard', $data);
    }
}