<?php
/**
 * Script debug toàn diện để kiểm tra flow upload và hiển thị ảnh sản phẩm
 * Truy cập: http://localhost/webduan1/scripts/debug_product_image_flow.php
 */

require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../configs/helper.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

echo "<h1>Debug: Kiểm tra Flow Ảnh Sản Phẩm</h1>";
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
</style>";

$pdo = getPDO();
$productModel = new ProductModel();

// ============================================
// 1. KIỂM TRA BẢNG product_images
// ============================================
echo "<div class='section'>";
echo "<h2>1. Kiểm tra bảng product_images</h2>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'product_images'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>✅ Bảng product_images tồn tại</p>";
        
        // Kiểm tra cấu trúc bảng
        $stmt = $pdo->query("DESCRIBE product_images");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Cấu trúc bảng:</h3>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Đếm số ảnh
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM product_images");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p><strong>Tổng số ảnh trong database:</strong> {$count}</p>";
        
    } else {
        echo "<p class='error'>❌ Bảng product_images KHÔNG tồn tại!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// ============================================
// 2. KIỂM TRA SẢN PHẨM MỚI NHẤT
// ============================================
echo "<div class='section'>";
echo "<h2>2. Kiểm tra sản phẩm mới nhất</h2>";

try {
    $stmt = $pdo->query("SELECT product_id, product_name FROM products ORDER BY product_id DESC LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo "<p class='warning'>⚠️ Không có sản phẩm nào trong database</p>";
    } else {
        echo "<table>";
        echo "<tr><th>Product ID</th><th>Product Name</th><th>Có ảnh trong product_images?</th><th>Image URL</th></tr>";
        
        foreach ($products as $product) {
            $productId = $product['product_id'];
            
            // Kiểm tra ảnh trong product_images
            $imgStmt = $pdo->prepare("SELECT image_url, is_primary FROM product_images WHERE product_id = :pid ORDER BY is_primary DESC LIMIT 1");
            $imgStmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $imgStmt->execute();
            $image = $imgStmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<tr>";
            echo "<td>{$productId}</td>";
            echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
            echo "<td>" . ($image ? "<span class='success'>✅ Có</span>" : "<span class='error'>❌ Không</span>") . "</td>";
            echo "<td>" . ($image ? htmlspecialchars($image['image_url']) : 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// ============================================
// 3. TEST QUERY GIỐNG getAdminProducts()
// ============================================
echo "<div class='section'>";
echo "<h2>3. Test query giống getAdminProducts()</h2>";

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
            ORDER BY p.product_id DESC
            LIMIT 5";
    
    echo "<h3>SQL Query:</h3>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Image URL từ query</th></tr>";
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        $imgUrl = $row['image_url'] ?? null;
        echo "<td>" . ($imgUrl ? htmlspecialchars($imgUrl) : "<span class='error'>NULL</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// ============================================
// 4. TEST QUERY GIỐNG getAllProducts()
// ============================================
echo "<div class='section'>";
echo "<h2>4. Test query giống getAllProducts()</h2>";

try {
    $sql = "SELECT 
                p.product_id as id,
                p.product_name as name,
                (
                    SELECT image_url FROM product_images i 
                    WHERE i.product_id = p.product_id 
                    ORDER BY i.is_primary DESC, i.image_id ASC 
                    LIMIT 1
                ) as image
            FROM products p
            ORDER BY p.product_id DESC
            LIMIT 5";
    
    echo "<h3>SQL Query:</h3>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Image từ query</th></tr>";
    foreach ($results as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        $imgUrl = $row['image'] ?? null;
        echo "<td>" . ($imgUrl ? htmlspecialchars($imgUrl) : "<span class='error'>NULL</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

// ============================================
// 5. KIỂM TRA THƯ MỤC UPLOAD
// ============================================
echo "<div class='section'>";
echo "<h2>5. Kiểm tra thư mục upload</h2>";

$uploadDir = PATH_ROOT . 'assets/uploads/products/';
echo "<p><strong>Đường dẫn thư mục:</strong> " . htmlspecialchars($uploadDir) . "</p>";

if (is_dir($uploadDir)) {
    echo "<p class='success'>✅ Thư mục tồn tại</p>";
    
    // Kiểm tra quyền
    $writable = is_writable($uploadDir);
    echo "<p><strong>Có quyền ghi:</strong> " . ($writable ? "<span class='success'>✅ Có</span>" : "<span class='error'>❌ Không</span>") . "</p>";
    
    // Liệt kê file
    $files = glob($uploadDir . '*');
    echo "<p><strong>Số file trong thư mục:</strong> " . count($files) . "</p>";
    
    if (count($files) > 0) {
        echo "<h3>5 file mới nhất:</h3>";
        echo "<table>";
        echo "<tr><th>File Name</th><th>Size</th><th>Modified</th><th>URL</th></tr>";
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        foreach (array_slice($files, 0, 5) as $file) {
            $fileName = basename($file);
            $fileSize = filesize($file);
            $fileModified = date('Y-m-d H:i:s', filemtime($file));
            $relativePath = str_replace(PATH_ROOT, '', $file);
            $fileUrl = BASE_URL . $relativePath;
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($fileName) . "</td>";
            echo "<td>" . number_format($fileSize) . " bytes</td>";
            echo "<td>" . $fileModified . "</td>";
            echo "<td><a href='" . htmlspecialchars($fileUrl) . "' target='_blank'>" . htmlspecialchars($fileUrl) . "</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p class='error'>❌ Thư mục KHÔNG tồn tại!</p>";
    echo "<p>Hãy tạo thư mục: <code>" . htmlspecialchars($uploadDir) . "</code></p>";
}
echo "</div>";

// ============================================
// 6. TEST HÀM getProductImageUrl()
// ============================================
echo "<div class='section'>";
echo "<h2>6. Test hàm getProductImageUrl()</h2>";

$testUrls = [
    'http://localhost/webduan1/assets/uploads/products/test.jpg',
    'assets/uploads/products/test.jpg',
    '/assets/uploads/products/test.jpg',
    BASE_URL . 'assets/uploads/products/test.jpg',
    null,
    ''
];

echo "<table>";
echo "<tr><th>Input URL</th><th>Output URL</th></tr>";
foreach ($testUrls as $testUrl) {
    $output = getProductImageUrl($testUrl, false);
    echo "<tr>";
    echo "<td>" . ($testUrl ? htmlspecialchars($testUrl) : 'NULL/EMPTY') . "</td>";
    echo "<td>" . ($output ? htmlspecialchars($output) : '<span class="error">EMPTY</span>') . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// ============================================
// 7. KIỂM TRA MỘT SẢN PHẨM CỤ THỂ
// ============================================
echo "<div class='section'>";
echo "<h2>7. Kiểm tra sản phẩm cụ thể (mới nhất)</h2>";

try {
    $stmt = $pdo->query("SELECT product_id FROM products ORDER BY product_id DESC LIMIT 1");
    $latestProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($latestProduct) {
        $productId = $latestProduct['product_id'];
        echo "<p><strong>Product ID:</strong> {$productId}</p>";
        
        // Lấy thông tin từ getProductById
        $product = $productModel->getProductById($productId);
        echo "<h3>Kết quả từ getProductById():</h3>";
        echo "<pre>" . print_r($product, true) . "</pre>";
        
        // Lấy ảnh từ getProductImages
        $images = $productModel->getProductImages($productId);
        echo "<h3>Kết quả từ getProductImages():</h3>";
        if (empty($images)) {
            echo "<p class='error'>❌ Không có ảnh nào!</p>";
        } else {
            echo "<pre>" . print_r($images, true) . "</pre>";
        }
        
        // Test getAdminProducts
        $adminProducts = $productModel->getAdminProducts();
        $adminProduct = null;
        foreach ($adminProducts as $ap) {
            if ($ap['product_id'] == $productId) {
                $adminProduct = $ap;
                break;
            }
        }
        echo "<h3>Kết quả từ getAdminProducts() cho sản phẩm này:</h3>";
        if ($adminProduct) {
            echo "<pre>" . print_r($adminProduct, true) . "</pre>";
        } else {
            echo "<p class='error'>❌ Không tìm thấy trong getAdminProducts()</p>";
        }
        
    } else {
        echo "<p class='warning'>⚠️ Không có sản phẩm nào</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>Kết thúc debug. Vui lòng kiểm tra các phần trên để tìm nguyên nhân.</strong></p>";

