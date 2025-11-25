<?php

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    // Admin: Danh sách danh mục
    public function adminList()
    {
        $categories = $this->categoryModel->getAllCategories();
        
        $title = 'Quản lý danh mục';
        $view = 'admin/categories/list';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Form thêm danh mục
    public function adminCreate()
    {
        $title = 'Thêm danh mục mới';
        $view = 'admin/categories/create';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý thêm danh mục
    public function adminStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        try {
            $this->categoryModel->createCategory($data);
            $_SESSION['success'] = 'Thêm danh mục thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    // Admin: Form sửa danh mục
    public function adminEdit()
    {
        $id = $_GET['id'] ?? 0;
        
        $category = $this->categoryModel->getCategoryById($id);
        
        if (!$category) {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }
        
        $title = 'Sửa danh mục';
        $view = 'admin/categories/edit';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý cập nhật danh mục
    public function adminUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        
        $category = $this->categoryModel->getCategoryById($id);
        
        if (!$category) {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? ''
        ];
        
        try {
            $this->categoryModel->updateCategory($id, $data);
            $_SESSION['success'] = 'Cập nhật danh mục thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    // Admin: Xóa danh mục
    public function adminDelete()
    {
        $id = $_GET['id'] ?? 0;
        
        $category = $this->categoryModel->getCategoryById($id);
        
        if ($category) {
            try {
                $this->categoryModel->deleteCategory($id);
                $_SESSION['success'] = 'Xóa danh mục thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Không thể xóa danh mục này. Có thể đang có sản phẩm thuộc danh mục này.';
            }
        }
        
        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }
}
