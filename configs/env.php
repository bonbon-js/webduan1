<?php

// Tự động phát hiện BASE_URL dựa trên server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . '://' . $host . $scriptPath;
// Đảm bảo có dấu / ở cuối
$baseUrl = rtrim($baseUrl, '/') . '/';

define('BASE_URL', $baseUrl);

define('PATH_ROOT',         __DIR__ . '/../');

define('PATH_VIEW',         PATH_ROOT . 'views/');

define('PATH_VIEW_MAIN',    PATH_ROOT . 'views/main.php');

define('BASE_ASSETS_UPLOADS',   BASE_URL . 'assets/uploads/');

define('PATH_ASSETS_UPLOADS',   PATH_ROOT . 'assets/uploads/');

define('PATH_CONTROLLER',       PATH_ROOT . 'controllers/');

define('PATH_MODEL',            PATH_ROOT . 'models/');

// Database Configuration
// Có thể kết nối từ máy khác bằng cách thay đổi DB_HOST
// Ví dụ: '192.168.1.100' hoặc domain name
define('DB_HOST',     'localhost');  // Thay đổi thành IP máy chủ database nếu kết nối từ xa
define('DB_PORT',     '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'bonbon_shop');
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 5, // Timeout 5 giây
]);

// SMTP configuration (cập nhật bằng thông tin Gmail/app password của bạn)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_USERNAME', 'le3221981@gmail.com');
define('MAIL_PASSWORD', 'pslo nbcf htvf ftij'); // App Password của Gmail
define('MAIL_FROM_ADDRESS', 'le3221981@gmail.com');
define('MAIL_FROM_NAME', 'bonbon_shop');
