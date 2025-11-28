<?php
/**
 * Script để thêm cột deleted_at vào bảng products
 * Chạy script này một lần để thêm cột deleted_at
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';

try {
    $pdo = getPDO();
    
    // Kiểm tra xem cột đã tồn tại chưa
    $checkColumn = $pdo->query("SHOW COLUMNS FROM products LIKE 'deleted_at'");
    if ($checkColumn->rowCount() > 0) {
        echo "Cột deleted_at đã tồn tại trong bảng products.\n";
        exit;
    }
    
    // Thêm cột deleted_at
    $pdo->exec("ALTER TABLE products ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL AFTER updated_at");
    
    echo "Đã thêm cột deleted_at vào bảng products thành công!\n";
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

