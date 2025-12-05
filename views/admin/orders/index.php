<!-- Trang quản trị: xem và cập nhật trạng thái đơn -->
<style>
/* Loại bỏ padding để rộng đầy đủ sang bên phải */
.admin-content {
    padding: 0 !important;
    margin-right: 0 !important;
    width: calc(100% - 260px) !important;
}
.admin-orders-page {
    padding: 2rem !important;
    padding-right: 0 !important;
    width: 100% !important;
    margin: 0 !important;
    margin-right: 0 !important;
    box-sizing: border-box;
}
.admin-orders-page .container {
    max-width: 100% !important;
    width: 100% !important;
    padding-left: 2rem !important;
    padding-right: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    box-sizing: border-box;
}
.admin-card {
    margin-right: 0 !important;
}
</style>
<section class="admin-orders-page">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Bảng điều khiển</p>
                <h2 class="fw-bold mb-0">Quản lý đơn hàng</h2>
            </div>
            <a href="<?= BASE_URL ?>" class="btn btn-outline-dark">Xem cửa hàng</a>
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

