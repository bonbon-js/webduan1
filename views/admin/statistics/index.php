<?php
require_once PATH_MODEL . 'OrderModel.php';
?>

<div class="statistics-page">
    <div class="page-header">
        <h1 class="page-title">Thống kê</h1>
        <p class="page-subtitle">Xem tổng quan về hoạt động của hệ thống</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Tổng đơn hàng</div>
                    <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
                    <div class="stat-change down">
                        <i class="bi bi-arrow-down"></i>
                        <span>0.1%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value"><?= number_format($stats['total_revenue'], 0, ',', '.') ?>₫</div>
                    <div class="stat-change up">
                        <i class="bi bi-arrow-up"></i>
                        <span>+0.2%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Tổng người dùng</div>
                    <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
                    <div class="stat-change down">
                        <i class="bi bi-arrow-down"></i>
                        <span>0.4%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <div class="stat-label">Sản phẩm bán chạy</div>
                    <div class="stat-value fs-125"><?= htmlspecialchars($stats['best_selling']) ?></div>
                    <div class="stat-change up">
                        <i class="bi bi-arrow-up"></i>
                        <span>+0.2%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Doanh thu theo tháng</h3>
                <div>
                    <div class="chart-value"><?= number_format($stats['total_revenue'], 0, ',', '.') ?>₫</div>
                    <div class="stat-change down">
                        <i class="bi bi-arrow-down"></i>
                        <span>1.5%</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title">Sản phẩm</h3>
                <div>
                    <div class="chart-value"><?= number_format($stats['total_products'] ?? 0) ?> Sản Phẩm</div>
                    <div class="stat-change down">
                        <i class="bi bi-arrow-down"></i>
                        <span>1.5%</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="productsChart"></canvas>
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
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlyRevenue['labels'] ?? []) ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?= json_encode($monthlyRevenue['data'] ?? []) ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
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

    // Products Chart
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    const productsChart = new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($monthlyProducts['labels'] ?? []) ?>,
            datasets: [{
                label: 'Sản phẩm',
                data: <?= json_encode($monthlyProducts['data'] ?? []) ?>,
                backgroundColor: '#10b981',
                borderRadius: 4
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
                        stepSize: 0.1
                    }
                }
            }
        }
    });
</script>

