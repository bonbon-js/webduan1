<?php

class CouponModel extends BaseModel
{
    private ?bool $hasDeletedAtColumn = null;
    private ?array $existingColumns = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->table = 'coupons';
    }
    
    /**
     * Kiểm tra xem cột deleted_at có tồn tại không
     * @return bool
     */
    private function hasDeletedAtColumn(): bool
    {
        if ($this->hasDeletedAtColumn === null) {
            try {
                $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table} LIKE 'deleted_at'");
                $this->hasDeletedAtColumn = $stmt->rowCount() > 0;
            } catch (PDOException $e) {
                $this->hasDeletedAtColumn = false;
            }
        }
        return $this->hasDeletedAtColumn;
    }
    
    /**
     * Lấy danh sách các cột có trong bảng
     * @return array
     */
    private function getExistingColumns(): array
    {
        if ($this->existingColumns === null) {
            try {
                $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table}");
                $this->existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
            } catch (PDOException $e) {
                $this->existingColumns = [];
            }
        }
        return $this->existingColumns;
    }
    
    /**
     * Kiểm tra xem cột có tồn tại không
     * @param string $columnName
     * @return bool
     */
    private function hasColumn(string $columnName): bool
    {
        return in_array($columnName, $this->getExistingColumns());
    }

    private function hasUsageTable(): bool
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'coupon_usage'");
            $cache = $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $cache = false;
        }
        return $cache;
    }

    private function getUserUsageCount(int $couponId, int $userId): int
    {
        if (!$this->hasUsageTable() || !$userId) {
            return 0;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS cnt FROM coupon_usage WHERE coupon_id = :cid AND user_id = :uid");
        $stmt->execute(['cid' => $couponId, 'uid' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    public function logUsage(int $couponId, ?int $userId, ?int $orderId, float $discountAmount): void
    {
        if (!$this->hasUsageTable()) {
            return;
        }
        $stmt = $this->pdo->prepare("INSERT INTO coupon_usage (coupon_id, user_id, order_id, discount_amount) VALUES (:cid, :uid, :oid, :amt)");
        $stmt->execute([
            'cid' => $couponId,
            'uid' => $userId,
            'oid' => $orderId,
            'amt' => $discountAmount
        ]);
    }

    /**
     * Validate mã giảm giá chi tiết, trả về thông điệp lỗi
     */
    public function validateCouponDetailed(
        string $code,
        float $orderAmount,
        ?int $userId = null,
        array $productIds = [],
        array $categoryIds = [],
        bool $isNewCustomer = false,
        bool $hasOtherCoupon = false,
        bool $hasSaleItem = false,
        bool $isVipToday = false
    ): array
    {
        $coupon = $this->getByCode($code, false);

        if (!$coupon) {
            return ['ok' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã bị xóa.', 'coupon' => null, 'discount' => null];
        }

        $status = $this->calculateStatus($coupon);
        if ($status === 'inactive') {
            return ['ok' => false, 'message' => 'Mã đã ngừng hoạt động.', 'coupon' => null, 'discount' => null];
        }
        if ($status === 'expired') {
            return ['ok' => false, 'message' => 'Mã đã hết hạn.', 'coupon' => null, 'discount' => null];
        }
        if ($status === 'out_of_stock') {
            return ['ok' => false, 'message' => 'Mã đã hết lượt sử dụng.', 'coupon' => null, 'discount' => null];
        }
        if ($status === 'pending') {
            return ['ok' => false, 'message' => 'Mã chưa đến thời gian áp dụng.', 'coupon' => null, 'discount' => null];
        }

        if ($coupon['min_order_amount'] > 0 && $orderAmount < (float)$coupon['min_order_amount']) {
            return ['ok' => false, 'message' => 'Giá trị đơn hàng chưa đạt mức tối thiểu.', 'coupon' => null, 'discount' => null];
        }

        // Không kèm mã khác
        if (!empty($coupon['exclude_other_coupons']) && $hasOtherCoupon) {
            return ['ok' => false, 'message' => 'Mã này không được dùng cùng mã khác.', 'coupon' => null, 'discount' => null];
        }

        // Yêu cầu đăng nhập
        if (!empty($coupon['require_login']) && !$userId) {
            return ['ok' => false, 'message' => 'Mã này yêu cầu bạn đăng nhập.', 'coupon' => null, 'discount' => null];
        }

        // Giới hạn mỗi khách hàng
        if (!empty($coupon['per_user_limit'])) {
            if (!$userId) {
                return ['ok' => false, 'message' => 'Vui lòng đăng nhập để áp dụng mã (giới hạn theo khách).', 'coupon' => null, 'discount' => null];
            }
            $usedByUser = $this->getUserUsageCount((int)$coupon['coupon_id'], $userId);
            if ($usedByUser >= (int)$coupon['per_user_limit']) {
                return ['ok' => false, 'message' => 'Bạn đã dùng hết số lượt cho mã này.', 'coupon' => null, 'discount' => null];
            }
        }

        // Khách mới
        if (!empty($coupon['new_customer_only']) && !$isNewCustomer) {
            return ['ok' => false, 'message' => 'Mã chỉ áp dụng cho khách hàng mới.', 'coupon' => null, 'discount' => null];
        }

        // Không áp dụng cho sản phẩm đang giảm giá (nếu có cờ)
        if (!empty($coupon['exclude_sale_items']) && $hasSaleItem) {
            return ['ok' => false, 'message' => 'Không áp dụng cho sản phẩm đang giảm giá.', 'coupon' => null, 'discount' => null];
        }

        // Nhóm khách hàng VIP: yêu cầu VIP và chỉ dùng 1 lần/người
        if (!empty($coupon['customer_group']) && $coupon['customer_group'] === 'vip_today') {
            if (!$userId) {
                return ['ok' => false, 'message' => 'Mã VIP yêu cầu đăng nhập.', 'coupon' => null, 'discount' => null];
            }
            if (!$isVipToday) {
                return ['ok' => false, 'message' => 'Mã chỉ áp dụng cho khách VIP (đơn >= 2.000.000đ đã giao thành công).', 'coupon' => null, 'discount' => null];
            }
            $usedByUserVip = $this->getUserUsageCount((int)$coupon['coupon_id'], $userId);
            if ($usedByUserVip >= 1) {
                return ['ok' => false, 'message' => 'Mã VIP chỉ dùng 1 lần cho mỗi khách hàng.', 'coupon' => null, 'discount' => null];
            }
        }

        $discount = $this->calculateDiscount($coupon, $orderAmount);

        return ['ok' => true, 'message' => null, 'coupon' => $coupon, 'discount' => $discount];
    }

    public function validateCoupon(string $code, float $orderAmount): ?array
    {
        $result = $this->validateCouponDetailed($code, $orderAmount);
        return $result['ok'] ? $result['coupon'] : null;
    }

    /**
     * Tính toán số tiền giảm giá
     * @param array $coupon
     * @param float $orderAmount
     * @return array ['discount_amount' => float, 'final_amount' => float]
     */
    public function calculateDiscount(array $coupon, float $orderAmount): array
    {
        $discountAmount = 0;
        
        if ($coupon['discount_type'] === 'percentage') {
            // Giảm theo phần trăm
            $discountAmount = ($orderAmount * (float)$coupon['discount_value']) / 100;
            
            // Áp dụng giới hạn tối đa nếu có
            if ($coupon['max_discount_amount'] !== null) {
                $discountAmount = min($discountAmount, (float)$coupon['max_discount_amount']);
            }
        } else {
            // Giảm cố định
            $discountAmount = (float)$coupon['discount_value'];
        }
        
        // Đảm bảo không giảm quá tổng tiền đơn hàng
        $discountAmount = min($discountAmount, $orderAmount);
        
        $finalAmount = max(0, $orderAmount - $discountAmount);
        
        return [
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ];
    }

    /**
     * Tăng số lần sử dụng mã giảm giá
     * @param int $couponId
     * @param int $quantity Số lượng đơn hàng sử dụng mã (mặc định 1)
     */
    public function incrementUsage(int $couponId, int $quantity = 1): void
    {
        $sql = "UPDATE {$this->table} 
                SET used_count = used_count + :quantity 
                WHERE coupon_id = :coupon_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'coupon_id' => $couponId,
            'quantity' => $quantity
        ]);
    }

    /**
     * Tính trạng thái mã giảm giá
     * @param array $coupon
     * @return string 'active', 'expired', 'out_of_stock', 'inactive'
     */
    private function calculateStatus(array $coupon): string
    {
        // Nếu status = inactive, trả về inactive
        if ($coupon['status'] === 'inactive') {
            return 'inactive';
        }
        
        // Sử dụng timezone Việt Nam
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $now = date('Y-m-d H:i:s');
        
        // Kiểm tra hết hạn (ưu tiên kiểm tra hết hạn trước)
        if ($now > $coupon['end_date']) {
            return 'expired';
        }
        
        // Kiểm tra hết lượt sử dụng (hết mã)
        if ($coupon['usage_limit'] !== null && $coupon['used_count'] >= $coupon['usage_limit']) {
            return 'out_of_stock';
        }
        
        // Kiểm tra chưa đến thời gian bắt đầu
        if ($now < $coupon['start_date']) {
            return 'pending'; // Chưa bắt đầu
        }
        
        return 'active';
    }

    /**
     * Lấy tất cả mã giảm giá (cho admin)
     * @param string|null $keyword
     * @param string|null $statusFilter 'active', 'expired', 'out_of_stock', 'inactive'
     * @param string|null $discountTypeFilter 'percentage', 'fixed'
     * @return array
     */
    public function getAll(
        ?string $keyword = null,
        ?string $statusFilter = null,
        ?string $discountTypeFilter = null,
        ?string $createdFrom = null,
        ?string $createdTo = null,
        bool $includeDeleted = false
    ): array
    {
        // Kiểm tra xem cột deleted_at có tồn tại không
        $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table} LIKE 'deleted_at'");
        $hasDeletedAt = $stmt->rowCount() > 0;
        
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Lọc bỏ các mã đã bị xóa mềm (trừ khi yêu cầu hiển thị)
        if ($hasDeletedAt && !$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        if ($keyword) {
            $sql .= " AND (code LIKE :keyword OR name LIKE :keyword OR description LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }
        
        if ($discountTypeFilter) {
            $sql .= " AND discount_type = :discount_type";
            $params['discount_type'] = $discountTypeFilter;
        }

        // Lọc theo ngày tạo nếu có cột created_at
        if ($this->hasColumn('created_at')) {
            if ($createdFrom) {
                $sql .= " AND created_at >= :created_from";
                $params['created_from'] = $createdFrom . ' 00:00:00';
            }
            if ($createdTo) {
                $sql .= " AND created_at <= :created_to";
                $params['created_to'] = $createdTo . ' 23:59:59';
            }
        }
        
        // Xây dựng ORDER BY clause dựa trên các cột có sẵn
        $orderByParts = [];
        
        // Kiểm tra và thêm CASE statement nếu các cột cần thiết tồn tại
        if ($this->hasColumn('status') || $this->hasColumn('end_date') || 
            ($this->hasColumn('usage_limit') && $this->hasColumn('used_count'))) {
            
            $caseWhen = "CASE";
            
            if ($this->hasColumn('status')) {
                $caseWhen .= " WHEN status = 'inactive' THEN 3";
            }
            
            if ($this->hasColumn('end_date')) {
                $caseWhen .= " WHEN NOW() > end_date THEN 2";
            }
            
            if ($this->hasColumn('usage_limit') && $this->hasColumn('used_count')) {
                $caseWhen .= " WHEN usage_limit IS NOT NULL AND used_count >= usage_limit THEN 2";
            }
            
            $caseWhen .= " ELSE 1 END";
            $orderByParts[] = $caseWhen;
        }
        
        // Thêm created_at nếu có
        if ($this->hasColumn('created_at')) {
            $orderByParts[] = "created_at DESC";
        } else if ($this->hasColumn('coupon_id')) {
            // Fallback: sắp xếp theo ID nếu không có created_at
            $orderByParts[] = "coupon_id DESC";
        }
        
        if (!empty($orderByParts)) {
            $sql .= " ORDER BY " . implode(", ", $orderByParts);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Thêm trạng thái tính toán vào mỗi coupon
        foreach ($coupons as &$coupon) {
            $coupon['calculated_status'] = $this->calculateStatus($coupon);
        }
        
        // Lọc theo trạng thái tính toán nếu có
        if ($statusFilter) {
            $coupons = array_filter($coupons, function($coupon) use ($statusFilter) {
                return $coupon['calculated_status'] === $statusFilter;
            });
            // Re-index array sau khi filter
            $coupons = array_values($coupons);
        }
        
        return $coupons;
    }

    /**
     * Lấy danh sách mã giảm giá khả dụng cho khách hàng (dựa trên tổng tiền đơn hàng)
     * Tự động ẩn mã hết hạn và hết mã
     * @param float $orderAmount
     * @return array
     */
    public function getAvailableCoupons(float $orderAmount): array
    {
        // Sử dụng timezone Việt Nam
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $now = date('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active'";
        
        if ($this->hasDeletedAtColumn()) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        // Hiển thị mã chưa hết hạn (kể cả chưa đến thời gian bắt đầu)
        // Chỉ loại bỏ mã đã hết hạn (end_date < now)
        $sql .= " AND end_date >= :now";
        
        // Nếu orderAmount = 0, chỉ lấy mã không yêu cầu đơn tối thiểu
        if ($orderAmount > 0) {
            $sql .= " AND (min_order_amount IS NULL OR min_order_amount = 0 OR min_order_amount <= :order_amount)";
        } else {
            $sql .= " AND (min_order_amount IS NULL OR min_order_amount = 0)";
        }
        
        $sql .= " AND (usage_limit IS NULL OR used_count < usage_limit)
                ORDER BY discount_value DESC, created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'now' => $now,
            'order_amount' => $orderAmount
        ]);
        
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dữ liệu
        $formatted = [];
        foreach ($coupons as $coupon) {
            $discount = $this->calculateDiscount($coupon, $orderAmount);
            $formatted[] = [
                'id' => $coupon['coupon_id'],
                'code' => $coupon['code'],
                'name' => $coupon['name'],
                'description' => $coupon['description'],
                'discount_type' => $coupon['discount_type'],
                'discount_value' => $coupon['discount_value'],
                'min_order_amount' => $coupon['min_order_amount'],
                'max_discount_amount' => $coupon['max_discount_amount'],
                'discount_amount' => $discount['discount_amount'],
                'final_amount' => $discount['final_amount'],
            ];
        }
        
        return $formatted;
    }

    /**
     * Lấy một mã giảm giá theo ID
     * @param int $couponId
     * @param bool $includeDeleted
     * @return array|null
     */
    public function getById(int $couponId, bool $includeDeleted = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE coupon_id = :coupon_id";
        
        // Lọc bỏ các mã đã bị xóa mềm (trừ khi yêu cầu hiển thị)
        if ($this->hasDeletedAtColumn() && !$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['coupon_id' => $couponId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    /**
     * Lấy một mã giảm giá theo code
     * @param string $code
     * @param bool $includeDeleted
     * @return array|null
     */
    public function getByCode(string $code, bool $includeDeleted = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code";
        
        // Lọc bỏ các mã đã bị xóa mềm (trừ khi yêu cầu hiển thị)
        if ($this->hasDeletedAtColumn() && !$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => strtoupper(trim($code))]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Tạo mã giảm giá mới
     * @param array $data
     * @return int coupon_id
     */
    public function create(array $data): int
    {
        // Loại bỏ PRIMARY KEY khỏi data
        $data = $this->removePrimaryKeyFromData($data, $this->table);
        
        // Nếu là giảm giá cố định, không cho phép max_discount_amount
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_amount'] = null;
        }
        
        $sql = "INSERT INTO {$this->table} 
                (code, name, description, discount_type, discount_value, min_order_amount, 
                 max_discount_amount, start_date, end_date, usage_limit, per_user_limit,
                 apply_scope, apply_product_ids, apply_category_ids,
                 require_login, new_customer_only, exclude_sale_items, exclude_other_coupons,
                 customer_group, return_on_refund, status) 
                VALUES 
                (:code, :name, :description, :discount_type, :discount_value, :min_order_amount, 
                 :max_discount_amount, :start_date, :end_date, :usage_limit, :per_user_limit,
                 :apply_scope, :apply_product_ids, :apply_category_ids,
                 :require_login, :new_customer_only, :exclude_sale_items, :exclude_other_coupons,
                 :customer_group, :return_on_refund, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_order_amount' => $data['min_order_amount'] ?? 0,
            'max_discount_amount' => $data['max_discount_amount'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'usage_limit' => $data['usage_limit'] ?? null,
            'per_user_limit' => $data['per_user_limit'] ?? null,
            'apply_scope' => $data['apply_scope'] ?? 'all',
            'apply_product_ids' => $data['apply_product_ids'] ?? null,
            'apply_category_ids' => $data['apply_category_ids'] ?? null,
            'require_login' => $data['require_login'] ?? 0,
            'new_customer_only' => $data['new_customer_only'] ?? 0,
            'exclude_sale_items' => $data['exclude_sale_items'] ?? 0,
            'exclude_other_coupons' => $data['exclude_other_coupons'] ?? 0,
            'customer_group' => $data['customer_group'] ?? null,
            'return_on_refund' => $data['return_on_refund'] ?? 0,
            'status' => $data['status'] ?? 'active',
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Cập nhật mã giảm giá
     * @param int $couponId
     * @param array $data
     */
    public function update(int $couponId, array $data): void
    {
        // Nếu là giảm giá cố định, không cho phép max_discount_amount
        if ($data['discount_type'] === 'fixed') {
            $data['max_discount_amount'] = null;
        }
        
        $sql = "UPDATE {$this->table} SET 
                code = :code,
                name = :name,
                description = :description,
                discount_type = :discount_type,
                discount_value = :discount_value,
                min_order_amount = :min_order_amount,
                max_discount_amount = :max_discount_amount,
                start_date = :start_date,
                end_date = :end_date,
                usage_limit = :usage_limit,
                per_user_limit = :per_user_limit,
                apply_scope = :apply_scope,
                apply_product_ids = :apply_product_ids,
                apply_category_ids = :apply_category_ids,
                require_login = :require_login,
                new_customer_only = :new_customer_only,
                exclude_sale_items = :exclude_sale_items,
                exclude_other_coupons = :exclude_other_coupons,
                customer_group = :customer_group,
                return_on_refund = :return_on_refund,
                status = :status
                WHERE coupon_id = :coupon_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'coupon_id' => $couponId,
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_order_amount' => $data['min_order_amount'] ?? 0,
            'max_discount_amount' => $data['max_discount_amount'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'usage_limit' => $data['usage_limit'] ?? null,
            'per_user_limit' => $data['per_user_limit'] ?? null,
            'apply_scope' => $data['apply_scope'] ?? 'all',
            'apply_product_ids' => $data['apply_product_ids'] ?? null,
            'apply_category_ids' => $data['apply_category_ids'] ?? null,
            'require_login' => $data['require_login'] ?? 0,
            'new_customer_only' => $data['new_customer_only'] ?? 0,
            'exclude_sale_items' => $data['exclude_sale_items'] ?? 0,
            'exclude_other_coupons' => $data['exclude_other_coupons'] ?? 0,
            'customer_group' => $data['customer_group'] ?? null,
            'return_on_refund' => $data['return_on_refund'] ?? 0,
            'status' => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Cập nhật trạng thái nhanh
     */
    public function updateStatusOnly(int $couponId, string $status): void
    {
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET status = :status WHERE coupon_id = :id");
        $stmt->execute([
            'status' => $status,
            'id' => $couponId,
        ]);
    }

    /**
     * Xóa mềm mã giảm giá (soft delete)
     * @param int $couponId
     */
    public function delete(int $couponId): void
    {
        if ($this->hasDeletedAtColumn()) {
            $sql = "UPDATE {$this->table} 
                    SET deleted_at = NOW() 
                    WHERE coupon_id = :coupon_id 
                      AND deleted_at IS NULL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['coupon_id' => $couponId]);
        } else {
            // Nếu chưa có cột deleted_at, xóa vĩnh viễn
            $this->forceDelete($couponId);
        }
    }
    
    /**
     * Khôi phục mã giảm giá từ thùng rác
     * @param int $couponId
     */
    public function restore(int $couponId): void
    {
        if (!$this->hasDeletedAtColumn()) {
            return; // Không thể khôi phục nếu chưa có cột deleted_at
        }
        
        $sql = "UPDATE {$this->table} 
                SET deleted_at = NULL 
                WHERE coupon_id = :coupon_id 
                  AND deleted_at IS NOT NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['coupon_id' => $couponId]);
    }
    
    /**
     * Xóa vĩnh viễn mã giảm giá (hard delete)
     * @param int $couponId
     */
    public function forceDelete(int $couponId): void
    {
        $sql = "DELETE FROM {$this->table} WHERE coupon_id = :coupon_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['coupon_id' => $couponId]);
    }
    
    /**
     * Lấy danh sách mã giảm giá đã bị xóa (thùng rác)
     * @return array
     */
    public function getDeleted(): array
    {
        if (!$this->hasDeletedAtColumn()) {
            return []; // Trả về mảng rỗng nếu chưa có cột deleted_at
        }
        
        $sql = "SELECT * FROM {$this->table} 
                WHERE deleted_at IS NOT NULL
                ORDER BY deleted_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Thêm trạng thái tính toán vào mỗi coupon
        foreach ($coupons as &$coupon) {
            $coupon['calculated_status'] = $this->calculateStatus($coupon);
        }
        
        return $coupons;
    }
}

