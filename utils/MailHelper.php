<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer (KHÔNG dùng composer)
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';
require_once __DIR__ . '/../phpmailer/src/Exception.php';

class MailHelper
{
    public static function sendRegistrationMail($toEmail, $toName)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;

            // Thay bằng email & app password của bạn
            $mail->Username   = 'yourgmail@gmail.com';
            $mail->Password   = 'your_app_password';

            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // From
            $mail->setFrom('yourgmail@gmail.com', 'Bon Bon Shop');

            // To
            $mail->addAddress($toEmail, $toName);

            // Title
            $mail->Subject = "🎉 Đăng ký tài khoản thành công – Bon Bon Shop";

            // Nội dung HTML
            $mail->isHTML(true);
            $mail->Body = "
                <h3>Xin chào <b>$toName</b>!</h3>
                <p>Bạn đã đăng ký tài khoản thành công tại <b>Bon Bon Shop</b>.</p>
                <p>Cảm ơn bạn đã tin tưởng chúng tôi ❤️</p>
            ";

            // Gửi mail
            return $mail->send();

        } catch (Exception $e) {
            error_log("Mail error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
