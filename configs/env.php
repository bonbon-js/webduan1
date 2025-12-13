<?php

// Thiết lập timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// CẤU HÌNH TÊN MIỀN CỦA BẠN
// Lưu ý: Nếu web của bạn có SSL (ổ khóa), hãy để https://
define('BASE_URL',          'https://phuongha.online/');

define('PATH_ROOT',         __DIR__ . '/../');
define('PATH_VIEW',         PATH_ROOT . 'views/');
define('PATH_VIEW_MAIN',    PATH_ROOT . 'views/main.php');
define('BASE_ASSETS_UPLOADS',   BASE_URL . 'assets/uploads/');
define('PATH_ASSETS_UPLOADS',   PATH_ROOT . 'assets/uploads/');
define('PATH_CONTROLLER',       PATH_ROOT . 'controllers/');
define('PATH_MODEL',            PATH_ROOT . 'models/');

// CẤU HÌNH DATABASE TRÊN HOSTING
// Bạn cần thay đổi thông tin bên dưới theo thông tin Hosting cung cấp khi bạn tạo Database
define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'bonbon_user');
define('DB_PASSWORD', 'Ha1119990343748764');
define('DB_NAME',     'bonbon_shop');

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Cấu hình Email (Giữ nguyên hoặc cập nhật nếu cần)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME', 'le3221981@gmail.com');
define('MAIL_PASSWORD', 'pslo nbcf htvf ftij'); 
define('MAIL_FROM_ADDRESS', 'le3221981@gmail.com');
define('MAIL_FROM_NAME', 'BonBon Shop');

// Cấu hình VNPay (Đã tự động cập nhật theo tên miền mới)
define('VNPAY_TMN_CODE', '1ZGKCU42'); 
define('VNPAY_HASH_SECRET', 'Z4VL78GMY70MK3S4E624BLLRFODGMQGG'); 
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); 
define('VNPAY_IPN_URL', BASE_URL . '?action=vnpay-ipn'); 
define('VNPAY_ENABLE_IPN', true); // Trên hosting thì BẬT cái này lên
