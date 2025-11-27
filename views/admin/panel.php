<style>
    .admin-panel {
        min-height: calc(100vh - 120px);
        padding: 40px 20px;
        background: #f8f9fa;
    }

    .admin-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #fff;
        padding: 40px;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
    }

    .admin-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .admin-header p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-card-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card-icon.users {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
    }

    .stat-card-icon.orders {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }

    .stat-card-icon.admins {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }

    .stat-card-icon.customers {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
    }

    .stat-card-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .stat-card-label {
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
    }

    .admin-features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .feature-card {
        background: #fff;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        text-decoration: none;
        color: inherit;
    }

    .feature-card-icon {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 20px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .feature-card p {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
        margin: 0;
    }

    .permissions-list {
        background: #fff;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-top: 30px;
    }

    .permissions-list h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .permission-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .permission-item i {
        color: #10b981;
        font-size: 1.25rem;
    }

    .permission-item span {
        color: #0f172a;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .admin-header h1 {
            font-size: 2rem;
        }

        .stats-grid,
        .admin-features {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="admin-panel">
    <div class="container">
        <div class="admin-header">
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                <?php 
                $logoPath = PATH_ROOT . 'assets/images/logo.png';
                if (file_exists($logoPath)): 
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSrc = 'data:image/png;base64,' . $logoData;
                ?>
                    <img src="<?= $logoSrc ?>" alt="BonBonwear" style="height: 60px; width: auto;">
                <?php else: ?>
                    <span style="font-size: 1.5rem; font-weight: 700;">BONBONWEAR</span>
                <?php endif; ?>
            </div>
            <h1>
                <i class="bi bi-shield-check"></i>
                Admin Panel
            </h1>
            <p>Trung tâm quản trị hệ thống - Quản lý tất cả các chức năng của website</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-value"><?= $stats['total_users'] ?? 0 ?></div>
                        <div class="stat-card-label">Tổng người dùng</div>
                    </div>
                    <div class="stat-card-icon users">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-value"><?= $stats['total_orders'] ?? 0 ?></div>
                        <div class="stat-card-label">Tổng đơn hàng</div>
                    </div>
                    <div class="stat-card-icon orders">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-value"><?= $stats['admin_count'] ?? 0 ?></div>
                        <div class="stat-card-label">Quản trị viên</div>
                    </div>
                    <div class="stat-card-icon admins">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-value"><?= $stats['customer_count'] ?? 0 ?></div>
                        <div class="stat-card-label">Khách hàng</div>
                    </div>
                    <div class="stat-card-icon customers">
                        <i class="bi bi-person-badge"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-features">
            <a href="<?= BASE_URL ?>?action=admin-stats" class="feature-card">
                <div class="feature-card-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3>Quản lý người dùng</h3>
                <p>Xem, chỉnh sửa, xóa và phân quyền cho người dùng trong hệ thống</p>
            </a>

            <a href="<?= BASE_URL ?>?action=admin-orders" class="feature-card">
                <div class="feature-card-icon">
                    <i class="bi bi-cart-fill"></i>
                </div>
                <h3>Quản lý đơn hàng</h3>
                <p>Xem tất cả đơn hàng, cập nhật trạng thái và quản lý giao hàng</p>
            </a>
        </div>

        <div class="permissions-list">
            <h2>
                <i class="bi bi-key-fill"></i>
                Quyền quản trị viên
            </h2>
            <div class="permissions-grid">
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Xem tất cả người dùng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Thay đổi quyền người dùng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Xóa tài khoản người dùng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Xem tất cả đơn hàng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Cập nhật trạng thái đơn hàng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Xem chi tiết đơn hàng</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Truy cập Admin Panel</span>
                </div>
                <div class="permission-item">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Quản lý hệ thống</span>
                </div>
            </div>
        </div>
    </div>
</section>

