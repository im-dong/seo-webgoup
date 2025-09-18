<?php
class Admin extends Controller {
    private $walletModel;

    public function __construct(){
        // IMPORTANT: Add admin authentication here
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
            flash('message', 'You are not authorized to view this page', 'alert alert-danger');
            header('location: ' . URLROOT);
            exit();
        }
    }

    public function index(){
        // 重定向到统一的admin panel
        header('Location: ' . URLROOT . '/newsletter/admin');
        exit();
    }

    // 重定向到newsletter管理页面
    public function newsletter(){
        header('Location: ' . URLROOT . '/newsletter/admin');
        exit();
    }

    public function withdrawals(){
        // 重定向到统一的提现管理页面
        header('Location: ' . URLROOT . '/newsletter/withdrawals');
        exit();
    }

    public function process_withdrawal(){
        // 重定向到新的提现处理方法
        header('Location: ' . URLROOT . '/newsletter/processWithdrawal');
        exit();
    }
}
