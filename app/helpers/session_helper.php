<?php
// 启动会话
session_start();

// Flash 消息助手
// 示例 - flash('register_success', 'You are now registered');
// 显示 - echo flash('register_success');
function flash($name = '', $message = '', $class = 'bg-success text-white'){
    if(!empty($name)){
        if(!empty($message) && empty($_SESSION[$name])){
            $_SESSION[$name] = $message;
            $_SESSION[$name. '_class'] = $class;
        } elseif(empty($message) && !empty($_SESSION[$name])){
            $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : 'bg-success text-white';
            echo '<div class="toast-container position-fixed top-0 end-0 p-3">
                    <div id="flash-toast" class="toast align-items-center '.$class.' border-0" role="alert" aria-live="assertive" aria-atomic="true">
                      <div class="d-flex">
                        <div class="toast-body">'.
                          $_SESSION[$name].
                        '</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                      </div>
                    </div>
                  </div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name. '_class']);
        }
    }
}

// 检查用户是否登录
function isLoggedIn(){
    if(isset($_SESSION['user_id'])){
        return true;
    }
    return false;
}
