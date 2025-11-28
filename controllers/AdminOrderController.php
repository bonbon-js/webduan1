<?php

// Controller dành cho admin xử lý danh sách/cập nhật trạng thái đơn
class AdminOrderController
{
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    // Trang danh sách đơn có bộ lọc
    public function index(): void
    {
        $this->requireAdmin();
        $keyword = $_GET['keyword'] ?? null;
        $status = $_GET['status'] ?? null;

        $orders = $this->orderModel->getAll($keyword, $status ?: null);
        $statusMap = OrderModel::statuses();

        $view = 'admin/orders/index';
        $title = 'Quản lý đơn hàng';
        $logoUrl = BASE_URL . 'assets/images/logo.png';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    // Xử lý form cập nhật trạng thái đơn
    public function updateStatus(): void
    {
        $this->requireAdmin();
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        $status = $_POST['status'] ?? '';

        if (!$orderId || !$status) {
            set_flash('danger', 'Thiếu dữ liệu cập nhật.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        try {
            $this->orderModel->updateStatus($orderId, $status);
            set_flash('success', 'Cập nhật trạng thái thành công.');
        } catch (Throwable $exception) {
            set_flash('danger', 'Không thể cập nhật trạng thái: ' . $exception->getMessage());
        }

        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
    }

    // Bảo vệ route chỉ dành cho admin
    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            set_flash('danger', 'Bạn cần quyền quản trị để truy cập khu vực này.');
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}
