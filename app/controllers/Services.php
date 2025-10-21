<?php
class Services extends Controller {
    protected $serviceModel;
    protected $industryModel;
    protected $orderModel;
    protected $userModel;

    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
        }
        $this->serviceModel = $this->model('Service');
        $this->industryModel = $this->model('Industry');
    }

    public function index($category = null){
        // Sanitize category, allowing null
        $category = $category ? htmlspecialchars($category, ENT_QUOTES, 'UTF-8') : null;

        // Get industry from query string
        $industry_id = isset($_GET['industry']) ? filter_input(INPUT_GET, 'industry', FILTER_VALIDATE_INT) : null;

        $filters = [
            'category' => $category,
            'industry_id' => $industry_id
        ];

        // 分页设置
        $current_page = getCurrentPage();
        $per_page = 12; // 每页显示12个服务

        // 获取服务总数
        $total_services = $this->serviceModel->getServicesCount($filters);

        // 计算分页信息
        $pagination = calculatePagination($total_services, $current_page, $per_page);

        // 获取分页后的服务
        $services = $this->serviceModel->getServices($filters, [
            'per_page' => $pagination['per_page'],
            'offset' => $pagination['offset']
        ]);

        // 构建基础URL（保留GET参数）
        $base_url = URLROOT . '/services' . ($category ? '/' . $category : '');
        $get_params = [];
        if ($industry_id) {
            $get_params['industry'] = $industry_id;
        }

        $data = [
            'title' => 'Marketplace',
            'description' => 'Browse and buy SEO services from our community marketplace. Find the perfect service to boost your website\'s ranking.',
            'keywords' => 'SEO marketplace, buy SEO services, link building services',
            'services' => $services,
            'industries' => $this->industryModel->getIndustries(),
            'current_category' => $category,
            'current_industry' => $industry_id,
            'pagination' => $pagination,
            'base_url' => $base_url,
            'get_params' => $get_params
        ];
        $this->view('services/index', $data);
    }

    public function show($id){
        try {
            // 调试信息
            error_log("Services::show() called with id: $id");

            $service = $this->serviceModel->getServiceById($id);
            if(!$service) {
                error_log("Service not found with id: $id");
                // 如果服务不存在，重定向到服务列表
                header('location: ' . URLROOT . '/services');
                exit();
            }

            error_log("Service found: " . $service->title);
            error_log("Service role: " . $service->role);

            // 尝试获取与此服务相关的最新订单ID，以便用于聊天
            $order_id_for_chat = null;
            if(isset($_SESSION['user_id'])){
                try {
                    $this->orderModel = $this->model('Order');
                    $latest_order = $this->orderModel->getLatestOrderByServiceAndUsers($id, $_SESSION['user_id'], $service->userId);
                    if($latest_order) {
                        $order_id_for_chat = $latest_order->id;
                    }
                } catch (Exception $e) {
                    // Order模型可能不存在或方法不可用，忽略错误
                    error_log('Order model error: ' . $e->getMessage());
                }
            }

            // 获取服务评论（使用合并方式获取service_id和order_id关联的评价）
            $reviews = $this->serviceModel->getAllServiceReviews($id);

            // 获取完整的用户信息（管理员需要）
            $sellerFullInfo = null;
            if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
                $this->userModel = $this->model('User');
                $sellerFullInfo = $this->userModel->getUserById($service->userId);
            }

            $data = [
                'title' => $service->title,
                'description' => substr(strip_tags($service->description), 0, 160),
                'keywords' => $service->title . ', ' . $service->service_category . ', SEO service',
                'service' => $service,
                'reviews' => $reviews,
                'order_id_for_chat' => $order_id_for_chat,
                'sellerFullInfo' => $sellerFullInfo
            ];

            error_log("Data prepared for view, calling view()");
            $this->view('services/show', $data);
            error_log("View call completed");
        } catch (Exception $e) {
            // 捕获并显示错误
            error_log("Exception in show method: " . $e->getMessage());
            die('Error in show method: ' . $e->getMessage());
        }
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data = [
                'seo_title' => 'Add Service',
                'seo_description' => 'Offer your own SEO services to the community. Create a listing and start earning.',
                'seo_keywords' => 'add service, sell SEO services, offer services',
                'title' => trim($_POST['title']),
                'description' => isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin' ? $_POST['description'] : $this->_sanitize_html($_POST['description']),
                'thumbnail_url' => '', // 默认设为空
                'site_url' => trim($_POST['site_url']),
                'price' => trim($_POST['price']),
                'delivery_time' => trim($_POST['delivery_time']),
                'link_type' => $_POST['link_type'],
                'service_category' => $_POST['service_category'],
                'industry_id' => $_POST['industry_id'],
                'is_adult_allowed' => $_POST['is_adult_allowed'],
                'is_new_window' => $_POST['is_new_window'],
                'duration' => trim($_POST['duration']),
                'terms' => isset($_POST['terms']) ? 'on' : '',
                'is_official' => isset($_POST['is_official']) && $_SESSION['user_role'] == 'admin' ? 1 : 0,
                'user_id' => $_SESSION['user_id'],
                'industries' => $this->industryModel->getIndustries(),
                'title_err' => '',
                'description_err' => '',
                'thumbnail_err' => '',
                'site_url_err' => '',
                'price_err' => '',
                'delivery_time_err' => '',
                'duration_err' => '',
                'service_category_err' => '',
                'industry_id_err' => '',
            'terms_err' => ''
        ];

        // -- 文件上传逻辑 --
        if(isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == 0){
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if(in_array($_FILES['thumbnail_image']['type'], $allowed_types) && $_FILES['thumbnail_image']['size'] <= $max_size){
                    $upload_dir = 'uploads/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $filename = uniqid() . '-' . basename($_FILES['thumbnail_image']['name']);
                    $target_file = $upload_dir . $filename;

                    if(move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $target_file)){
                        $data['thumbnail_url'] = '/' . $target_file;
                    } else {
                        $data['thumbnail_err'] = 'Failed to move uploaded file.';
                    }
                } else {
                    $data['thumbnail_err'] = 'Invalid file type or size (Max 5MB, JPG, PNG, GIF).';
                }
            }

            // -- 数据验证 --
            if(empty($data['title'])){ $data['title_err'] = 'Please enter a title'; }
            if(empty($data['description'])){ $data['description_err'] = 'Please enter a description'; }
            if(empty($data['site_url'])){ $data['site_url_err'] = 'Please enter your site URL'; }
            if(empty($data['price'])){ $data['price_err'] = 'Please enter a price'; }
            if(empty($data['delivery_time'])){ $data['delivery_time_err'] = 'Please enter a delivery time'; }
            if(empty($data['duration'])){ $data['duration_err'] = 'Please enter the link duration'; }
            if(empty($data['service_category'])){ $data['service_category_err'] = 'Please select a category'; }
            if(!isset($data['industry_id'])){ $data['industry_id_err'] = 'Please select an industry'; }
            if(empty($data['terms'])){ $data['terms_err'] = 'You must agree to the terms'; }

            // 检查所有错误是否为空
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['thumbnail_err']) && empty($data['site_url_err']) && empty($data['price_err']) && empty($data['delivery_time_err']) && empty($data['duration_err']) && empty($data['service_category_err']) && empty($data['industry_id_err']) && empty($data['terms_err'])){
                // 验证通过
                if($this->serviceModel->addService($data)){
                    flash('service_message', 'Your service has been published successfully!');
                    header('location: ' . URLROOT);
                } else {
                    die('Something went wrong');
                }
            } else {
                // 加载带有错误的视图
                $this->view('services/add', $data);
            }

        } else {
            $data = [
                'seo_title' => 'Add Service',
                'seo_description' => 'Offer your own SEO services to the community. Create a listing and start earning.',
                'seo_keywords' => 'add service, sell SEO services, offer services',
                'title' => '', 'description' => '', 'thumbnail_url' => '', 'site_url' => '', 'price' => '', 'delivery_time' => '', 'duration' => '', 'service_category' => '', 'industry_id' => '', 'terms' => '',
                'industries' => $this->industryModel->getIndustries(),
                'title_err' => '', 'description_err' => '', 'thumbnail_err' => '', 'site_url_err' => '', 'price_err' => '', 'delivery_time_err' => '', 'duration_err' => '', 'service_category_err' => '', 'industry_id_err' => '', 'terms_err' => ''
            ];
            $this->view('services/add', $data);
        }
    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data = [
                'seo_title' => 'Edit Service',
                'seo_description' => 'Edit your existing SEO service listing.',
                'seo_keywords' => 'edit service, update service, manage service',
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin' ? $_POST['description'] : $this->_sanitize_html($_POST['description']),
                'thumbnail_url' => trim($_POST['current_thumbnail_url']),
                'site_url' => trim($_POST['site_url']),
                'price' => trim($_POST['price']),
                'delivery_time' => trim($_POST['delivery_time']),
                'link_type' => $_POST['link_type'],
                'service_category' => $_POST['service_category'],
                'industry_id' => $_POST['industry_id'],
                'is_adult_allowed' => $_POST['is_adult_allowed'],
                'is_new_window' => $_POST['is_new_window'],
                'duration' => trim($_POST['duration']),
                'is_official' => isset($_POST['is_official']) && $_SESSION['user_role'] == 'admin' ? 1 : 0,
                'industries' => $this->industryModel->getIndustries(),
                'title_err' => '',
                'description_err' => '',
                'thumbnail_err' => '',
                'site_url_err' => '',
                'price_err' => '',
                'delivery_time_err' => '',
                'duration_err' => '',
                'service_category_err' => '',
                'industry_id_err' => ''
            ];

            // -- 文件上传逻辑 --
            if(isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == 0){
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if(in_array($_FILES['thumbnail_image']['type'], $allowed_types) && $_FILES['thumbnail_image']['size'] <= $max_size){
                    $upload_dir = 'uploads/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $filename = uniqid() . '-' . basename($_FILES['thumbnail_image']['name']);
                    $target_file = $upload_dir . $filename;

                    if(move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $target_file)){
                        // 如果上传了新图片，并且存在旧图片，则删除旧图片
                        $old_thumbnail = str_replace(URLROOT . '/', '', $data['thumbnail_url']);
                        if(file_exists($old_thumbnail)){
                            unlink($old_thumbnail);
                        }
                        $data['thumbnail_url'] = '/' . $target_file;
                    } else {
                        $data['thumbnail_err'] = 'Failed to move uploaded file.';
                    }
                } else {
                    $data['thumbnail_err'] = 'Invalid file type or size (Max 5MB, JPG, PNG, GIF).';
                }
            }

            // -- 数据验证 --
            if(empty($data['title'])){ $data['title_err'] = 'Please enter a title'; }
            if(empty($data['description'])){ $data['description_err'] = 'Please enter a description'; }
            if(empty($data['site_url'])){ $data['site_url_err'] = 'Please enter your site URL'; }
            if(empty($data['price'])){ $data['price_err'] = 'Please enter a price'; }
            if(empty($data['delivery_time'])){ $data['delivery_time_err'] = 'Please enter a delivery time'; }
            if(empty($data['duration'])){ $data['duration_err'] = 'Please enter the link duration'; }
            if(empty($data['service_category'])){ $data['service_category_err'] = 'Please select a category'; }
            if(!isset($data['industry_id'])){ $data['industry_id_err'] = 'Please select an industry'; }

            // 检查所有错误是否为空
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['thumbnail_err']) && empty($data['site_url_err']) && empty($data['price_err']) && empty($data['delivery_time_err']) && empty($data['duration_err']) && empty($data['service_category_err']) && empty($data['industry_id_err'])){
                // 验证通过
                if($this->serviceModel->updateService($data)){
                    flash('service_message', 'Service updated successfully!');
                    header('location: ' . URLROOT . '/services/edit/' . $id);
                } else {
                    flash('service_message', 'Something went wrong. Please try again.', 'alert alert-danger');
                    header('location: ' . URLROOT . '/services/edit/' . $id);
                }
            } else {
                // 加载带有错误的视图
                $this->view('services/edit', $data);
            }

        } else {
            // 获取现有服务数据
            $service = $this->serviceModel->getServiceById($id);

            // 确保是服务所有者
            if($service->userId != $_SESSION['user_id']){
                flash('service_message', 'You are not authorized to edit this service.', 'alert alert-danger');
                header('location: ' . URLROOT . '/users/dashboard');
                exit();
            }

            $data = [
                'seo_title' => 'Edit Service: ' . $service->title,
                'seo_description' => 'Edit your existing SEO service listing.',
                'seo_keywords' => 'edit service, update service, manage service',
                'id' => $id,
                'title' => $service->title,
                'description' => $service->description,
                'thumbnail_url' => $service->thumbnail_url,
                'site_url' => $service->site_url,
                'price' => $service->price,
                'delivery_time' => $service->delivery_time,
                'link_type' => $service->link_type,
                'service_category' => $service->service_category,
                'industry_id' => $service->industry_id,
                'is_adult_allowed' => $service->is_adult_allowed,
                'is_new_window' => $service->is_new_window,
                'duration' => $service->duration,
                'is_official' => $service->is_official,
                'industries' => $this->industryModel->getIndustries(),
                'title_err' => '',
                'description_err' => '',
                'thumbnail_err' => '',
                'site_url_err' => '',
                'price_err' => '',
                'delivery_time_err' => '',
                'duration_err' => '',
                'service_category_err' => '',
                'industry_id_err' => ''
            ];
            $this->view('services/edit', $data);
        }
    }

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 获取服务信息
            $service = $this->serviceModel->getServiceById($id);

            // 确保是服务所有者
            if($service->userId != $_SESSION['user_id']){
                flash('service_message', 'You are not authorized to delete this service.', 'alert alert-danger');
                header('location: ' . URLROOT . '/users/dashboard');
                exit();
            }

            // 删除服务图片文件
            if(!empty($service->thumbnail_url)){
                $thumbnail_path = ltrim($service->thumbnail_url, '/');
                if(file_exists($thumbnail_path)){
                    unlink($thumbnail_path);
                }
            }

            // 删除服务
            if($this->serviceModel->deleteService($id)){
                flash('service_message', 'Service removed successfully', 'alert alert-success');
                header('location: ' . URLROOT . '/users/dashboard');
            } else {
                flash('service_message', 'Something went wrong', 'alert alert-danger');
                header('location: ' . URLROOT . '/users/dashboard');
            }
        } else {
            header('location: ' . URLROOT . '/users/dashboard');
        }
    }

    public function adminDelete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // 检查是否为管理员
            if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin'){
                flash('service_message', 'You are not authorized to perform this action.', 'alert alert-danger');
                header('location: ' . URLROOT . '/services');
                exit();
            }

            // 获取服务信息
            $service = $this->serviceModel->getServiceById($id);
            if(!$service){
                flash('service_message', 'Service not found.', 'alert alert-danger');
                header('location: ' . URLROOT . '/services');
                exit();
            }

            // 删除服务图片文件
            if(!empty($service->thumbnail_url)){
                $thumbnail_path = ltrim($service->thumbnail_url, '/');
                if(file_exists($thumbnail_path)){
                    unlink($thumbnail_path);
                }
            }

            // 删除服务
            if($this->serviceModel->deleteService($id)){
                flash('service_message', 'Service deleted successfully by admin.', 'alert alert-success');
                header('location: ' . URLROOT . '/services');
            } else {
                flash('service_message', 'Something went wrong while deleting the service.', 'alert alert-danger');
                header('location: ' . URLROOT . '/services/show/' . $id);
            }
        } else {
            header('location: ' . URLROOT . '/services');
        }
    }

    public function order($service_id) {
        if (!isLoggedIn()) {
            header('location: ' . URLROOT . '/users/login');
            exit();
        }

        $service = $this->serviceModel->getServiceById($service_id);
        if (!$service || $service->userId == $_SESSION['user_id']) {
            flash('service_message', 'Invalid service or you cannot purchase your own service.', 'alert alert-danger');
            header('location: ' . URLROOT . '/services');
            exit();
        }

        // 重定向到统一的订单创建流程
        header('location: ' . URLROOT . '/orders/create/' . $service_id);
        exit();
    }

    private function _sanitize_html($html) {
        $allowed_tags = '<p><a><h1><h2><h3><h4><h5><h6><strong><em><u><ul><ol><li><br><img><span>';
        return strip_tags($html, $allowed_tags);
    }
}