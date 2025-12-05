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

        <div class="admin-card mb-4">
            <!-- Form lọc đơn hàng theo từ khóa/trạng thái -->
            <form class="row g-3 align-items-end" method="GET">
                <input type="hidden" name="action" value="admin-orders">
                <div class="col-md-6">
                    <label class="form-label small text-uppercase">Từ khóa</label>
                    <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Mã đơn, tên khách, số điện thoại...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-uppercase">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="">Tất cả</option>
                        <?php foreach ($statusMap as $key => $label): ?>
                            <option value="<?= $key ?>" <?= ($status ?? '') === $key ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100">Lọc</button>
                </div>
            </form>
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
                            <th>Trạng thái</th>
                            <th>Cập nhật</th>
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
                                        <span class="badge bg-<?= OrderModel::statusBadge($order['status']) ?> px-3 py-2">
                                            <?= OrderModel::statusLabel($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="<?= BASE_URL ?>?action=admin-order-update">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select class="form-select form-select-sm mb-2" name="status">
                                                <?php foreach ($statusMap as $key => $label): ?>
                                                    <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-sm btn-dark w-100" type="submit">Cập nhật</button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Xem</a>
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

