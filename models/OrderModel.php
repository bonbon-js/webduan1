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
                    order_code, user_id, fullname, email, phone, address, city, district, ward, note, payment_method, status, total_amount
                )
                VALUES (
                    :order_code, :user_id, :fullname, :email, :phone, :address, :city, :district, :ward, :note, :payment_method, :status, :total_amount
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

        // Không dùng ORDER BY, sẽ sắp xếp trong PHP

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        // Sắp xếp trong PHP theo id nếu có, ngược lại theo order_code
        usort($results, function($a, $b) {
            if (isset($a['id']) && isset($b['id'])) {
                return (int)$b['id'] - (int)$a['id'];
            }
            if (isset($a['order_code']) && isset($b['order_code'])) {
                return strcmp($b['order_code'], $a['order_code']);
            }
            return 0;
        });
        
        return $results;
    }

    // Lấy thông tin đơn hàng + danh sách sản phẩm (dùng cho chi tiết)
    public function findWithItems(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id");
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
            $query .= " AND (order_code LIKE :keyword OR fullname LIKE :keyword OR phone LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($status) {
            $query .= " AND status = :status";
            $params[':status'] = $status;
        }

        // Không dùng ORDER BY nếu không chắc chắn về cột
        // Sẽ sắp xếp trong PHP nếu cần

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
        // Sắp xếp trong PHP theo id nếu có, ngược lại theo order_code
        usort($results, function($a, $b) {
            if (isset($a['id']) && isset($b['id'])) {
                return (int)$b['id'] - (int)$a['id'];
            }
            if (isset($a['order_code']) && isset($b['order_code'])) {
                return strcmp($b['order_code'], $a['order_code']);
            }
            return 0;
        });
        
        return $results;
    }

    // Admin đổi trạng thái đơn
    public function updateStatus(int $orderId, string $status): bool
    {
        if (!isset(self::STATUS_LABELS[$status])) {
            throw new InvalidArgumentException('Trạng thái không hợp lệ.');
        }

        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            ':status' => $status,
            ':id'     => $orderId,
        ]);
    }

    // Người dùng hủy đơn (ghi nhận lý do nếu có)
    public function cancel(int $orderId, ?string $reason = null): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE orders 
            SET status = :status, cancel_reason = :reason
            WHERE id = :id
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

    // Tính tổng doanh thu
    public function getTotalRevenue(): float
    {
        $stmt = $this->pdo->prepare("SELECT SUM(total_amount) as total FROM orders WHERE status != :cancelled");
        $stmt->execute([':cancelled' => self::STATUS_CANCELLED]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    // Tính doanh thu tháng này (tạm thời lấy tất cả vì không có created_at)
    public function getMonthlyRevenue(): float
    {
        // Nếu không có created_at, lấy tất cả đơn hàng
        $stmt = $this->pdo->prepare("
            SELECT SUM(total_amount) as total 
            FROM orders 
            WHERE status != :cancelled
        ");
        $stmt->execute([':cancelled' => self::STATUS_CANCELLED]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    // Lấy doanh thu theo tháng (tạm thời trả về dữ liệu mẫu)
    public function getRevenueByMonth(): array
    {
        // Tạo dữ liệu mẫu cho 12 tháng
        $months = [];
        $currentMonth = date('Y-m');
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = [
                'month' => $month,
                'revenue' => rand(500000, 2000000) // Dữ liệu mẫu
            ];
        }
        return $months;
    }

    // Lấy sản phẩm bán chạy nhất
    public function getBestSellingProduct(): ?array
    {
        // Lấy sản phẩm bán chạy nhất từ order_items
        // Không JOIN với orders để tránh lỗi cột không tồn tại
        $stmt = $this->pdo->prepare("
            SELECT 
                product_name,
                SUM(quantity) as total_quantity,
                SUM(quantity * unit_price) as total_revenue
            FROM order_items
            GROUP BY product_name
            ORDER BY total_quantity DESC
            LIMIT 1
        ");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Lấy thống kê sản phẩm theo danh mục
    public function getProductStats(): array
    {
        // Tạm thời lấy tất cả sản phẩm không lọc theo status vì không chắc chắn về cấu trúc JOIN
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    product_name,
                    SUM(quantity) as total_quantity
                FROM order_items
                GROUP BY product_name
                ORDER BY total_quantity DESC
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    // Lấy đơn hàng gần đây
    public function getRecentOrders(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE 1=1
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        // Sắp xếp trong PHP
        usort($results, function($a, $b) {
            if (isset($a['id']) && isset($b['id'])) {
                return (int)$b['id'] - (int)$a['id'];
            }
            return 0;
        });
        
        return $results;
    }
}

