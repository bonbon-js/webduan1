<?php

require_once PATH_MODEL . 'CouponModel.php';

class CouponController
{
    private CouponModel $couponModel;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
    }

    /**
     * API để validate và tính toán mã giảm giá
     * POST: coupon_code, order_amount
     */
    public function validate()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $code = $data['coupon_code'] ?? '';
        $orderAmount = (float)($data['order_amount'] ?? 0);
        
        if (empty($code)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá'
            ]);
            exit;
        }
        
        if ($orderAmount <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Tổng tiền đơn hàng không hợp lệ'
            ]);
            exit;
        }
        
        $coupon = $this->couponModel->validateCoupon($code, $orderAmount);
        
        if (!$coupon) {
            echo json_encode([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn. Vui lòng kiểm tra lại điều kiện áp dụng.'
            ]);
            exit;
        }
        
        $discount = $this->couponModel->calculateDiscount($coupon, $orderAmount);
        
        // Lưu mã giảm giá vào session
        $_SESSION['applied_coupon'] = [
            'id' => $coupon['coupon_id'],
            'code' => $coupon['code'],
            'name' => $coupon['name'],
            'discount_amount' => $discount['discount_amount'],
        ];
        
        echo json_encode([
            'success' => true,
            'coupon' => [
                'id' => $coupon['coupon_id'],
                'code' => $coupon['code'],
                'name' => $coupon['name'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
            ],
            'discount_amount' => $discount['discount_amount'],
            'final_amount' => $discount['final_amount'],
            'message' => 'Áp dụng mã giảm giá thành công!'
        ]);
        exit;
    }

    /**
     * API để lấy danh sách mã giảm giá khả dụng
     * GET: order_amount
     */
    public function getAvailable()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $orderAmount = (float)($_GET['order_amount'] ?? 0);
        
        if ($orderAmount <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Tổng tiền đơn hàng không hợp lệ',
                'coupons' => []
            ]);
            exit;
        }
        
        $coupons = $this->couponModel->getAvailableCoupons($orderAmount);
        
        echo json_encode([
            'success' => true,
            'coupons' => $coupons
        ]);
        exit;
    }

    /**
     * API để xóa mã giảm giá đã áp dụng
     */
    public function remove()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        unset($_SESSION['applied_coupon']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá'
        ]);
        exit;
    }
}

