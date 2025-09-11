<?php
class Wallets extends Controller {
    protected $walletModel;
    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
        }
        $this->walletModel = $this->model('Wallet');
    }

    public function index(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 处理提现请求
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $wallet = $this->walletModel->getWalletByUserId($_SESSION['user_id']);

            $data = [
                'amount' => trim($_POST['amount']),
                'paypal_email' => trim($_POST['paypal_email']),
                'wallet' => $wallet,
                'amount_err' => '',
                'paypal_email_err' => ''
            ];

            if(empty($data['amount'])){ $data['amount_err'] = 'Please enter amount'; }
            elseif(!is_numeric($data['amount'])){ $data['amount_err'] = 'Invalid amount'; }
            elseif($data['amount'] > $wallet->withdrawable_balance){ $data['amount_err'] = 'Insufficient withdrawable balance'; }

            if(empty($data['paypal_email'])){ $data['paypal_email_err'] = 'Please enter PayPal email'; }
            elseif(!filter_var($data['paypal_email'], FILTER_VALIDATE_EMAIL)){ $data['paypal_email_err'] = 'Invalid email format'; }

            if(empty($data['amount_err']) && empty($data['paypal_email_err'])){
                if($this->walletModel->requestWithdrawal($_SESSION['user_id'], $data['amount'], $data['paypal_email'])){
                    flash('wallet_message', 'Your withdrawal request has been submitted.');
                    header('location: ' . URLROOT . '/wallets');
                } else {
                    die('Something went wrong');
                }
            } else {
                $this->view('wallets/index', $data);
            }

        } else {
            $wallet = $this->walletModel->getWalletByUserId($_SESSION['user_id']);
            $data = [
                'wallet' => $wallet,
                'amount' => '',
                'paypal_email' => '',
                'amount_err' => '',
                'paypal_email_err' => ''
            ];
            $this->view('wallets/index', $data);
        }
    }
}
