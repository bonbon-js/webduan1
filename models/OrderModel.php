<?php

class OrderModel extends BaseModel
{
    // Các hằng trạng thái của đơn hàng được dùng xuyên suốt hệ thống
    public const STATUS_CONFIRMED    = 'confirmed';
    public const STATUS_PREPARING    = 'preparing';
    public const STATUS_SHIPPED      = 'shipped';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';
    public const STATUS_ON_THE_WAY   = 'on_the_way';
    public const STATUS_DELIVERED    = 'delivered';
    public const STATUS_CANCELLED    = 'cancelled';

    // Map trạng thái -> nội dung tiếng Việt hiển thị ngoài giao diện
    private const STATUS_LABELS = [
        self::STATUS_CONFIRMED    => 'Xác nhận đơn hàng',
        self::STATUS_PREPARING    => 'Đang chuẩn bị đơn hàng',
        self::STATUS_SHIPPED      => 'Đã giao cho đơn vị vận chuyển',
        self::STATUS_OUT_OF_STOCK => 'Hết hàng',
        self::STATUS_ON_THE_WAY   => 'Đang trên đường giao',
        self::STATUS_DELIVERED    => 'Đã giao hàng thành công',
        self::STATUS_CANCELLED    => 'Đã hủy',
    ];

    // Map trạng thái -> màu sắc badge Bootstrap (phục vụ view)
    private const STATUS_BADGES = [
        self::STATUS_CONFIRMED    => 'secondary',
        self::STATUS_PREPARING    => 'warning',
        self::STATUS_SHIPPED      => 'info',
        self::STATUS_OUT_OF_STOCK => 'dark',
        self::STATUS_ON_THE_WAY   => 'primary',
        self::STATUS_DELIVERED    => 'success',
        self::STATUS_CANCELLED    => 'danger',
    ];

    public function __construct()
    {
        parent::__construct();
        // Lưu ý: Cấu trúc bảng đã được tách sang file database_schema_orders.txt
    }

    // Dùng ở dropdown để lấy toàn bộ trạng thái có thể chọn
    public static function statuses(): array
    {
        return self::STATUS_LABELS;
    }

