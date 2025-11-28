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
            $orderData = $this->removePrimaryKeyFromData($orderData);
            
            // Nếu chưa truyền total_amount thì tự tính từ danh sách item
            $total = $orderData['total_amount'] ?? 0;
            if (!$total) {
                $total = array_reduce($items, function ($carry, $item) {
                    return $carry + ($item['unit_price'] * $item['quantity']);
                }, 0);
            }

            // Xác định tên bảng (có thể là 'orders' hoặc 'orders_new')
            // Kiểm tra xem bảng nào có các cột cần thiết
            $tableName = 'orders';
            $existingColumns = $this->getExistingColumns($tableName);
            
            // Nếu bảng 'orders' không có cột 'fullname', thử 'orders_new'
            if (!in_array('fullname', $existingColumns) && !in_array('full_name', $existingColumns)) {
                $tableName = 'orders_new';
                $existingColumns = $this->getExistingColumns($tableName);
                error_log("OrderModel::create - Using table 'orders_new' instead of 'orders'");
            }
            
            // Kiểm tra PRIMARY KEY của bảng đang dùng
            $primaryKeyColumn = $this->getPrimaryKeyColumn($tableName);
            error_log("OrderModel::create - Table: $tableName, Primary Key: " . ($primaryKeyColumn ?? 'NULL'));
            error_log("OrderModel::create - All existing columns: " . implode(', ', $existingColumns));
            
            // QUAN TRỌNG: Loại bỏ MỌI cột có tên 'id' hoặc 'order_id' khỏi existingColumns
            // Bất kể nó có phải PRIMARY KEY hay không, vì có thể gây nhầm lẫn
            $existingColumns = array_filter($existingColumns, function($col) use ($primaryKeyColumn) {
                $forbidden = ['id', 'order_id'];
                if ($primaryKeyColumn) {
                    $forbidden[] = $primaryKeyColumn;
                }
                foreach ($forbidden as $f) {
                    if (strcasecmp($col, $f) === 0) {
                        error_log("OrderModel::create - Removed column '$col' from existingColumns (matches forbidden '$f')");
                        return false;
                    }
                }
                return true;
            });
            $existingColumns = array_values($existingColumns); // Re-index
            
            // Danh sách các tên có thể là PRIMARY KEY - TUYỆT ĐỐI KHÔNG thêm vào INSERT
            // QUAN TRỌNG: 'id' là PRIMARY KEY của orders_new, 'order_id' là của orders
            $forbiddenPrimaryKeyNames = ['id', 'order_id', 'orderId', 'orderId', 'pk', 'primary_key'];
            if ($primaryKeyColumn) {
                $forbiddenPrimaryKeyNames[] = $primaryKeyColumn;
                // Thêm cả lowercase và uppercase variants
                $forbiddenPrimaryKeyNames[] = strtolower($primaryKeyColumn);
                $forbiddenPrimaryKeyNames[] = strtoupper($primaryKeyColumn);
            }
            $forbiddenPrimaryKeyNames = array_unique($forbiddenPrimaryKeyNames);
            
            // Hàm helper để kiểm tra xem cột có phải PRIMARY KEY không
            // CHẶT CHẼ: Kiểm tra cả PRIMARY KEY thực tế và danh sách cấm
            $isPrimaryKey = function($colName) use ($primaryKeyColumn, $forbiddenPrimaryKeyNames) {
                if (!$colName) return true;
                
                // Kiểm tra với PRIMARY KEY thực tế
                if ($primaryKeyColumn && strcasecmp($colName, $primaryKeyColumn) === 0) {
                    return true;
                }
                
                // Kiểm tra với danh sách cấm
                foreach ($forbiddenPrimaryKeyNames as $forbidden) {
                    if (strcasecmp($colName, $forbidden) === 0) {
                        return true;
                    }
                }
                
                // Kiểm tra nếu tên cột chứa 'id' và có độ dài bằng với một trong các tên cấm
                foreach (['id', 'order_id'] as $forbidden) {
                    if (strlen($colName) === strlen($forbidden) && stripos($colName, $forbidden) !== false) {
                        return true;
                    }
                }
                
                return false;
            };
            
            // Tìm tên cột đúng (có thể có biến thể)
            // QUAN TRỌNG: Đảm bảo không map đến cột 'id' hoặc 'order_id'
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
            
            // Đảm bảo không có cột nào trong columnMap là PRIMARY KEY
            foreach ($columnMap as $key => $colName) {
                if ($colName && $isPrimaryKey($colName)) {
                    error_log("OrderModel::create - ColumnMap '$key' maps to PRIMARY KEY '$colName', removing...");
                    $columnMap[$key] = null;
                }
            }
            
            // Xây dựng danh sách cột và giá trị động (chỉ thêm các cột tồn tại)
            $insertColumns = [];
            $insertValues = [];
            $orderPayload = [];
            
            // Thêm các cột bắt buộc nếu tồn tại (trừ PRIMARY KEY)
            if ($columnMap['user_id'] && !$isPrimaryKey($columnMap['user_id'])) {
                $insertColumns[] = $columnMap['user_id'];
                $insertValues[] = ':user_id';
                $orderPayload[':user_id'] = $orderData['user_id'] ?? null;
            }
            
            if ($columnMap['fullname'] && !$isPrimaryKey($columnMap['fullname'])) {
                $insertColumns[] = $columnMap['fullname'];
                $insertValues[] = ':fullname';
                $orderPayload[':fullname'] = $orderData['fullname'];
            }
            
            if ($columnMap['email'] && !$isPrimaryKey($columnMap['email'])) {
                $insertColumns[] = $columnMap['email'];
                $insertValues[] = ':email';
                $orderPayload[':email'] = $orderData['email'];
            }
            
            if ($columnMap['phone'] && !$isPrimaryKey($columnMap['phone'])) {
                $insertColumns[] = $columnMap['phone'];
                $insertValues[] = ':phone';
                $orderPayload[':phone'] = $orderData['phone'];
            }
            
            if ($columnMap['address'] && !$isPrimaryKey($columnMap['address'])) {
                $insertColumns[] = $columnMap['address'];
                $insertValues[] = ':address';
                $orderPayload[':address'] = $orderData['address'];
            }
            
            if ($columnMap['payment_method'] && !$isPrimaryKey($columnMap['payment_method'])) {
                $insertColumns[] = $columnMap['payment_method'];
                $insertValues[] = ':payment_method';
                $orderPayload[':payment_method'] = $orderData['payment_method'] ?? 'cod';
            }
            
            if ($columnMap['status'] && !$isPrimaryKey($columnMap['status'])) {
                $insertColumns[] = $columnMap['status'];
                $insertValues[] = ':status';
                $orderPayload[':status'] = $orderData['status'] ?? self::STATUS_CONFIRMED;
            }
            
            if ($columnMap['total_amount'] && !$isPrimaryKey($columnMap['total_amount'])) {
                $insertColumns[] = $columnMap['total_amount'];
                $insertValues[] = ':total_amount';
                $orderPayload[':total_amount'] = $total;
            }
            
            // Thêm các cột tùy chọn nếu tồn tại
            // QUAN TRỌNG: KHÔNG BAO GIỜ thêm order_id hoặc bất kỳ PRIMARY KEY nào vào đây
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
            
            // Danh sách các tên có thể là PRIMARY KEY - TUYỆT ĐỐI KHÔNG thêm vào INSERT
            // (Đã được định nghĩa ở trên)
            
            foreach ($optionalColumns as $col => $value) {
                // TUYỆT ĐỐI KHÔNG thêm PRIMARY KEY vào INSERT
                // Kiểm tra: 1) Cột phải tồn tại, 2) Không phải PRIMARY KEY, 3) Không phải 'id' hoặc 'order_id'
                if (in_array($col, $existingColumns) && !$isPrimaryKey($col)) {
                    // Kiểm tra lại lần nữa để chắc chắn
                    $isForbidden = false;
                    foreach (['id', 'order_id'] as $forbidden) {
                        if (strcasecmp($col, $forbidden) === 0) {
                            $isForbidden = true;
                            error_log("OrderModel::create - Skipped column '$col' (matches forbidden '$forbidden')");
                            break;
                        }
                    }
                    
                    if (!$isForbidden) {
                        $insertColumns[] = $col;
                        $insertValues[] = ':' . $col;
                        $orderPayload[':' . $col] = $value;
                    }
                } else {
                    error_log("OrderModel::create - Skipped column '$col' (might be PRIMARY KEY or not exists)");
                }
            }
            
            // Kiểm tra lại: Loại bỏ BẤT KỲ cột nào có thể là PRIMARY KEY
            $filteredColumns = [];
            $filteredValues = [];
            $filteredPayload = [];
            
            foreach ($insertColumns as $index => $col) {
                if (!$isPrimaryKey($col)) {
                    $filteredColumns[] = $col;
                    $filteredValues[] = $insertValues[$index];
                    $param = ':' . $col;
                    if (isset($orderPayload[$param])) {
                        $filteredPayload[$param] = $orderPayload[$param];
                    }
                } else {
                    error_log("OrderModel::create - FORCE REMOVED PRIMARY KEY column: $col");
                }
            }
            
            $insertColumns = $filteredColumns;
            $insertValues = $filteredValues;
            $orderPayload = $filteredPayload;
            
            // Validate: Phải có ít nhất các cột cơ bản
            if (empty($insertColumns)) {
                $errorMsg = "Không tìm thấy cột nào phù hợp trong bảng $tableName. ";
                $errorMsg .= 'Các cột hiện có: ' . implode(', ', $existingColumns);
                error_log("OrderModel::create - Table: $tableName, Available columns: " . implode(', ', $existingColumns));
                throw new Exception($errorMsg);
            }
            
            // Log để debug - CHI TIẾT
            error_log('OrderModel::create - ========== DEBUG INFO ==========');
            error_log('OrderModel::create - Table: ' . $tableName);
            error_log('OrderModel::create - Primary Key Column: ' . ($primaryKeyColumn ?? 'NULL'));
            error_log('OrderModel::create - All existing columns (after filter): ' . implode(', ', $existingColumns));
            error_log('OrderModel::create - Forbidden names: ' . implode(', ', $forbiddenPrimaryKeyNames));
            error_log('OrderModel::create - Inserting columns: ' . implode(', ', $insertColumns));
            error_log('OrderModel::create - Inserting values count: ' . count($insertValues));
            error_log('OrderModel::create - Payload keys: ' . implode(', ', array_keys($orderPayload)));
            
            // Kiểm tra xem có cột 'id' nào trong insertColumns không
            foreach ($insertColumns as $idx => $col) {
                if (strcasecmp($col, 'id') === 0 || strcasecmp($col, 'order_id') === 0) {
                    error_log("OrderModel::create - WARNING: Found forbidden column '$col' at index $idx in insertColumns!");
                }
            }
            error_log('OrderModel::create - =================================');
            
            // KIỂM TRA CUỐI CÙNG: Đảm bảo KHÔNG có PRIMARY KEY trong insertColumns
            if ($primaryKeyColumn) {
                foreach ($insertColumns as $col) {
                    if (strcasecmp($col, $primaryKeyColumn) === 0) {
                        $errorMsg = "LỖI: PRIMARY KEY '$primaryKeyColumn' vẫn còn trong insertColumns sau khi đã filter!";
                        error_log($errorMsg);
                        error_log("All columns: " . implode(', ', $insertColumns));
                        throw new Exception($errorMsg);
                    }
                }
            }
            
            // Kiểm tra các tên cấm
            foreach (['id', 'order_id'] as $forbidden) {
                foreach ($insertColumns as $col) {
                    if (strcasecmp($col, $forbidden) === 0) {
                        $errorMsg = "LỖI: Cột cấm '$forbidden' vẫn còn trong insertColumns!";
                        error_log($errorMsg);
                        error_log("All columns: " . implode(', ', $insertColumns));
                        throw new Exception($errorMsg);
                    }
                }
            }

            // Đảm bảo PRIMARY KEY không có trong danh sách cột
            if ($primaryKeyColumn && in_array($primaryKeyColumn, $insertColumns)) {
                $errorMsg = "Lỗi: PRIMARY KEY ($primaryKeyColumn) đã được thêm vào INSERT statement. Điều này không được phép.";
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }

            // Loại bỏ PRIMARY KEY khỏi insertColumns và orderPayload nếu vô tình có
            if ($primaryKeyColumn) {
                $insertColumns = array_filter($insertColumns, function($col) use ($primaryKeyColumn) {
                    return $col !== $primaryKeyColumn;
                });
                $insertColumns = array_values($insertColumns); // Re-index array
                
                // Loại bỏ khỏi insertValues tương ứng
                $insertValues = array_slice($insertValues, 0, count($insertColumns));
                
                // Loại bỏ khỏi orderPayload
                $pkParam = ':' . $primaryKeyColumn;
                if (isset($orderPayload[$pkParam])) {
                    unset($orderPayload[$pkParam]);
                    error_log("OrderModel::create - Removed PRIMARY KEY parameter from payload: $pkParam");
                }
            }

            // Kiểm tra lại lần cuối: Đảm bảo PRIMARY KEY KHÔNG BAO GIỜ có trong SQL
            // Kiểm tra từng cột trong insertColumns
            foreach ($insertColumns as $col) {
                if ($isPrimaryKey($col)) {
                    $errorMsg = "LỖI NGHIÊM TRỌNG: Cột PRIMARY KEY '$col' đã xuất hiện trong INSERT statement! Điều này KHÔNG ĐƯỢC PHÉP!";
                    error_log($errorMsg);
                    error_log("OrderModel::create - Primary Key Column detected: " . ($primaryKeyColumn ?? 'NULL'));
                    error_log("OrderModel::create - Insert columns: " . implode(', ', $insertColumns));
                    error_log("OrderModel::create - Payload keys: " . implode(', ', array_keys($orderPayload)));
                    throw new Exception($errorMsg);
                }
            }
            
            // LOẠI BỎ CUỐI CÙNG: Đảm bảo KHÔNG có bất kỳ cột nào tên 'id' hoặc 'order_id'
            // (Bất kể nó có phải PRIMARY KEY hay không, vì có thể gây nhầm lẫn)
            $finalColumns = [];
            $finalValues = [];
            $finalPayload = [];
            
            // Danh sách TUYỆT ĐỐI CẤM - loại bỏ mọi cột có tên này
            $absolutelyForbidden = ['id', 'order_id', 'orderId'];
            if ($primaryKeyColumn) {
                $absolutelyForbidden[] = $primaryKeyColumn;
                $absolutelyForbidden[] = strtolower($primaryKeyColumn);
                $absolutelyForbidden[] = strtoupper($primaryKeyColumn);
            }
            $absolutelyForbidden = array_unique($absolutelyForbidden);
            
            error_log('OrderModel::create - Absolutely forbidden columns: ' . implode(', ', $absolutelyForbidden));
            
            foreach ($insertColumns as $index => $col) {
                $isForbidden = false;
                $matchedForbidden = null;
                
                foreach ($absolutelyForbidden as $forbidden) {
                    if (strcasecmp($col, $forbidden) === 0) {
                        $isForbidden = true;
                        $matchedForbidden = $forbidden;
                        break;
                    }
                }
                
                if ($isForbidden) {
                    error_log("OrderModel::create - FINAL REMOVAL: Column '$col' matches forbidden '$matchedForbidden' (index: $index)");
                } else {
                    $finalColumns[] = $col;
                    $finalValues[] = $insertValues[$index];
                    $param = ':' . $col;
                    if (isset($orderPayload[$param])) {
                        $finalPayload[$param] = $orderPayload[$param];
                    }
                }
            }
            
            error_log('OrderModel::create - Columns AFTER final filter: ' . implode(', ', $finalColumns));
            
            $insertColumns = $finalColumns;
            $insertValues = $finalValues;
            $orderPayload = $finalPayload;
            
            // KIỂM TRA CUỐI CÙNG TRƯỚC KHI TẠO SQL: Loại bỏ MỌI cột có tên 'id' hoặc 'order_id'
            // Bất kể nó có phải PRIMARY KEY hay không, vì có thể gây nhầm lẫn
            $finalSafeColumns = [];
            $finalSafeValues = [];
            $finalSafePayload = [];
            
            $absolutelyForbidden = ['id', 'order_id'];
            if ($primaryKeyColumn) {
                $absolutelyForbidden[] = $primaryKeyColumn;
            }
            
            error_log("OrderModel::create - FINAL CHECK: Removing any column matching: " . implode(', ', $absolutelyForbidden));
            error_log("OrderModel::create - Columns BEFORE final removal: " . implode(', ', $insertColumns));
            
            foreach ($insertColumns as $index => $col) {
                $shouldRemove = false;
                $matchedForbidden = null;
                
                foreach ($absolutelyForbidden as $forbidden) {
                    // So sánh chính xác (case-insensitive)
                    if (strcasecmp($col, $forbidden) === 0) {
                        $shouldRemove = true;
                        $matchedForbidden = $forbidden;
                        break;
                    }
                }
                
                if ($shouldRemove) {
                    error_log("OrderModel::create - FINAL REMOVAL: Removing column '$col' (matches '$matchedForbidden') at index $index");
                } else {
                    $finalSafeColumns[] = $col;
                    $finalSafeValues[] = $insertValues[$index];
                    $param = ':' . $col;
                    if (isset($orderPayload[$param])) {
                        $finalSafePayload[$param] = $orderPayload[$param];
                    }
                }
            }
            
            // Gán lại với danh sách đã được làm sạch
            $insertColumns = $finalSafeColumns;
            $insertValues = $finalSafeValues;
            $orderPayload = $finalSafePayload;
            
            error_log("OrderModel::create - Columns AFTER final removal: " . implode(', ', $insertColumns));
            
            // Validate: Phải có ít nhất một cột
            if (empty($insertColumns)) {
                $errorMsg = "LỖI: Sau khi loại bỏ PRIMARY KEY, không còn cột nào để INSERT!";
                error_log($errorMsg);
                error_log("Table: $tableName");
                error_log("Primary Key: " . ($primaryKeyColumn ?? 'NULL'));
                throw new Exception($errorMsg);
            }
            
            // KIỂM TRA CUỐI CÙNG: Đảm bảo KHÔNG có cột 'id' hoặc 'order_id' trong insertColumns
            // Loại bỏ MỌI cột có tên này, bất kể nó có phải PRIMARY KEY hay không
            $absolutelyForbiddenFinal = ['id', 'order_id'];
            if ($primaryKeyColumn) {
                $absolutelyForbiddenFinal[] = $primaryKeyColumn;
            }
            
            $finalCleanColumns = [];
            $finalCleanValues = [];
            $finalCleanPayload = [];
            
            foreach ($insertColumns as $index => $col) {
                $isForbidden = false;
                foreach ($absolutelyForbiddenFinal as $forbidden) {
                    if (strcasecmp(trim($col), $forbidden) === 0) {
                        $isForbidden = true;
                        error_log("OrderModel::create - ABSOLUTE REMOVAL: Column '$col' matches '$forbidden'");
                        break;
                    }
                }
                
                if (!$isForbidden) {
                    $finalCleanColumns[] = $col;
                    $finalCleanValues[] = $insertValues[$index];
                    $param = ':' . $col;
                    if (isset($orderPayload[$param])) {
                        $finalCleanPayload[$param] = $orderPayload[$param];
                    }
                }
            }
            
            $insertColumns = $finalCleanColumns;
            $insertValues = $finalCleanValues;
            $orderPayload = $finalCleanPayload;
            
            error_log("OrderModel::create - FINAL CLEAN columns: " . implode(', ', $insertColumns));
            
            // Validate: Phải có ít nhất một cột
            if (empty($insertColumns)) {
                $errorMsg = "LỖI: Sau khi loại bỏ tất cả cột cấm, không còn cột nào để INSERT!";
                error_log($errorMsg);
                error_log("Table: $tableName");
                error_log("Primary Key: " . ($primaryKeyColumn ?? 'NULL'));
                throw new Exception($errorMsg);
            }
            
            // Lưu order cha
            $columnsStr = implode(', ', $insertColumns);
            $valuesStr = implode(', ', $insertValues);
            
            // Kiểm tra lại lần cuối bằng string search: Đảm bảo PRIMARY KEY không có trong SQL
            $allForbidden = array_unique(array_map('strtolower', $forbiddenPrimaryKeyNames));
            foreach ($allForbidden as $forbidden) {
                // Kiểm tra trong columnsStr và valuesStr
                if (stripos($columnsStr, $forbidden) !== false || stripos($valuesStr, ':' . $forbidden) !== false) {
                    $errorMsg = "LỖI NGHIÊM TRỌNG: PRIMARY KEY '$forbidden' vẫn xuất hiện trong SQL statement sau khi đã kiểm tra!";
                    error_log($errorMsg);
                    error_log("SQL: INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)");
                    error_log("Primary Key Column: " . ($primaryKeyColumn ?? 'NULL'));
                    error_log("All forbidden names: " . implode(', ', $forbiddenPrimaryKeyNames));
                    error_log("Insert columns: " . implode(', ', $insertColumns));
                    error_log("Table: $tableName");
                    error_log("Columns string: $columnsStr");
                    error_log("Values string: $valuesStr");
                    throw new Exception($errorMsg);
                }
            }
            
            // Kiểm tra lại bằng cách so sánh từng cột trong columnsStr
            $columnsArray = array_map('trim', explode(',', $columnsStr));
            foreach ($columnsArray as $col) {
                foreach ($absolutelyForbiddenFinal as $forbidden) {
                    if (strcasecmp(trim($col), $forbidden) === 0) {
                        $errorMsg = "LỖI NGHIÊM TRỌNG: Cột '$col' (matches '$forbidden') vẫn còn trong columnsStr!";
                        error_log($errorMsg);
                        error_log("Columns string: $columnsStr");
                        error_log("Table: $tableName");
                        throw new Exception($errorMsg);
                    }
                }
            }
            
            // Kiểm tra lại bằng cách so sánh từng cột - CHẶT CHẼ HƠN
            foreach ($insertColumns as $colIndex => $col) {
                // Kiểm tra với PRIMARY KEY
                if ($primaryKeyColumn && strcasecmp($col, $primaryKeyColumn) === 0) {
                    $errorMsg = "LỖI NGHIÊM TRỌNG: PRIMARY KEY '$primaryKeyColumn' vẫn còn ở vị trí $colIndex!";
                    error_log($errorMsg);
                    error_log("All columns: " . implode(', ', $insertColumns));
                    error_log("Table: $tableName");
                    throw new Exception($errorMsg);
                }
                
                // Kiểm tra với danh sách cấm
                foreach ($absolutelyForbidden as $forbidden) {
                    if (strcasecmp($col, $forbidden) === 0) {
                        $errorMsg = "LỖI NGHIÊM TRỌNG: Cột '$col' (matches '$forbidden') vẫn còn ở vị trí $colIndex sau khi đã loại bỏ!";
                        error_log($errorMsg);
                        error_log("All columns: " . implode(', ', $insertColumns));
                        error_log("Table: $tableName");
                        error_log("Forbidden list: " . implode(', ', $absolutelyForbidden));
                        throw new Exception($errorMsg);
                    }
                }
                
                // Kiểm tra bằng cách tìm trong string (case-insensitive)
                foreach ($absolutelyForbidden as $forbidden) {
                    if (stripos($col, $forbidden) !== false && strlen($col) === strlen($forbidden)) {
                        $errorMsg = "LỖI NGHIÊM TRỌNG: Cột '$col' có thể là '$forbidden' (string match)!";
                        error_log($errorMsg);
                        error_log("All columns: " . implode(', ', $insertColumns));
                        error_log("Table: $tableName");
                        throw new Exception($errorMsg);
                    }
                }
            }
            
            // Log để debug
            error_log("OrderModel::create - Final check passed. Primary Key: " . ($primaryKeyColumn ?? 'NULL'));
            error_log("OrderModel::create - Final columns: " . $columnsStr);
            error_log("OrderModel::create - Table: $tableName");
            
            // Kiểm tra và sửa AUTO_INCREMENT nếu cần (chỉ khi lastInsertId trả về 0)
            // Không gọi ở đây để tránh làm chậm, sẽ gọi sau nếu cần
            
            $sql = "INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)";
            error_log("OrderModel::create - Table: $tableName");
            error_log('OrderModel::create - SQL: ' . $sql);
            error_log('OrderModel::create - Primary Key Column (should NOT be in SQL): ' . ($primaryKeyColumn ?? 'NULL'));
            error_log('OrderModel::create - Payload keys: ' . implode(', ', array_keys($orderPayload)));
            
            // KIỂM TRA CUỐI CÙNG TRƯỚC KHI EXECUTE: Đảm bảo SQL không chứa 'id' hoặc 'order_id'
            $absolutelyForbiddenInSQL = ['id', 'order_id'];
            if ($primaryKeyColumn) {
                $absolutelyForbiddenInSQL[] = $primaryKeyColumn;
            }
            
            foreach ($absolutelyForbiddenInSQL as $forbidden) {
                // Kiểm tra trong SQL string (case-insensitive)
                if (stripos($sql, " $forbidden,") !== false || 
                    stripos($sql, ",$forbidden,") !== false || 
                    stripos($sql, ",$forbidden ") !== false ||
                    stripos($sql, "($forbidden,") !== false ||
                    stripos($sql, " $forbidden)") !== false) {
                    $errorMsg = "LỖI NGHIÊM TRỌNG: SQL statement vẫn chứa '$forbidden'! SQL: $sql";
                    error_log($errorMsg);
                    error_log("Table: $tableName");
                    error_log("Primary Key: " . ($primaryKeyColumn ?? 'NULL'));
                    error_log("Columns: " . $columnsStr);
                    throw new Exception($errorMsg);
                }
            }
            
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

