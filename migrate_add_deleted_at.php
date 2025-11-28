<?php

require_once 'configs/env.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Migration: Thêm cột deleted_at</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Migration: Thêm cột deleted_at</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<p>✓ Đã kết nối database thành công!</p>";
    echo "<p>Database: <strong>" . DB_NAME . "</strong></p>";

    // Kiểm tra xem cột deleted_at đã tồn tại chưa
    $checkSql = "SELECT COUNT(*) as count 
                 FROM INFORMATION_SCHEMA.COLUMNS 
                 WHERE TABLE_SCHEMA = :db_name 
                 AND TABLE_NAME = 'coupons' 
                 AND COLUMN_NAME = 'deleted_at'";

    $stmt = $pdo->prepare($checkSql);
    $stmt->execute(['db_name' => DB_NAME]);
    $result = $stmt->fetch();

    if ($result['count'] > 0) {
        echo "<div class='info'>ℹ Cột 'deleted_at' đã tồn tại trong bảng 'coupons'. Không cần thêm nữa.</div>";
    } else {
        echo "<p>Đang kiểm tra cấu trúc bảng...</p>";
        
        // Thêm cột deleted_at
        $sql = "ALTER TABLE coupons ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL";
        $pdo->exec($sql);
        
        echo "<div class='success'>✓ Đã thêm cột 'deleted_at' vào bảng 'coupons' thành công!</div>";
        echo "<p>Bạn có thể đóng trang này và quay lại trang admin.</p>";
    }

} catch (PDOException $e) {
    echo "<div class='error'>✗ Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "    </div>
</body>
</html>";

