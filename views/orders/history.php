<style>
    .orders-page {
        padding: 60px 0;
        min-height: 70vh;
    }

    .order-card {
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .order-card:hover {
        border-color: #000;
        transition: border-color 0.2s ease;
    }

    .order-code {
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .order-meta {
        font-size: 0.9rem;
        color: #666;
    }

    .order-actions .btn {
        min-width: 160px;
    }
</style>

<!-- Trang lịch sử đơn hàng của user -->
<section class="orders-page">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">BonBonwear</p>
                <h2 class="fw-bold">Đơn hàng của tôi</h2>
            </div>
            <a href="<?= BASE_URL ?>" class="btn btn-outline-dark">Tiếp tục mua sắm</a>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Trường hợp chưa có đơn nào -->
            <div class="text-center py-5 bg-light rounded-3">
                <p class="mb-3">Bạn chưa có đơn hàng nào.</p>
                <a href="<?= BASE_URL ?>" class="btn btn-dark">Khám phá sản phẩm</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <!-- Thông tin chính của từng đơn -->
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <div class="order-code text-uppercase small text-muted mb-1">Mã đơn</div>
                            <h5 class="mb-0"><?= htmlspecialchars($order['order_code']) ?></h5>
                            <div class="order-meta">Ngày đặt: <?= isset($order['created_at']) && $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="order-code text-uppercase small text-muted mb-1">Trạng thái</div>
                            <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2">
                                <?= OrderModel::statusLabel($order['status']) ?>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <div class="order-code text-uppercase small text-muted mb-1">Giá trị</div>
                            <h5 class="mb-0"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</h5>
                        </div>
                        <div class="col-md-3 order-actions text-md-end">
                            <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>" class="btn btn-dark">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

