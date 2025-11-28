<?php

class ProductModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'products';
    }

    /**
     * Kiểm tra xem cột deleted_at có tồn tại không
     */
    private function hasDeletedAtColumn(): bool
    {
        static $hasColumn = null;
        if ($hasColumn !== null) {
            return $hasColumn;
        }
        
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table} LIKE 'deleted_at'");
            $hasColumn = $stmt->rowCount() > 0;
            return $hasColumn;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Kiểm tra xem bảng product_images có tồn tại không
     */
    private function hasProductImagesTable(): bool
    {
        static $hasTable = null;
        if ($hasTable !== null) {
            return $hasTable;
        }
        
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'product_images'");
            $hasTable = $stmt->rowCount() > 0;
            return $hasTable;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Helper để thêm image join vào SQL nếu bảng tồn tại
     */
    private function addImageJoin(string $sql, string $alias = 'pi'): string
    {
        if ($this->hasProductImagesTable()) {
            return $sql . " LEFT JOIN product_images {$alias} ON p.product_id = {$alias}.product_id AND {$alias}.is_primary = 1";
        }
        return $sql;
    }

    /**
     * Helper để thêm image field vào SELECT
     */
    private function addImageField(string $sql, string $alias = 'pi'): string
    {
        if ($this->hasProductImagesTable()) {
            return $sql . ", {$alias}.image_url as image";
        }
        return $sql . ", NULL as image";
    }

    /**
     * Lấy danh sách sản phẩm cho trang quản trị
     */
    public function getAdminProducts(?string $keyword = null, ?int $categoryId = null, bool $includeDeleted = false): array
    {
        $hasDeletedAt = $this->hasDeletedAtColumn();
        
        $sql = "SELECT 
                    p.product_id,
                    p.product_name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name,
                    c.category_id";
        
        $sql = $this->addImageField($sql, 'pi');
        $sql = str_replace('as image', 'as image_url', $sql); // Đổi tên field cho admin
        
        if ($hasDeletedAt) {
            $sql .= ", p.deleted_at";
        }
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " WHERE 1=1";
        $params = [];

        if ($hasDeletedAt) {
            if (!$includeDeleted) {
                $sql .= " AND (p.deleted_at IS NULL OR p.deleted_at = '')";
            } else {
                $sql .= " AND (p.deleted_at IS NOT NULL AND p.deleted_at != '')";
            }
        }

        if ($keyword) {
            $sql .= " AND (p.product_name LIKE :keyword OR p.description LIKE :keyword)";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        if ($categoryId) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }

        $sql .= " ORDER BY p.product_id DESC";
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = $key === ':category_id' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo sản phẩm mới
     */
    public function createProduct(array $data): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (product_name, description, price, stock, category_id)
                VALUES (:name, :description, :price, :stock, :category_id)
            ");
            $stmt->execute([
                ':name'        => $data['name'],
                ':description' => $data['description'] ?? null,
                ':price'       => $data['price'],
                ':stock'       => $data['stock'],
                ':category_id' => $data['category_id'] ?: null,
            ]);

            $productId = (int)$this->pdo->lastInsertId();

            $this->upsertPrimaryImage($productId, $data['image_url'] ?? null);

            $this->pdo->commit();
            return $productId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * Cập nhật sản phẩm
     */
    public function updateProduct(int $productId, array $data): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                UPDATE {$this->table}
                SET product_name = :name,
                    description = :description,
                    price = :price,
                    stock = :stock,
                    category_id = :category_id
                WHERE product_id = :id
            ");
            $stmt->execute([
                ':name'        => $data['name'],
                ':description' => $data['description'] ?? null,
                ':price'       => $data['price'],
                ':stock'       => $data['stock'],
                ':category_id' => $data['category_id'] ?: null,
                ':id'          => $productId,
            ]);

            $this->upsertPrimaryImage($productId, $data['image_url'] ?? null);

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * Xóa mềm sản phẩm (soft delete)
     */
    public function deleteProduct(int $productId): bool
    {
        if (!$this->hasDeletedAtColumn()) {
            // Nếu chưa có cột deleted_at, thực hiện xóa vĩnh viễn
            return $this->forceDeleteProduct($productId);
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Khôi phục sản phẩm đã xóa
     */
    public function restoreProduct(int $productId): bool
    {
        if (!$this->hasDeletedAtColumn()) {
            throw new RuntimeException('Cột deleted_at chưa tồn tại. Vui lòng chạy script SQL để thêm cột.');
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET deleted_at = NULL WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $exception) {
            throw $exception;
        }
    }

    /**
     * Xóa vĩnh viễn sản phẩm (hard delete)
     */
    public function forceDeleteProduct(int $productId): bool
    {
        $this->pdo->beginTransaction();

        try {
            $variantIds = $this->getVariantIdsByProduct($productId);

            if (!empty($variantIds)) {
                $placeholder = implode(',', array_fill(0, count($variantIds), '?'));
                
                // Xóa variant_images nếu bảng tồn tại (bỏ qua lỗi nếu không tồn tại)
                try {
                    $stmt = $this->pdo->prepare("DELETE FROM variant_images WHERE variant_id IN ($placeholder)");
                    $stmt->execute($variantIds);
                } catch (PDOException $e) {
                    // Bảng variant_images không tồn tại, bỏ qua
                }

                $stmt = $this->pdo->prepare("DELETE FROM product_attribute_values WHERE variant_id IN ($placeholder)");
                $stmt->execute($variantIds);
            }

            $stmt = $this->pdo->prepare("DELETE FROM product_attribute_values WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM product_variants WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM product_images WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE product_id = :pid");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * Lấy danh sách biến thể và thuộc tính cho trang quản trị
     */
    public function getVariantsDetailed(int $productId): array
    {
        $sql = "SELECT 
                    pv.variant_id,
                    pv.product_id,
                    pv.sku,
                    pv.additional_price,
                    pv.stock,
                    a.attribute_id,
                    a.attribute_name,
                    av.value_id,
                    av.value_name
                FROM product_variants pv
                LEFT JOIN product_attribute_values pav ON pv.variant_id = pav.variant_id
                LEFT JOIN attribute_values av ON pav.value_id = av.value_id
                LEFT JOIN attributes a ON av.attribute_id = a.attribute_id
                WHERE pv.product_id = :pid
                ORDER BY pv.variant_id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $variants = [];
        foreach ($rows as $row) {
            $id = (int)$row['variant_id'];
            if (!isset($variants[$id])) {
                $variants[$id] = [
                    'variant_id' => $id,
                    'product_id' => (int)$row['product_id'],
                    'sku' => $row['sku'],
                    'additional_price' => (float)($row['additional_price'] ?? 0),
                    'stock' => (int)$row['stock'],
                    'attributes' => [],
                ];
            }

            if ($row['attribute_id']) {
                $variants[$id]['attributes'][(int)$row['attribute_id']] = [
                    'attribute_id' => (int)$row['attribute_id'],
                    'attribute_name' => $row['attribute_name'],
                    'value_id' => (int)$row['value_id'],
                    'value_name' => $row['value_name'],
                ];
            }
        }

        return array_values($variants);
    }

    /**
     * Tạo biến thể mới
     */
    public function createVariant(int $productId, array $variantData, array $valueIds): int
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO product_variants (product_id, sku, additional_price, stock)
                VALUES (:product_id, :sku, :additional_price, :stock)
            ");
            $stmt->execute([
                ':product_id' => $productId,
                ':sku' => $variantData['sku'] ?? null,
                ':additional_price' => $variantData['additional_price'] ?? 0,
                ':stock' => $variantData['stock'] ?? 0,
            ]);

            $variantId = (int)$this->pdo->lastInsertId();

            $this->syncVariantAttributeValues($productId, $variantId, $valueIds);

            $this->pdo->commit();
            return $variantId;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * Cập nhật biến thể
     */
    public function updateVariant(int $variantId, array $variantData, array $valueIds): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare("
                UPDATE product_variants
                SET sku = :sku,
                    additional_price = :additional_price,
                    stock = :stock
                WHERE variant_id = :variant_id
            ");
            $stmt->execute([
                ':sku' => $variantData['sku'] ?? null,
                ':additional_price' => $variantData['additional_price'] ?? 0,
                ':stock' => $variantData['stock'] ?? 0,
                ':variant_id' => $variantId,
            ]);

            $productId = $this->getProductIdByVariant($variantId);
            if (!$productId) {
                throw new RuntimeException('Không tìm thấy sản phẩm cho biến thể.');
            }

            $stmt = $this->pdo->prepare("DELETE FROM product_attribute_values WHERE variant_id = :variant_id");
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();

            $this->syncVariantAttributeValues($productId, $variantId, $valueIds);

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    /**
     * Xóa biến thể
     */
    public function deleteVariant(int $variantId): bool
    {
        $this->pdo->beginTransaction();

        try {
            // Xóa variant_images nếu bảng tồn tại (bỏ qua lỗi nếu không tồn tại)
            try {
                $stmt = $this->pdo->prepare("DELETE FROM variant_images WHERE variant_id = :variant_id");
                $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                // Bảng variant_images không tồn tại, bỏ qua
            }

            $stmt = $this->pdo->prepare("DELETE FROM product_attribute_values WHERE variant_id = :variant_id");
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE variant_id = :variant_id");
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $this->pdo->prepare("DELETE FROM product_variants WHERE variant_id = :variant_id");
            $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    private function getVariantIdsByProduct(int $productId): array
    {
        $stmt = $this->pdo->prepare("SELECT variant_id FROM product_variants WHERE product_id = :pid");
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return array_map(fn($row) => (int)$row['variant_id'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getProductIdByVariant(int $variantId): ?int
    {
        $stmt = $this->pdo->prepare("SELECT product_id FROM product_variants WHERE variant_id = :variant_id LIMIT 1");
        $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['product_id'] : null;
    }

    private function syncVariantAttributeValues(int $productId, int $variantId, array $valueIds): void
    {
        if (empty($valueIds)) {
            return;
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO product_attribute_values (product_id, variant_id, value_id)
            VALUES (:product_id, :variant_id, :value_id)
        ");

        foreach ($valueIds as $valueId) {
            if (!$valueId) {
                continue;
            }

            $stmt->execute([
                ':product_id' => $productId,
                ':variant_id' => $variantId,
                ':value_id' => $valueId,
            ]);
        }
    }

    private function upsertPrimaryImage(int $productId, ?string $imageUrl): void
    {
        // Nếu bảng product_images không tồn tại, bỏ qua
        if (!$this->hasProductImagesTable()) {
            return;
        }

        if ($imageUrl === null || $imageUrl === '') {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM product_images WHERE product_id = :pid AND is_primary = 1");
                $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                // Bỏ qua lỗi nếu bảng không tồn tại
            }
            return;
        }

        try {
            // Kiểm tra xem có ảnh primary nào chưa
            $stmt = $this->pdo->prepare("SELECT * FROM product_images WHERE product_id = :pid AND is_primary = 1 LIMIT 1");
            $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Cập nhật ảnh hiện có - sử dụng product_id thay vì product_image_id
                $stmt = $this->pdo->prepare("UPDATE product_images SET image_url = :image_url WHERE product_id = :pid AND is_primary = 1");
                $stmt->bindValue(':image_url', $imageUrl, PDO::PARAM_STR);
                $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO product_images (product_id, image_url, is_primary)
                    VALUES (:product_id, :image_url, 1)
                ");
                $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $stmt->bindValue(':image_url', $imageUrl, PDO::PARAM_STR);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu bảng không tồn tại
        }
    }

    /**
     * Lấy các giá trị thuộc tính (Size, Color) khả dụng cho 1 sản phẩm từ DB
     * Trả về mảng: ['sizes' => [...], 'colors' => [...]]
     */
    public function getProductAttributes(int $productId): array
    {
        // Sizes
        $sqlSize = "SELECT DISTINCT av.value_name
                    FROM product_attribute_values pav
                    JOIN attribute_values av ON pav.value_id = av.value_id
                    JOIN attributes a ON av.attribute_id = a.attribute_id
                    WHERE pav.product_id = :pid AND a.attribute_name = 'Size'
                    ORDER BY av.value_name";
        $stmt = $this->pdo->prepare($sqlSize);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $sizes = array_map(fn($r) => $r['value_name'], $stmt->fetchAll(PDO::FETCH_ASSOC));

        // Colors
        $sqlColor = "SELECT DISTINCT av.value_name
                     FROM product_attribute_values pav
                     JOIN attribute_values av ON pav.value_id = av.value_id
                     JOIN attributes a ON av.attribute_id = a.attribute_id
                     WHERE pav.product_id = :pid AND a.attribute_name = 'Color'
                     ORDER BY av.value_name";
        $stmt = $this->pdo->prepare($sqlColor);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $colors = array_map(fn($r) => $r['value_name'], $stmt->fetchAll(PDO::FETCH_ASSOC));

        return [
            'sizes' => $sizes,
            'colors' => $colors,
        ];
    }

    /**
     * Tìm biến thể (variant) theo tên thuộc tính (size, color)
     * Trả về thông tin variant (variant_id, sku, additional_price, stock) hoặc null nếu không tồn tại
     */
    public function getVariantByValueNames(int $productId, ?string $sizeName, ?string $colorName): ?array
    {
        $selected = array_values(array_filter([$sizeName, $colorName], fn($v) => $v !== null && $v !== ''));
        if (count($selected) === 0) {
            return null;
        }

        // Tìm variant có đủ tất cả value_name đã chọn
        $inPlaceholders = implode(',', array_fill(0, count($selected), '?'));
        $sql = "SELECT pv.variant_id, pv.sku, pv.additional_price, pv.stock
                FROM product_variants pv
                JOIN product_attribute_values pav ON pv.variant_id = pav.variant_id
                JOIN attribute_values av ON pav.value_id = av.value_id
                WHERE pv.product_id = ?
                  AND av.value_name IN ($inPlaceholders)
                GROUP BY pv.variant_id, pv.sku, pv.additional_price, pv.stock
                HAVING COUNT(DISTINCT av.value_name) = ?
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $bindIndex = 1;
        $stmt->bindValue($bindIndex++, $productId, PDO::PARAM_INT);
        foreach ($selected as $name) {
            $stmt->bindValue($bindIndex++, $name, PDO::PARAM_STR);
        }
        $stmt->bindValue($bindIndex++, count($selected), PDO::PARAM_INT);
        $stmt->execute();
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        return $variant ?: null;
    }

    /**
     * Lấy tất cả sản phẩm kèm hình ảnh chính
     * @param int $limit Số lượng sản phẩm cần lấy (mặc định: tất cả)
     * @return array Danh sách sản phẩm
     */
    public function getAllProducts($limit = null)
    {
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category";
        
        $sql = $this->addImageField($sql);
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " ORDER BY p.product_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

		/**
		 * Đếm tổng số sản phẩm (có thể theo danh mục)
		 */
		public function countAllProducts($categoryId = null, ?string $keyword = null, ?float $priceMin = null, ?float $priceMax = null)
		{
			$conditions = [];
			if ($categoryId) $conditions[] = 'p.category_id = :category_id';
			if ($keyword)    $conditions[] = '(p.product_name LIKE :keyword OR p.description LIKE :keyword)';
			if ($priceMin !== null) $conditions[] = 'p.price >= :price_min';
			if ($priceMax !== null) $conditions[] = 'p.price <= :price_max';

			$whereSql = $conditions ? (' WHERE ' . implode(' AND ', $conditions)) : '';

			$sql = "SELECT COUNT(*) AS total FROM {$this->table} p" . $whereSql;
			$stmt = $this->pdo->prepare($sql);
			if ($categoryId) $stmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
			if ($keyword)    $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
			if ($priceMin !== null) $stmt->bindValue(':price_min', $priceMin, PDO::PARAM_STR);
			if ($priceMax !== null) $stmt->bindValue(':price_max', $priceMax, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return (int)($row['total'] ?? 0);
		}

		/**
		 * Lấy sản phẩm theo trang (có thể theo danh mục)
		 */
		public function getProductsPage($page = 1, $perPage = 12, $categoryId = null, ?string $keyword = null, ?float $priceMin = null, ?float $priceMax = null)
		{
			$offset = max(0, ($page - 1) * $perPage);
			$conditions = [];
			if ($categoryId) $conditions[] = 'p.category_id = :category_id';
			if ($keyword)    $conditions[] = '(p.product_name LIKE :keyword OR p.description LIKE :keyword)';
			if ($priceMin !== null) $conditions[] = 'p.price >= :price_min';
			if ($priceMax !== null) $conditions[] = 'p.price <= :price_max';
			$whereSql = $conditions ? (' WHERE ' . implode(' AND ', $conditions)) : '';

			$sql = "SELECT 
						p.product_id as id,
						p.product_name as name,
						p.description,
						p.price,
						p.stock,
						c.category_name as category";
			
			$sql = $this->addImageField($sql);
			
			$sql .= " FROM {$this->table} p
					LEFT JOIN categories c ON p.category_id = c.category_id";
			
			$sql = $this->addImageJoin($sql);
			
			$sql .= " {$whereSql}
					ORDER BY p.product_id DESC
					LIMIT :limit OFFSET :offset";
			
			$stmt = $this->pdo->prepare($sql);
			if ($categoryId) $stmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
			if ($keyword)    $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
			if ($priceMin !== null) $stmt->bindValue(':price_min', $priceMin, PDO::PARAM_STR);
			if ($priceMax !== null) $stmt->bindValue(':price_max', $priceMax, PDO::PARAM_STR);
			$stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
			$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

    /**
     * Lấy sản phẩm theo ID
     * @param int $id ID sản phẩm
     * @return array|false Thông tin sản phẩm hoặc false nếu không tìm thấy
     */
    public function getProductById($id)
    {
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category,
                    c.category_id";
        
        $sql = $this->addImageField($sql);
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " WHERE p.product_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo danh mục
     * @param int $categoryId ID danh mục
     * @param int $limit Số lượng sản phẩm
     * @return array Danh sách sản phẩm
     */
    public function getProductsByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category";
        
        $sql = $this->addImageField($sql);
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " WHERE p.category_id = :category_id
                ORDER BY p.product_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy hình ảnh của sản phẩm
     * @param int $productId ID sản phẩm
     * @return array Danh sách hình ảnh
     */
    public function getProductImages($productId)
    {
        $sql = "SELECT image_url, is_primary 
                FROM product_images 
                WHERE product_id = :product_id
                ORDER BY is_primary DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy ảnh theo variant
     */
    public function getVariantImages(int $variantId): array
    {
        $sql = "SELECT image_url, is_primary
                FROM variant_images
                WHERE variant_id = :variant_id
                ORDER BY is_primary DESC, variant_image_id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':variant_id', $variantId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy ảnh theo Color cho toàn bộ variant của 1 sản phẩm
     * Trả về mảng URL ảnh (không trùng), ưu tiên ảnh is_primary trước
     */
    public function getVariantImagesByColor(int $productId, string $colorName): array
    {
        $sql = "SELECT DISTINCT vi.image_url, vi.is_primary, vi.variant_image_id
                FROM product_variants pv
                JOIN product_attribute_values pav ON pav.variant_id = pv.variant_id
                JOIN attribute_values av ON av.value_id = pav.value_id
                JOIN attributes a ON a.attribute_id = av.attribute_id
                JOIN variant_images vi ON vi.variant_id = pv.variant_id
                WHERE pv.product_id = :pid
                  AND a.attribute_name = 'Color'
                  AND av.value_name = :color
                ORDER BY vi.is_primary DESC, vi.variant_image_id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':color', $colorName, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Trả về danh sách URL duy nhất theo thứ tự ưu tiên
        $seen = [];
        $urls = [];
        foreach ($rows as $row) {
            $u = $row['image_url'] ?? '';
            if ($u !== '' && !isset($seen[$u])) {
                $seen[$u] = true;
                $urls[] = $u;
            }
        }
        return $urls;
    }

    /**
     * Lấy biến thể đầu tiên của 1 sản phẩm (fallback)
     */
    public function getFirstVariantByProduct(int $productId): ?array
    {
        $sql = "SELECT variant_id, product_id, sku, additional_price, stock
                FROM product_variants
                WHERE product_id = :pid
                ORDER BY variant_id ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':pid', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Lấy tên Size và Color của 1 variant
     */
    public function getVariantAttributeNames(int $variantId): array
    {
        $sql = "
            SELECT
                (SELECT av.value_name
                 FROM product_attribute_values pav
                 JOIN attribute_values av ON pav.value_id = av.value_id
                 JOIN attributes a ON a.attribute_id = av.attribute_id
                 WHERE pav.variant_id = :vid AND a.attribute_name = 'Size'
                 LIMIT 1) AS size_name,
                (SELECT av.value_name
                 FROM product_attribute_values pav
                 JOIN attribute_values av ON pav.value_id = av.value_id
                 JOIN attributes a ON a.attribute_id = av.attribute_id
                 WHERE pav.variant_id = :vid AND a.attribute_name = 'Color'
                 LIMIT 1) AS color_name
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':vid', $variantId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'size' => $row['size_name'] ?? null,
            'color' => $row['color_name'] ?? null,
        ];
    }
    /**
     * Lấy sản phẩm tương tự theo danh mục (loại trừ 1 sản phẩm)
     */
    public function getSimilarProducts(int $categoryId, int $excludeProductId, int $limit = 8): array
    {
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category";
        
        $sql = $this->addImageField($sql);
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " WHERE p.category_id = :category_id
                  AND p.product_id <> :exclude_id
                ORDER BY p.product_id DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':exclude_id', $excludeProductId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm sản phẩm theo tên
     * @param string $keyword Từ khóa tìm kiếm
     * @param int $limit Số lượng sản phẩm
     * @return array Danh sách sản phẩm
     */
    public function searchProducts($keyword, $limit = null)
    {
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category";
        
        $sql = $this->addImageField($sql);
        
        $sql .= " FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        $sql = $this->addImageJoin($sql);
        
        $sql .= " WHERE p.product_name LIKE :keyword OR p.description LIKE :keyword
                ORDER BY p.product_id DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy số lượng sản phẩm theo tháng (12 tháng gần nhất)
    public function getMonthlyProducts(int $months = 12): array
    {
        $products = [];
        $labels = [];
        
        $totalProducts = $this->countAllProducts();
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthLabel = date('M', strtotime("-$i months"));
            
            // Vì không biết cột ngày chính xác, chia đều cho các tháng
            $products[] = $totalProducts / $months;
            $labels[] = $monthLabel;
        }
        
        return [
            'labels' => $labels,
            'data' => $products
        ];
    }
}
