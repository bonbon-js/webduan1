<?php

require_once PATH_ROOT . 'libs/VnPay.php';
require_once PATH_MODEL . 'OrderModel.php';

class VnPayController
{
    private VnPay $vnpay;
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->vnpay = new VnPay();
        $this->orderModel = new OrderModel();
    }

    /**
     * Xử lý callback từ VNPay sau khi thanh toán
     */
    public function return(): void
    {
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        
        if (!$orderId) {
            set_flash('danger', 'Không tìm thấy đơn hàng.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        }

        // Validate callback từ VNPay
        $result = $this->vnpay->validateCallback($_GET);

        if ($result['success'] && $result['response_code'] === '00') {
            // Thanh toán thành công
            $order = $this->orderModel->findWithItems($orderId);
            
            if ($order && $order['status'] === OrderModel::STATUS_CONFIRMED) {
                // Đơn hàng đã được tạo, không cần làm gì thêm
                // Có thể cập nhật thông tin thanh toán vào đơn hàng nếu cần
            }
            
            // Xóa đơn hàng tạm khỏi session
            unset($_SESSION['pending_vnpay_order']);
            
            // Xóa giỏ hàng và coupon
            if (!empty($_SESSION['selected_cart_items'])) {
                foreach ($_SESSION['selected_cart_items'] as $cartKey) {
                    if (isset($_SESSION['cart'][$cartKey])) {
                        unset($_SESSION['cart'][$cartKey]);
                    }
                }
            }
            unset($_SESSION['selected_cart_items']);
            unset($_SESSION['applied_coupon']);
            
            set_flash('success', 'Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.');
            header('Location: ' . BASE_URL . '?action=order-history');
            exit;
        } else {
            // Thanh toán thất bại hoặc bị hủy
            $order = $this->orderModel->findWithItems($orderId);
            
            if ($order) {
                // Có thể hủy đơn hàng hoặc giữ nguyên để user thanh toán lại
                // Ở đây ta sẽ giữ nguyên và thông báo
            }
            
            unset($_SESSION['pending_vnpay_order']);
            
            $message = $result['message'] ?? 'Thanh toán không thành công. Vui lòng thử lại.';
            set_flash('warning', $message);
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        }
    }
}

