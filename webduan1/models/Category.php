<?php

class Category extends BaseModel
{
    protected $table = 'categories';

    // Lấy tất cả danh mục
    public function getAllCategories()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY category_name ASC";
        
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll();
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    // Thêm danh mục mới
    public function createCategory($data)
    {
        $sql = "INSERT INTO {$this->table} (category_name, description, created_at) 
                VALUES (:name, :description, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? ''
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Cập nhật danh mục
    public function updateCategory($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET category_name = :name, 
                    description = :description
                WHERE category_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? ''
        ]);
    }

    // Xóa danh mục
    public function deleteCategory($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE category_id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
