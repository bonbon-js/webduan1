<style>
    .stats-page {
        min-height: calc(100vh - 120px);
        padding: 30px 100px;
        background: #f5f7fa;
    }

    .stats-page .container-fluid {
        max-width: 100%;
        padding: 0;
    }

    .stats-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stats-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .stats-header p {
        color: #64748b;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #3b82f6;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        transition: background 0.2s;
    }

    .btn-back:hover {
        background: #2563eb;
        color: #fff;
        text-decoration: none;
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .overview-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .overview-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .overview-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .overview-card-title {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
        margin: 0;
    }

    .overview-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 8px 0;
    }

    .overview-card-change {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .overview-card-change.positive {
        color: #10b981;
    }

    .overview-card-change.negative {
        color: #ef4444;
    }

    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .chart-card-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .orders-table-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .orders-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .orders-table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }

    .search-box {
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        width: 250px;
    }

    .search-box:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table thead {
        background: #f8f9fa;
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #64748b;
        font-size: 0.875rem;
        text-transform: uppercase;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 16px 12px;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge.success {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.info {
        background: #dbeafe;
        color: #1e40af;
    }

    .btn-view {
        padding: 6px 16px;
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: background 0.2s;
    }

    .btn-view:hover {
        background: #2563eb;
        color: #fff;
        text-decoration: none;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-info {
        color: #64748b;
        font-size: 0.875rem;
    }

    .pagination-controls {
        display: flex;
        gap: 8px;
    }

    .pagination-btn {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.875rem;
        color: #1e293b;
        transition: all 0.2s;
    }

    .pagination-btn:hover {
        background: #f8f9fa;
        border-color: #cbd5e1;
    }

    .pagination-btn.active {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
    }

    @media (max-width: 768px) {
        .charts-grid {
            grid-template-columns: 1fr;
        }

        .overview-grid {
            grid-template-columns: 1fr;
        }

        .orders-table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .search-box {
            width: 100%;
        }
    }
</style>

<section class="stats-page">
    <div class="container-fluid">
        <div class="stats-header">
            <div>
                <h1>Thống kê</h1>
                <p>Xem tổng quan về hoạt động của hệ thống</p>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-users" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Quay lại quản lý người dùng
            </a>
        </div>

        <!-- Overview Cards -->
        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <p class="overview-card-title">Tổng đơn hàng</p>
                    <span class="overview-card-change <?= ($stats['order_change'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                        <i class="bi bi-<?= ($stats['order_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= number_format(abs($stats['order_change'] ?? 0), 1) ?>%
                    </span>
                </div>
                <div class="overview-card-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <p class="overview-card-title">Tổng doanh thu</p>
                    <span class="overview-card-change <?= ($stats['revenue_change'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                        <i class="bi bi-<?= ($stats['revenue_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= number_format(abs($stats['revenue_change'] ?? 0), 1) ?>%
                    </span>
                </div>
                <div class="overview-card-value"><?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?>₫</div>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <p class="overview-card-title">Tổng người dùng</p>
                    <span class="overview-card-change <?= ($stats['user_change'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                        <i class="bi bi-<?= ($stats['user_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= number_format(abs($stats['user_change'] ?? 0), 1) ?>%
                    </span>
                </div>
                <div class="overview-card-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <p class="overview-card-title">Sản phẩm bán chạy</p>
                    <span class="overview-card-change <?= ($stats['product_change'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                        <i class="bi bi-<?= ($stats['product_change'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                        <?= number_format(abs($stats['product_change'] ?? 0), 1) ?>%
                    </span>
                </div>
                <div class="overview-card-value" style="font-size: 1.25rem;"><?= htmlspecialchars($stats['best_selling_product'] ?? 'Chưa có') ?></div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Doanh thu theo tháng</h3>
                    <div>
                        <div class="chart-card-value"><?= number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') ?>₫</div>
                        <span class="overview-card-change positive" style="font-size: 0.75rem;">
                            <i class="bi bi-arrow-up"></i> 1.5%
                        </span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-card-header">
                    <h3 class="chart-card-title">Sản phẩm</h3>
                    <div>
                        <div class="chart-card-value"><?= count($stats['product_stats'] ?? []) ?> Sản Phẩm</div>
                        <span class="overview-card-change positive" style="font-size: 0.75rem;">
                            <i class="bi bi-arrow-up"></i> 1.5%
                        </span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="productChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-card">
            <div class="orders-table-header">
                <h2 class="orders-table-title">Đơn hàng của tôi</h2>
                <input type="text" class="search-box" placeholder="Tìm kiếm đơn hàng...">
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Tên khách hàng</th>
                            <th>Ngày</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stats['recent_orders'])): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                    Chưa có đơn hàng nào
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($stats['recent_orders'], 0, 6) as $order): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($order['order_code'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($order['fullname'] ?? 'N/A') ?></td>
                                    <td><?= !empty($order['created_at']) ? date('Y-m-d', strtotime($order['created_at'])) : date('Y-m-d') ?></td>
                                    <td><?= number_format($order['total_amount'] ?? 0, 0, ',', '.') ?>₫</td>
                                    <td>
                                        <?php
                                        $status = $order['status'] ?? '';
                                        $statusLabel = OrderModel::statusLabel($status);
                                        $statusBadge = OrderModel::statusBadge($status);
                                        $badgeClass = match($statusBadge) {
                                            'success' => 'success',
                                            'danger' => 'danger',
                                            'warning' => 'warning',
                                            'info' => 'info',
                                            default => 'info'
                                        };
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=order-detail&id=<?= $order['id'] ?? ($order['order_id'] ?? '') ?>" class="btn-view">Xem chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <div class="pagination-info">Showing 1-6 of <?= count($stats['recent_orders'] ?? []) ?></div>
                <div class="pagination-controls">
                    <button class="pagination-btn">Previous</button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn">Next</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    const revenueData = <?= json_encode(array_column($stats['revenue_by_month'] ?? [], 'revenue')) ?>;
    const revenueLabels = <?= json_encode(array_map(function($m) { return date('M', strtotime($m['month'] . '-01')); }, $stats['revenue_by_month'] ?? [])) ?>;

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Doanh thu',
                data: revenueData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Product Chart
    const productCtx = document.getElementById('productChart');
    const productData = <?= json_encode(array_column($stats['product_stats'] ?? [], 'total_quantity')) ?>;
    const productLabels = <?= json_encode(array_column($stats['product_stats'] ?? [], 'product_name')) ?>;

    new Chart(productCtx, {
        type: 'bar',
        data: {
            labels: productLabels,
            datasets: [{
                label: 'Số lượng',
                data: productData,
                backgroundColor: productData.map((_, i) => i === productData.length - 1 ? '#3b82f6' : '#e2e8f0'),
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

