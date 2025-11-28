<div class="reviews-header">
    <h2>
        <div class="icon-wrapper">
            <i class="bi bi-star"></i>
        </div>
        Quản lý đánh giá
    </h2>
</div>

<!-- Form tìm kiếm và lọc -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>" id="searchForm">
            <input type="hidden" name="action" value="admin-reviews">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-bold">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control" 
                               placeholder="Tên người dùng, sản phẩm hoặc bình luận..." 
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                               id="searchKeyword">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Số sao</label>
                    <select name="rating" class="form-select" id="ratingFilter">
                        <option value="">Tất cả</option>
                        <option value="5" <?= ($_GET['rating'] ?? '') === '5' ? 'selected' : '' ?>>5 sao</option>
                        <option value="4" <?= ($_GET['rating'] ?? '') === '4' ? 'selected' : '' ?>>4 sao</option>
                        <option value="3" <?= ($_GET['rating'] ?? '') === '3' ? 'selected' : '' ?>>3 sao</option>
                        <option value="2" <?= ($_GET['rating'] ?? '') === '2' ? 'selected' : '' ?>>2 sao</option>
                        <option value="1" <?= ($_GET['rating'] ?? '') === '1' ? 'selected' : '' ?>>1 sao</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Trạng thái</label>
                    <select name="status" class="form-select" id="statusFilter">
                        <option value="">Tất cả</option>
                        <option value="visible" <?= ($_GET['status'] ?? '') === 'visible' ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="hidden" <?= ($_GET['status'] ?? '') === 'hidden' ? 'selected' : '' ?>>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                        <a href="<?= BASE_URL ?>?action=admin-reviews" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bảng đánh giá -->
<div class="card">
    <div class="card-body">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có đánh giá nào.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Sản phẩm</th>
                            <th>Đánh giá</th>
                            <th>Bình luận</th>
                            <th>Phản hồi</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr class="<?= $review['is_hidden'] ? 'table-secondary' : '' ?>">
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($review['user_name'] ?? 'N/A') ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($review['user_email'] ?? '') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($review['product_name'] ?? 'N/A') ?></strong>
                                        <?php if (!empty($review['variant_size']) || !empty($review['variant_color'])): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php if (!empty($review['variant_size'])): ?>
                                                    Size: <?= htmlspecialchars($review['variant_size']) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($review['variant_color'])): ?>
                                                    <?= !empty($review['variant_size']) ? ', ' : '' ?>Màu: <?= htmlspecialchars($review['variant_color']) ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="rating-display">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill text-warning' : '' ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-1"><?= $review['rating'] ?>/5</span>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($review['comment'])): ?>
                                        <div class="comment-preview" style="max-width: 200px;">
                                            <?= nl2br(htmlspecialchars(mb_substr($review['comment'], 0, 100))) ?>
                                            <?= mb_strlen($review['comment']) > 100 ? '...' : '' ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Không có bình luận</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($review['reply'])): ?>
                                        <div class="reply-preview" style="max-width: 200px;">
                                            <?= nl2br(htmlspecialchars(mb_substr($review['reply'], 0, 100))) ?>
                                            <?= mb_strlen($review['reply']) > 100 ? '...' : '' ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa phản hồi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($review['is_hidden']): ?>
                                        <span class="badge bg-secondary">Đã ẩn</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Hiển thị</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-primary" 
                                                onclick="openReplyModal(<?= $review['review_id'] ?>, '<?= htmlspecialchars(addslashes($review['reply'] ?? '')) ?>')"
                                                title="Phản hồi">
                                            <i class="bi bi-reply"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-<?= $review['is_hidden'] ? 'success' : 'warning' ?>" 
                                                onclick="toggleHidden(<?= $review['review_id'] ?>, <?= $review['is_hidden'] ? 0 : 1 ?>)"
                                                title="<?= $review['is_hidden'] ? 'Hiển thị' : 'Ẩn' ?>">
                                            <i class="bi bi-eye<?= $review['is_hidden'] ? '' : '-slash' ?>"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                onclick="deleteReview(<?= $review['review_id'] ?>)"
                                                title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Phản hồi -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Phản hồi đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <input type="hidden" id="replyReviewId" name="review_id">
                    <div class="mb-3">
                        <label class="form-label">Nội dung phản hồi</label>
                        <textarea class="form-control" id="replyContent" name="reply" rows="4" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="submitReply()">Gửi phản hồi</button>
            </div>
        </div>
    </div>
</div>

<script>
let replyModal;

document.addEventListener('DOMContentLoaded', function() {
    replyModal = new bootstrap.Modal(document.getElementById('replyModal'));
});

function openReplyModal(reviewId, currentReply) {
    document.getElementById('replyReviewId').value = reviewId;
    document.getElementById('replyContent').value = currentReply || '';
    replyModal.show();
}

function submitReply() {
    const reviewId = document.getElementById('replyReviewId').value;
    const reply = document.getElementById('replyContent').value.trim();
    
    if (!reply) {
        alert('Vui lòng nhập nội dung phản hồi');
        return;
    }
    
    fetch('<?= BASE_URL ?>?action=admin-review-reply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            review_id: parseInt(reviewId),
            reply: reply
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Phản hồi thành công!');
            replyModal.hide();
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

function toggleHidden(reviewId, currentStatus) {
    if (!confirm('Bạn có chắc chắn muốn ' + (currentStatus ? 'hiển thị' : 'ẩn') + ' đánh giá này?')) {
        return;
    }
    
    fetch('<?= BASE_URL ?>?action=admin-review-toggle-hidden', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            review_id: parseInt(reviewId)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

function deleteReview(reviewId) {
    if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này? Hành động này không thể hoàn tác.')) {
        return;
    }
    
    fetch('<?= BASE_URL ?>?action=admin-review-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            review_id: parseInt(reviewId)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Xóa thành công!');
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}
</script>

