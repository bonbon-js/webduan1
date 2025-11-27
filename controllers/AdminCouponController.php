<?php

require_once PATH_MODEL . 'CouponModel.php';

class AdminCouponController
{
    private CouponModel $couponModel;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
    }

    public function index(): void
    {
        $this->requireAdmin();

        $keyword = $_GET['keyword'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $discountTypeFilter = $_GET['discount_type'] ?? '';

        $coupons = $this->couponModel->getAll(
            $keyword ?: null,
            $statusFilter ?: null,
            $discountTypeFilter ?: null
        );

        $title = 'Quản lý mã giảm giá';
        $view  = 'admin/coupons/index';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $data = [
            'code' => strtoupper(trim($_POST['code'] ?? '')),
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? null,
            'discount_type' => $_POST['discount_type'] ?? 'percentage',
            'discount_value' => (float)($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount_amount' => !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null,
            'start_date' => $_POST['start_date'] ?? date('Y-m-d H:i:s'),
            'end_date' => $_POST['end_date'] ?? date('Y-m-d H:i:s', strtotime('+1 month')),
            'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'status' => $_POST['status'] ?? 'active',
        ];

        // Nếu là giảm giá cố định, không cho phép max_discount_amount
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_amount'] = null;
        }

        // Validation
        if (empty($data['code']) || empty($data['name']) || $data['discount_value'] <= 0) {
            set_flash('danger', 'Vui lòng nhập đầy đủ thông tin.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        // Validate ngày - ngày kết thúc phải sau ngày bắt đầu
        $startDate = strtotime($data['start_date']);
        $endDate = strtotime($data['end_date']);
        if ($endDate <= $startDate) {
            set_flash('danger', 'Ngày kết thúc phải sau ngày bắt đầu. Vui lòng nhập lại.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Kiểm tra trùng mã code
        $codeCheck = $this->couponModel->getByCode($data['code']);
        if ($codeCheck) {
            set_flash('danger', 'Mã giảm giá "' . htmlspecialchars($data['code']) . '" đã tồn tại. Vui lòng chọn mã khác.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Validate max_discount_amount khi là percentage
        if ($data['discount_type'] === 'percentage' && $data['max_discount_amount'] !== null) {
            if ($data['max_discount_amount'] <= 0) {
                set_flash('danger', 'Giảm tối đa phải lớn hơn 0.');
                header('Location: ' . BASE_URL . '?action=admin-coupons');
                exit;
            }
        }

        try {
            $this->couponModel->create($data);
            set_flash('success', 'Tạo mã giảm giá thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể tạo mã giảm giá: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-coupons');
        exit;
    }

    public function update(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $couponId = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;

        if (!$couponId) {
            set_flash('danger', 'Mã giảm giá không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        $data = [
            'code' => $_POST['code'] ?? '',
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? null,
            'discount_type' => $_POST['discount_type'] ?? 'percentage',
            'discount_value' => (float)($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount_amount' => !empty($_POST['max_discount_amount']) ? (float)$_POST['max_discount_amount'] : null,
            'start_date' => $_POST['start_date'] ?? date('Y-m-d H:i:s'),
            'end_date' => $_POST['end_date'] ?? date('Y-m-d H:i:s', strtotime('+1 month')),
            'usage_limit' => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'status' => $_POST['status'] ?? 'active',
        ];

        // Nếu là giảm giá cố định, không cho phép max_discount_amount
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_amount'] = null;
        }

        // Validation
        if (empty($data['code']) || empty($data['name']) || $data['discount_value'] <= 0) {
            set_flash('danger', 'Vui lòng nhập đầy đủ thông tin.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        // Validate ngày - ngày kết thúc phải sau ngày bắt đầu
        $startDate = strtotime($data['start_date']);
        $endDate = strtotime($data['end_date']);
        if ($endDate <= $startDate) {
            set_flash('danger', 'Ngày kết thúc phải sau ngày bắt đầu. Vui lòng nhập lại.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Kiểm tra mã giảm giá có tồn tại không
        $existingCoupon = $this->couponModel->getById($couponId, true); // Cho phép lấy cả mã đã xóa để kiểm tra
        if (!$existingCoupon) {
            set_flash('danger', 'Mã giảm giá không tồn tại.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Kiểm tra nếu mã đã bị xóa mềm
        if ($existingCoupon['deleted_at'] !== null) {
            set_flash('danger', 'Không thể sửa mã giảm giá đã bị xóa. Vui lòng khôi phục trước.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Kiểm tra trùng mã code (trừ chính mã đó)
        $codeCheck = $this->couponModel->getByCode($data['code']);
        if ($codeCheck && (int)$codeCheck['coupon_id'] !== $couponId) {
            set_flash('danger', 'Mã giảm giá "' . htmlspecialchars($data['code']) . '" đã tồn tại. Vui lòng chọn mã khác.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }
        
        // Validate max_discount_amount khi là percentage
        if ($data['discount_type'] === 'percentage' && $data['max_discount_amount'] !== null) {
            if ($data['max_discount_amount'] <= 0) {
                set_flash('danger', 'Giảm tối đa phải lớn hơn 0.');
                header('Location: ' . BASE_URL . '?action=admin-coupons');
                exit;
            }
        }
        
        // Validate usage_limit phải >= used_count hiện tại
        if ($data['usage_limit'] !== null && $data['usage_limit'] < $existingCoupon['used_count']) {
            set_flash('danger', 'Giới hạn số lần sử dụng không thể nhỏ hơn số lần đã sử dụng (' . $existingCoupon['used_count'] . ').');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        try {
            // Lưu ý: Khi sửa mã giảm giá, các đơn hàng đã đặt sẽ KHÔNG bị ảnh hưởng
            // vì thông tin mã giảm giá (code, name, discount_amount) đã được lưu snapshot
            // vào bảng orders tại thời điểm đặt hàng (coupon_code, coupon_name, discount_amount).
            // Các đơn hàng đã đặt sẽ luôn hiển thị thông tin mã giảm giá tại thời điểm đặt hàng.
            $this->couponModel->update($couponId, $data);
            set_flash('success', 'Cập nhật mã giảm giá thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-coupons');
        exit;
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $couponId = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;

        if (!$couponId) {
            set_flash('danger', 'Mã giảm giá không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-coupons');
            exit;
        }

        try {
            $this->couponModel->delete($couponId);
            set_flash('success', 'Xóa mã giảm giá thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-coupons');
        exit;
    }

    public function trash(): void
    {
        $this->requireAdmin();
        
        $deletedCoupons = $this->couponModel->getDeleted();
        
        $title = 'Thùng rác - Mã giảm giá';
        $view = 'admin/coupons/trash';
        
        require_once PATH_VIEW . 'admin/layout.php';
    }
    
    public function restore(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
            exit;
        }
        
        $couponId = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;
        
        if (!$couponId) {
            set_flash('danger', 'Mã giảm giá không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
            exit;
        }
        
        try {
            $this->couponModel->restore($couponId);
            set_flash('success', 'Khôi phục mã giảm giá thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể khôi phục: ' . $exception->getMessage());
        }
        
        header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
        exit;
    }
    
    public function forceDelete(): void
    {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
            exit;
        }
        
        $couponId = isset($_POST['coupon_id']) ? (int)$_POST['coupon_id'] : 0;
        
        if (!$couponId) {
            set_flash('danger', 'Mã giảm giá không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
            exit;
        }
        
        try {
            $this->couponModel->forceDelete($couponId);
            set_flash('success', 'Xóa vĩnh viễn mã giảm giá thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể xóa vĩnh viễn: ' . $exception->getMessage());
        }
        
        header('Location: ' . BASE_URL . '?action=admin-coupons-trash');
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

