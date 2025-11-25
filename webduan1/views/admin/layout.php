<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - BonBonWear Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background: #2c3e50; }
        .sidebar .nav-link { color: #ecf0f1; padding: 12px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #34495e; color: #fff; }
        .sidebar .nav-link i { margin-right: 10px; width: 20px; }
        .main-content { padding: 30px; background: #f8f9fa; min-height: 100vh; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn-action { padding: 5px 10px; font-size: 0.875rem; }
        .table-actions { white-space: nowrap; }
        .alert { border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-white border-bottom">
                    <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Admin Panel</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="<?= BASE_URL ?>?action=admin-dashboard">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a class="nav-link <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-product') !== false) ? 'active' : '' ?>" href="<?= BASE_URL ?>?action=admin-products">
                        <i class="bi bi-box-seam"></i> Sản phẩm
                    </a>
                    <a class="nav-link <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-categor') !== false) ? 'active' : '' ?>" href="<?= BASE_URL ?>?action=admin-categories">
                        <i class="bi bi-tags"></i> Danh mục
                    </a>
                    <a class="nav-link <?= (isset($_GET['action']) && strpos($_GET['action'], 'admin-attribute') !== false) ? 'active' : '' ?>" href="<?= BASE_URL ?>?action=admin-attributes">
                        <i class="bi bi-sliders"></i> Thuộc tính
                    </a>
                    <a class="nav-link" href="<?= BASE_URL ?>?action=admin-orders">
                        <i class="bi bi-cart-check"></i> Đơn hàng
                    </a>
                    <a class="nav-link" href="<?= BASE_URL ?>?action=admin-users">
                        <i class="bi bi-people"></i> Người dùng
                    </a>
                    <a class="nav-link" href="<?= BASE_URL ?>?action=admin-coupons">
                        <i class="bi bi-ticket-perforated"></i> Mã giảm giá
                    </a>
                    <hr class="text-white">
                    <a class="nav-link" href="<?= BASE_URL ?>">
                        <i class="bi bi-arrow-left-circle"></i> Về trang chủ
                    </a>
                    <a class="nav-link" href="<?= BASE_URL ?>?action=logout">
                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Page Content -->
                <?php require_once PATH_VIEW . 'admin/' . $view . '.php'; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm delete
        function confirmDelete(url, name) {
            if (confirm('Bạn có chắc chắn muốn xóa "' + name + '"?')) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
