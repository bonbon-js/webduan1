<?php
require_once PATH_MODEL . 'OrderModel.php';
?>

<div class="statistics-page">
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="page-title mb-1">Thống kê</h1>
            <p class="page-subtitle mb-0">Tổng quan hệ thống bán hàng</p>
        </div>
        <form method="GET" action="<?= BASE_URL ?>" class="d-flex flex-wrap gap-2">
            <input type="hidden" name="action" value="admin-statistics">
            <select name="preset" class="form-select">
                <option value="today" <?= ($_GET['preset'] ?? '') === 'today' ? 'selected' : '' ?>>Hôm nay</option>
                <option value="yesterday" <?= ($_GET['preset'] ?? '') === 'yesterday' ? 'selected' : '' ?>>Hôm qua</option>
                <option value="7d" <?= ($_GET['preset'] ?? '') === '7d' ? 'selected' : '' ?>>7 ngày</option>
                <option value="this_month" <?= ($_GET['preset'] ?? '') === 'this_month' ? 'selected' : '' ?>>Tháng này</option>
                <option value="last_month" <?= ($_GET['preset'] ?? '') === 'last_month' ? 'selected' : '' ?>>Tháng trước</option>
                <option value="quarter" <?= ($_GET['preset'] ?? '') === 'quarter' ? 'selected' : '' ?>>Quý hiện tại</option>
            </select>
            <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($filterFrom ?? '') ?>">
            <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($filterTo ?? '') ?>">
            <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            <a class="btn btn-outline-secondary" href="<?= BASE_URL ?>?action=admin-statistics"><i class="bi bi-x-lg"></i></a>
        </form>
    </div>

    <!-- Bộ lọc thời gian -->
    <div class="card mb-3 p-3">
        <form method="GET" action="<?= BASE_URL ?>" class="row g-3 align-items-end">
            <input type="hidden" name="action" value="admin-statistics">
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($filterFrom ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($filterTo ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <a class="btn btn-outline-secondary w-100" href="<?= BASE_URL ?>?action=admin-statistics"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Doanh thu (range)</div>
                    <div class="stat-value"><?= number_format($rangeStatsRevenue ?? 0, 0, ',', '.') ?>₫</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Đơn (range)</div>
                    <div class="stat-value"><?= number_format($rangeStatsOrders ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Đơn thành công</div>
                    <div class="stat-value"><?= number_format($chartStatusCounts['delivered'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Đơn hủy</div>
                    <div class="stat-value"><?= number_format($chartStatusCounts['cancelled'] ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Người dùng mới (tổng)</div>
                    <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Sản phẩm bán ra (top 5)</div>
                    <div class="stat-value"><?= number_format($listTopProducts ? array_sum(array_column($listTopProducts, 'qty')) : 0) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Doanh thu theo ngày (line)</h3>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Đơn hàng theo ngày (bar)</h3>
            </div>
            <div class="chart-container">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Tỉ lệ thanh toán (pie)</h3>
            </div>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Top sản phẩm (column)</h3>
            </div>
            <div class="chart-container">
                <canvas id="topProductChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="orders-section">
        <div class="section-header">
            <h2 class="section-title">Đơn hàng của tôi</h2>
            <input type="text" class="search-box" placeholder="Tìm kiếm đơn hàng....">
        </div>

        <table class="orders-table">
            <thead>
                <tr>
                    <th>ORDER ID</th>
                    <th>TÊN KHÁCH HÀNG</th>
                    <th>NGÀY</th>
                    <th>TỔNG TIỀN</th>
                    <th>TRẠNG THÁI</th>
                    <th>THAO TÁC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            Chưa có đơn hàng nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach (array_slice($orders, 0, 6) as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_code'] ?? $order['id'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($order['fullname'] ?? '-') ?></td>
                            <td><?= isset($order['created_at']) && $order['created_at'] ? date('d/m/Y', strtotime($order['created_at'])) : '-' ?></td>
                            <td><?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>₫</td>
                            <td>
                                <span class="badge bg-<?= OrderModel::statusBadge($order['status'] ?? 'confirmed') ?>">
                                    <?= OrderModel::statusLabel($order['status'] ?? 'confirmed') ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>?action=admin-orders" class="btn btn-sm btn-outline-primary">Xem</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination">
            <div class="pagination-info">
                Showing 1-<?= min(6, count($orders)) ?> of <?= count($orders) ?>
            </div>
            <div class="pagination-buttons">
                <button class="pagination-btn">Previous</button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn">Next</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    // Revenue Chart (line)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($chartDailyRevenue ?? [], 'd')) ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?= json_encode(array_map('floatval', array_column($chartDailyRevenue ?? [], 'revenue'))) ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Orders Chart (bar)
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($chartDailyOrders ?? [], 'd')) ?>,
            datasets: [{
                label: 'Đơn hàng',
                data: <?= json_encode(array_map('intval', array_column($chartDailyOrders ?? [], 'orders'))) ?>,
                backgroundColor: '#10b981',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Payment Chart (pie)
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentChart = new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($chartPayment ?? [], 'payment_method')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($chartPayment ?? [], 'orders'))) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Top products (column)
    const topProductCtx = document.getElementById('topProductChart').getContext('2d');
    const topProductChart = new Chart(topProductCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($listTopProducts ?? [], 'product_name')) ?>,
            datasets: [{
                label: 'Số lượng bán',
                data: <?= json_encode(array_map('intval', array_column($listTopProducts ?? [], 'qty'))) ?>,
                backgroundColor: '#6366f1',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
</script>

