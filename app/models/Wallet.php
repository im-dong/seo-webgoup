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
    public function addFundsToTotalBalance($user_id, $amount){
        $this->db->query('UPDATE user_wallets SET total_balance = total_balance + :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // 解锁资金 (从总余额移动到可提现余额)
    public function moveFundsToWithdrawable($user_id, $amount){
        $this->db->query('UPDATE user_wallets SET total_balance = total_balance - :amount, withdrawable_balance = withdrawable_balance + :amount WHERE user_id = :user_id');
        $this->db->bind(':amount', $amount);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
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
            return true;
        }
        return false;
    }
}
