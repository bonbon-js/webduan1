<?php
// Script để tạo hoặc cập nhật bảng coupons với đầy đủ các cột
// Truy cập: http://localhost/webduan1/create_coupons_table_complete.php

require_once 'configs/env.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Migration: Tạo/Cập nhật bảng coupons</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Migration: Tạo/Cập nhật bảng coupons</h1>";

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

    // Kiểm tra xem bảng coupons có tồn tại không
    $checkTable = "SELECT COUNT(*) as count 
                   FROM INFORMATION_SCHEMA.TABLES 
                   WHERE TABLE_SCHEMA = :db_name 
                   AND TABLE_NAME = 'coupons'";
    
    $stmt = $pdo->prepare($checkTable);
    $stmt->execute(['db_name' => DB_NAME]);
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "<div class='warning'>⚠ Bảng 'coupons' chưa tồn tại. Đang tạo bảng...</div>";
        
        // Tạo bảng coupons với đầy đủ các cột
        $createTable = "CREATE TABLE IF NOT EXISTS coupons (
            coupon_id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            discount_type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
            discount_value DECIMAL(10,2) NOT NULL,
            min_order_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
            max_discount_amount DECIMAL(10,2) NULL,
            start_date DATETIME NOT NULL,
            end_date DATETIME NOT NULL,
            usage_limit INT NULL,
            used_count INT NOT NULL DEFAULT 0,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL DEFAULT NULL,
            INDEX idx_code (code),
            INDEX idx_status (status),
            INDEX idx_dates (start_date, end_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($createTable);
        echo "<div class='success'>✓ Đã tạo bảng 'coupons' thành công!</div>";
    } else {
        echo "<div class='info'>ℹ Bảng 'coupons' đã tồn tại. Đang kiểm tra các cột...</div>";
        
        // Lấy danh sách các cột hiện có
        $stmt = $pdo->query("SHOW COLUMNS FROM coupons");
        $existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
        
        // Danh sách các cột cần thiết
        $requiredColumns = [
            'coupon_id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'code' => 'VARCHAR(50) NOT NULL',
            'name' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'discount_type' => "ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage'",
            'discount_value' => 'DECIMAL(10,2) NOT NULL',
            'min_order_amount' => 'DECIMAL(10,2) NOT NULL DEFAULT 0',
            'max_discount_amount' => 'DECIMAL(10,2) NULL',
            'start_date' => 'DATETIME NOT NULL',
            'end_date' => 'DATETIME NOT NULL',
            'usage_limit' => 'INT NULL',
            'used_count' => 'INT NOT NULL DEFAULT 0',
            'status' => "ENUM('active', 'inactive') NOT NULL DEFAULT 'active'",
            'created_at' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'DATETIME NULL ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at' => 'DATETIME NULL DEFAULT NULL'
        ];
        
        // Sửa dữ liệu không hợp lệ trước khi thêm cột
        echo "<div class='info'>Đang kiểm tra và sửa dữ liệu không hợp lệ...</div>";
        
        // Kiểm tra xem có dữ liệu không hợp lệ không
        try {
            $checkData = "SELECT COUNT(*) as count FROM coupons";
            $stmt = $pdo->query($checkData);
            $dataCount = $stmt->fetch()['count'];
            
            if ($dataCount > 0) {
                echo "<p>Phát hiện {$dataCount} bản ghi trong bảng. Đang kiểm tra dữ liệu...</p>";
                
                // Nếu có cột start_date hoặc end_date với giá trị không hợp lệ, sửa chúng
                if (in_array('start_date', $existingColumns)) {
                    $pdo->exec("UPDATE coupons SET start_date = NOW() WHERE start_date = '0000-00-00 00:00:00' OR start_date IS NULL");
                    echo "<p>✓ Đã sửa dữ liệu start_date không hợp lệ</p>";
                }
                if (in_array('end_date', $existingColumns)) {
                    $pdo->exec("UPDATE coupons SET end_date = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE end_date = '0000-00-00 00:00:00' OR end_date IS NULL");
                    echo "<p>✓ Đã sửa dữ liệu end_date không hợp lệ</p>";
                }
            }
        } catch (PDOException $e) {
            echo "<div class='warning'>⚠ Không thể kiểm tra dữ liệu: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        // Thứ tự thêm cột (theo thứ tự phụ thuộc)
        $columnOrder = [
            'code' => 'coupon_id',
            'name' => 'code',
            'description' => 'name',
            'discount_type' => 'description',
            'discount_value' => 'discount_type',
            'min_order_amount' => 'discount_value',
            'max_discount_amount' => 'min_order_amount',
            'start_date' => 'max_discount_amount',
            'end_date' => 'start_date',
            'usage_limit' => 'end_date',
            'used_count' => 'usage_limit',
            'status' => 'used_count',
            'created_at' => 'status',
            'updated_at' => 'created_at',
            'deleted_at' => 'updated_at'
        ];
        
        $addedColumns = [];
        foreach ($columnOrder as $colName => $afterCol) {
            if (!in_array($colName, $existingColumns)) {
                try {
                    // Kiểm tra xem cột "after" có tồn tại không
                    if (!in_array($afterCol, $existingColumns) && $afterCol !== 'coupon_id') {
                        // Nếu cột "after" chưa tồn tại, thêm vào cuối
                        $afterColumn = '';
                    } else {
                        $afterColumn = " AFTER {$afterCol}";
                    }
                    
                    $colDef = $requiredColumns[$colName];
                    
                    // Đối với các cột datetime NOT NULL, tạm thời cho phép NULL để tránh lỗi với dữ liệu hiện có
                    if (strpos($colDef, 'DATETIME NOT NULL') !== false && strpos($colDef, 'DEFAULT') === false) {
                        if ($colName === 'start_date' || $colName === 'end_date') {
                            // Thêm với NULL trước, sau đó cập nhật dữ liệu và đổi thành NOT NULL
                            $tempColDef = str_replace('DATETIME NOT NULL', 'DATETIME NULL', $colDef);
                            $alterSql = "ALTER TABLE coupons ADD COLUMN {$colName} {$tempColDef}{$afterColumn}";
                            $pdo->exec($alterSql);
                            
                            // Cập nhật giá trị cho các bản ghi hiện có
                            $defaultValue = $colName === 'start_date' ? 'NOW()' : 'DATE_ADD(NOW(), INTERVAL 1 MONTH)';
                            $updateSql = "UPDATE coupons SET {$colName} = {$defaultValue} WHERE {$colName} IS NULL";
                            $pdo->exec($updateSql);
                            
                            // Đổi thành NOT NULL
                            $modifySql = "ALTER TABLE coupons MODIFY COLUMN {$colName} {$colDef}";
                            $pdo->exec($modifySql);
                            
                            $addedColumns[] = $colName;
                            echo "<p>✓ Đã thêm cột: <strong>{$colName}</strong> (đã cập nhật dữ liệu)</p>";
                        } else {
                            // Các cột datetime khác
                            $alterSql = "ALTER TABLE coupons ADD COLUMN {$colName} {$colDef}{$afterColumn}";
                            $pdo->exec($alterSql);
                            $addedColumns[] = $colName;
                            echo "<p>✓ Đã thêm cột: <strong>{$colName}</strong></p>";
                        }
                    } else {
                        // Các cột không phải datetime NOT NULL
                        $alterSql = "ALTER TABLE coupons ADD COLUMN {$colName} {$colDef}{$afterColumn}";
                        $pdo->exec($alterSql);
                        $addedColumns[] = $colName;
                        echo "<p>✓ Đã thêm cột: <strong>{$colName}</strong></p>";
                    }
                    
                    // Cập nhật danh sách cột hiện có
                    $existingColumns[] = $colName;
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate column name') === false) {
                        echo "<div class='error'>✗ Lỗi khi thêm cột {$colName}: " . htmlspecialchars($e->getMessage()) . "</div>";
                        // Nếu lỗi do dữ liệu không hợp lệ, thử sửa dữ liệu trước
                        if (strpos($e->getMessage(), 'Invalid datetime') !== false || strpos($e->getMessage(), 'Incorrect datetime') !== false) {
                            echo "<div class='warning'>⚠ Đang cố gắng sửa dữ liệu không hợp lệ...</div>";
                            try {
                                // Xóa hoặc sửa các bản ghi có dữ liệu không hợp lệ
                                $pdo->exec("DELETE FROM coupons WHERE start_date = '0000-00-00 00:00:00' OR end_date = '0000-00-00 00:00:00'");
                                // Thử lại
                                $pdo->exec($alterSql);
                                $addedColumns[] = $colName;
                                echo "<p>✓ Đã thêm cột: <strong>{$colName}</strong> (sau khi sửa dữ liệu)</p>";
                                $existingColumns[] = $colName;
                            } catch (PDOException $e2) {
                                echo "<div class='error'>✗ Vẫn không thể thêm cột {$colName}: " . htmlspecialchars($e2->getMessage()) . "</div>";
                            }
                        }
                    }
                }
            }
        }
        
        if (empty($addedColumns)) {
            echo "<div class='success'>✓ Tất cả các cột cần thiết đã tồn tại!</div>";
        } else {
            echo "<div class='success'>✓ Đã thêm " . count($addedColumns) . " cột mới!</div>";
        }
    }

} catch (PDOException $e) {
    echo "<div class='error'>✗ Lỗi: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "    </div>
</body>
</html>";

