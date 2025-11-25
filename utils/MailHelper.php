<?php

require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailHelper
{
    /**
     * Gửi email đăng ký thành công
     */
    public static function sendRegistrationMail($email, $fullName)
    {
        $subject = 'Chào mừng đến với BonBon!';
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BonBon</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào, $fullName!</h2>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại BonBon.</p>
                    <p>Tài khoản của bạn đã được tạo thành công với email: <strong>$email</strong></p>
                    <p>Chúc bạn có những trải nghiệm tuyệt vời!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " BonBon. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return self::sendMail($email, $fullName, $subject, $body);
    }

    /**
     * Gửi email reset password
     */
    public static function sendResetPasswordMail($email, $fullName, $resetLink)
    {
        $subject = 'Đặt lại mật khẩu BonBon';
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #000; color: #fff; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .button { display: inline-block; padding: 12px 30px; background-color: #000; color: #fff; text-decoration: none; border-radius: 4px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>BonBon</h1>
                </div>
                <div class='content'>
                    <h2>Xin chào, $fullName!</h2>
                    <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>
                    <p>Vui lòng click vào nút bên dưới để đặt lại mật khẩu:</p>
                    <p style='text-align: center;'>
                        <a href='$resetLink' class='button'>Đặt lại mật khẩu</a>
                    </p>
                    <p>Hoặc copy link này vào trình duyệt:</p>
                    <p style='word-break: break-all; color: #0066cc;'>$resetLink</p>
                    <p><strong>Lưu ý:</strong> Link này sẽ hết hạn sau 1 giờ.</p>
                    <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " BonBon. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return self::sendMail($email, $fullName, $subject, $body);
    }

    /**
     * Hàm gửi email chung sử dụng PHPMailer
     */
    private static function sendMail($toEmail, $toName, $subject, $body)
    {
        $mail = null;

        try {
            $mail = new PHPMailer(true);

            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = self::getConfigValue('SMTP_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = self::getConfigValue('SMTP_USERNAME');
            $mail->Password   = self::getConfigValue('SMTP_PASSWORD');

            if (empty($mail->Username) || empty($mail->Password)) {
                throw new \Exception('SMTP chưa được cấu hình. Hãy cập nhật SMTP_USERNAME và SMTP_PASSWORD.');
            }
            
            // Xử lý SMTP_SECURE
            $smtpSecure = strtolower(self::getConfigValue('SMTP_SECURE', 'tls'));
            if ($smtpSecure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $smtpSecure = 'tls';
            }

            $defaultPort = $smtpSecure === 'ssl' ? 465 : 587;
            $mail->Port    = (int) self::getConfigValue('SMTP_PORT', $defaultPort);
            $mail->CharSet = 'UTF-8';

            // Nếu là localhost, có thể sử dụng debug mode
            if (self::getConfigValue('SMTP_DEBUG', false)) {
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            }

            $fromEmail = self::getConfigValue('SMTP_FROM_EMAIL', $mail->Username);
            if (empty($fromEmail)) {
                throw new \Exception('Không tìm thấy địa chỉ gửi email. Vui lòng cấu hình SMTP_FROM_EMAIL.');
            }

            $mail->setFrom(
                $fromEmail,
                self::getConfigValue('SMTP_FROM_NAME', 'BonBon')
            );

            // Người nhận
            $mail->addAddress($toEmail, $toName);

            // Định dạng HTML
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            // Gửi email
            $mail->send();
            return ['success' => true, 'message' => 'Email đã được gửi thành công'];
            
        } catch (\Throwable $e) {
            $errorMessage = $mail && !empty($mail->ErrorInfo) ? $mail->ErrorInfo : $e->getMessage();
            error_log("Mail Error: " . $errorMessage);
            return [
                'success' => false, 
                'message' => 'Không thể gửi email: ' . $errorMessage
            ];
        }
    }

    /**
     * Lấy giá trị cấu hình ưu tiên hằng số, sau đó tới biến môi trường
     */
    private static function getConfigValue(string $name, $default = '')
    {
        $value = defined($name) ? constant($name) : getenv($name);

        if ($value === false || $value === null) {
            return $default;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value === '' ? $default : $value;
    }
}

