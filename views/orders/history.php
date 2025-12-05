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

        <!-- Tabs trạng thái đơn hàng -->
        <div class="order-status-tabs mb-4">
            <div class="status-tabs-wrapper">
                <a href="<?= BASE_URL ?>?action=order-history" 
                   class="status-tab <?= !isset($_GET['status']) ? 'active' : '' ?>">
                    Tất cả
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_CONFIRMED ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_CONFIRMED ? 'active' : '' ?>">
                    Xác nhận đơn hàng
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_PREPARING ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_PREPARING ? 'active' : '' ?>">
                    Đang chuẩn bị đơn hàng
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_SHIPPED ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_SHIPPED ? 'active' : '' ?>">
                    Đã giao cho đơn vị vận chuyển
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_OUT_OF_STOCK ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_OUT_OF_STOCK ? 'active' : '' ?>">
                    Hết hàng
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_ON_THE_WAY ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_ON_THE_WAY ? 'active' : '' ?>">
                    Đang trên đường giao
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_DELIVERED ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_DELIVERED ? 'active' : '' ?>">
                    Đã giao hàng thành công
                </a>
                <a href="<?= BASE_URL ?>?action=order-history&status=<?= OrderModel::STATUS_CANCELLED ?>" 
                   class="status-tab <?= ($_GET['status'] ?? '') === OrderModel::STATUS_CANCELLED ? 'active' : '' ?>">
                    Đã hủy
                </a>
            </div>
        </div>

        <?php if (empty($orders)): ?>
            <!-- Trường hợp chưa có đơn nào -->
            <div class="text-center py-5 bg-light rounded-3">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p class="mb-3 fs-5">Bạn chưa có đơn hàng nào.</p>
                <a href="<?= BASE_URL ?>" class="btn btn-dark">
                    <i class="bi bi-bag me-2"></i>Khám phá sản phẩm
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $orderItems = $order['items'] ?? [];
                $itemCount = count($orderItems);
            ?>
                <div class="order-card mb-4 border rounded p-4">
                    <!-- Thông tin chính của từng đơn -->
                    <div class="row g-3 align-items-start mb-3">
                        <div class="col-md-3">
                            <div class="order-code text-uppercase small text-muted mb-1">Mã đơn</div>
                            <h5 class="mb-1"><?= htmlspecialchars($order['order_code'] ?? '#' . ($order['id'] ?? $order['order_id'] ?? '')) ?></h5>
                            <div class="order-meta small text-muted">Ngày đặt: <?= isset($order['created_at']) && $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></div>
                        </div>
                        <div class="col-md-2">
                            <div class="order-code text-uppercase small text-muted mb-1">Trạng thái</div>
                            <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2">
                                <?= OrderModel::statusLabel($order['status']) ?>
                            </span>
                        </div>
                        <div class="col-md-2">
                            <div class="order-code text-uppercase small text-muted mb-1">Số lượng</div>
                            <h6 class="mb-0"><?= $itemCount ?> sản phẩm</h6>
                        </div>
                        <div class="col-md-2">
                            <div class="order-code text-uppercase small text-muted mb-1">Tổng tiền</div>
                            <h5 class="mb-0 text-primary"><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</h5>
                        </div>
                        <div class="col-md-3 order-actions text-md-end">
                            <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>" class="btn btn-dark mb-2 d-block">
                                Xem chi tiết
                            </a>
                            <?php if ($order['status'] === OrderModel::STATUS_DELIVERED): ?>
                                <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>&review=true" class="btn btn-outline-dark mb-2 d-block">
                                    <i class="bi bi-star-fill"></i> Đánh giá ngay
                                </a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>" class="btn btn-outline-dark d-block">
                                <i class="bi bi-telephone-fill"></i> Liên hệ người bán
                            </a>
                        </div>
                    </div>
                    
                    <!-- Danh sách sản phẩm trong đơn hàng -->
                    <?php if (!empty($orderItems)): ?>
                        <div class="order-items-preview border-top pt-3">
                            <h6 class="mb-3 fw-bold">
                                <i class="bi bi-box-seam me-2"></i>Sản phẩm trong đơn hàng
                            </h6>
                            <div class="row g-3">
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center border rounded p-2 bg-light">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                     class="me-2" 
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <div class="me-2 bg-white d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; border-radius: 4px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <div class="fw-semibold small text-truncate" title="<?= htmlspecialchars($item['product_name']) ?>">
                                                    <?= htmlspecialchars($item['product_name']) ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?php if (!empty($item['variant_size']) || !empty($item['variant_color'])): ?>
                                                        <?php if (!empty($item['variant_size'])): ?>
                                                            Size: <?= htmlspecialchars($item['variant_size']) ?>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['variant_color'])): ?>
                                                            <?= !empty($item['variant_size']) ? ', ' : '' ?>Màu: <?= htmlspecialchars($item['variant_color']) ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="small">
                                                    <strong><?= number_format($item['unit_price'], 0, ',', '.') ?> đ</strong>
                                                    <span class="text-muted"> x <?= $item['quantity'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

