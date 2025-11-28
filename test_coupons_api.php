<?php
require_once 'configs/env.php';
require_once PATH_MODEL . 'BaseModel.php';
require_once PATH_MODEL . 'CouponModel.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Test Coupons API</h2>";

$couponModel = new CouponModel();

// Sử dụng timezone Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra ngày hiện tại
$now = date('Y-m-d H:i:s');
echo "<h3>Ngày hiện tại: $now</h3>";

// Test với orderAmount = 0
echo "<h3>Test với orderAmount = 0:</h3>";
$coupons0 = $couponModel->getAvailableCoupons(0);
echo "<pre>";
print_r($coupons0);
echo "</pre>";
echo "<p>Số lượng mã: " . count($coupons0) . "</p>";

// Test SQL query trực tiếp
echo "<h3>Test SQL query trực tiếp:</h3>";
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
    
    // Test 1: Chỉ lấy mã đang hoạt động (đã bắt đầu và chưa hết hạn)
    $sql1 = "SELECT * FROM coupons 
            WHERE status = 'active'
              AND deleted_at IS NULL
              AND start_date <= :now
              AND end_date >= :now
              AND (min_order_amount IS NULL OR min_order_amount = 0)
              AND (usage_limit IS NULL OR used_count < usage_limit)
            ORDER BY discount_value DESC, created_at DESC";
    
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute(['now' => $now]);
    $activeResults = $stmt1->fetchAll();
    
    echo "<h4>Mã đang hoạt động (đã bắt đầu và chưa hết hạn):</h4>";
    echo "<p>Số lượng: " . count($activeResults) . "</p>";
    echo "<pre>";
    print_r($activeResults);
    echo "</pre>";
    
    // Test 2: Lấy tất cả mã active (bao gồm cả chưa bắt đầu)
    $sql2 = "SELECT * FROM coupons 
            WHERE status = 'active'
              AND deleted_at IS NULL
              AND end_date >= :now
              AND (min_order_amount IS NULL OR min_order_amount = 0)
              AND (usage_limit IS NULL OR used_count < usage_limit)
            ORDER BY discount_value DESC, created_at DESC";
    
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute(['now' => $now]);
    $allActiveResults = $stmt2->fetchAll();
    
    echo "<h4>Mã active (bao gồm cả chưa bắt đầu):</h4>";
    echo "<p>Số lượng: " . count($allActiveResults) . "</p>";
    echo "<pre>";
    print_r($allActiveResults);
    echo "</pre>";
    
    // Test 3: SQL giống như trong getAvailableCoupons
    $sql = "SELECT * FROM coupons 
            WHERE status = 'active'
              AND deleted_at IS NULL
              AND start_date <= :now
              AND end_date >= :now
              AND (min_order_amount IS NULL OR min_order_amount = 0)
              AND (usage_limit IS NULL OR used_count < usage_limit)
            ORDER BY discount_value DESC, created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['now' => $now]);
    $directResults = $stmt->fetchAll();
    
    echo "<p>Số lượng mã từ SQL trực tiếp: " . count($directResults) . "</p>";
    echo "<pre>";
    print_r($directResults);
    echo "</pre>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Lỗi SQL: " . $e->getMessage() . "</p>";
}

// Test với orderAmount = 2006000 (tổng tiền từ hình ảnh)
echo "<h3>Test với orderAmount = 2006000:</h3>";
$coupons2006 = $couponModel->getAvailableCoupons(2006000);
echo "<pre>";
print_r($coupons2006);
echo "</pre>";
echo "<p>Số lượng mã: " . count($coupons2006) . "</p>";

// Kiểm tra tất cả mã giảm giá trong database
echo "<h3>Tất cả mã giảm giá trong database:</h3>";
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
    
    $sql = "SELECT coupon_id, code, name, status, start_date, end_date, min_order_amount, usage_limit, used_count, deleted_at 
            FROM coupons 
            ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $allCoupons = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Min Order</th><th>Usage Limit</th><th>Used</th><th>Deleted</th></tr>";
    foreach ($allCoupons as $coupon) {
        $now = date('Y-m-d H:i:s');
        $isActive = $coupon['status'] === 'active';
        $isInDate = $coupon['start_date'] <= $now && $coupon['end_date'] >= $now;
        $isAvailable = $coupon['usage_limit'] === null || $coupon['used_count'] < $coupon['usage_limit'];
        $isNotDeleted = $coupon['deleted_at'] === null;
        
        $rowColor = ($isActive && $isInDate && $isAvailable && $isNotDeleted) ? '#d4edda' : '#f8d7da';
        
        echo "<tr style='background-color: $rowColor;'>";
        echo "<td>{$coupon['coupon_id']}</td>";
        echo "<td>{$coupon['code']}</td>";
        echo "<td>{$coupon['name']}</td>";
        echo "<td>{$coupon['status']}</td>";
        echo "<td>{$coupon['start_date']}</td>";
        echo "<td>{$coupon['end_date']}</td>";
        echo "<td>{$coupon['min_order_amount']}</td>";
        echo "<td>" . ($coupon['usage_limit'] ?? 'NULL') . "</td>";
        echo "<td>{$coupon['used_count']}</td>";
        echo "<td>" . ($coupon['deleted_at'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Tổng số mã: " . count($allCoupons) . "</strong></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Lỗi: " . $e->getMessage() . "</p>";
}

