<?php
// 引入PHPMailer库
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require APPROOT . '/vendor/autoload.php';

class EmailHelper {
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromName;
    private $fromEmail;

    public function __construct() {
        $this->host = EMAIL_HOST;
        $this->port = EMAIL_PORT;
        $this->username = EMAIL_USERNAME;
        $this->password = EMAIL_PASSWORD;
        $this->fromName = EMAIL_FROM_NAME;
        $this->fromEmail = EMAIL_FROM_EMAIL;
    }

    public function sendEmail($to, $subject, $message, $isHtml = true) {
        $mail = new PHPMailer(true);

        try {
            // 服务器设置
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->CharSet = 'UTF-8';

            // 发件人和收件人
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);

            // 内容设置
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML($isHtml);

            // 调试信息
            $mail->SMTPDebug = 0; // 设置为2查看详细调试信息

            return $mail->send();
        } catch (Exception $e) {
            // 记录错误但不显示给用户
            error_log("邮件发送失败: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function sendVerificationEmail($email, $verificationCode) {
        $subject = "Verify Your Email - WebGoup";

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verify Your Email - WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 30px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .verification-code {
                    background: #087990;
                    color: white;
                    padding: 15px 25px;
                    font-size: 24px;
                    font-weight: bold;
                    text-align: center;
                    border-radius: 5px;
                    margin: 20px 0;
                    letter-spacing: 3px;
                }
                .btn {
                    display: inline-block;
                    background: #28a745;
                    color: white;
                    padding: 12px 25px;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚀 WebGoup</h1>
                    <p>Connecting Quality Services, Building Business Dreams</p>
                </div>

                <div class='content'>
                    <h2>Verify Your Email Address</h2>
                    <p>Hello!</p>
                    <p>Thank you for joining the WebGoup platform. To ensure your account security, please use the following verification code to complete email verification:</p>

                    <div class='verification-code'>
                        {$verificationCode}
                    </div>

                    <p><strong>Verification code expires in: 10 minutes</strong></p>

                    <p>If you did not register for a WebGoup account, please ignore this email.</p>

                    <h3>🌟 About WebGoup</h3>
                    <p>WebGoup is a professional link building platform dedicated to providing high-quality link building services for website owners and SEO experts. We believe in:</p>
                    <ul>
                        <li>✨ <strong>Quality First:</strong> Every link goes through strict review</li>
                        <li>🤝 <strong>Honest Partnership:</strong> Building long-term mutually beneficial relationships</li>
                        <li>🚀 <strong>Grow Together:</strong> Achieving business growth with our clients</li>
                    </ul>

                    <p>If you have any questions, please feel free to contact our customer service team.</p>

                    <p>Best regards!<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Making every link create value 💼</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($email, $subject, $message);
    }

    // 发送BCC邮件（用于Newsletter等群发）
    public function sendBCCEmail($to, $subject, $message, $bcc_emails = [], $isHtml = true) {
        // Gmail SMTP限制：每封邮件最多100个收件人（包括TO、CC、BCC）
        // 为了安全，我们限制每封邮件最多50个BCC收件人
        $max_bcc_per_email = 50;

        if (count($bcc_emails) <= $max_bcc_per_email) {
            // 如果数量不多，直接发送
            return $this->sendSingleBCCEmail($to, $subject, $message, $bcc_emails, $isHtml);
        } else {
            // 如果数量多，分批发送
            return $this->sendMultipleBCCEmails($to, $subject, $message, $bcc_emails, $isHtml, $max_bcc_per_email);
        }
    }

    // 发送单封BCC邮件
    private function sendSingleBCCEmail($to, $subject, $message, $bcc_emails, $isHtml) {
        $mail = new PHPMailer(true);

        try {
            // 服务器设置
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->CharSet = 'UTF-8';

            // 发件人和收件人
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);

            // 添加BCC收件人
            foreach ($bcc_emails as $bcc_email) {
                $mail->addBCC($bcc_email);
            }

            // 内容设置
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML($isHtml);

            // 调试信息
            $mail->SMTPDebug = 0;

            return $mail->send();
        } catch (Exception $e) {
            error_log("单封BCC邮件发送失败: " . $mail->ErrorInfo);
            return false;
        }
    }

    // 发送多封BCC邮件（分批发送）
    private function sendMultipleBCCEmails($to, $subject, $message, $bcc_emails, $isHtml, $batch_size) {
        $batches = array_chunk($bcc_emails, $batch_size);
        $success_count = 0;

        foreach ($batches as $index => $batch) {
            try {
                // 为每批创建新的邮件实例
                $mail = new PHPMailer(true);

                // 服务器设置
                $mail->isSMTP();
                $mail->Host = $this->host;
                $mail->Port = $this->port;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPAuth = true;
                $mail->Username = $this->username;
                $mail->Password = $this->password;
                $mail->CharSet = 'UTF-8';

                // 发件人和收件人
                $mail->setFrom($this->fromEmail, $this->fromName);
                $mail->addAddress($to);

                // 添加这批的BCC收件人
                foreach ($batch as $bcc_email) {
                    $mail->addBCC($bcc_email);
                }

                // 修改主题以标识这是第几批
                $batch_subject = $subject . " [" . ($index + 1) . "/" . count($batches) . "]";

                // 内容设置
                $mail->Subject = $batch_subject;
                $mail->Body = $message;
                $mail->isHTML($isHtml);

                // 发送邮件
                if ($mail->send()) {
                    $success_count += count($batch);
                    // 避免发送过快，Gmail限制每秒最多1封
                    sleep(2);
                } else {
                    error_log("BCC邮件批次 " . ($index + 1) . " 发送失败: " . $mail->ErrorInfo);
                }
            } catch (Exception $e) {
                error_log("BCC邮件批次 " . ($index + 1) . " 异常: " . $e->getMessage());
            }
        }

        return $success_count > 0;
    }

    // 发送新订单通知给卖家
    public function sendNewOrderNotification($seller_email, $seller_name, $order_data) {
        $subject = "New Order Received - WebGoup Order #" . $order_data['id'];

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Order - WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 30px 0; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .order-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
                .btn-primary { background: #007bff; }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 New Order Received!</h1>
                    <p>Congratulations! You've received a new order</p>
                </div>

                <div class='content'>
                    <h2>Order Details</h2>
                    <p>Hello <strong>" . htmlspecialchars($seller_name) . "</strong>,</p>
                    <p>You have received a new order for your service. Please review the details below:</p>

                    <div class='order-info'>
                        <h3>Order Information:</h3>
                        <p><strong>Order ID:</strong> #" . $order_data['id'] . "</p>
                        <p><strong>Service:</strong> " . htmlspecialchars($order_data['service_title']) . "</p>
                        <p><strong>Buyer:</strong> " . htmlspecialchars($order_data['buyer_name']) . "</p>
                        <p><strong>Amount:</strong> $" . number_format($order_data['amount'], 2) . "</p>
                        <p><strong>Order Date:</strong> " . $order_data['created_at'] . "</p>
                        " . (isset($order_data['remark']) ? "<p><strong>Buyer Note:</strong> " . htmlspecialchars($order_data['remark']) . "</p>" : "") . "
                    </div>

                    <h3>Next Steps:</h3>
                    <ol>
                        <li>Wait for payment confirmation (if not already paid)</li>
                        <li>Review order details and any special requirements</li>
                        <li>Start working on the service</li>
                        <li>Mark as complete when finished</li>
                    </ol>

                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='" . URLROOT . "/orders/details/" . $order_data['id'] . "' class='btn'>View Order Details</a>
                        <a href='" . URLROOT . "/users/dashboard' class='btn btn-primary'>Go to Dashboard</a>
                    </div>

                    <p><strong>Important:</strong> Please deliver the service within the specified timeframe to maintain good ratings and ensure timely payment release.</p>

                    <p>Best regards!<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Empowering SEO service providers worldwide 🚀</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($seller_email, $subject, $message);
    }

    // 发送订单状态变更通知
    public function sendOrderStatusUpdateNotification($user_email, $user_name, $order_data, $old_status, $new_status) {
        $statusLabels = array(
            'pending_payment' => 'Pending Payment',
            'paid' => 'Paid',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded'
        );

        $statusColors = array(
            'pending_payment' => '#ffc107',
            'paid' => '#28a745',
            'in_progress' => '#17a2b8',
            'completed' => '#28a745',
            'confirmed' => '#28a745',
            'cancelled' => '#dc3545',
            'refunded' => '#dc3545'
        );

        $oldStatusLabel = isset($statusLabels[$old_status]) ? $statusLabels[$old_status] : $old_status;
        $newStatusLabel = isset($statusLabels[$new_status]) ? $statusLabels[$new_status] : $new_status;
        $statusColor = isset($statusColors[$new_status]) ? $statusColors[$new_status] : '#6c757d';

        $subject = "Order Status Update - WebGoup Order #" . $order_data['id'] . " - " . $newStatusLabel;

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Order Status Update - WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 30px 0; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .status-update { background: " . $statusColor . "; color: white; padding: 15px; border-radius: 5px; margin: 15px 0; text-align: center; }
                .order-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .btn { display: inline-block; background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 Order Status Update</h1>
                    <p>Your order status has been updated</p>
                </div>

                <div class='content'>
                    <h2>Order #" . $order_data['id'] . " Status Changed</h2>
                    <p>Hello <strong>" . htmlspecialchars($user_name) . "</strong>,</p>
                    <p>The status of your order has been updated:</p>

                    <div class='status-update'>
                        <h3 style='margin: 0;'>" . $oldStatusLabel . " → " . $newStatusLabel . "</h3>
                    </div>

                    <div class='order-info'>
                        <h3>Order Information:</h3>
                        <p><strong>Order ID:</strong> #" . $order_data['id'] . "</p>
                        <p><strong>Service:</strong> " . htmlspecialchars($order_data['service_title']) . "</p>
                        <p><strong>Amount:</strong> $" . number_format($order_data['amount'], 2) . "</p>
                        <p><strong>Update Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

        // 根据状态添加特定信息
        if ($new_status == 'completed') {
            $message .= "<p><strong>Proof URL:</strong> <a href='" . URLROOT . $order_data['proof_url'] . "'>View Proof</a></p>";
            $message .= "<p><strong>Funds Release Date:</strong> " . $order_data['funds_release_date'] . "</p>";
        } elseif ($new_status == 'paid') {
            $message .= "<p><strong>Payment ID:</strong> " . $order_data['payment_id'] . "</p>";
        }

        $message .= "
                    </div>";

        // 根据状态添加行动提示
        if ($new_status == 'completed') {
            $message .= "
                    <h3>Next Steps:</h3>
                    <p>Please review the work and confirm if everything meets your requirements. Once confirmed, funds will be released to the seller.</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='" . URLROOT . "/orders/details/" . $order_data['id'] . "' class='btn'>Review Order</a>
                    </div>";
        } elseif ($new_status == 'confirmed') {
            $message .= "
                    <h3>Order Confirmed!</h3>
                    <p>Thank you for confirming the order. The payment has been released to the service provider.</p>";
        } else {
            $message .= "
                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='" . URLROOT . "/orders/details/" . $order_data['id'] . "' class='btn'>View Order Details</a>
                    </div>";
        }

        $message .= "
                    <p>If you have any questions or concerns, please don't hesitate to contact us or the service provider.</p>

                    <p>Best regards!<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Connecting quality services with businesses 🤝</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($user_email, $subject, $message);
    }

    // 发送新消息通知
    public function sendNewMessageNotification($recipient_email, $recipient_name, $message_data) {
        $subject = "New Message - WebGoup Order #" . $message_data['order_id'];

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>New Message - WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 30px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .message-bubble { background: #e9ecef; padding: 15px; border-radius: 10px; margin: 15px 0; border-left: 4px solid #667eea; }
                .order-info { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>💬 New Message Received</h1>
                    <p>You have a new message regarding your order</p>
                </div>

                <div class='content'>
                    <h2>Message from " . htmlspecialchars($message_data['sender_name']) . "</h2>
                    <p>Hello <strong>" . htmlspecialchars($recipient_name) . "</strong>,</p>
                    <p>You have received a new message:</p>

                    <div class='message-bubble'>
                        <p style='margin: 0;'>" . nl2br(htmlspecialchars($message_data['message_text'])) . "</p>
                        <p style='margin: 10px 0 0 0; font-size: 0.9em; color: #6c757d;'>
                            <em>Sent at: " . $message_data['created_at'] . "</em>
                        </p>
                    </div>

                    <div class='order-info'>
                        <h3>Related Order:</h3>
                        <p><strong>Order ID:</strong> #" . $message_data['order_id'] . "</p>
                        <p><strong>Service:</strong> " . htmlspecialchars($message_data['service_title']) . "</p>
                    </div>

                    <div style='text-align: center; margin: 20px 0;'>
                        <a href='" . URLROOT . "/conversations/show/" . $message_data['conversation_id'] . "' class='btn'>View Message</a>
                        <a href='" . URLROOT . "/orders/details/" . $message_data['order_id'] . "' class='btn'>View Order</a>
                    </div>

                    <p><strong>Quick Reply Tip:</strong> You can reply to this message directly on the WebGoup platform to keep all communication organized and secure.</p>

                    <p>Best regards!<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Keeping communication seamless and secure 🔒</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($recipient_email, $subject, $message);
    }

    // 发送欢迎邮件给新注册用户
    public function sendWelcomeEmail($user_email, $user_name, $user_data = array()) {
        $subject = "Welcome to WebGoup - Start Your SEO Journey Today!";

        $message = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 40px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 40px 30px; border-radius: 0 0 10px 10px; }
                .welcome-text { font-size: 1.2em; color: #495057; margin: 20px 0; }
                .feature-list { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .feature-list h4 { color: #667eea; margin-top: 0; }
                .btn { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 10px 5px; }
                .btn-primary { background: #28a745; }
                .stats { display: flex; justify-content: space-around; margin: 30px 0; text-align: center; }
                .stat-item { flex: 1; }
                .stat-number { font-size: 1.8em; font-weight: bold; color: #667eea; }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Welcome to WebGoup!</h1>
                    <p>Your Gateway to Premium SEO Services</p>
                </div>

                <div class='content'>
                    <h2>Hello " . htmlspecialchars($user_name) . "!</h2>

                    <div class='welcome-text'>
                        <p>🚀 <strong>Welcome to the WebGoup community!</strong> We're thrilled to have you join our platform of SEO professionals and service seekers.</p>

                        <p>Your account has been successfully created and you're now ready to explore our marketplace of high-quality SEO services.</p>
                    </div>

                    <div class='feature-list'>
                        <h4>🌟 What Can You Do on WebGoup?</h4>
                        <ul>
                            <li><strong>Buy SEO Services:</strong> Browse our curated marketplace of vetted SEO providers</li>
                            <li><strong>Sell Your Services:</strong> Monetize your SEO expertise by offering services to our global community</li>
                            <li><strong>Track Orders:</strong> Manage all your orders and communications in one place</li>
                            <li><strong>Secure Payments:</strong> Enjoy protected transactions with our escrow system</li>
                        </ul>
                    </div>

                    <div class='stats'>
                        <div class='stat-item'>
                            <div class='stat-number'>70%</div>
                            <div>Revenue to Sellers</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-number'>Global</div>
                            <div>Marketplace</div>
                        </div>
                        <div class='stat-item'>
                            <div class='stat-number'>Secure</div>
                            <div>Payments</div>
                        </div>
                    </div>

                    <h3>🚀 Get Started Now!</h3>
                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='" . URLROOT . "/services' class='btn btn-primary'>Browse Services</a>
                        <a href='" . URLROOT . "/services/add' class='btn'>Sell Your Services</a>
                    </div>

                    <div class='feature-list'>
                        <h4>💡 Pro Tips for Success:</h4>
                        <ul>
                            <li>Complete your profile to build trust with other users</li>
                            <li>As a seller, create detailed service descriptions</li>
                            <li>Communicate clearly through our messaging system</li>
                            <li>Deliver quality work on time to build your reputation</li>
                        </ul>
                    </div>

                    <h3>📚 Need Help?</h3>
                    <p>Our team is here to support you every step of the way. If you have any questions or need assistance, don't hesitate to reach out.</p>

                    <div style='text-align: center; margin: 25px 0;'>
                        <a href='" . URLROOT . "/users/dashboard' class='btn'>Go to Dashboard</a>
                    </div>

                    <p>We're excited to see you succeed on WebGoup!</p>

                    <p>Best regards,<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Empowering SEO professionals worldwide 🌍</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $this->sendEmail($user_email, $subject, $message);
    }

  }
?>