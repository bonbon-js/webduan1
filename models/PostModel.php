<?php

class PostModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'posts';
    }

    /**
     * Lấy các tin tức nổi bật (featured) để hiển thị trên trang chủ
     * @param int $limit Số lượng tin tức cần lấy
     * @return array
     */
    public function getFeaturedPosts(int $limit = 3): array
    {
        // Kiểm tra xem cột is_featured có tồn tại không
        $stmt = $this->pdo->query("SHOW COLUMNS FROM {$this->table} LIKE 'is_featured'");
        $hasIsFeatured = $stmt->rowCount() > 0;
        
        if ($hasIsFeatured) {
            $sql = "SELECT 
                        post_id,
                        title,
                        excerpt,
                        content,
                        thumbnail,
                        is_featured,
                        created_at,
                        updated_at
                    FROM {$this->table}
                    WHERE is_featured = 1
                      AND (status = 'published' OR status IS NULL)
                    ORDER BY created_at DESC
                    LIMIT :limit";
        } else {
            // Nếu chưa có cột is_featured, lấy 3 tin tức mới nhất
            $sql = "SELECT 
                        post_id,
                        title,
                        excerpt,
                        content,
                        thumbnail,
                        created_at,
                        updated_at
                    FROM {$this->table}
                    WHERE status = 'published' OR status IS NULL
                    ORDER BY created_at DESC
                    LIMIT :limit";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dữ liệu để tương thích với view hiện tại
        $formatted = [];
        foreach ($posts as $post) {
            $imageUrl = $post['thumbnail'] ?? '';
            if ($imageUrl && strpos($imageUrl, 'http') !== 0) {
                $imageUrl = BASE_URL . $imageUrl;
            } elseif (!$imageUrl) {
                $imageUrl = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80';
            }
            
            $formatted[] = [
                'id' => $post['post_id'],
                'title' => $post['title'] ?? '',
                'excerpt' => $post['excerpt'] ?? mb_substr(strip_tags($post['content'] ?? ''), 0, 100) . '...',
                'content' => $post['content'] ?? '',
                'image' => $imageUrl,
                'date' => $post['created_at'] ? date('d/m/Y', strtotime($post['created_at'])) : date('d/m/Y'),
                'created_at' => $post['created_at'],
            ];
        }
        
        return $formatted;
    }

    /**
     * Lấy tất cả tin tức (có phân trang)
     * @param int $page Trang hiện tại
     * @param int $perPage Số tin tức mỗi trang
     * @return array ['posts' => [...], 'total' => int, 'totalPages' => int]
     */
    public function getAllPosts(int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        
        // Đếm tổng số tin tức
        $countSql = "SELECT COUNT(*) as total 
                     FROM {$this->table}
                     WHERE status = 'published'";
        $countStmt = $this->pdo->query($countSql);
        $total = (int)$countStmt->fetch()['total'];
        $totalPages = ceil($total / $perPage);
        
        // Lấy tin tức
        $sql = "SELECT 
                    post_id,
                    title,
                    excerpt,
                    content,
                    thumbnail,
                    is_featured,
                    created_at,
                    updated_at
                FROM {$this->table}
                WHERE status = 'published' OR status IS NULL
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dữ liệu
        $formatted = [];
        foreach ($posts as $post) {
            $imageUrl = $post['thumbnail'] ?? '';
            if ($imageUrl && strpos($imageUrl, 'http') !== 0) {
                $imageUrl = BASE_URL . $imageUrl;
            } elseif (!$imageUrl) {
                $imageUrl = 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=600&q=80';
            }
            
            $formatted[] = [
                'id' => $post['post_id'],
                'title' => $post['title'] ?? '',
                'excerpt' => $post['excerpt'] ?? mb_substr(strip_tags($post['content'] ?? ''), 0, 150) . '...',
                'content' => $post['content'] ?? '',
                'image' => $imageUrl,
                'date' => $post['created_at'] ? date('d/m/Y', strtotime($post['created_at'])) : date('d/m/Y'),
                'created_at' => $post['created_at'],
                'is_featured' => isset($post['is_featured']) ? (bool)$post['is_featured'] : false,
            ];
        }
        
        return [
            'posts' => $formatted,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ];
    }

    /**
     * Lấy chi tiết một tin tức
     * @param int $postId
     * @return array|null
     */
    public function getPostById(int $postId): ?array
    {
        $sql = "SELECT 
                    post_id,
                    title,
                    excerpt,
                    content,
                    thumbnail,
                    is_featured,
                    created_at,
                    updated_at
                FROM {$this->table}
                WHERE post_id = :post_id
                  AND (status = 'published' OR status IS NULL)
                LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            return null;
        }
        
        $imageUrl = $post['thumbnail'] ?? '';
        if ($imageUrl && strpos($imageUrl, 'http') !== 0) {
            $imageUrl = BASE_URL . $imageUrl;
        }
        
        return [
            'id' => $post['post_id'],
            'title' => $post['title'] ?? '',
            'excerpt' => $post['excerpt'] ?? '',
            'content' => $post['content'] ?? '',
            'image' => $imageUrl ?: null,
            'date' => $post['created_at'] ? date('d/m/Y', strtotime($post['created_at'])) : date('d/m/Y'),
            'created_at' => $post['created_at'],
            'is_featured' => isset($post['is_featured']) ? (bool)$post['is_featured'] : false,
        ];
    }
}

