<?php

class CheckoutController
{
    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: ' . BASE_URL . '?action=cart-list');
            exit;
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Logo cho view
        $logoUrl = BASE_URL . 'assets/images/logo.png';
        if (file_exists(PATH_ROOT . 'assets/images/logo.png')) {
            $data = file_get_contents(PATH_ROOT . 'assets/images/logo.png');
            $type = pathinfo(PATH_ROOT . 'assets/images/logo.png', PATHINFO_EXTENSION);
            $logoUrl = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $view = 'checkout';
        $title = 'Thanh Toán - BonBonWear';
        require_once PATH_VIEW . 'main.php';
    }

    public function process()
    {
        // Xử lý đặt hàng (Giả lập)
        // 1. Validate dữ liệu
        // 2. Lưu vào DB (Order, OrderItems)
        // 3. Xóa giỏ hàng
        // 4. Redirect trang cảm ơn

        unset($_SESSION['cart']);
        
        // Redirect về trang chủ với thông báo (hoặc trang success riêng)
        echo "<script>alert('Đặt hàng thành công! Cảm ơn bạn đã mua sắm.'); window.location.href='" . BASE_URL . "';</script>";
        exit;
    }
}
