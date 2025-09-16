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
}
?>