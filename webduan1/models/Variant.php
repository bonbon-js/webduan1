<?php

class Variant extends BaseModel
{
    protected $table = 'product_variants';

    // Lấy tất cả biến thể của sản phẩm
    public function getByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY variant_id ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':product_id' => $productId]);
        
        return $stmt->fetchAll();
    }

    // Lấy biến thể theo ID
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE variant_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    // Thêm biến thể mới
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (product_id, sku, additional_price, stock) 
                VALUES 
                (:product_id, :sku, :additional_price, :stock)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $data['product_id'],
            ':sku' => $data['sku'],
            ':additional_price' => $data['additional_price'] ?? 0,
            ':stock' => $data['stock'] ?? 0
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Cập nhật biến thể
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET sku = :sku, 
                    additional_price = :additional_price, 
                    stock = :stock
                WHERE variant_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':sku' => $data['sku'],
            ':additional_price' => $data['additional_price'] ?? 0,
            ':stock' => $data['stock'] ?? 0
        ]);
    }

    // Xóa biến thể
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE variant_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Xóa tất cả biến thể của sản phẩm
    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE product_id = :product_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':product_id' => $productId]);
    }
}
