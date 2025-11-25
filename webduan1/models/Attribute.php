<?php

class Attribute extends BaseModel
{
    protected $table = 'attributes';

    // Lấy tất cả thuộc tính
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY attribute_name ASC";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    // Lấy thuộc tính theo ID
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE attribute_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    // Lấy giá trị của thuộc tính
    public function getValues($attributeId)
    {
        $sql = "SELECT * FROM attribute_values WHERE attribute_id = :attribute_id ORDER BY value_name ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':attribute_id' => $attributeId]);
        
        return $stmt->fetchAll();
    }

    // Thêm thuộc tính mới
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (attribute_name) VALUES (:name)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $data['name']]);
        
        return $this->pdo->lastInsertId();
    }

    // Thêm giá trị thuộc tính
    public function createValue($attributeId, $valueName)
    {
        $sql = "INSERT INTO attribute_values (attribute_id, value_name) VALUES (:attribute_id, :value_name)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':attribute_id' => $attributeId,
            ':value_name' => $valueName
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Cập nhật thuộc tính
    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET attribute_name = :name WHERE attribute_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name']
        ]);
    }

    // Xóa thuộc tính
    public function delete($id)
    {
        // Xóa các giá trị trước
        $sql = "DELETE FROM attribute_values WHERE attribute_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Xóa thuộc tính
        $sql = "DELETE FROM {$this->table} WHERE attribute_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Xóa giá trị thuộc tính
    public function deleteValue($valueId)
    {
        $sql = "DELETE FROM attribute_values WHERE value_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $valueId]);
    }

    // Liên kết thuộc tính với sản phẩm
    public function linkToProduct($productId, $variantId, $valueId)
    {
        $sql = "INSERT INTO product_attribute_values (product_id, variant_id, value_id) 
                VALUES (:product_id, :variant_id, :value_id)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':product_id' => $productId,
            ':variant_id' => $variantId,
            ':value_id' => $valueId
        ]);
    }

    // Xóa liên kết thuộc tính với sản phẩm
    public function unlinkFromProduct($productId)
    {
        $sql = "DELETE FROM product_attribute_values WHERE product_id = :product_id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':product_id' => $productId]);
    }
}
