<style>
    .admin-dashboard {
        padding: 2rem;
        background: #f5f5f5;
        min-height: 100vh;
    }
    
    .admin-banner {
        background: #1e3a8a;
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .admin-banner-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .admin-banner-icon {
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1e3a8a;
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .admin-banner-title {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    
    .admin-banner-desc {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
        margin-top: 0.5rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #1e293b;
        margin: 0;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-icon.blue {
        background: #3b82f6;
    }
    
    .stat-icon.green {
        background: #10b981;
    }
    
    .stat-icon.red {
        background: #ef4444;
    }
    
    .stat-icon.orange {
        background: #f97316;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }
    
    .management-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    
    .management-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .management-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        color: inherit;
    }
    
    .management-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .management-icon {
        width: 60px;
        height: 60px;
        background: #f97316;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    
    .management-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1e293b;
        margin: 0;
    }
    
    .management-desc {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
        line-height: 1.6;
    }
    
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 768px) {
        .admin-dashboard {
            padding: 1rem;
        }
        
        .admin-banner {
            flex-direction: column;
            text-align: center;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .management-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard">
    <!-- Admin Panel Banner -->
    <div class="admin-banner">
        <div class="admin-banner-left">
            <div class="admin-banner-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <div>
                <h1 class="admin-banner-title">Trung tâm quản trị</h1>
                <p class="admin-banner-desc">Trung tâm quản trị hệ thống - Quản lý tất cả các chức năng của website</p>
            </div>
        </div>
    </div>

    <!-- Statistical Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <p class="stat-number"><?= number_format($stats['total_users']) ?></p>
                    <p class="stat-label">Tổng người dùng</p>
                </div>
                <div class="stat-icon blue">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <p class="stat-number"><?= number_format($stats['total_orders']) ?></p>
                    <p class="stat-label">Tổng đơn hàng</p>
                </div>
                <div class="stat-icon green">
                    <i class="bi bi-cart"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <p class="stat-number"><?= number_format($stats['total_admins']) ?></p>
                    <p class="stat-label">Quản trị viên</p>
                </div>
                <div class="stat-icon red">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div>
                    <p class="stat-number"><?= number_format($stats['total_customers']) ?></p>
                    <p class="stat-label">Khách hàng</p>
                </div>
                <div class="stat-icon orange">
                    <i class="bi bi-person"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Quick-Link Cards -->
    <div class="management-grid">
        <a href="<?= BASE_URL ?>?action=admin-users" class="management-card">
            <div class="management-card-header">
                <div class="management-icon">
                    <i class="bi bi-people"></i>
                </div>
                <h2 class="management-title">Quản lý người dùng</h2>
            </div>
            <p class="management-desc">Xem, chỉnh sửa, xóa và phân quyền cho người dùng trong hệ thống</p>
        </a>

        <a href="<?= BASE_URL ?>?action=admin-orders" class="management-card">
            <div class="management-card-header">
                <div class="management-icon">
                    <i class="bi bi-cart"></i>
                </div>
                <h2 class="management-title">Quản lý đơn hàng</h2>
            </div>
            <p class="management-desc">Xem tất cả đơn hàng, cập nhật trạng thái và quản lý giao hàng</p>
        </a>
    </div>
</div>

