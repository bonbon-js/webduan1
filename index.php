<?php 

// Cấu hình session để hoạt động tốt trên mọi thiết bị
if (session_status() === PHP_SESSION_NONE) {
    // Cấu hình session cookie để hoạt động trên mọi thiết bị
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
}

spl_autoload_register(function ($class) {    
    $fileName = "$class.php";

    $fileModel              = PATH_MODEL . $fileName;
    $fileController         = PATH_CONTROLLER . $fileName;

    if (is_readable($fileModel)) {
        require_once $fileModel;
    } 
    else if (is_readable($fileController)) {
        require_once $fileController;
    }
});

require_once './configs/env.php';
require_once './configs/helper.php';

// Điều hướng
require_once './routes/index.php';
