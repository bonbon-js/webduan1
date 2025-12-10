<?php
/**
 * Script để kiểm tra ảnh sản phẩm có được lưu vào database không
 * Truy cập: http://localhost/webduan1/scripts/check_product_image.php?product_id=1
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($productId <= 0) {
    die("Vui lòng cung cấp product_id. Ví dụ: ?product_id=1");
}

$productModel = new ProductModel();
$pdo = getPDO();

echo "<h2>Kiểm tra ảnh sản phẩm ID: {$productId}</h2>";

// 1. Kiểm tra sản phẩm có tồn tại không
$product = $productModel->getProductById($productId);
if (!$product) {
    die("<p style='color: red;'>Sản phẩm không tồn tại!</p>");
}

echo "<h3>1. Thông tin sản phẩm:</h3>";
echo "<p><strong>Tên:</strong> " . htmlspecialchars($product['name'] ?? 'N/A') . "</p>";
echo "<p><strong>Image từ getProductById():</strong> " . ($product['image'] ?? 'NULL') . "</p>";

// 2. Kiểm tra ảnh trong bảng product_images
echo "<h3>2. Ảnh trong bảng product_images:</h3>";
try {
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :pid ORDER BY is_primary DESC, image_id ASC");
    $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($images)) {
        echo "<p style='color: red;'>❌ Không có ảnh nào trong bảng product_images cho sản phẩm này!</p>";
    } else {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Image ID</th><th>Product ID</th><th>Image URL</th><th>Is Primary</th><th>Preview</th></tr>";
        foreach ($images as $img) {
            $imgUrl = $img['image_url'] ?? '';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($img['image_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($img['product_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($imgUrl) . "</td>";
            echo "<td>" . ($img['is_primary'] ? 'Yes' : 'No') . "</td>";
            echo "<td>";
            if (!empty($imgUrl)) {
                $processedUrl = getProductImageUrl($imgUrl, false);
                echo "<img src='" . htmlspecialchars($processedUrl) . "' style='max-width: 100px; max-height: 100px;' onerror='this.style.display=\"none\"; this.nextSibling.style.display=\"inline\";'><span style='display:none; color:red;'>❌ Lỗi tải ảnh</span>";
            } else {
                echo "NULL";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 3. Kiểm tra file ảnh có tồn tại không
echo "<h3>3. Kiểm tra file ảnh:</h3>";
if (!empty($images)) {
    foreach ($images as $img) {
        $imgUrl = $img['image_url'] ?? '';
        if (empty($imgUrl)) continue;
        
        // Lấy đường dẫn file từ URL
        $filePath = '';
        if (strpos($imgUrl, BASE_URL) === 0) {
            $relativePath = str_replace(BASE_URL, '', $imgUrl);
            $filePath = PATH_ROOT . $relativePath;
        } elseif (strpos($imgUrl, 'assets/') === 0) {
            $filePath = PATH_ROOT . $imgUrl;
        }
        
        if (!empty($filePath)) {
            $exists = file_exists($filePath);
            echo "<p><strong>File:</strong> " . htmlspecialchars($filePath) . "</p>";
            echo "<p><strong>Tồn tại:</strong> " . ($exists ? "✅ Yes" : "❌ No") . "</p>";
            if ($exists) {
                echo "<p><strong>Kích thước:</strong> " . filesize($filePath) . " bytes</p>";
            }
        }
    }
}

// 4. Test query giống getAdminProducts
echo "<h3>4. Test query giống getAdminProducts():</h3>";
try {
    $sql = "SELECT 
                p.product_id,
                p.product_name,
                (
                    SELECT image_url FROM product_images i 
                    WHERE i.product_id = p.product_id 
                    ORDER BY i.is_primary DESC, i.image_id ASC 
                    LIMIT 1
                ) as image_url
            FROM products p
            WHERE p.product_id = :pid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p><strong>Product ID:</strong> " . htmlspecialchars($result['product_id'] ?? 'N/A') . "</p>";
        echo "<p><strong>Product Name:</strong> " . htmlspecialchars($result['product_name'] ?? 'N/A') . "</p>";
        echo "<p><strong>Image URL từ query:</strong> " . ($result['image_url'] ?? 'NULL') . "</p>";
    } else {
        echo "<p style='color: red;'>Không tìm thấy kết quả!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}

