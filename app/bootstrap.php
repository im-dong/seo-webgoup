<?php
// 定义App根目录
define('APPROOT', dirname(__DIR__));

// 加载配置文件
require_once APPROOT . '/config/config.php';
// 加载助手
require_once APPROOT . '/app/helpers/session_helper.php';

// 自动加载核心库
spl_autoload_register(function($className){
    // 检查核心库中是否存在该类文件
    $core_file = APPROOT . '/app/core/' . $className . '.php';
    if(file_exists($core_file)){
        require_once $core_file;
    }
});

// 启动核心路由类
$init = new Core();
