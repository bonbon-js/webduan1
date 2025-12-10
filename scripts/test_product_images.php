<?php
/**
 * Script test để kiểm tra ảnh sản phẩm có được lưu vào database không
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

$productModel = new ProductModel();

// Lấy sản phẩm mới nhất
$products = $productModel->getAllProducts(5);

echo "<h2>Kiểm tra ảnh sản phẩm</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Product ID</th><th>Product Name</th><th>Image từ getAllProducts()</th><th>Ảnh từ product_images table</th></tr>";

foreach ($products as $product) {
    $productId = $product['id'] ?? 0;
    $productName = $product['name'] ?? 'N/A';
    $imageFromQuery = $product['image'] ?? 'NULL';
    
    // Lấy ảnh trực tiếp từ database
    $images = $productModel->getProductImages($productId);
    $imageFromTable = !empty($images) ? $images[0]['image_url'] : 'NULL';
    
    echo "<tr>";
    echo "<td>{$productId}</td>";
    echo "<td>{$productName}</td>";
    echo "<td>" . ($imageFromQuery ?: 'NULL') . "</td>";
    echo "<td>" . ($imageFromTable ?: 'NULL') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Kiểm tra bảng product_images có tồn tại không
try {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM product_images");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<h3>Tổng số ảnh trong bảng product_images: {$count}</h3>";
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Lỗi: " . $e->getMessage() . "</h3>";
}

