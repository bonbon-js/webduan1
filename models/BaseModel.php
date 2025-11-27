<?php

class BaseModel
{
    protected $table;
    protected $pdo;

    // Kết nối CSDL
    public function __construct()
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

        try {
            $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
            // Đảm bảo kết nối hoạt động
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            // Log lỗi chi tiết
            error_log('Database connection failed: ' . $e->getMessage());
            error_log('Connection details: Host=' . DB_HOST . ', Port=' . DB_PORT . ', DB=' . DB_NAME);
            
            // Xử lý lỗi kết nối với thông báo rõ ràng hơn
            $errorMsg = "Kết nối cơ sở dữ liệu thất bại.";
            
            // Thông báo cụ thể hơn cho từng loại lỗi
            if (strpos($e->getMessage(), 'Access denied') !== false) {
                $errorMsg .= " Sai thông tin đăng nhập (username/password).";
            } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
                $errorMsg .= " Database '" . DB_NAME . "' không tồn tại.";
            } elseif (strpos($e->getMessage(), 'Connection refused') !== false || 
                      strpos($e->getMessage(), 'No connection') !== false) {
                $errorMsg .= " Không thể kết nối đến server database tại " . DB_HOST . ":" . DB_PORT . ".";
                $errorMsg .= " Vui lòng kiểm tra MySQL đã chạy và cho phép kết nối từ xa chưa.";
            } else {
                $errorMsg .= " " . $e->getMessage();
            }
            
            die($errorMsg . " Vui lòng thử lại sau hoặc liên hệ quản trị viên.");
        }
    }

    // Hủy kết nối CSDL
    public function __destruct()
    {
        $this->pdo = null;
    }
}
