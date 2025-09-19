<?php
/*
 * PDO 数据库类
 * 连接数据库，绑定数值，返回结果集和行
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; // Database Handler
    private $stmt; // Statement
    private $error;

    public function __construct(){
        // 设置 DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
        );

        // 创建 PDO 实例
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // 预处理语句
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }

    // 绑定数值
    public function bind($param, $value, $type = null){
        if(is_null($type)){
            switch(true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // 执行预处理语句
    public function execute(){
        return $this->stmt->execute();
    }

    // 获取结果集，作为对象数组
    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // 获取单个记录作为对象
    public function single(){
        $this->execute();
        return $this->stmt->fetch();
    }

    // 获取行数
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    // 获取最后插入的ID
    public function lastInsertId(){
        return $this->dbh->lastInsertId();
    }
}
