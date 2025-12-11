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
    function set_flash(string $type, string $message, array $data = []): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
            'data'    => $data,
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

/**
 * Hàm xử lý URL ảnh sản phẩm - chuyển đổi đường dẫn tương đối thành URL đầy đủ hoặc base64
 * @param string|null $imageUrl Đường dẫn ảnh (có thể là tương đối, URL đầy đủ, hoặc base64)
 * @param bool $useBase64 Nếu true, chuyển thành base64 (giống home page), nếu false thì dùng BASE_URL
 * @return string URL ảnh đầy đủ hoặc base64 data URL
 */
if (!function_exists('getProductImageUrl')) {
    function getProductImageUrl(?string $imageUrl, bool $useBase64 = false): string
    {
        if (empty($imageUrl)) {
            return '';
        }
        
        // Nếu đã là data URL (base64) hoặc URL đầy đủ (http/https), trả về nguyên
        if (strpos($imageUrl, 'data:image/') === 0 || strpos($imageUrl, 'http://') === 0 || strpos($imageUrl, 'https://') === 0) {
            return $imageUrl;
        }
        
        // Nếu là đường dẫn tương đối bắt đầu bằng assets/
        if (strpos($imageUrl, 'assets/') === 0) {
            $filePath = PATH_ROOT . $imageUrl;
            if (file_exists($filePath)) {
                if ($useBase64) {
                    // Chuyển thành base64 giống home page
                    $data = file_get_contents($filePath);
                    $type = pathinfo($filePath, PATHINFO_EXTENSION);
                    return 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    // Dùng BASE_URL
                    return BASE_URL . $imageUrl;
                }
            } else {
                // File không tồn tại, trả về URL tương đối
                return BASE_URL . $imageUrl;
            }
        }
        
        // Nếu là đường dẫn tương đối khác (không bắt đầu bằng assets/)
        if (!strpos($imageUrl, '://')) {
            // Thử kiểm tra file tồn tại
            $filePath = PATH_ROOT . $imageUrl;
            if (file_exists($filePath)) {
                if ($useBase64) {
                    $data = file_get_contents($filePath);
                    $type = pathinfo($filePath, PATHINFO_EXTENSION);
                    return 'data:image/' . $type . ';base64,' . base64_encode($data);
                } else {
                    return BASE_URL . $imageUrl;
                }
            } else {
                // Nếu file không tồn tại, thử thêm assets/uploads/
                $filePath = PATH_ROOT . 'assets/uploads/' . $imageUrl;
                if (file_exists($filePath)) {
                    if ($useBase64) {
                        $data = file_get_contents($filePath);
                        $type = pathinfo($filePath, PATHINFO_EXTENSION);
                        return 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        return BASE_URL . 'assets/uploads/' . $imageUrl;
                    }
                } else {
                    // Không tìm thấy file, trả về URL với BASE_URL
                    return BASE_URL . $imageUrl;
                }
            }
        }
        
        // Trường hợp còn lại, trả về nguyên
        return $imageUrl;
    }
}

