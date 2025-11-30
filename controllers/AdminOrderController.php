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
        $keyword = trim($_GET['keyword'] ?? '');
        $status = trim($_GET['status'] ?? '');

        // Lấy danh sách đơn hàng với filter
        $orders = $this->orderModel->getAll(
            !empty($keyword) ? $keyword : null,
            !empty($status) ? $status : null
        );
        
        // Lấy map trạng thái để hiển thị trong dropdown
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
        $status = trim($_POST['status'] ?? '');

        // Log để debug
        error_log("AdminOrderController::updateStatus - Order ID: $orderId, Status: $status");

        if (!$orderId || !$status) {
            set_flash('danger', 'Thiếu dữ liệu cập nhật. Vui lòng chọn đơn hàng và trạng thái.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        // Validate trạng thái
        $validStatuses = OrderModel::statuses();
        if (!isset($validStatuses[$status])) {
            set_flash('danger', 'Trạng thái không hợp lệ.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        try {
            // Kiểm tra đơn hàng có tồn tại không
            $oldOrder = $this->orderModel->findWithItems($orderId);
            if (!$oldOrder) {
                set_flash('danger', 'Không tìm thấy đơn hàng với ID: ' . $orderId);
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            }

            // Lưu trạng thái cũ để log
            $oldStatus = $oldOrder['status'] ?? 'unknown';
            error_log("AdminOrderController::updateStatus - Old status: $oldStatus, New status: $status");

            // Cập nhật trạng thái
            $success = $this->orderModel->updateStatus($orderId, $status);
            
            if (!$success) {
                throw new Exception('Không thể cập nhật trạng thái đơn hàng.');
            }
            
            // Nếu trạng thái được đặt thành "giao hàng thành công", thông báo cho admin
            if ($status === OrderModel::STATUS_DELIVERED) {
                // Lấy thông tin đơn hàng để gửi thông báo cho user
                $order = $this->orderModel->findWithItems($orderId);
                if ($order && isset($order['user_id'])) {
                    // Có thể gửi email thông báo ở đây nếu cần
                    error_log("AdminOrderController::updateStatus - Order #$orderId đã được giao thành công. User ID: " . $order['user_id']);
                }
                set_flash('success', 'Cập nhật trạng thái thành công. Khách hàng có thể đánh giá sản phẩm khi xem chi tiết đơn hàng.');
            } else {
                $statusLabel = OrderModel::statusLabel($status);
                set_flash('success', "Đã cập nhật trạng thái đơn hàng thành: $statusLabel");
            }
        } catch (InvalidArgumentException $e) {
            set_flash('danger', 'Trạng thái không hợp lệ: ' . $e->getMessage());
        } catch (Throwable $exception) {
            error_log("AdminOrderController::updateStatus - Error: " . $exception->getMessage());
            error_log("AdminOrderController::updateStatus - Stack trace: " . $exception->getTraceAsString());
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
