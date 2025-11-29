<?php

require_once PATH_MODEL . 'BaseModel.php';

class ReviewModel extends BaseModel
{
    protected $table = 'reviews';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Tạo đánh giá mới
     */
    public function create(array $data): int
    {
        // Loại bỏ PRIMARY KEY khỏi data
        $data = $this->removePrimaryKeyFromData($data, $this->table);
        
        // Lưu images dưới dạng JSON nếu có
        $imagesJson = null;
        if (!empty($data['images']) && is_array($data['images'])) {
            $imagesJson = json_encode($data['images']);
        } elseif (!empty($data['images']) && is_string($data['images'])) {
            $imagesJson = $data['images'];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table} (
                order_id, order_item_id, product_id, user_id, rating, comment, images
            ) VALUES (
                :order_id, :order_item_id, :product_id, :user_id, :rating, :comment, :images
            )
        ");

        $stmt->execute([
            ':order_id' => $data['order_id'],
            ':order_item_id' => $data['order_item_id'],
            ':product_id' => $data['product_id'],
            ':user_id' => $data['user_id'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment'] ?? null,
            ':images' => $imagesJson,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Lấy tất cả đánh giá của một sản phẩm (chỉ hiển thị những cái không bị ẩn)
     */
    public function getByProduct(int $productId, bool $includeHidden = false): array
    {
        $whereClause = "product_id = :product_id";
        if (!$includeHidden) {
            $whereClause .= " AND is_hidden = 0";
        }

        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                u.full_name as user_name,
                u.email as user_email
            FROM {$this->table} r
            INNER JOIN users u ON r.user_id = u.user_id
            WHERE {$whereClause}
            ORDER BY r.created_at DESC
        ");

        $stmt->execute([':product_id' => $productId]);
        $reviews = $stmt->fetchAll();
        
        // Parse images JSON thành array
        foreach ($reviews as &$review) {
            if (!empty($review['images'])) {
                $images = json_decode($review['images'], true);
                $review['images'] = is_array($images) ? $images : [];
            } else {
                $review['images'] = [];
            }
        }
        unset($review);
        
        return $reviews;
    }

    /**
     * Lấy đánh giá theo order_item_id (để kiểm tra đã đánh giá chưa)
     */
    public function getByOrderItem(int $orderItemId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM {$this->table}
            WHERE order_item_id = :order_item_id
            LIMIT 1
        ");

        $stmt->execute([':order_item_id' => $orderItemId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Lấy tất cả đánh giá của một đơn hàng
     */
    public function getByOrder(int $orderId): array
    {
        // Sử dụng LEFT JOIN để tránh lỗi nếu order_items không có cột id
        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                u.full_name as user_name,
                p.product_name,
                oi.variant_size,
                oi.variant_color
            FROM {$this->table} r
            INNER JOIN users u ON r.user_id = u.user_id
            LEFT JOIN order_items oi ON r.order_item_id = oi.id OR r.order_item_id = oi.order_item_id
            INNER JOIN products p ON r.product_id = p.product_id
            WHERE r.order_id = :order_id
            ORDER BY r.created_at DESC
        ");

        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả đánh giá (cho admin)
     */
    public function getAll(?string $keyword = null, ?int $productId = null, ?int $rating = null): array
    {
        $conditions = ['1=1'];
        $params = [];

        if ($keyword) {
            $conditions[] = "(r.comment LIKE :keyword OR u.full_name LIKE :keyword OR p.product_name LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }

        if ($productId) {
            $conditions[] = "r.product_id = :product_id";
            $params[':product_id'] = $productId;
        }

        if ($rating) {
            $conditions[] = "r.rating = :rating";
            $params[':rating'] = $rating;
        }

        $whereClause = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare("
            SELECT 
                r.*,
                u.full_name as user_name,
                u.email as user_email,
                p.product_name,
                p.product_id,
                oi.variant_size,
                oi.variant_color
            FROM {$this->table} r
            INNER JOIN users u ON r.user_id = u.user_id
            INNER JOIN products p ON r.product_id = p.product_id
            LEFT JOIN order_items oi ON (r.order_item_id = oi.id OR r.order_item_id = oi.order_item_id)
            WHERE {$whereClause}
            ORDER BY r.created_at DESC
        ");

        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Cập nhật reply của admin
     */
    public function updateReply(int $reviewId, string $reply): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET reply = :reply, updated_at = CURRENT_TIMESTAMP
            WHERE review_id = :review_id
        ");

        return $stmt->execute([
            ':reply' => $reply,
            ':review_id' => $reviewId,
        ]);
    }

    /**
     * Toggle ẩn/hiện đánh giá
     */
    public function toggleHidden(int $reviewId): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET is_hidden = NOT is_hidden, updated_at = CURRENT_TIMESTAMP
            WHERE review_id = :review_id
        ");

        return $stmt->execute([':review_id' => $reviewId]);
    }

    /**
     * Xóa đánh giá
     */
    public function delete(int $reviewId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE review_id = :review_id");
        return $stmt->execute([':review_id' => $reviewId]);
    }

    /**
     * Tính điểm trung bình và số lượng đánh giá của sản phẩm
     */
    public function getProductStats(int $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
            FROM {$this->table}
            WHERE product_id = :product_id AND is_hidden = 0
        ");

        $stmt->execute([':product_id' => $productId]);
        $result = $stmt->fetch();

        return [
            'total_reviews' => (int)($result['total_reviews'] ?? 0),
            'average_rating' => round((float)($result['average_rating'] ?? 0), 1),
            'rating_5' => (int)($result['rating_5'] ?? 0),
            'rating_4' => (int)($result['rating_4'] ?? 0),
            'rating_3' => (int)($result['rating_3'] ?? 0),
            'rating_2' => (int)($result['rating_2'] ?? 0),
            'rating_1' => (int)($result['rating_1'] ?? 0),
        ];
    }

    /**
     * Kiểm tra user đã đánh giá order_item này chưa
     */
    public function hasReviewed(int $orderItemId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM {$this->table}
            WHERE order_item_id = :order_item_id AND user_id = :user_id
        ");

        $stmt->execute([
            ':order_item_id' => $orderItemId,
            ':user_id' => $userId,
        ]);

        $result = $stmt->fetch();
        return (int)($result['count'] ?? 0) > 0;
    }
}

