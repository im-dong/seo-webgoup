<?php
class Services extends Controller {
    protected $serviceModel;
    protected $industryModel;

    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
        }
        $this->serviceModel = $this->model('Service');
        $this->industryModel = $this->model('Industry');
    }

    public function index($category = null){
        // Sanitize category, allowing null
        $category = $category ? filter_var($category, FILTER_SANITIZE_STRING) : null;
        
        // Get industry from query string
        $industry_id = isset($_GET['industry']) ? filter_input(INPUT_GET, 'industry', FILTER_VALIDATE_INT) : null;

        $filters = [
            'category' => $category,
            'industry_id' => $industry_id
        ];

        // 获取所有服务
        $services = $this->serviceModel->getServices($filters);
        
        $data = [
            'services' => $services,
            'industries' => $this->industryModel->getIndustries(),
            'current_category' => $category,
            'current_industry' => $industry_id
        ];
        $this->view('services/index', $data);
    }

    public function show($id){
        $service = $this->serviceModel->getServiceById($id);
        // 尝试获取与此服务相关的最新订单ID，以便用于聊天
        $order_id_for_chat = null;
        if(isset($_SESSION['user_id'])){
            $this->orderModel = $this->model('Order');
            $latest_order = $this->orderModel->getLatestOrderByServiceAndUsers($id, $_SESSION['user_id'], $service->userId);
            if($latest_order) {
                $order_id_for_chat = $latest_order->id;
            }
        }

        $data = [
            'service' => $service,
            'order_id_for_chat' => $order_id_for_chat
        ];
        $this->view('services/show', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
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

            // ... (file upload logic is the same)

            // -- 数据验证 --
            if(empty($data['title'])){ $data['title_err'] = 'Please enter a title'; }
            if(empty($data['description'])){ $data['description_err'] = 'Please enter a description'; }
            if(empty($data['site_url'])){ $data['site_url_err'] = 'Please enter your site URL'; }
            if(empty($data['price'])){ $data['price_err'] = 'Please enter a price'; }
            if(empty($data['delivery_time'])){ $data['delivery_time_err'] = 'Please enter a delivery time'; }
            if(empty($data['duration'])){ $data['duration_err'] = 'Please enter the link duration'; }
            if(empty($data['service_category'])){ $data['service_category_err'] = 'Please select a category'; }
            if(empty($data['industry_id'])){ $data['industry_id_err'] = 'Please select an industry'; }
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
                'id' => $id,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
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

            // ... (file upload logic is the same)

            // -- 数据验证 --
            if(empty($data['title'])){ $data['title_err'] = 'Please enter a title'; }
            if(empty($data['description'])){ $data['description_err'] = 'Please enter a description'; }
            if(empty($data['site_url'])){ $data['site_url_err'] = 'Please enter your site URL'; }
            if(empty($data['price'])){ $data['price_err'] = 'Please enter a price'; }
            if(empty($data['delivery_time'])){ $data['delivery_time_err'] = 'Please enter a delivery time'; }
            if(empty($data['duration'])){ $data['duration_err'] = 'Please enter the link duration'; }
            if(empty($data['service_category'])){ $data['service_category_err'] = 'Please select a category'; }
            if(empty($data['industry_id'])){ $data['industry_id_err'] = 'Please select an industry'; }

            // 检查所有错误是否为空
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['thumbnail_err']) && empty($data['site_url_err']) && empty($data['price_err']) && empty($data['delivery_time_err']) && empty($data['duration_err']) && empty($data['service_category_err']) && empty($data['industry_id_err'])){
                // 验证通过
                if($this->serviceModel->updateService($data)){
                    flash('service_message', 'Service updated successfully!');
                    header('location: ' . URLROOT . '/users/dashboard');
                } else {
                    die('Something went wrong');
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

    // ... (delete and _process_and_crop_image methods are the same)
}