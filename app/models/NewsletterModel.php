<?php
class NewsletterModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // 添加新的订阅者
    public function subscribe($email, $name = '', $ip_address = '', $user_agent = '') {
        // 检查邮箱是否已存在
        if ($this->emailExists($email)) {
            // 如果存在但已取消订阅，重新激活
            if ($this->isInactive($email)) {
                $this->reactivate($email);
                return ['success' => true, 'message' => 'Welcome back! You have been resubscribed to our newsletter.'];
            }
            return ['success' => false, 'message' => 'This email is already subscribed.'];
        }

        // 生成取消订阅令牌
        $unsubscribe_token = $this->generateUnsubscribeToken($email);

        $this->db->query('INSERT INTO newsletter_subscribers (email, name, ip_address, user_agent, unsubscribe_token) VALUES (:email, :name, :ip_address, :user_agent, :unsubscribe_token)');

        $this->db->bind(':email', $email);
        $this->db->bind(':name', $name);
        $this->db->bind(':ip_address', $ip_address);
        $this->db->bind(':user_agent', $user_agent);
        $this->db->bind(':unsubscribe_token', $unsubscribe_token);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Successfully subscribed to newsletter!', 'token' => $unsubscribe_token];
        } else {
            return ['success' => false, 'message' => 'Failed to subscribe. Please try again.'];
        }
    }

    // 检查邮箱是否已存在
    public function emailExists($email) {
        $this->db->query('SELECT id FROM newsletter_subscribers WHERE email = :email');
        $this->db->bind(':email', $email);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    // 检查邮箱是否已取消订阅
    public function isInactive($email) {
        $this->db->query('SELECT is_active FROM newsletter_subscribers WHERE email = :email');
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return isset($row->is_active) && !$row->is_active;
    }

    // 重新激活订阅
    public function reactivate($email) {
        $this->db->query('UPDATE newsletter_subscribers SET is_active = TRUE, subscribed_at = CURRENT_TIMESTAMP WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    // 取消订阅
    public function unsubscribe($token) {
        $this->db->query('UPDATE newsletter_subscribers SET is_active = FALSE WHERE unsubscribe_token = :token');
        $this->db->bind(':token', $token);

        if ($this->db->execute()) {
            return $this->db->rowCount() > 0;
        }
        return false;
    }

    // 获取所有活跃的订阅者
    public function getActiveSubscribers($limit = null, $offset = 0) {
        $sql = 'SELECT * FROM newsletter_subscribers WHERE is_active = TRUE ORDER BY subscribed_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // 获取订阅者总数
    public function getActiveSubscribersCount() {
        $this->db->query('SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = TRUE');
        $row = $this->db->single();
        return $row->total;
    }

    // 获取所有订阅者（包括非活跃的）
    public function getAllSubscribers($limit = null, $offset = 0) {
        $sql = 'SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // 获取所有订阅者总数
    public function getAllSubscribersCount() {
        $this->db->query('SELECT COUNT(*) as total FROM newsletter_subscribers');
        $row = $this->db->single();
        return $row->total;
    }

    // 删除订阅者
    public function deleteSubscriber($id) {
        $this->db->query('DELETE FROM newsletter_subscribers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // 更新订阅者信息
    public function updateSubscriber($id, $data) {
        $sql = 'UPDATE newsletter_subscribers SET ';
        $updates = [];

        if (isset($data['email'])) {
            $updates[] = 'email = :email';
        }
        if (isset($data['name'])) {
            $updates[] = 'name = :name';
        }
        if (isset($data['is_active'])) {
            $updates[] = 'is_active = :is_active';
        }

        if (empty($updates)) {
            return false;
        }

        $sql .= implode(', ', $updates) . ' WHERE id = :id';
        $this->db->query($sql);

        if (isset($data['email'])) {
            $this->db->bind(':email', $data['email']);
        }
        if (isset($data['name'])) {
            $this->db->bind(':name', $data['name']);
        }
        if (isset($data['is_active'])) {
            $this->db->bind(':is_active', $data['is_active']);
        }

        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // 生成取消订阅令牌
    private function generateUnsubscribeToken($email) {
        return md5($email . time() . rand());
    }

    // 获取最近的订阅统计
    public function getSubscriptionStats() {
        $stats = [];

        // 总订阅数
        $this->db->query('SELECT COUNT(*) as total FROM newsletter_subscribers');
        $stats['total'] = $this->db->single()->total;

        // 活跃订阅数
        $this->db->query('SELECT COUNT(*) as active FROM newsletter_subscribers WHERE is_active = TRUE');
        $stats['active'] = $this->db->single()->active;

        // 今日新增
        $this->db->query('SELECT COUNT(*) as today FROM newsletter_subscribers WHERE DATE(subscribed_at) = CURDATE()');
        $stats['today'] = $this->db->single()->today;

        // 本周新增
        $this->db->query('SELECT COUNT(*) as week FROM newsletter_subscribers WHERE subscribed_at >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)');
        $stats['week'] = $this->db->single()->week;

        // 本月新增
        $this->db->query('SELECT COUNT(*) as month FROM newsletter_subscribers WHERE MONTH(subscribed_at) = MONTH(CURRENT_DATE) AND YEAR(subscribed_at) = YEAR(CURRENT_DATE)');
        $stats['month'] = $this->db->single()->month;

        return $stats;
    }

    // 创建新的邮件发送记录
    public function createSendRecord($subject, $content, $template_html, $sent_by, $total_recipients) {
        $this->db->query('INSERT INTO newsletter_sends (subject, content, template_html, sent_by, total_recipients) VALUES (:subject, :content, :template_html, :sent_by, :total_recipients)');

        $this->db->bind(':subject', $subject);
        $this->db->bind(':content', $content);
        $this->db->bind(':template_html', $template_html);
        $this->db->bind(':sent_by', $sent_by);
        $this->db->bind(':total_recipients', $total_recipients);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // 更新发送记录状态
    public function updateSendRecord($send_id, $status, $successful_sends = null, $failed_sends = null) {
        $sql = 'UPDATE newsletter_sends SET status = :status';
        if ($successful_sends !== null) {
            $sql .= ', successful_sends = :successful_sends';
        }
        if ($failed_sends !== null) {
            $sql .= ', failed_sends = :failed_sends';
        }
        $sql .= ' WHERE id = :send_id';

        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':send_id', $send_id);

        if ($successful_sends !== null) {
            $this->db->bind(':successful_sends', $successful_sends);
        }
        if ($failed_sends !== null) {
            $this->db->bind(':failed_sends', $failed_sends);
        }

        return $this->db->execute();
    }

    // 记录发送详情
    public function recordSendDetail($send_id, $subscriber_id, $email, $status, $error_message = null) {
        $this->db->query('INSERT INTO newsletter_send_details (send_id, subscriber_id, email, status, error_message, sent_at) VALUES (:send_id, :subscriber_id, :email, :status, :error_message, NOW())');

        $this->db->bind(':send_id', $send_id);
        $this->db->bind(':subscriber_id', $subscriber_id);
        $this->db->bind(':email', $email);
        $this->db->bind(':status', $status);
        $this->db->bind(':error_message', $error_message);

        return $this->db->execute();
    }

    // 获取发送记录列表
    public function getSendRecords($limit = null, $offset = 0) {
        $sql = 'SELECT ns.*, u.username as sent_by_name
                FROM newsletter_sends ns
                LEFT JOIN users u ON ns.sent_by = u.id
                ORDER BY ns.created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }

        $this->db->query($sql);
        return $this->db->resultSet();
    }

    // 获取发送记录总数
    public function getSendRecordsCount() {
        $this->db->query('SELECT COUNT(*) as total FROM newsletter_sends');
        $row = $this->db->single();
        return $row->total;
    }

    // 获取发送记录详情
    public function getSendRecordById($send_id) {
        $this->db->query('SELECT ns.*, u.username as sent_by_name
                          FROM newsletter_sends ns
                          LEFT JOIN users u ON ns.sent_by = u.id
                          WHERE ns.id = :send_id');
        $this->db->bind(':send_id', $send_id);
        return $this->db->single();
    }

    // 获取发送详情记录
    public function getSendDetails($send_id, $limit = null, $offset = 0) {
        $sql = 'SELECT nsd.*, ns.name as subscriber_name
                FROM newsletter_send_details nsd
                LEFT JOIN newsletter_subscribers ns ON nsd.subscriber_id = ns.id
                WHERE nsd.send_id = :send_id
                ORDER BY nsd.created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }

        $this->db->query($sql);
        $this->db->bind(':send_id', $send_id);
        return $this->db->resultSet();
    }

    // 获取发送详情总数
    public function getSendDetailsCount($send_id) {
        $this->db->query('SELECT COUNT(*) as total FROM newsletter_send_details WHERE send_id = :send_id');
        $this->db->bind(':send_id', $send_id);
        $row = $this->db->single();
        return $row->total;
    }

    // 生成邮件模板HTML
    public function generateTemplateHTML($subject, $content) {
        $template = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .header h1 {
            color: #087990;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }
        .unsubscribe {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .unsubscribe a {
            color: #dc3545;
            text-decoration: none;
        }
        .unsubscribe a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>WebGoup Newsletter</h1>
        </div>
        <div class="content">
            ' . $content . '
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' WebGoup. All rights reserved.</p>
            <div class="unsubscribe">
                <p>This newsletter was sent to our subscribers. To manage your subscription or unsubscribe, please <a href="' . URLROOT . '/contact">contact us</a> or visit your account settings.</p>
            </div>
        </div>
    </div>
</body>
</html>';
        return $template;
    }
}