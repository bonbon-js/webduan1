<?php

class AdminAttributeController
{
    private AttributeModel $attributeModel;

    public function __construct()
    {
        $this->attributeModel = new AttributeModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        $attributes = $this->attributeModel->getAttributesWithValues();

        $title = 'Quản lý thuộc tính';
        $view  = 'admin/attributes/index';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function store(): void
    {
        $this->requireAdmin();

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            set_flash('danger', 'Tên thuộc tính không được để trống.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->createAttribute($name);
            set_flash('success', 'Đã thêm thuộc tính.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể thêm thuộc tính: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    public function update(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['attribute_id']) ? (int)$_POST['attribute_id'] : 0;
        $name = trim($_POST['name'] ?? '');

        if ($id <= 0 || $name === '') {
            set_flash('danger', 'Dữ liệu thuộc tính không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->updateAttribute($id, $name);
            set_flash('success', 'Đã cập nhật thuộc tính.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $id = isset($_POST['attribute_id']) ? (int)$_POST['attribute_id'] : 0;
        if ($id <= 0) {
            set_flash('danger', 'Thuộc tính không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->deleteAttribute($id);
            set_flash('success', 'Đã xóa thuộc tính.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    public function storeValue(): void
    {
        $this->requireAdmin();

        $attributeId = isset($_POST['attribute_id']) ? (int)$_POST['attribute_id'] : 0;
        $valueName = trim($_POST['value_name'] ?? '');

        if ($attributeId <= 0 || $valueName === '') {
            set_flash('danger', 'Dữ liệu giá trị thuộc tính không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->createValue($attributeId, $valueName);
            set_flash('success', 'Đã thêm giá trị.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể thêm giá trị: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    public function updateValue(): void
    {
        $this->requireAdmin();

        $valueId = isset($_POST['value_id']) ? (int)$_POST['value_id'] : 0;
        $valueName = trim($_POST['value_name'] ?? '');

        if ($valueId <= 0 || $valueName === '') {
            set_flash('danger', 'Dữ liệu giá trị không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->updateValue($valueId, $valueName);
            set_flash('success', 'Đã cập nhật giá trị.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật giá trị: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    public function deleteValue(): void
    {
        $this->requireAdmin();

        $valueId = isset($_POST['value_id']) ? (int)$_POST['value_id'] : 0;
        if ($valueId <= 0) {
            set_flash('danger', 'Giá trị không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }

        try {
            $this->attributeModel->deleteValue($valueId);
            set_flash('success', 'Đã xóa giá trị.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa giá trị: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-attributes');
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

