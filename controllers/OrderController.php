<?php

// Controller xử lý các chức năng xem/hủy đơn hàng phía người dùng
class OrderController
{
    private OrderModel $orderModel;

    public function __construct()
    {
        // Khởi tạo model để tái sử dụng ở mọi action
        $this->orderModel = new OrderModel();
    }

    // Trang liệt kê lịch sử các đơn của tài khoản đang đăng nhập
    public function history(): void
    {
        $user = $this->requireUser();
        
        // Lấy user_id từ nhiều nguồn có thể (user_id hoặc id)
        $userId = (int)($user['user_id'] ?? $user['id'] ?? 0);
        $userEmail = $user['email'] ?? null;
        
        // Lấy status filter từ URL
        $statusFilter = $_GET['status'] ?? null;
        
        // Debug: Log để kiểm tra
        error_log("OrderController::history - User ID: $userId, Email: " . ($userEmail ?? 'N/A'));
        error_log("OrderController::history - User data: " . json_encode(array_keys($user)));
        error_log("OrderController::history - Status filter: " . ($statusFilter ?? 'all'));
        
        $orders = $this->orderModel->getHistory($userId, $userEmail);
        
        // Lọc theo status nếu có
        if ($statusFilter) {
            $orders = array_filter($orders, function($order) use ($statusFilter) {
                return ($order['status'] ?? '') === $statusFilter;
            });
        }
        
        error_log("OrderController::history - Found " . count($orders) . " orders");

        // Load items cho mỗi đơn hàng để hiển thị sản phẩm
        foreach ($orders as &$order) {
            $orderId = (int)($order['id'] ?? 0);
            if ($orderId > 0) {
                $order['items'] = $this->orderModel->getItems($orderId);
            } else {
                $order['items'] = [];
            }
        }
        unset($order);

        $view = 'orders/history';
        $title = 'Đơn hàng của tôi';
        $logoUrl = BASE_URL . 'assets/images/logo.png';
        $statusMap = OrderModel::statuses();

        require_once PATH_VIEW . 'main.php';
    }

    // Trang chi tiết đơn hàng
    public function detail(): void
    {
        $user = $this->requireUser();
        $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$orderId) {
            set_flash('warning', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        }

        $order = $this->orderModel->findWithItems($orderId);

        if (!$order || !$this->canViewOrder($order, $user)) {
            set_flash('danger', 'Bạn không có quyền xem đơn hàng này.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        }

        $canCancel = $this->orderModel->canCancel($order);
        
        // Load thông tin đánh giá nếu đơn hàng đã được giao
        // QUAN TRỌNG: Khi trạng thái là "delivered", tự động cho phép đánh giá
        $reviews = [];
        $canReview = ($order['status'] === OrderModel::STATUS_DELIVERED);
        
        if ($canReview) {
            require_once PATH_MODEL . 'ReviewModel.php';
            $reviewModel = new ReviewModel();
            $userId = (int)($user['id'] ?? 0);
            
            // Kiểm tra đã đánh giá chưa cho từng item
            foreach ($order['items'] as &$item) {
                // Thử lấy id từ nhiều nguồn khác nhau
                $orderItemId = 0;
                if (isset($item['id']) && $item['id']) {
                    $orderItemId = (int)$item['id'];
                } elseif (isset($item['order_item_id']) && $item['order_item_id']) {
                    $orderItemId = (int)$item['order_item_id'];
                }
                
                if ($orderItemId > 0) {
                    $item['has_reviewed'] = $reviewModel->hasReviewed($orderItemId, $userId);
                    $existingReview = $reviewModel->getByOrderItem($orderItemId);
                    if ($existingReview) {
                        // Parse images nếu có
                        if (!empty($existingReview['images'])) {
                            $images = json_decode($existingReview['images'], true);
                            $existingReview['images'] = is_array($images) ? $images : [];
                        } else {
                            $existingReview['images'] = [];
                        }
                    }
                    $item['review'] = $existingReview;
                } else {
                    $item['has_reviewed'] = false;
                    $item['review'] = null;
                }
            }
            unset($item);
            
            // Nếu có tham số review=true trong URL, đánh dấu để tự động cuộn đến form đánh giá
            // (Khi admin vừa cập nhật trạng thái thành delivered)
            if (isset($_GET['review']) && $_GET['review'] === 'true') {
                // Có thể thêm logic ở đây nếu cần
            }
        }
        
        $view = 'orders/detail';
        $title = 'Chi tiết đơn hàng';
        $statusMap = OrderModel::statuses();

        require_once PATH_VIEW . 'main.php';
    }

    // Xử lý yêu cầu hủy đơn hàng
    public function cancel(): void
    {
        $user = $this->requireUser();
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

        if (!$orderId) {
            set_flash('warning', 'Thiếu mã đơn hàng.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        }

        $order = $this->orderModel->findWithItems($orderId);
        if (!$order || !$this->canViewOrder($order, $user)) {
            set_flash('danger', 'Bạn không có quyền hủy đơn hàng này.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        }

        if (!$this->orderModel->canCancel($order)) {
            set_flash('warning', 'Chỉ có thể hủy khi đơn hàng đang chuẩn bị.');
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        }

        $reason = $_POST['reason'] ?? null;
        $this->orderModel->cancel($orderId, $reason);
        set_flash('success', 'Đơn hàng đã được hủy thành công.');
        header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
        exit;
    }

    // Bắt buộc đăng nhập, nếu không sẽ điều hướng về trang login
    private function requireUser(): array
    {
        if (!isset($_SESSION['user'])) {
            set_flash('warning', 'Vui lòng đăng nhập để xem đơn hàng.');
            header('Location: ' . BASE_URL . '?action=show-login');
            exit;
        }

        return $_SESSION['user'];
    }

    // Kiểm tra quyền truy cập đơn hàng (user sở hữu hoặc admin)
    private function canViewOrder(array $order, array $user): bool
    {
        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        // Lấy user_id từ nhiều nguồn
        $userId = (int)($user['user_id'] ?? $user['id'] ?? 0);
        $orderUserId = (int)($order['user_id'] ?? 0);
        
        // Kiểm tra theo user_id
        if ($userId > 0 && $orderUserId > 0 && $orderUserId === $userId) {
            return true;
        }
        
        // Kiểm tra theo email nếu user_id không khớp
        $userEmail = $user['email'] ?? null;
        $orderEmail = $order['email'] ?? null;
        
        if ($userEmail && $orderEmail && strtolower(trim($userEmail)) === strtolower(trim($orderEmail))) {
            return true;
        }
        
        return false;
    }
}
