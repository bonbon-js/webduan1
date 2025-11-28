<?php

// Thiết lập timezone mặc định cho toàn bộ ứng dụng
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('BASE_URL',          'http://localhost/webduan1/');

define('PATH_ROOT',         __DIR__ . '/../');

define('PATH_VIEW',         PATH_ROOT . 'views/');

define('PATH_VIEW_MAIN',    PATH_ROOT . 'views/main.php');

define('BASE_ASSETS_UPLOADS',   BASE_URL . 'assets/uploads/');

define('PATH_ASSETS_UPLOADS',   PATH_ROOT . 'assets/uploads/');

define('PATH_CONTROLLER',       PATH_ROOT . 'controllers/');

define('PATH_MODEL',            PATH_ROOT . 'models/');

define('DB_HOST',     'localhost');
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'bonbon_shop');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// SMTP configuration (cập nhật bằng thông tin Gmail/app password của bạn)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME', 'le3221981@gmail.com');
define('MAIL_PASSWORD', 'pslo nbcf htvf ftij'); // App Password của Gmail
define('MAIL_FROM_ADDRESS', 'le3221981@gmail.com');
define('MAIL_FROM_NAME', 'bonbon_shop');

// VNPay Configuration
// Lấy thông tin từ https://sandbox.vnpayment.vn/apis/docs/
define('VNPAY_TMN_CODE', ''); // Điền mã website của bạn
define('VNPAY_HASH_SECRET', ''); // Điền mã bảo mật từ VNPay
define('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); // Sandbox URL
// Production URL: 'https://www.vnpayment.vn/paymentv2/vpcpay.html'
