<?php
class Services extends Controller {
    protected $serviceModel;
    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
        }
        $this->serviceModel = $this->model('Service');
    }

    public function index(){
        // 获取所有服务
        $services = $this->serviceModel->getServices();
        $data = [
            'services' => $services
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
                'is_adult_allowed' => $_POST['is_adult_allowed'],
                'is_new_window' => $_POST['is_new_window'],
                'duration' => trim($_POST['duration']),
                'terms' => isset($_POST['terms']) ? 'on' : '',
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'description_err' => '',
                'thumbnail_err' => '',
                'site_url_err' => '',
                'price_err' => '',
                'delivery_time_err' => '',
                'duration_err' => '',
                'terms_err' => ''
            ];

            // 处理缩略图上传
            if(isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == 0){
                $upload_dir = 'uploads/images/thumbnails/';
                $file_name = uniqid() . '-' . basename($_FILES['thumbnail_image']['name']);
                $target_file = $upload_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // 验证
                if(getimagesize($_FILES['thumbnail_image']['tmp_name']) === false) {
                    $data['thumbnail_err'] = 'File is not an image.';
                } elseif ($_FILES['thumbnail_image']['size'] > 500000) { // 500kb
                    $data['thumbnail_err'] = 'Sorry, your file is too large.';
                } elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $data['thumbnail_err'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
                }

                if (empty($data['thumbnail_err'])) {
                    if (move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $target_file)) {
                        $data['thumbnail_url'] = '/' . $target_file;
                    } else {
                        $data['thumbnail_err'] = 'Sorry, there was an error uploading your file.';
                    }
                }
            }

            // -- 数据验证 --
            if(empty($data['title'])){ $data['title_err'] = 'Please enter a title'; }
            if(empty($data['description'])){ $data['description_err'] = 'Please enter a description'; }
            if(empty($data['site_url'])){ $data['site_url_err'] = 'Please enter your site URL'; }
            if(empty($data['price'])){ $data['price_err'] = 'Please enter a price'; }
            if(empty($data['delivery_time'])){ $data['delivery_time_err'] = 'Please enter a delivery time'; }
            if(empty($data['duration'])){ $data['duration_err'] = 'Please enter the link duration'; }
            if(empty($data['terms'])){ $data['terms_err'] = 'You must agree to the terms'; }

            // 检查所有错误是否为空
            if(empty($data['title_err']) && empty($data['description_err']) && empty($data['thumbnail_err']) && empty($data['site_url_err']) && empty($data['price_err']) && empty($data['delivery_time_err']) && empty($data['duration_err']) && empty($data['terms_err'])){
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
                'title' => '', 'description' => '', 'thumbnail_url' => '', 'site_url' => '', 'price' => '', 'delivery_time' => '', 'duration' => '', 'terms' => '',
                'title_err' => '', 'description_err' => '', 'thumbnail_err' => '', 'site_url_err' => '', 'price_err' => '', 'delivery_time_err' => '', 'duration_err' => '', 'terms_err' => ''
            ];
            $this->view('services/add', $data);
        }
    }
}
