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

        // Lấy danh sách sản phẩm đã chọn để mua
        $selectedItems = $_SESSION['selected_cart_items'] ?? [];
        
        // Nếu có danh sách đã chọn, chỉ lấy các sản phẩm đó
        if (!empty($selectedItems)) {
            $filteredCart = [];
            foreach ($selectedItems as $cartKey) {
                if (isset($cart[$cartKey])) {
                    $filteredCart[$cartKey] = $cart[$cartKey];
                }
            }
            $cart = $filteredCart;
        }
        
        // Nếu không có sản phẩm nào được chọn, quay về giỏ hàng
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

        // Lấy danh sách sản phẩm đã chọn để mua
        $selectedItems = $_SESSION['selected_cart_items'] ?? [];
        
        // Nếu có danh sách đã chọn, chỉ lấy các sản phẩm đó
        if (!empty($selectedItems)) {
            $filteredCart = [];
            foreach ($selectedItems as $cartKey) {
                if (isset($cart[$cartKey])) {
                    $filteredCart[$cartKey] = $cart[$cartKey];
                }
            }
            $cart = $filteredCart;
        }
        
        // Nếu không có sản phẩm nào được chọn, quay về giỏ hàng
        if (empty($cart)) {
            set_flash('warning', 'Vui lòng chọn ít nhất một sản phẩm để mua.');
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
        
        // Xử lý mã giảm giá (ưu tiên từ session, sau đó từ POST)
        $couponId = null;
        $discountAmount = 0;
        $finalTotal = $total;
        
        require_once PATH_MODEL . 'CouponModel.php';
        $couponModel = new CouponModel();
        
        // Biến để lưu snapshot thông tin mã giảm giá
        $couponCode = null;
        $couponName = null;
        
        // Kiểm tra mã giảm giá từ session trước
        if (isset($_SESSION['applied_coupon'])) {
            $appliedCoupon = $_SESSION['applied_coupon'];
            $coupon = $couponModel->validateCoupon($appliedCoupon['code'], $total);
            if ($coupon && (int)$coupon['coupon_id'] === (int)$appliedCoupon['id']) {
                $discount = $couponModel->calculateDiscount($coupon, $total);
                $discountAmount = $discount['discount_amount'];
                $finalTotal = $discount['final_amount'];
                $couponId = (int)$coupon['coupon_id'];
                // Lưu snapshot thông tin mã giảm giá
                $couponCode = $coupon['code'];
                $couponName = $coupon['name'];
            } else {
                // Mã không còn hợp lệ, xóa khỏi session
                unset($_SESSION['applied_coupon']);
            }
        }
        
        // Nếu không có từ session, kiểm tra từ POST
        if (!$couponId && !empty($_POST['coupon_id']) && !empty($_POST['applied_coupon_code'])) {
            $coupon = $couponModel->validateCoupon($_POST['applied_coupon_code'], $total);
            if ($coupon && (int)$coupon['coupon_id'] === (int)$_POST['coupon_id']) {
                $discount = $couponModel->calculateDiscount($coupon, $total);
                $discountAmount = $discount['discount_amount'];
                $finalTotal = $discount['final_amount'];
                $couponId = (int)$coupon['coupon_id'];
                // Lưu snapshot thông tin mã giảm giá
                $couponCode = $coupon['code'];
                $couponName = $coupon['name'];
            }
        }
        
        // Xóa danh sách đã chọn sau khi đặt hàng
        unset($_SESSION['selected_cart_items']);

        // Gói dữ liệu đơn hàng chính (bao gồm snapshot mã giảm giá)
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
            'total_amount'   => $finalTotal,
            'coupon_id'      => $couponId,
            'discount_amount' => $discountAmount,
            'coupon_code'    => $couponCode,  // Snapshot: mã code tại thời điểm đặt hàng
            'coupon_name'    => $couponName,  // Snapshot: tên mã tại thời điểm đặt hàng
        ];

        try {
            // Lưu đơn + item, sau đó chuyển hướng sang trang chi tiết
            $orderId = $this->orderModel->create($orderPayload, $items);
            
            // Tăng số lần sử dụng mã giảm giá nếu có (mỗi đơn hàng = 1 lần sử dụng)
            if ($couponId) {
                require_once PATH_MODEL . 'CouponModel.php';
                $couponModel = new CouponModel();
                $couponModel->incrementUsage($couponId, 1);
                // Xóa mã giảm giá khỏi session sau khi sử dụng
                unset($_SESSION['applied_coupon']);
            }
            
            // Xóa các sản phẩm đã đặt hàng khỏi giỏ hàng
            if (!empty($selectedItems)) {
                foreach ($selectedItems as $cartKey) {
                    if (isset($_SESSION['cart'][$cartKey])) {
                        unset($_SESSION['cart'][$cartKey]);
                    }
                }
            } else {
                // Nếu không có danh sách đã chọn, xóa toàn bộ giỏ hàng
                unset($_SESSION['cart']);
            }
            
            // Xóa danh sách đã chọn
            unset($_SESSION['selected_cart_items']);
            
            // Xóa khỏi database nếu user đã đăng nhập
            if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
                require_once PATH_MODEL . 'CartModel.php';
                $cartModel = new CartModel();
                $userId = (int)$_SESSION['user']['id'];
                $cartId = $cartModel->getOrCreateCartIdByUserId($userId);
                
                if (!empty($selectedItems)) {
                    foreach ($selectedItems as $cartKey) {
                        // Parse cartKey để tìm cart_item_id
                        $parts = explode('_', $cartKey);
                        if (count($parts) >= 1) {
                            $productId = (int)$parts[0];
                            $size = ($parts[1] ?? 'null') !== 'null' ? $parts[1] : null;
                            $color = ($parts[2] ?? 'null') !== 'null' ? $parts[2] : null;
                            
                            // Tìm variant_id
                            $variantId = null;
                            if ($size || $color) {
                                require_once PATH_MODEL . 'ProductModel.php';
                                $productModel = new ProductModel();
                                $variant = $productModel->getVariantByValueNames($productId, $size, $color);
                                if ($variant) {
                                    $variantId = (int)$variant['variant_id'];
                                }
                            }
                            
                            // Tìm và xóa cart_item
                            $cartItemId = $cartModel->findCartItemIdByKey($cartId, $cartKey, $variantId);
                            if ($cartItemId) {
                                $cartModel->deleteItem($cartItemId);
                            }
                        }
                    }
                }
            }
            
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
