<?php

require_once PATH_MODEL . 'UserModel.php';
require_once PATH_MODEL . 'ReturnRequestModel.php';

// Controller dành cho admin xử lý danh sách/cập nhật trạng thái đơn
class AdminOrderController
{
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function approveCancel(): void
    {
        $this->requireAdmin();
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        if (!$orderId) {
            set_flash('danger', 'Thiếu mã đơn.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $order = $this->orderModel->findWithItems($orderId);
        if (!$order) {
            set_flash('danger', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        if ($order['status'] !== OrderModel::STATUS_CANCEL_REQUEST) {
            set_flash('warning', 'Đơn không ở trạng thái yêu cầu hủy.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        // Khôi phục tồn kho trước khi hủy đơn
        $this->orderModel->restoreStock($orderId);

        $this->orderModel->updateStatus($orderId, OrderModel::STATUS_CANCELLED);
        set_flash('success', 'Đã xác nhận hủy đơn.');
        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
    }

    public function confirmOrder(): void
    {
        $this->requireAdmin();
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        if (!$orderId) {
            set_flash('danger', 'Thiếu mã đơn.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $order = $this->orderModel->findWithItems($orderId);
        if (!$order) {
            set_flash('danger', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        // Chỉ xác nhận từ PENDING -> TO_SHIP
        if (!OrderModel::isValidTransition($order['status'], OrderModel::STATUS_TO_SHIP, $order['payment_method'] ?? 'cod')) {
            set_flash('warning', 'Không thể xác nhận đơn ở trạng thái hiện tại.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $this->orderModel->updateStatus($orderId, OrderModel::STATUS_TO_SHIP);
        set_flash('success', 'Đã xác nhận đơn và chuyển sang Đang Giao.');
        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
    }

    public function confirmDelivered(): void
    {
        $this->requireAdmin();
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
        if (!$orderId) {
            set_flash('danger', 'Thiếu mã đơn.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $order = $this->orderModel->findWithItems($orderId);
        if (!$order) {
            set_flash('danger', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        if (!OrderModel::isValidTransition($order['status'], OrderModel::STATUS_DELIVERED, $order['payment_method'] ?? 'cod')) {
            set_flash('warning', 'Chỉ xác nhận giao khi đơn đang Đang Giao.');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $this->orderModel->updateStatus($orderId, OrderModel::STATUS_DELIVERED);
        set_flash('success', 'Đã xác nhận đơn hàng đã giao.');
        header('Location: ' . BASE_URL . '?action=admin-orders');
        exit;
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

        // Lấy map trả hàng cho các đơn
        $returnMap = [];
        try {
            $returnModel = new ReturnRequestModel();
            $orderIds = array_map(function ($o) {
                return (int)($o['id'] ?? 0);
            }, $orders);
            $returnMap = $returnModel->getLatestByOrderIds($orderIds);
        } catch (Throwable $e) {
            // ignore
        }
        
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

            $oldStatus = $oldOrder['status'] ?? 'unknown';
            $paymentMethod = $oldOrder['payment_method'] ?? 'cod';

            // Không cho nhảy cóc / lùi trạng thái
            if (!OrderModel::isValidTransition($oldStatus, $status, $paymentMethod)) {
                set_flash('danger', 'Chuyển trạng thái không hợp lệ theo thứ tự cho phép.');
                header('Location: ' . BASE_URL . '?action=admin-orders');
                exit;
            }

            error_log("AdminOrderController::updateStatus - Old status: $oldStatus, New status: $status");

            // Nếu chuyển sang trạng thái CANCELLED, khôi phục tồn kho trước
            if ($status === OrderModel::STATUS_CANCELLED && $oldStatus !== OrderModel::STATUS_CANCELLED) {
                $this->orderModel->restoreStock($orderId);
            }

            // Cập nhật trạng thái (sẽ tự notify trong OrderModel)
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

                    // Thăng hạng VIP nếu đơn đạt điều kiện (>= 2.000.000đ, giao thành công)
                    $totalAmount = (float)($order['total_amount'] ?? 0);
                    if ($totalAmount >= 2000000) {
                        $userModel = new UserModel();
                        $currentRank = $userModel->getRank((int)$order['user_id']) ?? 'customer';
                        if ($currentRank !== 'VIP') {
                            $userModel->updateRank((int)$order['user_id'], 'VIP');
                            error_log("AdminOrderController::updateStatus - User #" . $order['user_id'] . " promoted to VIP.");
                        }
                    }
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

    public function detail(): void
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            set_flash('danger', 'Thiếu mã đơn hàng');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $order = $this->orderModel->findWithItems($id);
        $returnRequest = null;
        try {
            $returnModel = new ReturnRequestModel();
            $returnRequest = $returnModel->findByOrder($id);
        } catch (Throwable $e) {
            // ignore
        }
        if (!$order) {
            set_flash('danger', 'Không tìm thấy đơn hàng');
            header('Location: ' . BASE_URL . '?action=admin-orders');
            exit;
        }

        $statusMap = OrderModel::statuses();
        $title = 'Chi tiết đơn hàng';
        $view  = 'admin/orders/show';
        require_once PATH_VIEW . 'admin/layout.php';
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
