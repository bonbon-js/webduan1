<?php

class CheckoutController
{
    private OrderModel $orderModel;

    public function __construct()
    {
        // Chuẩn bị model order để tái sử dụng
        $this->orderModel = new OrderModel();
    }

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
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            set_flash('warning', 'Giỏ hàng trống, không thể đặt hàng.');
            header('Location: ' . BASE_URL . '?action=cart-list');
            exit;
        }

        // Kiểm tra nhanh các field bắt buộc
        $requiredFields = ['fullname', 'phone', 'email', 'address'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                set_flash('danger', 'Vui lòng nhập đầy đủ thông tin giao hàng.');
                header('Location: ' . BASE_URL . '?action=checkout');
                exit;
            }
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $total = 0;
        $items = [];

        // Chuyển dữ liệu giỏ hàng thành payload để lưu vào DB
        foreach ($cart as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $total += $lineTotal;
            $items[] = [
                'product_id'    => $item['id'] ?? null,
                'product_name'  => $item['name'],
                'variant_size'  => $item['size'] ?? null,
                'variant_color' => $item['color'] ?? null,
                'quantity'      => $item['quantity'],
                'unit_price'    => $item['price'],
                'image_url'     => $item['image'] ?? null,
            ];
        }

        // Gói dữ liệu đơn hàng chính
        $orderPayload = [
            'user_id'        => $userId,
            'fullname'       => trim($_POST['fullname']),
            'email'          => trim($_POST['email']),
            'phone'          => trim($_POST['phone']),
            'address'        => trim($_POST['address']),
            'city'           => $_POST['city'] ?? null,
            'district'       => $_POST['district'] ?? null,
            'ward'           => $_POST['ward'] ?? null,
            'note'           => $_POST['note'] ?? null,
            'payment_method' => $_POST['payment_method'] ?? 'cod',
            'status'         => OrderModel::STATUS_CONFIRMED,
            'total_amount'   => $total,
        ];

        try {
            // Lưu đơn + item, sau đó chuyển hướng sang trang chi tiết
            $orderId = $this->orderModel->create($orderPayload, $items);
            unset($_SESSION['cart']);
            set_flash('success', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ để xác nhận.');
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
            exit;
        } catch (Throwable $exception) {
            set_flash('danger', 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại sau.');
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }
    }
}
