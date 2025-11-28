<?php
/**
 * Script tự động sửa hoàn toàn lỗi Duplicate entry '0' for key 'PRIMARY'
 * Chạy script này một lần để sửa tất cả vấn đề
 */

require_once __DIR__ . '/configs/env.php';

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', DB_HOST, DB_PORT, DB_NAME),
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    echo "========================================\n";
    echo "SỬA LỖI: Duplicate entry '0' for key 'PRIMARY'\n";
    echo "========================================\n\n";
    
    // Bước 1: Tìm PRIMARY KEY
    echo "[1/6] Tìm PRIMARY KEY column...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM orders WHERE `Key` = 'PRI'");
    $primaryKey = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$primaryKey) {
        echo "✗ Không tìm thấy PRIMARY KEY!\n";
        exit(1);
    }
    
    $pkField = $primaryKey['Field'];
    $hasAutoIncrement = strpos($primaryKey['Extra'] ?? '', 'auto_increment') !== false;
    
    echo "  ✓ Primary Key: $pkField\n";
    echo "  " . ($hasAutoIncrement ? '✓' : '✗') . " AUTO_INCREMENT: " . ($hasAutoIncrement ? 'Có' : 'Không') . "\n";
    
    if (!$hasAutoIncrement) {
        echo "\n[1.1] Đang thêm AUTO_INCREMENT...\n";
        try {
            $pdo->exec("ALTER TABLE orders MODIFY $pkField INT(11) NOT NULL AUTO_INCREMENT");
            echo "  ✓ Đã thêm AUTO_INCREMENT\n";
        } catch (PDOException $e) {
            echo "  ✗ Lỗi: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
    
    // Bước 2: Xóa dữ liệu có order_id = 0
    echo "\n[2/6] Xóa dữ liệu có $pkField = 0 hoặc NULL...\n";
    
    // Xóa order_items trước
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM order_items WHERE order_id = 0 OR order_id IS NULL");
        $countItems = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($countItems['count'] > 0) {
            $pdo->exec("DELETE FROM order_items WHERE order_id = 0 OR order_id IS NULL");
            echo "  ✓ Đã xóa {$countItems['count']} bản ghi trong order_items\n";
        } else {
            echo "  ✓ Không có dữ liệu cần xóa trong order_items\n";
        }
    } catch (PDOException $e) {
        echo "  ⚠️ Lỗi khi xóa order_items: " . $e->getMessage() . "\n";
    }
    
    // Xóa orders
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE $pkField = 0 OR $pkField IS NULL");
        $countOrders = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($countOrders['count'] > 0) {
            $pdo->exec("DELETE FROM orders WHERE $pkField = 0 OR $pkField IS NULL");
            echo "  ✓ Đã xóa {$countOrders['count']} bản ghi trong orders\n";
        } else {
            echo "  ✓ Không có dữ liệu cần xóa trong orders\n";
        }
    } catch (PDOException $e) {
        echo "  ⚠️ Lỗi khi xóa orders: " . $e->getMessage() . "\n";
    }
    
    // Bước 3: Lấy MAX ID
    echo "\n[3/6] Lấy ID lớn nhất...\n";
    $stmt = $pdo->query("SELECT MAX($pkField) as max_id FROM orders");
    $maxId = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextId = max(1, ($maxId['max_id'] ?? 0) + 1);
    echo "  ID lớn nhất: " . ($maxId['max_id'] ?? 0) . "\n";
    echo "  ID tiếp theo sẽ là: $nextId\n";
    
    // Bước 4: Set AUTO_INCREMENT
    echo "\n[4/6] Set AUTO_INCREMENT = $nextId...\n";
    try {
        $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = $nextId");
        echo "  ✓ Đã set AUTO_INCREMENT = $nextId\n";
    } catch (PDOException $e) {
        echo "  ⚠️ Không thể set AUTO_INCREMENT: " . $e->getMessage() . "\n";
    }
    
    // Bước 5: Kiểm tra lại
    echo "\n[5/6] Kiểm tra lại...\n";
    $stmt = $pdo->query("SHOW TABLE STATUS LIKE 'orders'");
    $tableStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentAutoIncrement = $tableStatus['Auto_increment'] ?? 'NULL';
    echo "  AUTO_INCREMENT hiện tại: $currentAutoIncrement\n";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM orders WHERE `Key` = 'PRI'");
    $pkInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    $hasAutoIncrementFinal = strpos($pkInfo['Extra'] ?? '', 'auto_increment') !== false;
    
    if (!$hasAutoIncrementFinal) {
        echo "\n✗ AUTO_INCREMENT vẫn chưa được kích hoạt!\n";
        exit(1);
    }
    
    // Bước 6: Test insert
    echo "\n[6/6] Test insert (sẽ rollback)...\n";
    try {
        $pdo->beginTransaction();
        
        // Tìm các cột có thể insert
        $stmt = $pdo->query("SHOW COLUMNS FROM orders");
        $testColumns = [];
        $testValues = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $field = $row['Field'];
            // Bỏ qua PRIMARY KEY
            if ($row['Key'] === 'PRI') {
                continue;
            }
            // Chỉ lấy các cột có thể NULL hoặc có default
            if ($row['Null'] === 'YES' || $row['Default'] !== null) {
                $testColumns[] = $field;
                if ($row['Default'] !== null) {
                    $testValues[] = is_numeric($row['Default']) ? $row['Default'] : "'{$row['Default']}'";
                } else {
                    $testValues[] = 'NULL';
                }
            }
        }
        
        if (!empty($testColumns)) {
            $colsStr = implode(', ', $testColumns);
            $valsStr = implode(', ', $testValues);
            $testSql = "INSERT INTO orders ($colsStr) VALUES ($valsStr)";
            
            $pdo->exec($testSql);
            $testId = $pdo->lastInsertId();
            $pdo->rollBack();
            
            if ($testId > 0) {
                echo "  ✓ Test insert thành công!\n";
                echo "  ✓ ID được tạo: $testId\n";
                echo "  ✓ AUTO_INCREMENT hoạt động đúng!\n";
            } else {
                echo "  ✗ Test insert thất bại: lastInsertId() = 0\n";
            }
        } else {
            echo "  ⚠️ Không có cột phù hợp để test\n";
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo "  ✗ Test insert thất bại: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    echo "========================================\n";
    echo "✓✓✓ HOÀN TẤT!\n";
    echo "========================================\n";
    echo "Bảng orders đã được sửa thành công.\n";
    echo "Bây giờ bạn có thể thử đặt hàng lại.\n";
    echo "\nNếu vẫn lỗi, vui lòng:\n";
    echo "1. Kiểm tra error log để xem chi tiết\n";
    echo "2. Đảm bảo code không insert giá trị vào PRIMARY KEY\n";
    echo "3. Kiểm tra lại AUTO_INCREMENT trong phpMyAdmin\n";

} catch (PDOException $e) {
    echo "\n✗ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

