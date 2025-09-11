<?php
/*
 * 基础控制器
 * 加载模型和视图
 */
class Controller {
    // 加载模型
    public function model($model){
        // 引入模型文件
        require_once APPROOT . '/app/models/' . $model . '.php';
        // 实例化模型
        return new $model();
    }

    // 加载视图
    public function view($view, $data = []){
        // 检查视图文件是否存在
        if(file_exists(APPROOT . '/views/' . $view . '.php')){
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            // 视图不存在
            die('View does not exist');
        }
    }
}