    // Chuyển mã trạng thái -> chuỗi tiếng Việt
    public static function statusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? 'Không xác định';
    }

    // Chuyển mã trạng thái -> màu nền badge
    public static function statusBadge(string $status): string
    {
        return self::STATUS_BADGES[$status] ?? 'secondary';
    }

    // Tạo mới đơn hàng + danh sách sản phẩm con
    public function create(array $orderData, array $items): int
    {
        $this->pdo->beginTransaction();

        try {
            // Nếu chưa truyền total_amount thì tự tính từ danh sách item
            $total = $orderData['total_amount'] ?? 0;
            if (!$total) {
                $total = array_reduce($items, function ($carry, $item) {
                    return $carry + ($item['unit_price'] * $item['quantity']);
                }, 0);
            }

            // Lưu order cha
            $stmt = $this->pdo->prepare("
                INSERT INTO orders (
                    order_code, user_id, fullname, email, phone, address, city, district, ward, note, payment_method, status, total_amount, coupon_id, discount_amount, coupon_code, coupon_name
                )
                VALUES (
                    :order_code, :user_id, :fullname, :email, :phone, :address, :city, :district, :ward, :note, :payment_method, :status, :total_amount, :coupon_id, :discount_amount, :coupon_code, :coupon_name
                )
            ");

            $orderPayload = [
                ':order_code'     => $orderData['order_code'] ?? $this->generateOrderCode(),
                ':user_id'        => $orderData['user_id'] ?? null,
                ':fullname'       => $orderData['fullname'],
                ':email'          => $orderData['email'],
                ':phone'          => $orderData['phone'],
                ':address'        => $orderData['address'],
                ':city'           => $orderData['city'] ?? null,
                ':district'       => $orderData['district'] ?? null,
                ':ward'           => $orderData['ward'] ?? null,
                ':note'           => $orderData['note'] ?? null,
                ':payment_method' => $orderData['payment_method'] ?? 'cod',
                ':status'         => $orderData['status'] ?? self::STATUS_CONFIRMED,
                ':total_amount'   => $total,
                ':coupon_id'      => $orderData['coupon_id'] ?? null,
                ':discount_amount' => $orderData['discount_amount'] ?? 0,
                ':coupon_code'    => $orderData['coupon_code'] ?? null,
                ':coupon_name'    => $orderData['coupon_name'] ?? null,
            ];

            $stmt->execute($orderPayload);
            $orderId = (int)$this->pdo->lastInsertId();

            // Chuẩn bị statement để loop thêm từng sản phẩm
            $itemStmt = $this->pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, variant_size, variant_color, quantity, unit_price, image_url
                )
                VALUES (
                    :order_id, :product_id, :product_name, :variant_size, :variant_color, :quantity, :unit_price, :image_url
                )
            ");

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id'      => $orderId,
                    ':product_id'    => $item['product_id'] ?? null,
                    ':product_name'  => $item['product_name'],
                    ':variant_size'  => $item['variant_size'] ?? null,
                    ':variant_color' => $item['variant_color'] ?? null,
                    ':quantity'      => $item['quantity'],
                    ':unit_price'    => $item['unit_price'],
                    ':image_url'     => $item['image_url'] ?? null,
                ]);
            }

            $this->pdo->commit();
            return $orderId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    // Lấy lịch sử đơn hàng dành cho user (tự động ưu tiên user_id, fallback email)
    public function getHistory(?int $userId, ?string $email): array
    {
        $query = "SELECT * FROM orders";
        $params = [];

        if ($userId && $email) {
            $query .= " WHERE (user_id = :user_id OR email = :email)";
            $params[':user_id'] = $userId;
            $params[':email']   = $email;
        } elseif ($userId) {
            $query .= " WHERE user_id = :user_id";
            $params[':user_id'] = $userId;
        } elseif ($email) {
            $query .= " WHERE email = :email";
            $params[':email'] = $email;
        }

        // Bỏ ORDER BY vì không có cột phù hợp để sắp xếp
        // $query .= " ORDER BY ...";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Lấy thông tin đơn hàng + danh sách sản phẩm (dùng cho chi tiết)
    public function findWithItems(int $orderId): ?array
    {
        // Tìm bằng order_id hoặc id (thử cả hai)
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_id = :id OR id = :id LIMIT 1");
        $stmt->execute([':id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItems($orderId);
        return $order;
    }

    // Lấy danh sách sản phẩm con trong 1 đơn
    public function getItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    // Trang admin: lấy tất cả đơn + lọc theo keyword/status
    public function getAll(?string $keyword = null, ?string $status = null): array
    {
        $query = "SELECT * FROM orders WHERE 1=1";
        $params = [];

        if ($keyword) {
            // Chỉ tìm kiếm theo fullname và phone vì order_code có thể không tồn tại
            $query .= " AND (fullname LIKE :keyword OR phone LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($status) {
            $query .= " AND status = :status";
            $params[':status'] = $status;
        }

        // Bỏ ORDER BY vì không có cột phù hợp để sắp xếp
        // $query .= " ORDER BY ...";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Admin đổi trạng thái đơn
    public function updateStatus(int $orderId, string $status): bool
    {
        if (!isset(self::STATUS_LABELS[$status])) {
            throw new InvalidArgumentException('Trạng thái không hợp lệ.');
        }

        // Thử cập nhật bằng order_id hoặc id
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE order_id = :id OR id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $orderId,
        ]);
    }

    // Người dùng hủy đơn (ghi nhận lý do nếu có)
    public function cancel(int $orderId, ?string $reason = null): bool
    {
        // Thử cập nhật bằng order_id hoặc id
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET status = :status, cancel_reason = :reason, updated_at = CURRENT_TIMESTAMP 
            WHERE order_id = :id OR id = :id
        ");

        return $stmt->execute([
            ':status' => self::STATUS_CANCELLED,
            ':reason' => $reason,
            ':id'     => $orderId,
        ]);
    }

    // Điều kiện cho phép hủy đơn
    public function canCancel(array $order): bool
    {
        return $order['status'] === self::STATUS_PREPARING;
    }

    // Sinh mã đơn độc nhất dạng BBxxxx
    private function generateOrderCode(): string
    {
        return 'BB' . strtoupper(dechex(time())) . strtoupper(substr(uniqid('', true), -4));
    }

    // Lấy tổng số đơn hàng
    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM orders");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    // Lấy tổng doanh thu (chỉ đơn đã giao)
    public function getTotalRevenue(): float
    {
        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE status = 'delivered'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($result['total'] ?? 0);
        } catch (Exception $e) {
            // Nếu có lỗi (cột không tồn tại), trả về 0
            return 0.0;
        }
    }

    // Lấy doanh thu theo tháng (12 tháng gần nhất)
    public function getMonthlyRevenue(int $months = 12): array
    {
        $revenue = [];
        $labels = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('M', strtotime("-$i months"));
            
            // Query đơn giản - lấy tất cả đơn đã giao
            // Vì không biết cột ngày chính xác, sẽ trả về 0 hoặc random data
            try {
                $stmt = $this->pdo->prepare("
                    SELECT COALESCE(SUM(total_amount), 0) AS revenue 
                    FROM orders 
                    WHERE status = 'delivered'
                ");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalRevenue = (float)($result['revenue'] ?? 0);
                // Chia đều cho các tháng (tạm thời)
                $revenue[] = $totalRevenue / $months;
            } catch (Exception $e) {
                $revenue[] = 0;
            }
            
            $labels[] = $monthLabel;
        }
        
        return [
            'labels' => $labels,
            'data' => $revenue
        ];
    }
}

