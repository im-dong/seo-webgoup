<?php
class Admin extends Controller {
    private $walletModel;
    private $userModel;
    private $serviceModel;
    private $orderModel;

    public function __construct(){
        // IMPORTANT: Add admin authentication here
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin'){
            flash('message', 'You are not authorized to view this page', 'alert alert-danger');
            header('location: ' . URLROOT);
            exit();
        }

        $this->userModel = $this->model('User');
        $this->serviceModel = $this->model('Service');
        $this->orderModel = $this->model('Order');
    }

    public function index(){
        // 重定向到统一的admin panel
        header('Location: ' . URLROOT . '/newsletter/admin');
        exit();
    }

    // 重定向到newsletter管理页面
    public function newsletter(){
        header('Location: ' . URLROOT . '/newsletter/admin');
        exit();
    }

    public function withdrawals(){
        // 重定向到统一的提现管理页面
        header('Location: ' . URLROOT . '/newsletter/withdrawals');
        exit();
    }

    public function process_withdrawal(){
        // 重定向到新的提现处理方法
        header('Location: ' . URLROOT . '/newsletter/processWithdrawal');
        exit();
    }

    public function users(){
        // 分页设置
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 20;
        $offset = ($current_page - 1) * $per_page;

        // 获取用户总数
        $total_users = $this->userModel->getTotalUsersCount();

        // 获取用户列表
        $users = $this->userModel->getUsersWithPagination($per_page, $offset);

        // 计算分页信息
        $total_pages = ceil($total_users / $per_page);

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'pagination' => [
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'total_users' => $total_users
            ]
        ];

        $this->view('admin/users', $data);
    }

    public function services(){
        // 分页设置
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 20;
        $offset = ($current_page - 1) * $per_page;

        // 获取服务总数
        $total_services = $this->serviceModel->getTotalServicesCount();

        // 获取服务列表
        $services = $this->serviceModel->getServicesWithPagination($per_page, $offset);

        // 计算分页信息
        $total_pages = ceil($total_services / $per_page);

        $data = [
            'title' => 'Service Management',
            'services' => $services,
            'pagination' => [
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'total_services' => $total_services
            ]
        ];

        $this->view('admin/services', $data);
    }

    public function orders(){
        // 分页设置
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $per_page = 20;
        $offset = ($current_page - 1) * $per_page;

        // 获取订单总数
        $total_orders = $this->orderModel->getTotalOrdersCount();

        // 获取订单列表
        $orders = $this->orderModel->getAllOrdersWithPagination($per_page, $offset);

        // 计算分页信息
        $total_pages = ceil($total_orders / $per_page);

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'pagination' => [
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'total_orders' => $total_orders
            ]
        ];

        $this->view('admin/orders', $data);
    }

    public function toggleUserStatus($user_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $user = $this->userModel->getUserById($user_id);
            if(!$user){
                echo json_encode(['success' => false, 'message' => 'User not found']);
                return;
            }

            $new_status = ($user->status ?? 1) == 1 ? 0 : 1;

            if($this->userModel->updateUserStatus($user_id, $new_status)){
                echo json_encode(['success' => true, 'new_status' => $new_status]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user status']);
            }
        }
    }

    public function toggleServiceStatus($service_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $service = $this->serviceModel->getServiceById($service_id);
            if(!$service){
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }

            // 假设services表有status字段，如果没有则删除功能
            $new_status = ($service->status ?? 1) == 1 ? 0 : 1;

            if($this->serviceModel->updateServiceStatus($service_id, $new_status)){
                echo json_encode(['success' => true, 'new_status' => $new_status]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update service status']);
            }
        }
    }

    public function deleteService($service_id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $service = $this->serviceModel->getServiceById($service_id);
            if(!$service){
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }

            // 删除服务图片文件
            if(!empty($service->thumbnail_url)){
                $thumbnail_path = ltrim($service->thumbnail_url, '/');
                if(file_exists($thumbnail_path)){
                    unlink($thumbnail_path);
                }
            }

            if($this->serviceModel->deleteService($service_id)){
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
            }
        }
    }
}
