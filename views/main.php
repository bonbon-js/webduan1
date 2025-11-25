<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'BonBon' ?></title>

    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="header-wrapper">
        <div class="header-background"></div>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="<?= BASE_URL ?>">BonBon</a>
                
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    <a class="nav-link-custom" href="#">
                        SẢN PHẨM <i class="fas fa-chevron-down"></i>
                    </a>
                    <a class="nav-link-custom" href="#">
                        ĐỊA CHỈ SHOP <i class="fas fa-chevron-down"></i>
                    </a>
                    <a class="nav-link-custom" href="#">
                        ĐỊA CHỈ - C_SÁCH <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="nav-icons">
                        <a href="#" class="nav-icon">
                            <i class="fas fa-search"></i>
                        </a>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="user-menu">
                                <a href="#" class="nav-icon">
                                    <i class="fas fa-user"></i>
                                </a>
                                <div class="user-dropdown">
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>?action=accounts">Quản lý tài khoản</a>
                                        <hr class="dropdown-divider">
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>?action=profile">Thông tin cá nhân</a>
                                    <a href="<?= BASE_URL ?>?action=logout">Đăng xuất</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>?action=login" class="nav-icon">
                                <i class="fas fa-user"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="#" class="nav-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="container mt-3">
        <?php 
        $flashMap = [
            'success' => 'success',
            'warning' => 'warning',
            'error'   => 'danger'
        ];
        foreach ($flashMap as $key => $bootstrapClass):
            if (!empty($_SESSION[$key])): ?>
                <div class="alert alert-<?= $bootstrapClass ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION[$key]) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php 
                unset($_SESSION[$key]);
            endif;
        endforeach; 
        ?>
    </div>

    <div class="container-fluid px-0 main-content">
        <?php
        if (isset($view)) {
            require_once PATH_VIEW . $view . '.php';
        }
        ?>
    </div>

</body>

</html>