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

    // 通过用户名查找用户
    public function findUserByUsername($username){
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // 检查行数
        if($this->db->rowCount() > 0){
            return true;
        }
        return false;
    }

    // 保存验证码
    public function saveVerificationCode($email, $code, $expiresAt){
        // 先删除该邮箱的旧验证码
        $this->db->query('DELETE FROM email_verifications WHERE email = :email');
        $this->db->bind(':email', $email);
        $this->db->execute();

        // 插入新验证码
        $this->db->query('INSERT INTO email_verifications (email, verification_code, expires_at) VALUES (:email, :code, :expires_at)');
        $this->db->bind(':email', $email);
        $this->db->bind(':code', $code);
        $this->db->bind(':expires_at', $expiresAt);

        return $this->db->execute();
    }

    // 验证验证码
    public function verifyCode($email, $code){
        // 使用UTC时间而不是NOW()，避免时区问题
        $currentTime = gmdate('Y-m-d H:i:s');
        $this->db->query('SELECT * FROM email_verifications WHERE email = :email AND verification_code = :code AND expires_at > :currentTime AND is_used = 0');
        $this->db->bind(':email', $email);
        $this->db->bind(':code', $code);
        $this->db->bind(':currentTime', $currentTime);

        $row = $this->db->single();

        if($this->db->rowCount() > 0){
            // 标记验证码已使用
            $this->db->query('UPDATE email_verifications SET is_used = 1 WHERE id = :id');
            $this->db->bind(':id', $row->id);
            $this->db->execute();

            return true;
        }
        return false;
    }

    // 通过用户名或邮箱查找用户（用于登录）
    public function findUserByUsernameOrEmail($usernameOrEmail){
        $this->db->query('SELECT * FROM users WHERE username = :username OR email = :email');
        $this->db->bind(':username', $usernameOrEmail);
        $this->db->bind(':email', $usernameOrEmail);

        $row = $this->db->single();

        if($this->db->rowCount() > 0){
            return $row;
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

    // 登录用户（邮箱登录）
    public function login($email, $password){
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row && password_verify($password, $row->password)){
            return $row;
        } else {
            return false;
        }
    }

    // 通过用户名或邮箱登录
    public function loginByUsernameOrEmail($usernameOrEmail, $password){
        $this->db->query('SELECT * FROM users WHERE username = :username OR email = :email');
        $this->db->bind(':username', $usernameOrEmail);
        $this->db->bind(':email', $usernameOrEmail);

        $row = $this->db->single();

        if($row && password_verify($password, $row->password)){
            return $row;
        } else {
            return false;
        }
    }

    // 根据ID获取用户
    public function getUserById($id){
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // 更新用户资料
    public function updateProfile($data){
        if (count($data) <= 1) { // 只包含id，没有其他字段
            return true;
        }

        $query = 'UPDATE users SET ';
        $fields = [];
        foreach($data as $key => $value){
            if($key !== 'id'){
                $fields[] = "$key = :$key";
            }
        }
        $query .= implode(', ', $fields);
        $query .= ' WHERE id = :id';

        $this->db->query($query);

        foreach($data as $key => $value){
            $this->db->bind(":$key", $value);
        }

        if($this->db->execute()){
            return true;
        }
        return false;
    }
}