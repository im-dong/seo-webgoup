<?php
class User {
    private $db;

    public function __construct(){
        // 实例化数据库类
        $this->db = new Database;
    }

    // 通过邮箱查找用户
    public function findUserByEmail($email){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // 检查行数
        if($this->db->rowCount() > 0){
            return true;
        }
        return false;
    }

    // 注册用户
    public function register($data){
        $this->db->query('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
        // 绑定数值
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // 执行
        if($this->db->execute()){
            return $this->db->lastInsertId();
        }
        return false;
    }

    // 登录用户
    public function login($email, $password){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        $hashed_password = $row->password;
        if(password_verify($password, $hashed_password)){
            return $row;
        } else {
            return false;
        }
    return false;
    }

    // 根据ID获取用户
    public function getUserById($id){
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // 更新用户资料
    public function updateProfile($data){
        $this->db->query('UPDATE users SET bio = :bio WHERE id = :id');
        $this->db->bind(':bio', $data['bio']);
        $this->db->bind(':id', $data['id']);
        if($this->db->execute()){
            return true;
        }
        return false;
    }
}
