<?php 

// Bật hiển thị lỗi để debug (tắt khi deploy production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Load configs trước để có PATH_MODEL và PATH_CONTROLLER
require_once './configs/env.php';
require_once './configs/helper.php';

spl_autoload_register(function ($class) {    
    $fileName = "$class.php";

    $fileModel              = PATH_MODEL . $fileName;
    $fileController         = PATH_CONTROLLER . $fileName;

    if (file_exists($fileModel) && is_readable($fileModel)) {
        require_once $fileModel;
    } 
    else if (file_exists($fileController) && is_readable($fileController)) {
        require_once $fileController;
    }
});

// Điều hướng
require_once './routes/index.php';
