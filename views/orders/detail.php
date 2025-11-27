<style>
    .order-detail-page {
        padding: 60px 0;
    }

    .order-summary-card,
    .order-items-card {
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .status-steps {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .status-steps li {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #d1d5db;
        margin-right: 10px;
    }

    .status-dot.active {
        background: #000;
    }
</style>

<!-- Trang chi tiết đơn hàng hiển thị cho user -->
<section class="order-detail-page">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Mã đơn: <?= htmlspecialchars($order['order_code']) ?></p>
                <h2 class="fw-bold">Chi tiết đơn hàng</h2>
                <p class="text-muted mb-0">Đặt lúc <?= isset($order['created_at']) && $order['created_at'] ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></p>
            </div>
            <a href="<?= BASE_URL ?>?action=order-history" class="btn btn-outline-dark">Quay lại danh sách</a>
        </div>

        <div class="row g-4">
            <!-- Cột trái: thông tin giao nhận + trạng thái + hủy -->
            <div class="col-lg-4">
                <div class="order-summary-card mb-4">
                    <h5 class="fw-bold mb-3">Thông tin giao hàng</h5>
                    <p class="mb-1"><?= htmlspecialchars($order['fullname']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['phone']) ?> • <?= htmlspecialchars($order['email']) ?></p>
                    <p class="mb-1"><?= htmlspecialchars($order['address']) ?></p>
                    <p class="mb-0"><?= htmlspecialchars(($order['ward'] ?? '') . ', ' . ($order['district'] ?? '') . ', ' . ($order['city'] ?? '')) ?></p>
                </div>

                <div class="order-summary-card mb-4">
                    <h5 class="fw-bold mb-3">Trạng thái đơn hàng</h5>
                    <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2 mb-3">
                        <?= OrderModel::statusLabel($order['status']) ?>
                    </span>

                    <ul class="status-steps">
                        <?php foreach (OrderModel::statuses() as $key => $label): ?>
                            <li>
                                <span class="status-dot <?= $order['status'] === $key ? 'active' : '' ?>"></span>
                                <span><?= $label ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($order['cancel_reason'])): ?>
                        <div class="alert alert-light border mt-3">
                            <strong>Lý do hủy:</strong>
                            <div><?= htmlspecialchars($order['cancel_reason']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($canCancel): ?>
                    <div class="order-summary-card">
                        <h5 class="fw-bold mb-3">Hủy đơn hàng</h5>
                        <form method="POST" action="<?= BASE_URL ?>?action=order-cancel">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Lý do (tuỳ chọn)</label>
                                <textarea class="form-control" name="reason" rows="3" placeholder="Ví dụ: Đổi ý, đặt nhầm size..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger w-100">Hủy đơn</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cột phải: danh sách sản phẩm + ghi chú -->
            <div class="col-lg-8">
                <div class="order-items-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Sản phẩm</h5>
                        <div class="text-muted">
                            <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                <div class="text-end">
                                    <div class="small text-muted">Tạm tính: <?= number_format($order['total_amount'] + $order['discount_amount'], 0, ',', '.') ?> đ</div>
                                    <?php if (!empty($order['coupon_code'])): ?>
                                        <div class="small text-success">Mã giảm giá: <?= htmlspecialchars($order['coupon_code']) ?> (<?= htmlspecialchars($order['coupon_name'] ?? '') ?>)</div>
                                        <div class="small text-success">Giảm: -<?= number_format($order['discount_amount'], 0, ',', '.') ?> đ</div>
                                    <?php endif; ?>
                                    <div class="fw-bold">Tổng cộng: <?= number_format($order['total_amount'], 0, ',', '.') ?> đ</div>
                                </div>
                            <?php else: ?>
                                <div class="text-end">Tổng cộng: <strong><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</strong></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Thuộc tính</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($item['product_name']) ?></div>
                                        </td>
                                        <td>
                                            Size: <?= htmlspecialchars($item['variant_size'] ?? '-') ?> <br>
                                            Màu: <?= htmlspecialchars($item['variant_color'] ?? '-') ?>
                                        </td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td class="text-end"><?= number_format($item['quantity'] * $item['unit_price'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (!empty($order['note'])): ?>
                    <div class="order-items-card">
                        <h6 class="fw-bold mb-2">Ghi chú của bạn</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($order['note'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

