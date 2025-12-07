<div class="admin-page-header mb-4">
    <div>
        <p class="text-uppercase text-muted mb-1 small">Đơn hàng</p>
        <h2 class="fw-bold mb-0">Chi tiết đơn #<?= htmlspecialchars($order['order_code'] ?? $order['id']) ?></h2>
        <?php if (!empty($order['created_at'])): ?>
            <div class="text-muted small">Đặt lúc <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
        <?php endif; ?>
    </div>
    <div class="admin-page-actions">
        <a href="<?= BASE_URL ?>?action=admin-orders" class="btn btn-light-soft">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Khách hàng</h5>
                <div class="mb-2"><strong>Tên:</strong> <?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></div>
                <div class="mb-2"><strong>Điện thoại:</strong> <?= htmlspecialchars($order['phone'] ?? 'N/A') ?></div>
                <div class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'N/A') ?></div>
                <div class="mb-0"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address'] ?? '') ?></div>
                <div class="text-muted small"><?= htmlspecialchars(($order['ward'] ?? '') . ', ' . ($order['district'] ?? '') . ', ' . ($order['city'] ?? '')) ?></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Trạng thái</h5>
                <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2">
                    <?= OrderModel::statusLabel($order['status']) ?>
                </span>
                <div class="mt-3">
                    <strong>Tổng tiền:</strong> <?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?> đ
                </div>
                <?php if (!empty($order['coupon_code'])): ?>
                    <div class="mt-2 small text-muted">Mã giảm giá: <?= htmlspecialchars($order['coupon_code']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Sản phẩm</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Thuộc tính</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] ?? [] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" class="me-2" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                            <?php else: ?>
                                                <div class="me-2 placeholder-60-box bg-light text-muted d-flex align-items-center justify-content-center" style="width:60px;height:60px;border-radius:6px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['product_name'] ?? '') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small">
                                        <div><strong>Size:</strong> <?= htmlspecialchars($item['variant_size'] ?? '-') ?></div>
                                        <div><strong>Màu:</strong> <?= htmlspecialchars($item['variant_color'] ?? '-') ?></div>
                                    </td>
                                    <td class="text-center"><?= (int)($item['quantity'] ?? 0) ?></td>
                                    <td class="text-end">
                                        <div class="fw-semibold"><?= number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 0, ',', '.') ?> đ</div>
                                        <div class="text-muted small"><?= number_format($item['unit_price'] ?? 0, 0, ',', '.') ?> đ/SP</div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

