<?php

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $variantModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->variantModel = new Variant();
    }

    // Hiển thị danh sách sản phẩm (BB-01, BB-12)
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        
        // Lấy filters từ query string
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'order_by' => $_GET['order_by'] ?? 'p.id',
            'order_dir' => $_GET['order_dir'] ?? 'DESC'
        ];
        
        $products = $this->productModel->getAllProducts($page, $limit, $filters);
        $totalProducts = $this->productModel->countProducts($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        $categories = $this->categoryModel->getAllCategories();
        
        $title = 'Sản phẩm';
        $view = 'products/list';
        
        require_once PATH_VIEW . 'main.php';
    }

    // Hiển thị chi tiết sản phẩm (BB-01)
    public function detail()
    {
        $id = $_GET['id'] ?? 0;
        
        $product = $this->productModel->getProductById($id);
        
        if (!$product) {
            header('Location: ' . BASE_URL . '?action=products');
            exit;
        }
        
        $images = $this->productModel->getProductImages($id);
        $variants = $this->productModel->getProductVariants($id);
        $attributes = $this->productModel->getProductAttributes($id);
        $relatedProducts = $this->productModel->getRelatedProducts($id, $product['category_id']);
        
        $title = $product['product_name'];
        $view = 'products/detail';
        
        require_once PATH_VIEW . 'main.php';
    }

    // Admin: Danh sách sản phẩm (BB-03)
    public function adminList()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        
        $filters = [
            'search' => $_GET['search'] ?? null,
            'category_id' => $_GET['category'] ?? null
        ];
        
        $products = $this->productModel->getAllProducts($page, $limit, $filters);
        $totalProducts = $this->productModel->countProducts($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        $categories = $this->categoryModel->getAllCategories();
        
        $title = 'Quản lý sản phẩm';
        $view = 'admin/products/list';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Form thêm sản phẩm (BB-03)
    public function adminCreate()
    {
        $categories = $this->categoryModel->getAllCategories();
        
        $title = 'Thêm sản phẩm mới';
        $view = 'admin/products/create';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý thêm sản phẩm (BB-03)
    public function adminStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'category_id' => $_POST['category_id'] ?? 0,
            'stock' => $_POST['stock'] ?? 0
        ];
        
        try {
            $productId = $this->productModel->createProduct($data);
            
            // Xử lý upload ảnh
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                try {
                    $imageUrl = upload_file('products', $_FILES['image']);
                    $this->productModel->addProductImage($productId, $imageUrl, 1);
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
            
            $_SESSION['success'] = 'Thêm sản phẩm thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    // Admin: Form sửa sản phẩm (BB-03)
    public function adminEdit()
    {
        $id = $_GET['id'] ?? 0;
        
        $product = $this->productModel->getProductById($id);
        
        if (!$product) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $images = $this->productModel->getProductImages($id);
        $categories = $this->categoryModel->getAllCategories();
        
        $title = 'Sửa sản phẩm';
        $view = 'admin/products/edit';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý cập nhật sản phẩm (BB-03)
    public function adminUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        
        $product = $this->productModel->getProductById($id);
        
        if (!$product) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price' => $_POST['price'] ?? 0,
            'category_id' => $_POST['category_id'] ?? 0,
            'stock' => $_POST['stock'] ?? 0
        ];
        
        try {
            $this->productModel->updateProduct($id, $data);
            
            // Xử lý upload ảnh mới
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                try {
                    $imageUrl = upload_file('products', $_FILES['image']);
                    
                    // Lấy ảnh chính hiện tại
                    $images = $this->productModel->getProductImages($id);
                    $oldPrimaryImage = null;
                    foreach ($images as $img) {
                        if ($img['is_primary']) {
                            $oldPrimaryImage = $img;
                            break;
                        }
                    }
                    
                    // Xóa ảnh cũ khỏi database và file
                    if ($oldPrimaryImage) {
                        if (file_exists(PATH_ASSETS_UPLOADS . $oldPrimaryImage['image_url'])) {
                            unlink(PATH_ASSETS_UPLOADS . $oldPrimaryImage['image_url']);
                        }
                        $sql = "DELETE FROM product_images WHERE image_id = :id";
                        $stmt = $this->productModel->pdo->prepare($sql);
                        $stmt->execute([':id' => $oldPrimaryImage['image_id']]);
                    }
                    
                    // Thêm ảnh mới
                    $this->productModel->addProductImage($id, $imageUrl, 1);
                } catch (Exception $e) {
                    $_SESSION['error'] = $e->getMessage();
                }
            }
            
            $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    // Admin: Xóa sản phẩm (BB-03)
    public function adminDelete()
    {
        $id = $_GET['id'] ?? 0;
        
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            // Xóa tất cả ảnh của sản phẩm
            $images = $this->productModel->getProductImages($id);
            foreach ($images as $img) {
                if (file_exists(PATH_ASSETS_UPLOADS . $img['image_url'])) {
                    unlink(PATH_ASSETS_UPLOADS . $img['image_url']);
                }
            }
            
            $this->productModel->deleteProduct($id);
            $_SESSION['success'] = 'Xóa sản phẩm thành công!';
        }
        
        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }
}

    // Admin: Quản lý biến thể của sản phẩm
    public function adminVariants()
    {
        $productId = $_GET['product_id'] ?? 0;
        
        $product = $this->productModel->getProductById($productId);
        
        if (!$product) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $variants = $this->variantModel->getByProductId($productId);
        
        $title = 'Quản lý biến thể - ' . $product['product_name'];
        $view = 'admin/variants/list';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Form thêm biến thể
    public function adminVariantCreate()
    {
        $productId = $_GET['product_id'] ?? 0;
        
        $product = $this->productModel->getProductById($productId);
        
        if (!$product) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $title = 'Thêm biến thể mới';
        $view = 'admin/variants/create';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý thêm biến thể
    public function adminVariantStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $productId = $_POST['product_id'] ?? 0;
        
        $data = [
            'product_id' => $productId,
            'sku' => $_POST['sku'] ?? '',
            'additional_price' => $_POST['additional_price'] ?? 0,
            'stock' => $_POST['stock'] ?? 0
        ];
        
        try {
            $this->variantModel->create($data);
            $_SESSION['success'] = 'Thêm biến thể thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-product-variants&product_id=' . $productId);
        exit;
    }

    // Admin: Form sửa biến thể
    public function adminVariantEdit()
    {
        $id = $_GET['id'] ?? 0;
        
        $variant = $this->variantModel->getById($id);
        
        if (!$variant) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $product = $this->productModel->getProductById($variant['product_id']);
        
        $title = 'Sửa biến thể';
        $view = 'admin/variants/edit';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý cập nhật biến thể
    public function adminVariantUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        $variant = $this->variantModel->getById($id);
        
        if (!$variant) {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }
        
        $data = [
            'sku' => $_POST['sku'] ?? '',
            'additional_price' => $_POST['additional_price'] ?? 0,
            'stock' => $_POST['stock'] ?? 0
        ];
        
        try {
            $this->variantModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật biến thể thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-product-variants&product_id=' . $variant['product_id']);
        exit;
    }

    // Admin: Xóa biến thể
    public function adminVariantDelete()
    {
        $id = $_GET['id'] ?? 0;
        
        $variant = $this->variantModel->getById($id);
        
        if ($variant) {
            $productId = $variant['product_id'];
            $this->variantModel->delete($id);
            $_SESSION['success'] = 'Xóa biến thể thành công!';
            header('Location: ' . BASE_URL . '?action=admin-product-variants&product_id=' . $productId);
        } else {
            header('Location: ' . BASE_URL . '?action=admin-products');
        }
        
        exit;
    }
