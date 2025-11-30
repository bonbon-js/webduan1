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
            // Loại bỏ PRIMARY KEY khỏi orderData bằng helper function
            // Truyền tên bảng để xác định chính xác PRIMARY KEY
            $orderData = $this->removePrimaryKeyFromData($orderData, 'orders_new');
            
            // Nếu chưa truyền total_amount thì tự tính từ danh sách item
            $total = $orderData['total_amount'] ?? 0;
            if (!$total) {
                $total = array_reduce($items, function ($carry, $item) {
                    return $carry + ($item['unit_price'] * $item['quantity']);
                }, 0);
            }

            // Luôn sử dụng bảng 'orders_new' vì có đầy đủ các cột cần thiết
            $tableName = 'orders_new';
            $primaryKeyColumn = 'id'; // PRIMARY KEY của orders_new là 'id'
            
            // Định nghĩa rõ ràng danh sách cột được phép INSERT (KHÔNG BAO GỒM PRIMARY KEY 'id')
            // Danh sách này dựa trên cấu trúc bảng orders_new
            $allowedColumns = [
                'order_code', 'user_id', 'fullname', 'email', 'phone', 'address',
                'city', 'district', 'ward', 'note', 'payment_method', 'status',
                'total_amount', 'coupon_id', 'discount_amount', 'coupon_code', 'coupon_name'
            ];
            
            // Xây dựng danh sách cột và giá trị để INSERT
            $insertColumns = [];
            $insertValues = [];
            $orderPayload = [];
            
            // Thêm các cột bắt buộc
            if (isset($orderData['user_id'])) {
                $insertColumns[] = 'user_id';
                $insertValues[] = ':user_id';
                $orderPayload[':user_id'] = $orderData['user_id'];
            }
            
            if (isset($orderData['fullname'])) {
                $insertColumns[] = 'fullname';
                $insertValues[] = ':fullname';
                $orderPayload[':fullname'] = $orderData['fullname'];
            }
            
            if (isset($orderData['email'])) {
                $insertColumns[] = 'email';
                $insertValues[] = ':email';
                $orderPayload[':email'] = $orderData['email'];
            }
            
            if (isset($orderData['phone'])) {
                $insertColumns[] = 'phone';
                $insertValues[] = ':phone';
                $orderPayload[':phone'] = $orderData['phone'];
            }
            
            if (isset($orderData['address'])) {
                $insertColumns[] = 'address';
                $insertValues[] = ':address';
                $orderPayload[':address'] = $orderData['address'];
            }
            
            $insertColumns[] = 'payment_method';
            $insertValues[] = ':payment_method';
            $orderPayload[':payment_method'] = $orderData['payment_method'] ?? 'cod';
            
            $insertColumns[] = 'status';
            $insertValues[] = ':status';
            $orderPayload[':status'] = $orderData['status'] ?? self::STATUS_CONFIRMED;
            
            $insertColumns[] = 'total_amount';
            $insertValues[] = ':total_amount';
            $orderPayload[':total_amount'] = $total;
            
            // Thêm các cột tùy chọn
            $optionalData = [
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
            
            foreach ($optionalData as $col => $value) {
                // Chỉ thêm nếu cột nằm trong danh sách được phép và không phải PRIMARY KEY
                if (in_array($col, $allowedColumns) && $col !== $primaryKeyColumn) {
                    $insertColumns[] = $col;
                    $insertValues[] = ':' . $col;
                    $orderPayload[':' . $col] = $value;
                }
            }
            
            // KIỂM TRA CUỐI CÙNG: Đảm bảo KHÔNG có PRIMARY KEY trong insertColumns
            foreach ($insertColumns as $col) {
                if ($col === 'id' || $col === 'order_id' || $col === $primaryKeyColumn) {
                    throw new Exception("LỖI NGHIÊM TRỌNG: PRIMARY KEY '$col' đã được thêm vào INSERT statement!");
                }
            }
            
            // Validate: Phải có ít nhất một cột
            if (empty($insertColumns)) {
                throw new Exception("LỖI: Không có cột nào để INSERT vào bảng $tableName!");
            }
            
            // Xây dựng SQL statement
            $columnsStr = implode(', ', $insertColumns);
            $valuesStr = implode(', ', $insertValues);
            $sql = "INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)";
            
            error_log("OrderModel::create - SQL: $sql");
            error_log("OrderModel::create - Columns: " . implode(', ', $insertColumns));
            
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute($orderPayload);
            $orderId = (int)$this->pdo->lastInsertId();
            
            if ($orderId === 0) {
                error_log('OrderModel::create - WARNING: lastInsertId() returned 0. This might indicate an issue with AUTO_INCREMENT.');
                
                // Kiểm tra xem có bản ghi với id = 0 không và xóa nó
                $this->fixZeroIdRecord($tableName, $primaryKeyColumn);
                
                // Đảm bảo AUTO_INCREMENT được bật
                if ($primaryKeyColumn) {
                    $this->ensureAutoIncrement($tableName, $primaryKeyColumn);
                }
                
                // Thử lấy ID bằng cách khác
                $stmt = $this->pdo->query("SELECT LAST_INSERT_ID() as id");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result && isset($result['id']) && $result['id'] > 0) {
                    $orderId = (int)$result['id'];
                    error_log('OrderModel::create - Got ID from LAST_INSERT_ID(): ' . $orderId);
                } else {
                    // Thử lấy ID từ bản ghi vừa insert bằng cách khác
                    if ($primaryKeyColumn) {
                        $stmt = $this->pdo->query("SELECT MAX({$primaryKeyColumn}) as max_id FROM $tableName");
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($result && isset($result['max_id']) && $result['max_id'] > 0) {
                            $orderId = (int)$result['max_id'];
                            error_log('OrderModel::create - Got ID from MAX(): ' . $orderId);
                        } else {
                            throw new Exception('Không thể lấy ID của đơn hàng vừa tạo. Có thể do lỗi AUTO_INCREMENT hoặc có bản ghi với id = 0. Vui lòng kiểm tra cấu hình database.');
                        }
                    } else {
                        throw new Exception('Không thể lấy ID của đơn hàng vừa tạo. Không tìm thấy PRIMARY KEY column.');
                    }
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
        // Nếu không có cả user_id và email, trả về mảng rỗng
        if (!$userId && !$email) {
            error_log("OrderModel::getHistory - No user_id or email provided");
            return [];
        }
        
        // Sử dụng bảng orders_new với PRIMARY KEY là 'id'
        // Tìm theo user_id HOẶC email (nếu user_id không khớp thì vẫn tìm được theo email)
        $query = "SELECT * FROM orders_new WHERE (";
        $params = [];
        $conditions = [];
        
        if ($userId) {
            $conditions[] = "user_id = :user_id";
            $params[':user_id'] = $userId;
        }
        
        if ($email) {
            $conditions[] = "email = :email";
            $params[':email'] = $email;
        }
        
        // Nếu có cả user_id và email, dùng OR để tìm theo cả hai
        // Nếu chỉ có một trong hai, chỉ tìm theo cái đó
        if (count($conditions) > 0) {
            $query .= implode(" OR ", $conditions);
        } else {
            error_log("OrderModel::getHistory - No valid conditions");
            return [];
        }
        
        $query .= ") ORDER BY id DESC";
        
        error_log("OrderModel::getHistory - Query: $query");
        error_log("OrderModel::getHistory - Params: " . json_encode($params));

        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            error_log("OrderModel::getHistory - Found " . count($results) . " orders");
            
            return $results;
        } catch (PDOException $e) {
            error_log("OrderModel::getHistory - Error: " . $e->getMessage());
            return [];
        }
    }

    // Lấy thông tin đơn hàng + danh sách sản phẩm (dùng cho chi tiết)
    public function findWithItems(int $orderId): ?array
    {
        // Sử dụng bảng orders_new với PRIMARY KEY là 'id'
        $stmt = $this->pdo->prepare("SELECT * FROM orders_new WHERE id = :id LIMIT 1");
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
        // Sử dụng bảng orders_new
        $query = "SELECT * FROM orders_new WHERE 1=1";
        $params = [];

        if ($keyword) {
            // Tìm kiếm theo fullname, phone, hoặc order_code
            $query .= " AND (fullname LIKE :keyword OR phone LIKE :keyword OR order_code LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($status) {
            $query .= " AND status = :status";
            $params[':status'] = $status;
        }

        // Sắp xếp theo id (mới nhất trước)
        $query .= " ORDER BY id DESC";

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

        // Sử dụng bảng orders_new với PRIMARY KEY là 'id'
        $stmt = $this->pdo->prepare("
            UPDATE orders_new 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
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
        // Sử dụng bảng orders_new với PRIMARY KEY là 'id'
        $stmt = $this->pdo->prepare("
            UPDATE orders_new 
            SET status = :status, cancel_reason = :reason, updated_at = CURRENT_TIMESTAMP 
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

    /**
     * Đảm bảo cột PRIMARY KEY có AUTO_INCREMENT
     */
    private function ensureAutoIncrement(string $tableName, ?string $primaryKeyColumn): void
    {
        if (!$primaryKeyColumn) {
            return;
        }
        
        try {
            // Kiểm tra xem cột có AUTO_INCREMENT không
            $stmt = $this->pdo->query("SHOW COLUMNS FROM {$tableName} WHERE Field = '{$primaryKeyColumn}'");
            $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($columnInfo && strpos($columnInfo['Extra'], 'auto_increment') === false) {
                error_log("OrderModel::ensureAutoIncrement - Column {$primaryKeyColumn} does not have AUTO_INCREMENT. Attempting to fix...");
                
                // Lấy kiểu dữ liệu của cột
                $dataType = $columnInfo['Type'];
                $nullInfo = $columnInfo['Null'] === 'NO' ? 'NOT NULL' : '';
                
                // Sửa lại cột để có AUTO_INCREMENT
                // Lưu ý: ALTER TABLE có thể mất thời gian với bảng lớn
                $sql = "ALTER TABLE {$tableName} MODIFY COLUMN {$primaryKeyColumn} {$dataType} {$nullInfo} AUTO_INCREMENT";
                $this->pdo->exec($sql);
                
                error_log("OrderModel::ensureAutoIncrement - Fixed AUTO_INCREMENT for column {$primaryKeyColumn} in table {$tableName}");
            }
        } catch (Exception $e) {
            error_log("OrderModel::ensureAutoIncrement - Error: " . $e->getMessage());
            // Không throw exception vì đây chỉ là thử sửa, không phải bắt buộc
            // Có thể do không có quyền ALTER TABLE hoặc lỗi khác
        }
    }
    
    /**
     * Xóa bản ghi có id = 0 nếu có (gây conflict với AUTO_INCREMENT)
     */
    private function fixZeroIdRecord(string $tableName, ?string $primaryKeyColumn): void
    {
        if (!$primaryKeyColumn) {
            return;
        }
        
        try {
            // Kiểm tra xem có bản ghi với id = 0 không
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM {$tableName} WHERE {$primaryKeyColumn} = 0");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && (int)$result['count'] > 0) {
                error_log("OrderModel::fixZeroIdRecord - Found " . $result['count'] . " record(s) with {$primaryKeyColumn} = 0 in table {$tableName}. Deleting...");
                
                // Xóa bản ghi có id = 0
                $deleteStmt = $this->pdo->prepare("DELETE FROM {$tableName} WHERE {$primaryKeyColumn} = 0");
                $deleteStmt->execute();
                
                error_log("OrderModel::fixZeroIdRecord - Deleted records with {$primaryKeyColumn} = 0");
                
                // Reset AUTO_INCREMENT về giá trị đúng
                $maxStmt = $this->pdo->query("SELECT MAX({$primaryKeyColumn}) as max_id FROM {$tableName}");
                $maxResult = $maxStmt->fetch(PDO::FETCH_ASSOC);
                $nextId = (int)($maxResult['max_id'] ?? 0) + 1;
                
                $resetStmt = $this->pdo->prepare("ALTER TABLE {$tableName} AUTO_INCREMENT = ?");
                $resetStmt->execute([$nextId]);
                
                error_log("OrderModel::fixZeroIdRecord - Reset AUTO_INCREMENT to {$nextId} for table {$tableName}");
            }
        } catch (Exception $e) {
            error_log("OrderModel::fixZeroIdRecord - Error: " . $e->getMessage());
            // Không throw exception vì đây chỉ là thử sửa, không phải bắt buộc
        }
    }

    // Lấy tổng số đơn hàng
    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM orders_new");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    // Lấy tổng doanh thu (chỉ đơn đã giao)
    public function getTotalRevenue(): float
    {
        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders_new WHERE status = 'delivered'");
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
            
            // Sử dụng bảng orders_new với cột created_at để lọc theo tháng
            try {
                $stmt = $this->pdo->prepare("
                    SELECT COALESCE(SUM(total_amount), 0) AS revenue 
                    FROM orders_new 
                    WHERE status = 'delivered'
                    AND DATE_FORMAT(created_at, '%Y-%m') = :month
                ");
                $stmt->execute([':month' => $month]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $revenue[] = (float)($result['revenue'] ?? 0);
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

