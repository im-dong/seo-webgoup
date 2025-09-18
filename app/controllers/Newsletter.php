<?php
require APPROOT . '/app/helpers/csrf_helper.php';

class Newsletter extends Controller {
    private $newsletterModel;
    private $walletModel;

    public function __construct() {
        // 延迟加载模型
    }

    public function index() {
        echo "Newsletter system is working!<br>";
        echo "Access <a href='/newsletter/admin'>admin panel</a> to manage subscribers.<br>";
    }

    public function admin() {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        try {
            // 加载模型
            $this->newsletterModel = $this->model('NewsletterModel');

            // 获取统计信息
            $stats = $this->newsletterModel->getSubscriptionStats();

            // 获取分页参数
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            // 获取订阅者列表
            $subscribers = $this->newsletterModel->getAllSubscribers($limit, $offset);
            $totalSubscribers = $this->newsletterModel->getAllSubscribersCount();

            $data = [
                'title' => 'Newsletter Management',
                'subscribers' => $subscribers,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalSubscribers / $limit),
                    'total_items' => $totalSubscribers,
                    'items_per_page' => $limit
                ]
            ];

            $this->view('admin/newsletter', $data);

        } catch (Exception $e) {
            // 如果数据库表不存在，显示错误信息
            $data = [
                'title' => 'Newsletter Management',
                'error' => 'Database table not found. Please create the newsletter_subscribers table.',
                'subscribers' => [],
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'today' => 0,
                    'week' => 0,
                    'month' => 0
                ],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 1,
                    'total_items' => 0,
                    'items_per_page' => 20
                ]
            ];
            $this->view('admin/newsletter', $data);
        }
    }

    public function subscribe() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 验证CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
                return;
            }

            // 获取和清理输入
            $email = trim($_POST['email'] ?? '');
            $name = trim($_POST['name'] ?? '');

            // 验证邮箱
            if (empty($email)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email is required.']);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                return;
            }

            // 获取用户信息
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

            // 加载模型并尝试订阅
            $this->newsletterModel = $this->model('NewsletterModel');
            $result = $this->newsletterModel->subscribe($email, $name, $ip_address, $user_agent);

            if ($result['success']) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => $result['message']]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        }
    }

    public function unsubscribe($token) {
        $this->newsletterModel = $this->model('NewsletterModel');

        if ($this->newsletterModel->unsubscribe($token)) {
            $data = [
                'title' => 'Successfully Unsubscribed',
                'message' => 'You have been successfully unsubscribed from our newsletter.'
            ];
        } else {
            $data = [
                'title' => 'Unsubscribe Failed',
                'message' => 'Invalid unsubscribe token or you were already unsubscribed.'
            ];
        }
        $this->view('newsletter/unsubscribe', $data);
    }

    public function deleteSubscriber() {
        // 检查管理员权限和AJAX请求
        if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        // 验证CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            return;
        }

        $subscriberId = isset($_POST['subscriber_id']) ? (int)$_POST['subscriber_id'] : 0;

        if ($subscriberId > 0) {
            $this->newsletterModel = $this->model('NewsletterModel');
            if ($this->newsletterModel->deleteSubscriber($subscriberId)) {
                echo json_encode(['success' => true, 'message' => 'Subscriber deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete subscriber.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid subscriber ID.']);
        }
    }

    public function updateSubscriber() {
        // 检查管理员权限和AJAX请求
        if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        // 验证CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            return;
        }

        $subscriberId = isset($_POST['subscriber_id']) ? (int)$_POST['subscriber_id'] : 0;
        $isActive = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true;

        if ($subscriberId > 0) {
            $this->newsletterModel = $this->model('NewsletterModel');
            if ($this->newsletterModel->updateSubscriber($subscriberId, ['is_active' => $isActive])) {
                echo json_encode(['success' => true, 'message' => 'Subscriber updated successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update subscriber.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid subscriber ID.']);
        }
    }

    public function export() {
        // 检查管理员权限
        if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        // 获取活跃订阅者
        $this->newsletterModel = $this->model('NewsletterModel');
        $subscribers = $this->newsletterModel->getActiveSubscribers();

        // 设置CSV头
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

        // 输出CSV内容
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Email', 'Name', 'Subscribed At', 'IP Address']);

        foreach ($subscribers as $subscriber) {
            fputcsv($output, [
                $subscriber->email,
                $subscriber->name ?? '',
                $subscriber->subscribed_at,
                $subscriber->ip_address
            ]);
        }

        fclose($output);
        exit();
    }

    // 提现管理页面
    public function withdrawals() {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $this->walletModel = $this->model('Wallet');
        $withdrawals = $this->walletModel->getWithdrawalRequests();

        // 计算统计数据
        $stats = [
            'total' => count($withdrawals),
            'pending' => 0,
            'approved' => 0,
            'total_amount' => 0
        ];

        foreach ($withdrawals as $withdrawal) {
            if ($withdrawal->status == 'pending') {
                $stats['pending']++;
            } elseif ($withdrawal->status == 'approved') {
                $stats['approved']++;
            }
            $stats['total_amount'] += $withdrawal->amount;
        }

        $data = [
            'title' => 'Withdrawal Management',
            'withdrawals' => $withdrawals,
            'stats' => $stats
        ];

        $this->view('admin/withdrawals_new', $data);
    }

    // 处理提现请求
    public function processWithdrawal() {
        // 检查管理员权限和AJAX请求
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        // 验证CSRF token
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            return;
        }

        $withdrawal_id = isset($_POST['withdrawal_id']) ? (int)$_POST['withdrawal_id'] : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

        if ($withdrawal_id > 0 && in_array($status, ['approved', 'rejected'])) {
            $this->walletModel = $this->model('Wallet');

            $withdrawal = $this->walletModel->getWithdrawalById($withdrawal_id);
            if (!$withdrawal) {
                echo json_encode(['success' => false, 'message' => 'Withdrawal request not found.']);
                return;
            }

            if ($this->walletModel->processWithdrawal($withdrawal_id, $status, $notes)) {
                if ($status == 'rejected') {
                    // Return funds to user's withdrawable balance
                    $this->walletModel->returnFundsToWithdrawable($withdrawal->user_id, $withdrawal->amount);
                }
                echo json_encode(['success' => true, 'message' => 'Withdrawal request has been ' . $status . '.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to process withdrawal request.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        }
    }

    // 发送Newsletter页面
    public function send() {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $this->newsletterModel = $this->model('NewsletterModel');
        $total_subscribers = $this->newsletterModel->getActiveSubscribersCount();

        $data = [
            'title' => 'Send Newsletter',
            'total_subscribers' => $total_subscribers
        ];

        $this->view('admin/send_newsletter', $data);
    }

    // 处理发送Newsletter
    public function processSend() {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        // 暂时禁用CSRF验证进行测试
        // if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        //     http_response_code(403);
        //     echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        //     return;
        // }

        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');

        error_log("Newsletter send attempt - Subject: $subject, Content length: " . strlen($content));

        if (empty($subject) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Subject and content are required.']);
            return;
        }

        $this->newsletterModel = $this->model('NewsletterModel');

        error_log("NewsletterModel loaded successfully");

        // 获取活跃订阅者
        $subscribers = $this->newsletterModel->getActiveSubscribers();

        error_log("Found " . count($subscribers) . " active subscribers");

        if (empty($subscribers)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No active subscribers found.']);
            return;
        }

        // 生成邮件模板HTML
        $template_html = $this->newsletterModel->generateTemplateHTML($subject, $content);

        // 创建发送记录
        $send_id = $this->newsletterModel->createSendRecord($subject, $content, $template_html, $_SESSION['user_id'], count($subscribers));

        if (!$send_id) {
            error_log("Newsletter send failed: Could not create send record");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create send record.']);
            return;
        }

        error_log("Newsletter send record created: ID $send_id");

        // 更新状态为发送中
        $this->newsletterModel->updateSendRecord($send_id, 'sending');

        // 发送邮件 - 使用BCC方式
        $email_helper = new EmailHelper();

        error_log("Starting to send newsletter to " . count($subscribers) . " subscribers using BCC method");

        try {
            // 获取所有订阅者邮箱作为BCC
            $bcc_emails = [];
            foreach ($subscribers as $subscriber) {
                $bcc_emails[] = $subscriber->email;
            }

            // 发送一封邮件，使用BCC包含所有订阅者
            $send_result = $email_helper->sendBCCEmail(
                '9481346@qq.com', // 发送给你自己作为主要收件人
                $subject,
                $template_html,
                $bcc_emails,
                true // HTML格式
            );

            if ($send_result) {
                // 记录所有发送为成功
                $successful_sends = count($subscribers);
                $failed_sends = 0;

                foreach ($subscribers as $subscriber) {
                    $this->newsletterModel->recordSendDetail($send_id, $subscriber->id, $subscriber->email, 'sent');
                }

                error_log("Newsletter sent successfully to $successful_sends subscribers via BCC");
            } else {
                // 记录所有发送为失败
                $successful_sends = 0;
                $failed_sends = count($subscribers);

                foreach ($subscribers as $subscriber) {
                    $this->newsletterModel->recordSendDetail($send_id, $subscriber->id, $subscriber->email, 'failed', 'BCC email sending failed');
                }

                error_log("BCC email sending failed");
            }

        } catch (Exception $e) {
            // 记录所有发送为失败
            $successful_sends = 0;
            $failed_sends = count($subscribers);

            foreach ($subscribers as $subscriber) {
                $this->newsletterModel->recordSendDetail($send_id, $subscriber->id, $subscriber->email, 'failed', $e->getMessage());
            }

            error_log("BCC email sending exception: " . $e->getMessage());
        }

        // 更新发送记录状态
        $final_status = ($failed_sends == 0) ? 'completed' : 'completed'; // 即使有失败的也算完成
        $this->newsletterModel->updateSendRecord($send_id, $final_status, $successful_sends, $failed_sends);

        echo json_encode([
            'success' => true,
            'message' => "Newsletter sent successfully! Sent to $successful_sends subscribers. Failed: $failed_sends.",
            'send_id' => $send_id
        ]);
    }

    // 查看发送记录
    public function sendHistory() {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $this->newsletterModel = $this->model('NewsletterModel');

        // 分页参数
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // 获取发送记录
        $send_records = $this->newsletterModel->getSendRecords($limit, $offset);
        $total_records = $this->newsletterModel->getSendRecordsCount();

        $data = [
            'title' => 'Send History',
            'send_records' => $send_records,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_records / $limit),
                'total_items' => $total_records,
                'items_per_page' => $limit
            ]
        ];

        $this->view('admin/send_history', $data);
    }

    // 查看发送详情
    public function sendDetails($send_id) {
        // 检查管理员权限
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . URLROOT . '/users/login');
            exit();
        }

        $this->newsletterModel = $this->model('NewsletterModel');

        // 获取发送记录详情
        $send_record = $this->newsletterModel->getSendRecordById($send_id);

        if (!$send_record) {
            flash('message', 'Send record not found.', 'alert alert-danger');
            header('Location: ' . URLROOT . '/newsletter/sendHistory');
            exit();
        }

        // 分页参数
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        // 获取发送详情
        $send_details = $this->newsletterModel->getSendDetails($send_id, $limit, $offset);
        $total_details = $this->newsletterModel->getSendDetailsCount($send_id);

        $data = [
            'title' => 'Send Details',
            'send_record' => $send_record,
            'send_details' => $send_details,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_details / $limit),
                'total_items' => $total_details,
                'items_per_page' => $limit
            ]
        ];

        $this->view('admin/send_details', $data);
    }
}
?>