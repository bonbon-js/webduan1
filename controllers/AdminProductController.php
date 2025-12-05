<?php

class AdminProductController
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private AttributeModel $attributeModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->attributeModel = new AttributeModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $priceRange = trim($_GET['price_range'] ?? '');

        // Lấy tất cả sản phẩm trước
        $products = $this->productModel->getAdminProducts($keyword ?: null, $categoryId ?: null);
        
        // Lọc theo giá nếu có
        if ($priceRange && strpos($priceRange, '-') !== false) {
            list($minPrice, $maxPrice) = explode('-', $priceRange);
            $minPrice = (float)$minPrice;
            $maxPrice = (float)$maxPrice;
            
            $products = array_filter($products, function($product) use ($minPrice, $maxPrice) {
                $price = (float)($product['price'] ?? 0);
                return $price >= $minPrice && $price <= $maxPrice;
            });
        }
        
        $categories = $this->categoryModel->getAllCategories();

        $title = 'Quản lý sản phẩm';
        $view  = 'admin/products/index';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        $categories = $this->categoryModel->getAllCategories();
        $attributes = $this->attributeModel->getAttributesWithValues();

        $title = 'Thêm sản phẩm';
        $view  = 'admin/products/form';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function edit(): void
    {
        $this->requireAdmin();

        $productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            set_flash('danger', 'Không tìm thấy sản phẩm.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $categories = $this->categoryModel->getAllCategories();
        $attributes = $this->attributeModel->getAttributesWithValues();
        $variants   = $this->productModel->getVariantsDetailed($productId);

        $title = 'Chỉnh sửa sản phẩm';
        $view  = 'admin/products/form';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function store(): void
    {
        $this->requireAdmin();
        $payload = $this->buildProductPayload();

        if (!$payload) {
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }

        // Đảm bảo KHÔNG có id trong payload
        unset($payload['id'], $payload['product_id'], $payload['productId']);
        
        try {
            $productId = $this->productModel->createProduct($payload);
            set_flash('success', 'Đã tạo sản phẩm. Bạn có thể thêm biến thể ngay bây giờ.');
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
            exit;
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể tạo sản phẩm: ' . $exception->getMessage());
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }
    }

    public function update(): void
    {
        $this->requireAdmin();
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $payload = $this->buildProductPayload();
        if (!$payload) {
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
            exit;
        }

        try {
            $this->productModel->updateProduct($productId, $payload);
            set_flash('success', 'Đã cập nhật sản phẩm.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật sản phẩm: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        try {
            $this->productModel->deleteProduct($productId);
            set_flash('success', 'Đã xóa sản phẩm vào thùng rác. Bạn có thể khôi phục sau.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa sản phẩm: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    public function trash(): void
    {
        $this->requireAdmin();

        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        $products = $this->productModel->getAdminProducts($keyword ?: null, $categoryId ?: null, true);
        $categories = $this->categoryModel->getAllCategories();

        $title = 'Thùng rác sản phẩm';
        $view  = 'admin/products/trash';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function restore(): void
    {
        $this->requireAdmin();

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products-trash');
            exit;
        }

        try {
            $this->productModel->restoreProduct($productId);
            set_flash('success', 'Đã khôi phục sản phẩm.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể khôi phục sản phẩm: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-products-trash');
        exit;
    }

    public function forceDelete(): void
    {
        $this->requireAdmin();

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products-trash');
            exit;
        }

        try {
            $this->productModel->forceDeleteProduct($productId);
            set_flash('success', 'Đã xóa vĩnh viễn sản phẩm.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa vĩnh viễn sản phẩm: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-products-trash');
        exit;
    }

    public function storeVariant(): void
    {
        $this->requireAdmin();

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if ($productId <= 0) {
            set_flash('danger', 'Sản phẩm không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $variantData = [
            'sku' => trim($_POST['sku'] ?? ''),
            'additional_price' => $this->toFloat($_POST['additional_price'] ?? 0),
            'stock' => (int)($_POST['stock'] ?? 0),
        ];
        $valueIds = $this->collectAttributeValues($_POST['attribute_values'] ?? []);

        if (empty($valueIds)) {
            set_flash('danger', 'Vui lòng chọn ít nhất một thuộc tính cho biến thể.');
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
            exit;
        }

        // Đảm bảo KHÔNG có id trong variantData
        unset($variantData['id'], $variantData['variant_id'], $variantData['variantId']);
        
        try {
            $this->productModel->createVariant($productId, $variantData, $valueIds);
            set_flash('success', 'Đã thêm biến thể.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể thêm biến thể: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
        exit;
    }

    public function updateVariant(): void
    {
        $this->requireAdmin();

        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($variantId <= 0 || $productId <= 0) {
            set_flash('danger', 'Biến thể không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $variantData = [
            'sku' => trim($_POST['sku'] ?? ''),
            'additional_price' => $this->toFloat($_POST['additional_price'] ?? 0),
            'stock' => (int)($_POST['stock'] ?? 0),
        ];
        $valueIds = $this->collectAttributeValues($_POST['attribute_values'] ?? []);

        if (empty($valueIds)) {
            set_flash('danger', 'Vui lòng chọn đầy đủ thuộc tính.');
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
            exit;
        }

        try {
            $this->productModel->updateVariant($variantId, $variantData, $valueIds);
            set_flash('success', 'Đã cập nhật biến thể.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật biến thể: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
        exit;
    }

    public function deleteVariant(): void
    {
        $this->requireAdmin();

        $variantId = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($variantId <= 0 || $productId <= 0) {
            set_flash('danger', 'Biến thể không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        try {
            $this->productModel->deleteVariant($variantId);
            set_flash('success', 'Đã xóa biến thể.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa biến thể: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $productId);
        exit;
    }

    private function buildProductPayload(): ?array
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;
        $price = $this->toFloat($_POST['price'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        
        // Xử lý upload ảnh
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->handleImageUpload($_FILES['image']);
            if (!$imageUrl) {
                set_flash('danger', 'Không thể upload ảnh. Vui lòng thử lại.');
                return null;
            }
        }

        if ($name === '' || $price < 0) {
            set_flash('danger', 'Vui lòng nhập ít nhất tên và giá hợp lệ.');
            return null;
        }

        $payload = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => max(0, $stock),
            'category_id' => $categoryId,
        ];
        
        // Chỉ thêm image_url nếu có upload mới
        if ($imageUrl) {
            $payload['image_url'] = $imageUrl;
        }
        
        return $payload;
    }
    
    private function handleImageUpload(array $file): ?string
    {
        // Kiểm tra file có hợp lệ không
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        
        // Kiểm tra kích thước file (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            set_flash('danger', 'Kích thước ảnh không được vượt quá 5MB.');
            return null;
        }
        
        // Kiểm tra loại file
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            set_flash('danger', 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).');
            return null;
        }
        
        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'product_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Đường dẫn lưu file
        $uploadDir = 'assets/uploads/products/';
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $fileName;
        
        // Di chuyển file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return BASE_URL . $uploadPath;
        }
        
        return null;
    }

    private function collectAttributeValues(array $rawValues): array
    {
        $valueIds = [];
        foreach ($rawValues as $attributeId => $valueId) {
            $valueId = (int)$valueId;
            if ($valueId > 0) {
                $valueIds[] = $valueId;
            }
        }

        return $valueIds;
    }

    private function toFloat($value): float
    {
        $value = str_replace(['.', ','], ['', '.'], (string)$value);
        return (float)$value;
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            set_flash('danger', 'Bạn cần quyền quản trị để truy cập trang này.');
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}

