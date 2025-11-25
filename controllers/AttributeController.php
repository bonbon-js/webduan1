<?php

class AttributeController
{
    private $attributeModel;

    public function __construct()
    {
        $this->attributeModel = new Attribute();
    }

    // Admin: Danh sách thuộc tính
    public function adminList()
    {
        $attributes = $this->attributeModel->getAll();
        
        // Lấy giá trị cho mỗi thuộc tính
        foreach ($attributes as &$attr) {
            $attr['values'] = $this->attributeModel->getValues($attr['attribute_id']);
        }
        
        $title = 'Quản lý thuộc tính';
        $view = 'admin/attributes/list';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Form thêm thuộc tính
    public function adminCreate()
    {
        $title = 'Thêm thuộc tính mới';
        $view = 'admin/attributes/create';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý thêm thuộc tính
    public function adminStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? ''
        ];
        
        try {
            $attributeId = $this->attributeModel->create($data);
            
            // Thêm các giá trị nếu có
            if (!empty($_POST['values'])) {
                $values = explode(',', $_POST['values']);
                foreach ($values as $value) {
                    $value = trim($value);
                    if (!empty($value)) {
                        $this->attributeModel->createValue($attributeId, $value);
                    }
                }
            }
            
            $_SESSION['success'] = 'Thêm thuộc tính thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    // Admin: Form sửa thuộc tính
    public function adminEdit()
    {
        $id = $_GET['id'] ?? 0;
        
        $attribute = $this->attributeModel->getById($id);
        
        if (!$attribute) {
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }
        
        $values = $this->attributeModel->getValues($id);
        
        $title = 'Sửa thuộc tính';
        $view = 'admin/attributes/edit';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Admin: Xử lý cập nhật thuộc tính
    public function adminUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }
        
        $id = $_POST['id'] ?? 0;
        
        $attribute = $this->attributeModel->getById($id);
        
        if (!$attribute) {
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }
        
        $data = [
            'name' => $_POST['name'] ?? ''
        ];
        
        try {
            $this->attributeModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật thuộc tính thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    // Admin: Xóa thuộc tính
    public function adminDelete()
    {
        $id = $_GET['id'] ?? 0;
        
        $attribute = $this->attributeModel->getById($id);
        
        if ($attribute) {
            try {
                $this->attributeModel->delete($id);
                $_SESSION['success'] = 'Xóa thuộc tính thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Không thể xóa thuộc tính này.';
            }
        }
        
        header('Location: ' . BASE_URL . '?action=admin-attributes');
        exit;
    }

    // Admin: Thêm giá trị cho thuộc tính
    public function adminAddValue()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-attributes');
            exit;
        }
        
        $attributeId = $_POST['attribute_id'] ?? 0;
        $valueName = $_POST['value_name'] ?? '';
        
        try {
            $this->attributeModel->createValue($attributeId, $valueName);
            $_SESSION['success'] = 'Thêm giá trị thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '?action=admin-attribute-edit&id=' . $attributeId);
        exit;
    }

    // Admin: Xóa giá trị thuộc tính
    public function adminDeleteValue()
    {
        $valueId = $_GET['value_id'] ?? 0;
        $attributeId = $_GET['attribute_id'] ?? 0;
        
        try {
            $this->attributeModel->deleteValue($valueId);
            $_SESSION['success'] = 'Xóa giá trị thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Không thể xóa giá trị này.';
        }
        
        header('Location: ' . BASE_URL . '?action=admin-attribute-edit&id=' . $attributeId);
        exit;
    }
}
