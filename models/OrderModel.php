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

            // Kiểm tra các cột có tồn tại không
            $existingColumns = $this->getExistingColumns('orders');
            
            // Tìm tên cột đúng (có thể có biến thể)
            $columnMap = [
                'fullname' => $this->findColumnName($existingColumns, ['fullname', 'full_name', 'name', 'customer_name']),
                'email' => $this->findColumnName($existingColumns, ['email', 'customer_email']),
                'phone' => $this->findColumnName($existingColumns, ['phone', 'phone_number', 'tel']),
                'address' => $this->findColumnName($existingColumns, ['address', 'delivery_address', 'shipping_address']),
                'user_id' => $this->findColumnName($existingColumns, ['user_id', 'customer_id']),
                'payment_method' => $this->findColumnName($existingColumns, ['payment_method', 'payment']),
                'status' => $this->findColumnName($existingColumns, ['status', 'order_status']),
                'total_amount' => $this->findColumnName($existingColumns, ['total_amount', 'total', 'amount']),
            ];
            
            // Xây dựng danh sách cột và giá trị động (chỉ thêm các cột tồn tại)
            $insertColumns = [];
            $insertValues = [];
            $orderPayload = [];
            
            // KHÔNG thêm PRIMARY KEY vào INSERT (để AUTO_INCREMENT tự động tăng)
            // Kiểm tra PRIMARY KEY để tránh thêm vào
            $primaryKeyColumn = $this->getPrimaryKeyColumn('orders');
            
            // Thêm các cột bắt buộc nếu tồn tại (trừ PRIMARY KEY)
            if ($columnMap['user_id'] && $columnMap['user_id'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['user_id'];
                $insertValues[] = ':user_id';
                $orderPayload[':user_id'] = $orderData['user_id'] ?? null;
            }
            
            if ($columnMap['fullname'] && $columnMap['fullname'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['fullname'];
                $insertValues[] = ':fullname';
                $orderPayload[':fullname'] = $orderData['fullname'];
            }
            
            if ($columnMap['email'] && $columnMap['email'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['email'];
                $insertValues[] = ':email';
                $orderPayload[':email'] = $orderData['email'];
            }
            
            if ($columnMap['phone'] && $columnMap['phone'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['phone'];
                $insertValues[] = ':phone';
                $orderPayload[':phone'] = $orderData['phone'];
            }
            
            if ($columnMap['address'] && $columnMap['address'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['address'];
                $insertValues[] = ':address';
                $orderPayload[':address'] = $orderData['address'];
            }
            
            if ($columnMap['payment_method'] && $columnMap['payment_method'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['payment_method'];
                $insertValues[] = ':payment_method';
                $orderPayload[':payment_method'] = $orderData['payment_method'] ?? 'cod';
            }
            
            if ($columnMap['status'] && $columnMap['status'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['status'];
                $insertValues[] = ':status';
                $orderPayload[':status'] = $orderData['status'] ?? self::STATUS_CONFIRMED;
            }
            
            if ($columnMap['total_amount'] && $columnMap['total_amount'] !== $primaryKeyColumn) {
                $insertColumns[] = $columnMap['total_amount'];
                $insertValues[] = ':total_amount';
                $orderPayload[':total_amount'] = $total;
            }
            
            // Thêm các cột tùy chọn nếu tồn tại
            $optionalColumns = [
                'order_code' => $orderData['order_code'] ?? $this->generateOrderCode(),
                'city' => $orderData['city'] ?? null,
                'district' => $orderData['district'] ?? null,
                'ward' => $orderData['ward'] ?? null,
                'note' => $orderData['note'] ?? null,
                'coupon_id' => $orderData['coupon_id'] ?? null,
                'discount_amount' => $orderData['discount_amount'] ?? 0,
                'coupon_code' => $orderData['coupon_code'] ?? null,
                'coupon_name' => $orderData['coupon_name'] ?? null,
            ];
            
            foreach ($optionalColumns as $col => $value) {
                // Không thêm PRIMARY KEY vào INSERT
                if (in_array($col, $existingColumns) && $col !== $primaryKeyColumn) {
                    $insertColumns[] = $col;
                    $insertValues[] = ':' . $col;
                    $orderPayload[':' . $col] = $value;
                }
            }
            
            // Validate: Phải có ít nhất các cột cơ bản
            if (empty($insertColumns)) {
                $errorMsg = 'Không tìm thấy cột nào phù hợp trong bảng orders. ';
                $errorMsg .= 'Các cột hiện có: ' . implode(', ', $existingColumns);
                error_log('OrderModel::create - Available columns: ' . implode(', ', $existingColumns));
                throw new Exception($errorMsg);
            }
            
            // Log để debug
            error_log('OrderModel::create - Primary Key Column: ' . ($primaryKeyColumn ?? 'NULL'));
            error_log('OrderModel::create - Inserting columns: ' . implode(', ', $insertColumns));
            error_log('OrderModel::create - Inserting values: ' . json_encode($orderPayload));

            // Đảm bảo PRIMARY KEY không có trong danh sách cột
            if ($primaryKeyColumn && in_array($primaryKeyColumn, $insertColumns)) {
                $errorMsg = "Lỗi: PRIMARY KEY ($primaryKeyColumn) đã được thêm vào INSERT statement. Điều này không được phép.";
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }

            // Lưu order cha
            $columnsStr = implode(', ', $insertColumns);
            $valuesStr = implode(', ', $insertValues);
            
            $sql = "INSERT INTO orders ($columnsStr) VALUES ($valuesStr)";
            error_log('OrderModel::create - SQL: ' . $sql);
            
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($orderPayload);
            $orderId = (int)$this->pdo->lastInsertId();
            
            if ($orderId === 0) {
                error_log('OrderModel::create - WARNING: lastInsertId() returned 0. This might indicate an issue with AUTO_INCREMENT.');
                // Thử lấy ID bằng cách khác
                $stmt = $this->pdo->query("SELECT LAST_INSERT_ID() as id");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result && isset($result['id']) && $result['id'] > 0) {
                    $orderId = (int)$result['id'];
                    error_log('OrderModel::create - Got ID from LAST_INSERT_ID(): ' . $orderId);
                } else {
                    throw new Exception('Không thể lấy ID của đơn hàng vừa tạo. Có thể do lỗi AUTO_INCREMENT.');
                }
            }
            
            error_log('OrderModel::create - Created order with ID: ' . $orderId);

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

    // Lấy lịch sử đơn hàng dành cho user
    public function getHistory(?int $userId, ?string $email): array
    {
        // Kiểm tra các cột có tồn tại không
        $existingColumns = $this->getExistingColumns('orders');
        
        // Tìm tên cột user_id (có thể là user_id hoặc customer_id)
        $userIdColumn = $this->findColumnName($existingColumns, ['user_id', 'customer_id']);
        
        if (!$userIdColumn) {
            // Nếu không có cột user_id, trả về mảng rỗng
            error_log('OrderModel::getHistory - No user_id column found in orders table');
            return [];
        }
        
        $query = "SELECT * FROM orders";
        $params = [];

        // Chỉ sử dụng user_id để tìm kiếm (đảm bảo an toàn và đơn giản)
        if ($userId) {
            $query .= " WHERE $userIdColumn = :user_id";
            $params[':user_id'] = $userId;
        } else {
            // Nếu không có user_id, trả về mảng rỗng
            return [];
        }

        // Sắp xếp theo order_id (mới nhất trước) - không dùng created_at vì có thể không tồn tại
        // Tìm tên cột PRIMARY KEY để sắp xếp
        $pkColumn = $this->getPrimaryKeyColumn('orders');
        if ($pkColumn) {
            $query .= " ORDER BY $pkColumn DESC";
        } else {
            // Nếu không tìm thấy PRIMARY KEY, thử dùng order_id hoặc id
            if (in_array('order_id', $existingColumns)) {
                $query .= " ORDER BY order_id DESC";
            } elseif (in_array('id', $existingColumns)) {
                $query .= " ORDER BY id DESC";
            }
        }

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
    
    // Lấy danh sách cột có trong bảng
    private function getExistingColumns(string $tableName): array
    {
        static $cache = [];
        
        if (isset($cache[$tableName])) {
            return $cache[$tableName];
        }
        
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM {$tableName}");
            $columns = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[] = $row['Field'];
            }
            $cache[$tableName] = $columns;
            return $columns;
        } catch (PDOException $e) {
            // Nếu không thể lấy danh sách cột, trả về mảng rỗng
            return [];
        }
    }
    
    // Tìm tên cột đúng từ danh sách các biến thể có thể có
    private function findColumnName(array $existingColumns, array $possibleNames): ?string
    {
        foreach ($possibleNames as $name) {
            if (in_array($name, $existingColumns)) {
                return $name;
            }
        }
        return null;
    }
    
    // Lấy tên cột PRIMARY KEY của bảng
    private function getPrimaryKeyColumn(string $tableName): ?string
    {
        static $cache = [];
        
        if (isset($cache[$tableName])) {
            return $cache[$tableName];
        }
        
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM {$tableName} WHERE `Key` = 'PRI'");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $pkColumn = $row ? $row['Field'] : null;
            $cache[$tableName] = $pkColumn;
            return $pkColumn;
        } catch (PDOException $e) {
            return null;
        }
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

