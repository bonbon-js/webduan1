<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - BonBonWear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
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
                <?php
                $currentAction = $_GET['action'] ?? '';
                $isActive = function($action) use ($currentAction) {
                    return $currentAction === $action ? 'active' : '';
                };
                ?>
                <a href="#" class="nav-item">
                    <i class="bi bi-gear"></i>
                    <span>Quản lý danh mục</span>
                </a>
                <a href="<?= BASE_URL ?>?action=admin-users" class="nav-item <?= $isActive('admin-users') ?>">
                    <i class="bi bi-people"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="bi bi-briefcase"></i>
                    <span>Quản lý sản phẩm</span>
                </a>
                
                <a href="#" class="nav-item">
                    <i class="bi bi-chat-dots"></i>
                    <span>Quản lý bình luận</span>
                </a>
                <a href="<?= BASE_URL ?>?action=admin-orders" class="nav-item <?= $isActive('admin-orders') ?>">
                    <i class="bi bi-cart"></i>
                    <span>Quản lý đơn hàng</span>
                </a>
                <a href="<?= BASE_URL ?>?action=admin-coupons" class="nav-item <?= $isActive('admin-coupons') ?>">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>Quản lý mã giảm giá</span>
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

