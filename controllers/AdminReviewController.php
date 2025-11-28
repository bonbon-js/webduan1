<?php

require_once PATH_MODEL . 'ReviewModel.php';

class AdminReviewController
{
    private ReviewModel $reviewModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Trang quản lý đánh giá
     */
    public function index(): void
    {
        $this->requireAdmin();

        $keyword = $_GET['keyword'] ?? null;
        $productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;
        $rating = isset($_GET['rating']) ? (int)$_GET['rating'] : null;

        $reviews = $this->reviewModel->getAll($keyword, $productId, $rating);

        $view = 'admin/reviews/index';
        $title = 'Quản lý đánh giá';
        $logoUrl = BASE_URL . 'assets/images/logo.png';

        require_once PATH_VIEW . 'admin/layout.php';
    }

    /**
     * Toggle ẩn/hiện đánh giá
     */
    public function toggleHidden(): void
    {
        $this->requireAdmin();

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $reviewId = (int)($data['review_id'] ?? 0);

        if (!$reviewId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu review_id']);
            exit;
        }

        try {
            $success = $this->reviewModel->toggleHidden($reviewId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Reply đánh giá
     */
    public function reply(): void
    {
        $this->requireAdmin();

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $reviewId = (int)($data['review_id'] ?? 0);
        $reply = trim($data['reply'] ?? '');

        if (!$reviewId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu review_id']);
            exit;
        }

        if (empty($reply)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung phản hồi']);
            exit;
        }

        try {
            $success = $this->reviewModel->updateReply($reviewId, $reply);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Phản hồi thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể phản hồi']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa đánh giá
     */
    public function delete(): void
    {
        $this->requireAdmin();

        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $reviewId = (int)($data['review_id'] ?? 0);

        if (!$reviewId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu review_id']);
            exit;
        }

        try {
            $success = $this->reviewModel->delete($reviewId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Xóa thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            set_flash('danger', 'Bạn không có quyền truy cập.');
            header('Location: ' . BASE_URL);
            exit;
        }
    }
}

