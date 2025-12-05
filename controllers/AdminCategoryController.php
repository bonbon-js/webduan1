<?php

class AdminCategoryController
{
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        $categories = $this->categoryModel->getAllCategories();

        $title = 'Quản lý danh mục';
        $view  = 'admin/categories/index';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        $title = 'Thêm danh mục mới';
        $view  = 'admin/categories/form';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function edit(): void
    {
        $this->requireAdmin();

        $categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($categoryId <= 0) {
            set_flash('danger', 'Danh mục không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $category = $this->categoryModel->getCategoryById($categoryId);
        if (!$category) {
            set_flash('danger', 'Không tìm thấy danh mục.');
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $title = 'Chỉnh sửa danh mục';
        $view  = 'admin/categories/form';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function store(): void
    {
        $this->requireAdmin();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;

        if ($name === '') {
            set_flash('danger', 'Tên danh mục không được để trống.');
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->createCategory($name, $description);
            set_flash('success', 'Thêm danh mục thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể tạo danh mục: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function update(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '') ?: null;

        if ($id <= 0 || $name === '') {
            set_flash('danger', 'Dữ liệu danh mục không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->updateCategory($id, $name, $description);
            set_flash('success', 'Cập nhật danh mục thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        if ($id <= 0) {
            set_flash('danger', 'Danh mục không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->deleteCategory($id);
            set_flash('success', 'Đã xóa danh mục.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Danh mục đang có sản phẩm, không thể xóa.');
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
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

