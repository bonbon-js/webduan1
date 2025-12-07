<section class="admin-orders-page">
    <div class="container">
        <div class="admin-page-header mb-4">
            <div class="title-wrap">
                <p class="text-uppercase mb-1 small">Bảng điều khiển</p>
                <h2 class="fw-bold mb-0">Quản lý đơn hàng</h2>
            </div>
            <div class="admin-page-actions">
                <a href="<?= BASE_URL ?>" class="btn btn-light-soft">Xem cửa hàng</a>
            </div>
        </div>

        <!-- Form tìm kiếm và lọc -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?= BASE_URL ?>" id="searchForm">
                    <input type="hidden" name="action" value="admin-orders">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase fw-bold">Tìm kiếm</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" 
                                       name="keyword" 
                                       class="form-control" 
                                       placeholder="Mã đơn, tên khách, số điện thoại..." 
                                       value="<?= htmlspecialchars($keyword ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-uppercase fw-bold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                <?php foreach ($statusMap as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($status ?? '') === $key ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                                <a href="<?= BASE_URL ?>?action=admin-orders" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <!-- Bảng danh sách đơn hàng -->
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th>Giá trị</th>
                            <th>Trạng thái / Cập nhật</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Không có đơn hàng nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($order['order_code']) ?></div>
                                        <?php if (isset($order['created_at']) && $order['created_at']): ?>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($order['fullname']) ?></td>
                                    <td>
                                        <div><?= htmlspecialchars($order['phone']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small>
                                    </td>
                                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <?php
                                            $pm = strtolower($order['payment_method'] ?? 'cod');
                                            $status = $order['status'];
                                        ?>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="badge bg-<?= OrderModel::statusBadge($status) ?> px-3 py-2">
                                                    <?= OrderModel::statusLabel($status) ?>
                                                </span>
                                                <?php 
                                                    $isPaid = in_array($status, [
                                                        OrderModel::STATUS_PAID,
                                                        OrderModel::STATUS_PENDING,
                                                        OrderModel::STATUS_TO_SHIP,
                                                        OrderModel::STATUS_DELIVERED,
                                                        OrderModel::STATUS_COMPLETED,
                                                    ], true) && ($order['payment_method'] ?? '') === 'banking';
                                                ?>
                                                <?php if ($isPaid): ?>
                                                    <span class="badge bg-success px-2 py-1">Đã thanh toán</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($status === OrderModel::STATUS_CANCEL_REQUEST): ?>
                                                <form method="POST" action="<?= BASE_URL ?>?action=admin-order-approve-cancel" class="mb-0">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button class="btn btn-sm btn-outline-danger w-100">Xác nhận hủy</button>
                                                </form>
                                            <?php elseif ($status === OrderModel::STATUS_PENDING): ?>
                                                <form method="POST" action="<?= BASE_URL ?>?action=admin-order-confirm" class="mb-0">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button class="btn btn-sm btn-dark w-100">Xác nhận đơn</button>
                                                </form>
                                            <?php elseif ($status === OrderModel::STATUS_TO_SHIP): ?>
                                                <form method="POST" action="<?= BASE_URL ?>?action=admin-order-delivered" class="mb-0">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button class="btn btn-sm btn-outline-primary w-100">Xác nhận đã giao</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>?action=admin-order-detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary">Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

