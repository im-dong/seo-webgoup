<?php
class Reviews extends Controller {
    protected $reviewModel;
    protected $orderModel;
    protected $userModel;

    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/users/login');
            exit();
        }
        $this->reviewModel = $this->model('Review');
        $this->orderModel = $this->model('Order');
        $this->userModel = $this->model('User');
    }

    public function add($order_id){
        $order = $this->orderModel->getOrderById($order_id);

        // 确保订单存在，且当前用户是买家，且订单已支付，且未被评价
        if(!$order || $order->buyer_id != $_SESSION['user_id'] || $order->status == 'pending_payment' || $this->reviewModel->hasReviewed($order_id)){
            flash('order_message', 'You cannot review this order.', 'alert alert-danger');
            header('location: ' . URLROOT . '/users/dashboard');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
            $data = [
                'order_id' => $order_id,
                'reviewer_id' => $_SESSION['user_id'],
                'seller_id' => $order->seller_id,
                'rating' => trim($_POST['rating']),
                'comment' => trim($_POST['comment']),
                'rating_err' => '',
                'comment_err' => ''
            ];

            if(empty($data['rating']) || !in_array($data['rating'], [1,2,3,4,5])){
                $data['rating_err'] = 'Please select a rating.';
            }
            if(empty($data['comment'])){
                $data['comment_err'] = 'Please enter a comment.';
            }

            if(empty($data['rating_err']) && empty($data['comment_err'])){
                if($this->reviewModel->addReview($data)){
                    flash('order_message', 'Your review has been submitted.');
                    header('location: ' . URLROOT . '/users/dashboard');
                } else {
                    die('Something went wrong.');
                }
            } else {
                $data['order'] = $order;
                $this->view('reviews/add', $data);
            }

        } else {
            $data = [
                'order_id' => $order_id,
                'order' => $order,
                'rating' => '',
                'comment' => '',
                'rating_err' => '',
                'comment_err' => ''
            ];
            $this->view('reviews/add', $data);
        }
    }
}
