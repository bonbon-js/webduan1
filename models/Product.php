<?php

class Product extends BaseModel
{
    protected $table = 'products';

    // Lấy tất cả sản phẩm với phân trang và lọc
    public function getAllProducts($page = 1, $limit = 12, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT p.*, c.category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE 1=1";
        
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
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND product_name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
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
}
