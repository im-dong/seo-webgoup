<?php
class Admin extends Controller {
    public function __construct(){
        // IMPORTANT: Add admin authentication here
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
            flash('message', 'You are not authorized to view this page', 'alert alert-danger');
            header('location: ' . URLROOT);
            exit();
        }
    }

    public function index(){
        $this->withdrawals();
    }

    public function withdrawals(){
        $this->walletModel = $this->model('Wallet');
        $withdrawals = $this->walletModel->getWithdrawalRequests();

        $data = [
            'withdrawals' => $withdrawals
        ];

        $this->view('admin/withdrawals', $data);
    }

    public function process_withdrawal(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->walletModel = $this->model('Wallet');

            $withdrawal_id = $_POST['withdrawal_id'];
            $status = $_POST['status'];
            $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

            $withdrawal = $this->walletModel->getWithdrawalById($withdrawal_id);

            if(!$withdrawal){
                flash('withdrawal_message', 'Withdrawal request not found.', 'alert alert-danger');
                header('location: ' . URLROOT . '/admin/withdrawals');
                exit();
            }

            if($this->walletModel->processWithdrawal($withdrawal_id, $status, $notes)){
                if($status == 'rejected'){
                    // Return funds to user's withdrawable balance
                    $this->walletModel->returnFundsToWithdrawable($withdrawal->user_id, $withdrawal->amount);
                }
                flash('withdrawal_message', 'Withdrawal request has been ' . $status . '.');
            } else {
                flash('withdrawal_message', 'Something went wrong. Please try again.', 'alert alert-danger');
            }
            header('location: ' . URLROOT . '/admin/withdrawals');
        } else {
            header('location: ' . URLROOT . '/admin/withdrawals');
        }
    }
}
