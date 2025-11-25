<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - BonBonWear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
        }
        .admin-header {
            background: #1e293b;
            color: #fff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-sidebar {
            width: 260px;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            min-height: calc(100vh - 60px);
            padding: 2rem 0;
            position: fixed;
            left: 0;
            top: 60px;
        }
        .admin-sidebar .brand {
            padding: 0 2rem 2rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        .admin-sidebar .nav-item {
            padding: 0.75rem 2rem;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }
        .admin-sidebar .nav-item:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .admin-sidebar .nav-item.active {
            background: #e2e8f0;
            color: #1e293b;
            font-weight: 600;
        }
        .admin-sidebar .nav-item i {
            width: 20px;
        }
        .admin-content {
            margin-left: 260px;
            padding: 2rem;
        }
        .admin-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .admin-table table {
            margin: 0;
        }
        .admin-table thead {
            background: #f8fafc;
        }
        .admin-table th {
            font-weight: 600;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem;
        }
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .role-badge.user {
            background: #3b82f6;
            color: #fff;
        }
        .role-badge.admin {
            background: #ef4444;
            color: #fff;
        }
        .btn-delete {
            background: #ef4444;
            color: #fff;
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div>
            <h5 class="mb-0">Bảng điều khiển quản trị</h5>
        </div>
        <div class="dropdown">
            <button class="btn btn-link text-white text-decoration-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> Administrator
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>">Xem website</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=logout">Đăng xuất</a></li>
            </ul>
        </div>
    </header>

    <div class="d-flex">
        <aside class="admin-sidebar">
            <div class="brand">BonBon</div>
            <nav>
                <a href="#" class="nav-item">
                    <i class="bi bi-gear"></i>
                    <span>Quản lý danh mục</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-briefcase"></i>
                    <span>Quản lý sản phẩm</span>
                </a>
                <a href="<?= BASE_URL ?>?action=admin-users" class="nav-item active">
                    <i class="bi bi-people"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-chat-dots"></i>
                    <span>Quản lý bình luận</span>
                </a>
                <a href="<?= BASE_URL ?>?action=admin-orders" class="nav-item">
                    <i class="bi bi-cart"></i>
                    <span>Quản lý đơn hàng</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-bar-chart"></i>
                    <span>Thống kê</span>
                </a>
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0;">
                    <a href="<?= BASE_URL ?>" class="nav-item">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Xem website</span>
                    </a>
                    <a href="<?= BASE_URL ?>?action=logout" class="nav-item">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="admin-content">
            <?php
            if (isset($view)) {
                require_once PATH_VIEW . $view . '.php';
            }
            ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

