<?php
// 定义App根目录
define('APPROOT', dirname(__DIR__));

// 加载配置文件
require_once APPROOT . '/config/config.php';
// 加载助手
require_once APPROOT . '/app/helpers/session_helper.php';

// 自动加载核心库
spl_autoload_register(function($className){
    $core_file = APPROOT . '/app/core/' . $className . '.php';
    $model_file = APPROOT . '/app/models/' . $className . '.php';

    if(file_exists($core_file)){
        require_once $core_file;
    } elseif(file_exists($model_file)){
        require_once $model_file;
    }
});

// 启动核心路由类
$init = new Core();
