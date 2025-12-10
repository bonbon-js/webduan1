<?php
/**
 * Script kiểm tra sản phẩm ID 19
 * Truy cập: http://localhost/webduan1/scripts/check_product_19.php
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

$productId = 19;

echo "<h1>Kiểm tra Sản phẩm ID: {$productId}</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .section { margin: 30px 0; padding: 20px; border: 1px solid #ccc; }
    pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    img { max-width: 200px; max-height: 200px; border: 1px solid #ccc; }
</style>";

$pdo = getPDO();
$productModel = new ProductModel();

// 1. Kiểm tra sản phẩm trong bảng products
echo "<div class='section'>";
echo "<h2>1. Thông tin sản phẩm từ bảng products</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :pid");
    $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo "<table>";
        foreach ($product as $key => $value) {
            echo "<tr><th>" . htmlspecialchars($key) . "</th><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Sản phẩm không tồn tại!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 2. Kiểm tra ảnh trong bảng product_images
echo "<div class='section'>";
echo "<h2>2. Ảnh trong bảng product_images</h2>";
try {
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :pid ORDER BY is_primary DESC, image_id ASC");
    $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($images)) {
        echo "<p class='error'>❌ KHÔNG CÓ ẢNH NÀO trong bảng product_images cho sản phẩm này!</p>";
        echo "<p class='warning'>⚠️ Đây có thể là nguyên nhân chính: Ảnh không được lưu vào database khi tạo sản phẩm.</p>";
    } else {
        echo "<p class='success'>✅ Tìm thấy " . count($images) . " ảnh</p>";
        echo "<table>";
        echo "<tr><th>Image ID</th><th>Product ID</th><th>Image URL</th><th>Is Primary</th><th>Preview</th><th>File Exists</th></tr>";
        foreach ($images as $img) {
            $imgUrl = $img['image_url'] ?? '';
            $fileExists = false;
            $filePath = '';
            
            if (!empty($imgUrl)) {
                // Lấy đường dẫn file
                if (strpos($imgUrl, BASE_URL) === 0) {
                    $relativePath = str_replace(BASE_URL, '', $imgUrl);
                    $filePath = PATH_ROOT . $relativePath;
                } elseif (strpos($imgUrl, 'assets/') === 0) {
                    $filePath = PATH_ROOT . $imgUrl;
                }
                
                $fileExists = !empty($filePath) && file_exists($filePath);
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($img['image_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($img['product_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($imgUrl ?: 'NULL') . "</td>";
            echo "<td>" . ($img['is_primary'] ? 'Yes' : 'No') . "</td>";
            echo "<td>";
            if (!empty($imgUrl)) {
                $processedUrl = getProductImageUrl($imgUrl, false);
                echo "<img src='" . htmlspecialchars($processedUrl) . "' onerror='this.style.display=\"none\"; this.nextSibling.style.display=\"inline\";'><span style='display:none; color:red;'>❌ Lỗi tải ảnh</span>";
            } else {
                echo "NULL";
            }
            echo "</td>";
            echo "<td>" . ($fileExists ? "<span class='success'>✅ Yes</span>" : "<span class='error'>❌ No</span>") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 3. Test getProductById()
echo "<div class='section'>";
echo "<h2>3. Kết quả từ getProductById()</h2>";
try {
    $product = $productModel->getProductById($productId);
    if ($product) {
        echo "<pre>" . print_r($product, true) . "</pre>";
        $imageFromModel = $product['image'] ?? null;
        echo "<p><strong>Image từ model:</strong> " . ($imageFromModel ? htmlspecialchars($imageFromModel) : "<span class='error'>NULL</span>") . "</p>";
        
        if ($imageFromModel) {
            $processedUrl = getProductImageUrl($imageFromModel, false);
            echo "<p><strong>Image URL sau xử lý:</strong> " . htmlspecialchars($processedUrl) . "</p>";
            echo "<p><strong>Preview:</strong></p>";
            echo "<img src='" . htmlspecialchars($processedUrl) . "' onerror='this.style.display=\"none\"; this.nextSibling.style.display=\"inline\";'><span style='display:none; color:red;'>❌ Lỗi tải ảnh</span>";
        }
    } else {
        echo "<p class='error'>❌ getProductById() trả về null</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 4. Test query giống getProductById
echo "<div class='section'>";
echo "<h2>4. Test query giống getProductById()</h2>";
try {
    $sql = "SELECT 
                p.product_id as id,
                p.product_name as name,
                p.description,
                CAST(p.price AS DECIMAL(10,2)) as price,
                p.stock,
                c.category_name as category,
                c.category_id,
                (
                    SELECT image_url FROM product_images i 
                    WHERE i.product_id = p.product_id 
                    ORDER BY i.is_primary DESC, i.image_id ASC 
                    LIMIT 1
                ) as image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = :id";
    
    echo "<h3>SQL Query:</h3>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<pre>" . print_r($result, true) . "</pre>";
        $imageFromQuery = $result['image'] ?? null;
        echo "<p><strong>Image từ query:</strong> " . ($imageFromQuery ? htmlspecialchars($imageFromQuery) : "<span class='error'>NULL</span>") . "</p>";
    } else {
        echo "<p class='error'>❌ Query không trả về kết quả</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// 5. Kiểm tra biến thể
echo "<div class='section'>";
echo "<h2>5. Biến thể của sản phẩm</h2>";
try {
    $variants = $productModel->getVariantsDetailed($productId);
    if (empty($variants)) {
        echo "<p class='warning'>⚠️ Không có biến thể nào</p>";
    } else {
        echo "<p class='success'>✅ Tìm thấy " . count($variants) . " biến thể</p>";
        echo "<table>";
        echo "<tr><th>Variant ID</th><th>SKU</th><th>Image URL</th><th>Preview</th></tr>";
        foreach ($variants as $variant) {
            $variantImgUrl = $variant['image_url'] ?? null;
            echo "<tr>";
            echo "<td>" . htmlspecialchars($variant['variant_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($variant['sku'] ?? 'N/A') . "</td>";
            echo "<td>" . ($variantImgUrl ? htmlspecialchars($variantImgUrl) : 'NULL') . "</td>";
            echo "<td>";
            if ($variantImgUrl) {
                $processedUrl = getProductImageUrl($variantImgUrl, false);
                echo "<img src='" . htmlspecialchars($processedUrl) . "' style='max-width: 100px;' onerror='this.style.display=\"none\";'>";
            } else {
                echo "NULL";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

echo "<hr>";
echo "<h2>KẾT LUẬN</h2>";
echo "<p>Nếu không có ảnh trong bảng product_images, nguyên nhân là: <strong>Ảnh không được lưu vào database khi tạo/cập nhật sản phẩm.</strong></p>";
echo "<p>Hãy kiểm tra log PHP để xem có lỗi gì trong quá trình upload và lưu ảnh không.</p>";

