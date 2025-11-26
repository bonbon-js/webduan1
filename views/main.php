<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'Home' ?></title>

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-white">

    <header class="border-bottom">
        <div class="container header-container d-flex flex-wrap justify-content-between align-items-center">
            <a class="navbar-brand d-flex align-items-center text-decoration-none" href="<?= BASE_URL ?>">
                <?php 
                $logoPath = PATH_ROOT . 'assets/images/logo.png';
                if (file_exists($logoPath)): 
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSrc = 'data:image/png;base64,' . $logoData;
                ?>
                    <img class="logo-image" src="<?= $logoSrc ?>" alt="BonBonwear">
                <?php else: ?>
                    <span class="logo-text">BONBONWEAR</span>
                <?php endif; ?>
            </a>

            <nav class="nav text-uppercase small">
                <a class="text-decoration-none text-dark" href="#">Sản phẩm</a>
                <a class="text-decoration-none text-dark" href="#">Bộ sưu tập</a>
                <a class="text-decoration-none text-dark" href="#">Tin tức</a>
                <a class="text-decoration-none text-dark" href="#">Liên hệ</a>
            </nav>

            <div class="d-flex align-items-center header-icons">
                <a class="text-dark" href="#" title="Tìm kiếm"><i class="bi bi-search"></i></a>
                
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="dropdown">
                        <a class="text-dark dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản">
                            <i class="bi bi-person-circle"></i>
                            <span class="ms-1 small"><?= $_SESSION['user']['fullname'] ?? 'User' ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=profile">Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=order-history">Đơn hàng của tôi</a></li>
                            <?php if (($_SESSION['user']['role'] ?? null) === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=admin-users">Quản lý người dùng</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=admin-orders">Quản lý đơn hàng</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=logout">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="text-dark" href="<?= BASE_URL ?>?action=show-login" title="Đăng nhập"><i class="bi bi-person-circle"></i></a>
                <?php endif; ?>
                
                <?php
                $cartCount = 0;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $cartCount += $item['quantity'];
                    }
                }
                ?>
                <a class="text-dark cart-icon-wrapper" href="<?= BASE_URL ?>?action=cart-list" title="Giỏ hàng">
                    <i class="bi bi-bag"></i>
                    <span class="cart-badge" id="cartBadge"><?= $cartCount ?></span>
                </a>
            </div>
        </div>
    </header>

    <?php $flash = get_flash(); ?>
    <!-- Hiển thị flash message 1 lần -->
    <?php if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>

    <main>
        <?php
        if (isset($view)) {
            require_once PATH_VIEW . $view . '.php';
        }
        ?>
    </main>

</body>

</html>