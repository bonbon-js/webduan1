<?php
/**
 * Script thêm cột deleted_at vào bảng products (chỉ cần chạy 1 lần)
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';

try {
    $pdo = getPDO();

    // Kiểm tra cột deleted_at đã tồn tại chưa
    $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE 'deleted_at'");
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "⚠️  Cột deleted_at đã tồn tại trong bảng products.\n";
        exit;
    }

    // Thêm cột deleted_at
    $alterSQL = "ALTER TABLE products 
                 ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL 
                 AFTER updated_at";

    $pdo->exec($alterSQL);

    echo "✅ Đã thêm cột deleted_at vào bảng products thành công!\n";

} catch (PDOException $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
