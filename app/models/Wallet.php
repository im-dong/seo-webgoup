<?php
class Wallet {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // 为新用户创建钱包
    public function createWallet($user_id){
        $this->db->query('INSERT INTO user_wallets (user_id) VALUES (:user_id)');
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // 根据用户ID获取钱包信息
    public function getWalletByUserId($user_id){
        $this->db->query('SELECT * FROM user_wallets WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    // 增加总余额 (资金进入托管)
    public function addFundsToTotalBalance($user_id, $amount, $order_id = null){
        $this->db->query('UPDATE user_wallets SET total_balance = total_balance + :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        if($this->db->execute()){
            return $this->logTransaction($user_id, 'credit', $amount, 'Funds added from order #' . $order_id, $order_id);
        }
        return false;
    }

    // 解锁资金 (从总余额移动到可提现余额)
    public function moveFundsToWithdrawable($user_id, $amount, $order_id = null){
        $this->db->query('UPDATE user_wallets SET total_balance = total_balance - :amount, withdrawable_balance = withdrawable_balance + :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        if($this->db->execute()){
            $description = 'Funds released from order #' . $order_id;
            return $this->logTransaction($user_id, 'credit', $amount, $description, $order_id);
        }
        return false;
    }

    // 请求提现
    public function requestWithdrawal($user_id, $amount, $paypal_email){
        // 1. 从可提现余额中扣除金额
        $this->db->query('UPDATE user_wallets SET withdrawable_balance = withdrawable_balance - :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        if(!$this->db->execute()){
            return false;
        }

        // 2. 在提现记录表中创建一条记录
        $this->db->query('INSERT INTO withdrawals (user_id, amount, paypal_email) VALUES (:user_id, :amount, :paypal_email)');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':paypal_email', $paypal_email);
        if($this->db->execute()){
            $withdrawal_id = $this->db->lastInsertId();
            $description = 'Withdrawal request to ' . $paypal_email;
            return $this->logTransaction($user_id, 'withdrawal_request', $amount, $description, null);
        }
        return false;
    }

    public function logTransaction($user_id, $type, $amount, $description, $order_id = null){
        $this->db->query('INSERT INTO wallet_transactions (user_id, order_id, type, amount, description) VALUES (:user_id, :order_id, :type, :amount, :description)');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':order_id', $order_id);
        $this->db->bind(':type', $type);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    public function getTransactionsByUserId($user_id){
        $this->db->query('SELECT * FROM wallet_transactions WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getWithdrawalRequests(){
        $this->db->query('SELECT w.*, u.username FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC');
        return $this->db->resultSet();
    }

    public function processWithdrawal($withdrawal_id, $status, $notes){
        $this->db->query('UPDATE withdrawals SET status = :status, notes = :notes WHERE id = :withdrawal_id');
        $this->db->bind(':status', $status);
        $this->db->bind(':notes', $notes);
        $this->db->bind(':withdrawal_id', $withdrawal_id);
        return $this->db->execute();
    }

    public function getWithdrawalById($withdrawal_id){
        $this->db->query('SELECT * FROM withdrawals WHERE id = :withdrawal_id');
        $this->db->bind(':withdrawal_id', $withdrawal_id);
        return $this->db->single();
    }

    public function returnFundsToWithdrawable($user_id, $amount){
        $this->db->query('UPDATE user_wallets SET withdrawable_balance = withdrawable_balance + :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
