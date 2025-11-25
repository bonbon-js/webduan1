<?php

class Product extends BaseModel
{
    protected $table = 'products';

    // Lấy tất cả sản phẩm với phân trang và lọc
    public function getAllProducts($page = 1, $limit = 12, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT DISTINCT p.*, c.category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.category_id";
        
        // Join với bảng thuộc tính nếu có lọc theo thuộc tính
        if (!empty($filters['attributes'])) {
            $sql .= " LEFT JOIN product_attribute_values pav ON p.product_id = pav.product_id";
        }
        
        $sql .= " WHERE 1=1";
        
        $params = [];
        
        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        // Tìm kiếm theo tên
        if (!empty($filters['search'])) {
            $sql .= " AND p.product_name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Lọc theo khoảng giá
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        // Lọc theo trạng thái còn hàng
        if (isset($filters['in_stock']) && $filters['in_stock'] !== '') {
            if ($filters['in_stock'] == '1') {
                $sql .= " AND p.stock > 0";
            } else {
                $sql .= " AND p.stock = 0";
            }
        }
        
        // Lọc theo thuộc tính (màu sắc, kích thước, v.v.)
        if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
            $attrConditions = [];
            foreach ($filters['attributes'] as $idx => $valueId) {
                $paramKey = ":attr_value_$idx";
                $attrConditions[] = "pav.value_id = $paramKey";
                $params[$paramKey] = $valueId;
            }
            if (!empty($attrConditions)) {
                $sql .= " AND (" . implode(' OR ', $attrConditions) . ")";
            }
        }
        
        // Sắp xếp
        $orderBy = $filters['order_by'] ?? 'p.product_id';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $sql .= " ORDER BY {$orderBy} {$orderDir}";
        
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Đếm tổng số sản phẩm (cho phân trang)
    public function countProducts($filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT p.product_id) as total FROM {$this->table} p";
        
        // Join với bảng thuộc tính nếu có lọc theo thuộc tính
        if (!empty($filters['attributes'])) {
            $sql .= " LEFT JOIN product_attribute_values pav ON p.product_id = pav.product_id";
        }
        
        $sql .= " WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND p.product_name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }
        
        if (isset($filters['in_stock']) && $filters['in_stock'] !== '') {
            if ($filters['in_stock'] == '1') {
                $sql .= " AND p.stock > 0";
            } else {
                $sql .= " AND p.stock = 0";
            }
        }
        
        if (!empty($filters['attributes']) && is_array($filters['attributes'])) {
            $attrConditions = [];
            foreach ($filters['attributes'] as $idx => $valueId) {
                $paramKey = ":attr_value_$idx";
                $attrConditions[] = "pav.value_id = $paramKey";
                $params[$paramKey] = $valueId;
            }
            if (!empty($attrConditions)) {
                $sql .= " AND (" . implode(' OR ', $attrConditions) . ")";
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch()['total'];
    }

    // Lấy chi tiết sản phẩm
    public function getProductById($id)
    {
        $sql = "SELECT p.*, c.category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.product_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    // Lấy tất cả hình ảnh của sản phẩm
    public function getProductImages($productId)
    {
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_primary DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        
        return $stmt->fetchAll();
    }

    // Lấy biến thể của sản phẩm
    public function getProductVariants($productId)
    {
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        
        return $stmt->fetchAll();
    }

    // Lấy thuộc tính của sản phẩm với giá trị
    public function getProductAttributes($productId)
    {
        $sql = "SELECT a.attribute_name, av.value_name, pav.variant_id
                FROM product_attribute_values pav
                JOIN attribute_values av ON pav.value_id = av.value_id
                JOIN attributes a ON av.attribute_id = a.attribute_id
                WHERE pav.product_id = :product_id
                ORDER BY a.attribute_name, av.value_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        
        return $stmt->fetchAll();
    }

    // Thêm sản phẩm mới
    public function createProduct($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (product_name, description, price, category_id, stock, created_at) 
                VALUES 
                (:name, :description, :price, :category_id, :stock, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':category_id' => $data['category_id'],
            ':stock' => $data['stock'] ?? 0
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Thêm hình ảnh sản phẩm
    public function addProductImage($productId, $imageUrl, $isPrimary = 0)
    {
        $sql = "INSERT INTO product_images (product_id, image_url, is_primary) 
                VALUES (:product_id, :image_url, :is_primary)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':product_id' => $productId,
            ':image_url' => $imageUrl,
            ':is_primary' => $isPrimary
        ]);
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET product_name = :name, 
                    description = :description, 
                    price = :price, 
                    category_id = :category_id, 
                    stock = :stock
                WHERE product_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':category_id' => $data['category_id'],
            ':stock' => $data['stock'] ?? 0
        ]);
    }

    // Xóa sản phẩm
    public function deleteProduct($id)
    {
        // Xóa hình ảnh trước
        $sql = "DELETE FROM product_images WHERE product_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Xóa sản phẩm
        $sql = "DELETE FROM {$this->table} WHERE product_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Lấy sản phẩm liên quan
    public function getRelatedProducts($productId, $categoryId, $limit = 4)
    {
        $sql = "SELECT p.*,
                (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} p
                WHERE p.category_id = :category_id 
                AND p.product_id != :product_id 
                ORDER BY RAND() 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Thêm nhiều hình ảnh cho sản phẩm
    public function addMultipleImages($productId, $images)
    {
        foreach ($images as $image) {
            $this->addProductImage($productId, $image['url'], $image['is_primary'] ?? 0);
        }
        return true;
    }

    // Xóa hình ảnh sản phẩm
    public function deleteProductImage($imageId)
    {
        $sql = "DELETE FROM product_images WHERE image_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $imageId]);
    }

    // Đặt ảnh chính
    public function setPrimaryImage($productId, $imageId)
    {
        // Bỏ primary của tất cả ảnh
        $sql = "UPDATE product_images SET is_primary = 0 WHERE product_id = :product_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        
        // Đặt ảnh mới làm primary
        $sql = "UPDATE product_images SET is_primary = 1 WHERE image_id = :image_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':image_id' => $imageId]);
    }

    // Gán thuộc tính cho sản phẩm
    public function assignAttribute($productId, $valueId, $variantId = null)
    {
        $sql = "INSERT INTO product_attribute_values (product_id, value_id, variant_id) 
                VALUES (:product_id, :value_id, :variant_id)
                ON DUPLICATE KEY UPDATE variant_id = :variant_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':product_id' => $productId,
            ':value_id' => $valueId,
            ':variant_id' => $variantId
        ]);
    }

    // Xóa thuộc tính của sản phẩm
    public function removeAttribute($productId, $valueId)
    {
        $sql = "DELETE FROM product_attribute_values 
                WHERE product_id = :product_id AND value_id = :value_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':product_id' => $productId,
            ':value_id' => $valueId
        ]);
    }

    // Lấy tất cả thuộc tính có thể lọc
    public function getFilterableAttributes()
    {
        $sql = "SELECT DISTINCT a.attribute_id, a.attribute_name, av.value_id, av.value_name
                FROM attributes a
                JOIN attribute_values av ON a.attribute_id = av.attribute_id
                JOIN product_attribute_values pav ON av.value_id = pav.value_id
                ORDER BY a.attribute_name, av.value_name";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        // Nhóm theo attribute
        $grouped = [];
        foreach ($results as $row) {
            $attrId = $row['attribute_id'];
            if (!isset($grouped[$attrId])) {
                $grouped[$attrId] = [
                    'attribute_id' => $row['attribute_id'],
                    'attribute_name' => $row['attribute_name'],
                    'values' => []
                ];
            }
            $grouped[$attrId]['values'][] = [
                'value_id' => $row['value_id'],
                'value_name' => $row['value_name']
            ];
        }
        
        return array_values($grouped);
    }
}
