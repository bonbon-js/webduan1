<?php

require_once PATH_ROOT . 'phpmailer/src/PHPMailer.php';
require_once PATH_ROOT . 'phpmailer/src/SMTP.php';
require_once PATH_ROOT . 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Hàm debug: in dữ liệu và dừng chương trình
 */
if (!function_exists('debug')) {
    function debug($data)
    {
        echo '<pre>';
        print_r($data);
        die;
    }
}

/**
 * Hàm upload file
 */
if (!function_exists('upload_file')) {
    function upload_file($folder, $file)
    {
        $targetFile = $folder . '/' . time() . '-' . $file["name"];

        if (move_uploaded_file($file["tmp_name"], PATH_ASSETS_UPLOADS . $targetFile)) {
            return $targetFile;
        }

        throw new Exception('Upload file không thành công!');
    }
}

/**
 * Flash message: lưu 1 lần
 */
if (!function_exists('set_flash')) {
    function set_flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('get_flash')) {
    function get_flash(): ?array
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
}

/**
 * Hàm gửi email bằng PHPMailer
 */
if (!function_exists('send_mail')) {
    function send_mail(string $to, string $subject, string $html, string $toName = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($to, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Hàm kết nối PDO — BẠN BỊ THIẾU HÀM NÀY nên VS Code báo lỗi getPDO()
 */
if (!function_exists('getPDO')) {
    function getPDO()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8';

            $pdo = new PDO(
                $dsn,
                DB_USERNAME,   // sử dụng đúng tên hằng từ env.php
                DB_PASSWORD,   // sử dụng đúng tên hằng từ env.php
                DB_OPTIONS     // mảng options bạn đã định nghĩa sẵn
            );

            return $pdo;

        } catch (PDOException $e) {
            die("Kết nối database thất bại: " . $e->getMessage());
        }
    }
}

