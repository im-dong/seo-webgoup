<?php
/*
 * App 核心类
 * 创建 URL & 加载核心控制器
 * URL 格式 - /controller/method/params
 */
class Core {
    protected $currentController = 'Pages'; // 默认控制器
    protected $currentMethod = 'index'; // 默认方法
    protected $params = [];

    public function __construct(){
        $url = $this->getUrl();

        // 在控制器中寻找第一个值 (controller)
        if(isset($url[0]) && file_exists(APPROOT . '/app/controllers/' . ucwords($url[0]). '.php')){
            // 如果存在，则设置为当前控制器
            $this->currentController = ucwords($url[0]);
            // Unset 0 Index
            unset($url[0]);
        }

        // 引入控制器
        require_once APPROOT . '/app/controllers/'. $this->currentController . '.php';

        // 实例化控制器类
        $this->currentController = new $this->currentController;

        // 检查 URL 的第二部分 (method)
        if(isset($url[1])){
            // 检查控制器中是否存在该方法
            if(method_exists($this->currentController, $url[1])){
                $this->currentMethod = $url[1];
                // Unset 1 index
                unset($url[1]);
            }
        }

        // 获取参数
        $this->params = $url ? array_values($url) : [];

        // 使用参数调用方法
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
        if(isset($_GET['url'])){
            $url = trim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
