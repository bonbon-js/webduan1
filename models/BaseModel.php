<?php

class BaseModel
{
    protected $table;
    protected $pdo;

    // Kết nối CSDL
    public function __construct()
    {
        if (empty(DB_NAME)) {
            die("Vui lòng cấu hình tên database trong file configs/env.php (DB_NAME)");
        }

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME);

        try {
            $this->pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
        } catch (PDOException $e) {
            // Xử lý lỗi kết nối
            die("Kết nối cơ sở dữ liệu thất bại: {$e->getMessage()}.<br>Vui lòng kiểm tra:<br>1. Database đã được tạo chưa?<br>2. Thông tin kết nối trong configs/env.php đã đúng chưa?<br>3. MySQL đã chạy chưa?");
        }
    }

    // Hủy kết nối CSDL
    public function __destruct()
    {
        $this->pdo = null;
    }
}
