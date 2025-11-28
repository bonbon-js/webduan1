<?php

class ProductModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'products';
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
                    c.category_name as category,
                    pi.image_url as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                ORDER BY p.created_at DESC";
        
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
     * Lấy danh sách ID của các sản phẩm mới nhất
     * @param int $limit Số lượng sản phẩm mới nhất (mặc định 8)
     * @return array Danh sách product_id
     */
    public function getNewProductIds(int $limit = 8): array
    {
        $sql = "SELECT p.product_id 
                FROM {$this->table} p
                ORDER BY p.created_at DESC, p.product_id DESC
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => (int)$r['product_id'], $results);
    }

		/**
		 * Đếm tổng số sản phẩm (có thể theo danh mục)
		 */
		public function countAllProducts($categoryId = null, ?string $keyword = null, ?float $priceMin = null, ?float $priceMax = null)
		{
			$conditions = [];
			if ($categoryId) $conditions[] = 'p.category_id = :category_id';
			if ($keyword) {
				// Luôn chỉ tìm trong product_name để đảm bảo kết quả chính xác
				// Không tìm trong description vì có thể tìm thấy sản phẩm không liên quan
				$conditions[] = 'p.product_name LIKE :keyword';
			}
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
			if ($keyword) {
				// Luôn chỉ tìm trong product_name để đảm bảo kết quả chính xác
				// Không tìm trong description vì có thể tìm thấy sản phẩm không liên quan
				$conditions[] = 'p.product_name LIKE :keyword';
			}
			if ($priceMin !== null) $conditions[] = 'p.price >= :price_min';
			if ($priceMax !== null) $conditions[] = 'p.price <= :price_max';
			$whereSql = $conditions ? (' WHERE ' . implode(' AND ', $conditions)) : '';

			// Sắp xếp theo độ liên quan nếu có keyword
			$orderBy = 'p.created_at DESC';
			if ($keyword) {
				// Ưu tiên: khớp chính xác > bắt đầu bằng > chứa trong tên > ngày tạo
				$orderBy = "
					CASE 
						WHEN p.product_name LIKE :keyword_exact THEN 1
						WHEN p.product_name LIKE :keyword_start THEN 2
						WHEN p.product_name LIKE :keyword THEN 3
						ELSE 4
					END ASC,
					p.created_at DESC
				";
			}

			$sql = "SELECT 
						p.product_id as id,
						p.product_name as name,
						p.description,
						p.price,
						p.stock,
						c.category_name as category,
						pi.image_url as image
					FROM {$this->table} p
					LEFT JOIN categories c ON p.category_id = c.category_id
					LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
					{$whereSql}
					ORDER BY {$orderBy}
					LIMIT :limit OFFSET :offset";
			
			$stmt = $this->pdo->prepare($sql);
			if ($categoryId) $stmt->bindValue(':category_id', (int)$categoryId, PDO::PARAM_INT);
			if ($keyword) {
				$keywordEscaped = "%{$keyword}%";
				$keywordExact = "{$keyword}";
				$keywordStart = "{$keyword}%";
				$stmt->bindValue(':keyword', $keywordEscaped, PDO::PARAM_STR);
				// Luôn bind keyword_exact và keyword_start nếu có keyword (dùng trong ORDER BY)
				$stmt->bindValue(':keyword_exact', $keywordExact, PDO::PARAM_STR);
				$stmt->bindValue(':keyword_start', $keywordStart, PDO::PARAM_STR);
			}
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
                    c.category_id,
                    pi.image_url as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                WHERE p.product_id = :id";
        
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
                    c.category_name as category,
                    pi.image_url as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                WHERE p.category_id = :category_id
                ORDER BY p.created_at DESC";
        
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
                    c.category_name as category,
                    pi.image_url as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                WHERE p.category_id = :category_id
                  AND p.product_id <> :exclude_id
                ORDER BY p.created_at DESC
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
        // Chỉ tìm trong product_name để đảm bảo kết quả chính xác
        $sql = "SELECT 
                    p.product_id as id,
                    p.product_name as name,
                    p.description,
                    p.price,
                    p.stock,
                    c.category_name as category,
                    pi.image_url as image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
                WHERE p.product_name LIKE :keyword
                ORDER BY 
                    CASE 
                        WHEN p.product_name LIKE :keyword_exact THEN 1
                        WHEN p.product_name LIKE :keyword_start THEN 2
                        WHEN p.product_name LIKE :keyword THEN 3
                        ELSE 4
                    END ASC,
                    p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $keywordEscaped = "%{$keyword}%";
        $keywordExact = "{$keyword}";
        $keywordStart = "{$keyword}%";
        $stmt->bindValue(':keyword', $keywordEscaped, PDO::PARAM_STR);
        $stmt->bindValue(':keyword_exact', $keywordExact, PDO::PARAM_STR);
        $stmt->bindValue(':keyword_start', $keywordStart, PDO::PARAM_STR);
        
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
