<?php
class Testemail extends Controller {
    private $emailHelper;

    public function __construct() {
        echo "TestEmail controller loaded successfully<br>";
        $this->emailHelper = new EmailHelper();
    }

    public function index() {
        echo "Index method called<br>";

        // 检查是否是管理员，或者简单允许所有用户访问测试页面
        // 如果需要限制访问，可以在这里添加权限检查

        $data = array(
            'title' => 'Email Test',
            'description' => 'Test email sending functionality',
            'keywords' => 'email test, smtp test',
            'test_result' => '',
            'test_email' => '',
            'email_err' => ''
        );

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

            $data['test_email'] = trim($_POST['test_email']);

            // 验证邮箱地址
            if (empty($data['test_email'])) {
                $data['email_err'] = 'Please enter an email address';
            } elseif (!filter_var($data['test_email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            }

            if (empty($data['email_err'])) {
                // 生成测试内容
                $testSubject = 'Test Email from WebGoup - ' . date('Y-m-d H:i:s');
                $testMessage = $this->generateTestEmailContent();

                // 发送测试邮件
                try {
                    $result = $this->emailHelper->sendEmail($data['test_email'], $testSubject, $testMessage, true);

                    if ($result) {
                        $data['test_result'] = 'success';
                        flash('email_test_result', 'Test email sent successfully to ' . htmlspecialchars($data['test_email']));
                    } else {
                        $data['test_result'] = 'error';
                        $data['error_message'] = 'Failed to send email. Please check server logs for details.';
                    }
                } catch (Exception $e) {
                    $data['test_result'] = 'error';
                    $data['error_message'] = 'Email sending error: ' . $e->getMessage();
                }
            }
        }

        $this->view('test_email/index', $data);
    }

    private function generateTestEmailContent() {
        $currentTime = date('Y-m-d H:i:s');
        $configInfo = [
            'SMTP Host' => EMAIL_HOST,
            'SMTP Port' => EMAIL_PORT,
            'From Email' => EMAIL_FROM_EMAIL,
            'From Name' => EMAIL_FROM_NAME,
            'Test Time' => $currentTime
        ];

        $content = "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Email Test - WebGoup</title>
            <style>
                body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { text-align: center; padding: 30px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .test-info { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 15px 0; }
                .test-info h3 { color: #495057; margin-top: 0; }
                .test-info table { width: 100%; border-collapse: collapse; }
                .test-info td { padding: 8px; border-bottom: 1px solid #dee2e6; }
                .test-info td:first-child { font-weight: bold; width: 40%; }
                .success { color: #28a745; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; padding: 20px; background: #e9ecef; border-radius: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📧 Email Test Successful</h1>
                    <p>WebGoup Email System Test</p>
                </div>

                <div class='content'>
                    <h2 class='success'>✅ Email Sending Test</h2>
                    <p>Congratulations! The email sending system is working correctly.</p>

                    <div class='test-info'>
                        <h3>Test Configuration Details:</h3>
                        <table>";

        foreach ($configInfo as $key => $value) {
            $content .= "<tr><td>" . htmlspecialchars($key) . ":</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }

        $content .= "
                        </table>
                    </div>

                    <h3>What This Test Confirms:</h3>
                    <ul>
                        <li>✅ SMTP connection is working</li>
                        <li>✅ Email authentication is successful</li>
                        <li>✅ HTML email content renders properly</li>
                        <li>✅ Gmail SMTP configuration is correct</li>
                        <li>✅ PHPMailer library is functioning</li>
                    </ul>

                    <p>If you received this email, it means your email system is ready to send:</p>
                    <ul>
                        <li>User registration verification codes</li>
                        <li>Order notifications</li>
                        <li>Newsletter campaigns</li>
                        <li>System notifications</li>
                    </ul>

                    <p><strong>Test completed at:</strong> " . $currentTime . "</p>

                    <p>Best regards!<br>
                    <strong>The WebGoup Team</strong></p>
                </div>

                <div class='footer'>
                    <p>© 2024 WebGoup. All rights reserved.</p>
                    <p>Email System Test 🚀</p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $content;
    }
}