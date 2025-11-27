<?php

require_once PATH_MODEL . 'BaseModel.php';

class CategoryModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'categories';
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAllCategories(): array
    {
        $sql = "SELECT category_id, category_name, description 
                FROM {$this->table} 
                ORDER BY category_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getCategoryById(int $categoryId): ?array
    {
        $sql = "SELECT category_id, category_name, description 
                FROM {$this->table} 
                WHERE category_id = :category_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Tìm kiếm danh mục theo tên
     */
    public function searchCategories(string $keyword): array
    {
        $sql = "SELECT category_id, category_name, description 
                FROM {$this->table} 
                WHERE category_name LIKE :keyword
                ORDER BY category_name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

