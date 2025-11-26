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

    <style>
        /* Header Styling */
        header {
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .header-container {
            padding: 0.6rem 2rem;
            align-items: center;
        }

        /* Logo Styling - Nổi bật và to hơn */
        .logo-image {
            height: 100px;
            width: auto;
            object-fit: contain;
            border-radius: 8px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15));
            transition: all 0.3s ease;
        }

        .logo-image:hover {
            transform: scale(1.08);
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.2));
        }

        .logo-text {
            font-size: 2.4rem;
            font-weight: 900;
            color: #000;
            letter-spacing: 3px;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        
        .logo-text:hover {
            transform: scale(1.05);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0.3rem 0;
        }

        /* Navigation Menu - Hẹp và gọn */
        .nav {
            gap: 1.2rem !important;
        }

        .nav a {
            font-size: 0.7rem;
            font-weight: 400;
            padding: 0.3rem 0;
            position: relative;
            transition: color 0.3s ease;
            letter-spacing: 0.3px;
        }

        .nav a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1.5px;
            background: #000;
            transition: width 0.3s ease;
        }

        .nav a:hover {
            color: #000 !important;
        }

        .nav a:hover::after {
            width: 100%;
        }

        /* Icon Styling */
        .header-icons {
            gap: 1.2rem !important;
        }

        .header-icons a,
        .header-icons .dropdown-toggle {
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .header-icons a:hover,
        .header-icons .dropdown-toggle:hover {
            transform: translateY(-2px);
            color: #000 !important;
        }

        /* Dropdown User */
        .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dropdown-toggle i {
            font-size: 18px;
        }

        .dropdown-toggle .small {
            font-size: 0.8rem !important;
            font-weight: 500;
        }
        
        /* Cart Badge */
        .cart-icon-wrapper {
            position: relative;
            display: inline-block;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #000;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            height: 18px;
            width: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .nav {
                gap: 0.8rem !important;
            }

            .nav a {
                font-size: 0.65rem;
            }

            .header-icons {
                gap: 0.8rem !important;
            }

            .logo-image {
                height: 80px;
            }
            
            .logo-text {
                font-size: 2rem;
            }
            
            .header-container {
                padding: 0.5rem 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .logo-image {
                height: 70px;
            }

            .logo-text {
                font-size: 1.8rem;
                letter-spacing: 2px;
            }

            .header-container {
                padding: 0.5rem 1rem;
            }
            
            .nav a {
                font-size: 0.6rem;
            }
            
            .nav {
                gap: 0.6rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .logo-image {
                height: 60px;
            }
            
            .logo-text {
                font-size: 1.5rem;
            }
        }

        /* ===== Footer (global) ===== */
        .modern-footer {
            background-color: #000;
            color: #fff;
            padding: 80px 0 30px;
            margin-top: 80px;
            font-size: 0.9rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.5fr;
            gap: 60px;
            margin-bottom: 60px;
        }
        .footer-brand p {
            color: #999;
            line-height: 1.8;
            margin-bottom: 20px;
            max-width: 300px;
        }
        .footer-logo-img {
            height: 80px;
            width: auto;
            margin-bottom: 25px;
            filter: brightness(0) invert(1);
        }
        .footer-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-bottom: 25px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links li { margin-bottom: 15px; }
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }
        .footer-links a:hover { 
            color: #fff; 
            padding-left: 5px; 
        }
        .newsletter-form { position: relative; margin-top: 20px; }
        .newsletter-input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #333;
            padding: 10px 0;
            color: #fff;
            outline: none;
            transition: border-color 0.3s;
        }
        .newsletter-input:focus { border-color: #fff; }
        .newsletter-btn {
            position: absolute;
            right: 0;
            top: 10px;
            background: none;
            border: none;
            color: #fff;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            cursor: pointer;
        }
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #999;
            font-size: 0.8rem;
        }
        .social-links { display: flex; gap: 20px; }
        .social-links a { color: #fff; font-size: 1.2rem; transition: opacity 0.3s; }
        .social-links a:hover { opacity: 0.7; }

        /* Responsive Footer */
        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
            }
        }
        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .modern-footer {
                padding: 60px 0 20px;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
        }

        /* ===== Global Black & White Theme ===== */
        /* Buttons */
        .btn-dark, .btn-black {
            background-color: #000;
            border-color: #000;
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .btn-dark:hover, .btn-black:hover {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
            color: #fff;
        }
        
        .btn-outline-dark {
            border-color: #000;
            color: #000;
            background: transparent;
            transition: all 0.3s ease;
        }
        
        .btn-outline-dark:hover {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
        
        /* Links */
        a {
            color: #000;
            transition: color 0.3s ease;
        }
        
        a:hover {
            color: #1a1a1a;
        }
        
        /* Text colors */
        .text-muted {
            color: #666 !important;
        }
        
        /* Borders */
        .border {
            border-color: #e0e0e0 !important;
        }
        
        .border-dark {
            border-color: #000 !important;
        }
        
        /* Backgrounds */
        .bg-light {
            background-color: #fafafa !important;
        }
        
        /* Form inputs */
        .form-control, .form-select {
            border-color: #ddd;
            color: #000;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
        }
        
        /* Dropdown */
        .dropdown-menu {
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-item {
            color: #000;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f5f5f5;
            color: #000;
        }
        
        /* Alerts */
        .alert {
            border: 1px solid #e0e0e0;
        }
        
        .alert-success {
            background-color: #f5f5f5;
            border-color: #000;
            color: #000;
        }
        
        .alert-danger {
            background-color: #fff;
            border-color: #000;
            color: #000;
        }
        
        /* Pagination */
        .page-link {
            color: #000;
            border-color: #ddd;
        }
        
        .page-link:hover {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
        
        .page-item.active .page-link {
            background-color: #000;
            border-color: #000;
            color: #fff;
        }
        
        /* ===== Header Search Input (Đẹp và hiện đại) ===== */
        .header-search-wrapper {
            width: 0;
            overflow: hidden;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-right: 0.5rem;
        }
        
        .header-search-wrapper.show {
            width: 280px;
        }
        
        .header-search-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .header-search-input {
            width: 100%;
            padding: 0.5rem 3rem 0.5rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 0.8rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fafafa;
            color: #000;
            font-weight: 400;
        }
        
        .header-search-input::placeholder {
            color: #999;
            font-weight: 400;
        }
        
        .header-search-input:focus {
            border-color: #000;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        
        .header-search-btn {
            position: absolute;
            right: 0.25rem;
            background: #000;
            color: #fff;
            border: none;
            padding: 0.4rem 0.9rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            min-width: 36px;
            height: 36px;
        }
        
        .header-search-btn:hover {
            background: #1a1a1a;
            transform: scale(1.05);
        }
        
        .header-search-btn:active {
            transform: scale(0.95);
        }
        
        .search-icon-toggle {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-icon-toggle:hover {
            background: #f5f5f5;
            transform: scale(1.1);
        }
        
        .search-icon-toggle.hide {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.8);
        }
        
        /* Hiệu ứng slide vào khi submit */
        .header-search-wrapper.slide-in {
            animation: slideInSearch 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        @keyframes slideInSearch {
            0% {
                width: 280px;
                opacity: 1;
            }
            100% {
                width: 0;
                opacity: 0;
            }
        }
        
        /* Hiệu ứng khi input mở */
        .header-search-wrapper.show .header-search-input {
            animation: searchInputAppear 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        @keyframes searchInputAppear {
            0% {
                opacity: 0;
                transform: translateX(-10px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .header-search-wrapper.show {
                width: 200px;
            }
            
            .header-search-input {
                font-size: 0.75rem;
                padding: 0.45rem 2.8rem 0.45rem 0.9rem;
            }
            
            .header-search-btn {
                padding: 0.35rem 0.8rem;
                min-width: 32px;
                height: 32px;
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 576px) {
            .header-search-wrapper.show {
                width: 160px;
            }
            
            .header-search-input {
                font-size: 0.7rem;
                padding: 0.4rem 2.5rem 0.4rem 0.8rem;
            }
        }
    </style>

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
                <a class="text-decoration-none text-dark" href="<?= BASE_URL ?>?action=products">Sản phẩm</a>
                <a class="text-decoration-none text-dark" href="#">Bộ sưu tập</a>
                <a class="text-decoration-none text-dark" href="<?= BASE_URL ?>?action=posts">Tin tức</a>
                <a class="text-decoration-none text-dark" href="#">Liên hệ</a>
            </nav>

            <div class="d-flex align-items-center header-icons">
                <div class="header-search-wrapper" id="headerSearchWrapper">
                    <div class="header-search-input-container">
                        <label for="headerSearchInput" class="visually-hidden">Tìm kiếm sản phẩm</label>
                        <input 
                            type="text" 
                            class="header-search-input" 
                            id="headerSearchInput" 
                            name="search"
                            placeholder="Tìm kiếm..."
                            autocomplete="off"
                            aria-label="Tìm kiếm sản phẩm"
                        >
                        <button type="button" class="header-search-btn" id="headerSearchBtn" title="Tìm kiếm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <a class="text-dark search-icon-toggle" href="#" id="searchIconToggle" title="Tìm kiếm"><i class="bi bi-search"></i></a>
                
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
                if (isset($_SESSION['user']) && isset($_SESSION['cart'])) {
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

    <?php
        // Footer logo (base64) for all pages
        $footerLogoSrc = '';
        $footerLogoPath = PATH_ROOT . 'assets/images/logo.png';
        if (file_exists($footerLogoPath)) {
            $footerLogoData = base64_encode(file_get_contents($footerLogoPath));
            $footerLogoSrc = 'data:image/png;base64,' . $footerLogoData;
        }
    ?>
    <footer class="modern-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-brand">
                    <?php if ($footerLogoSrc): ?>
                        <img src="<?= $footerLogoSrc ?>" alt="BonBonWear Logo" class="footer-logo-img">
                    <?php endif; ?>
                    <p>Định hình phong cách thời trang đương đại. Sự kết hợp hoàn hảo giữa tính ứng dụng và vẻ đẹp nghệ thuật.</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-tiktok"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>

                <!-- Links Column 1 -->
                <div>
                    <h4 class="footer-heading">MUA SẮM</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>?action=products">Sản Phẩm Mới</a></li>
                        <li><a href="<?= BASE_URL ?>?action=products">Bán Chạy Nhất</a></li>
                        <li><a href="#">Bộ Sưu Tập</a></li>
                        <li><a href="#">Khuyến Mãi</a></li>
                    </ul>
                </div>

                <!-- Links Column 2 -->
                <div>
                    <h4 class="footer-heading">HỖ TRỢ</h4>
                    <ul class="footer-links">
                        <li><a href="<?= BASE_URL ?>?action=order-history">Trạng Thái Đơn Hàng</a></li>
                        <li><a href="#">Chính Sách Đổi Trả</a></li>
                        <li><a href="#">Hướng Dẫn Chọn Size</a></li>
                        <li><a href="#">Liên Hệ</a></li>
                    </ul>
                </div>

                <!-- Newsletter Column -->
                <div>
                    <h4 class="footer-heading">BẢN TIN</h4>
                    <p style="color: #999; margin-bottom: 20px;">Đăng ký để nhận thông tin về bộ sưu tập mới và ưu đãi độc quyền.</p>
                    <form class="newsletter-form">
                        <label for="newsletterEmail" class="visually-hidden">Email đăng ký nhận tin</label>
                        <input type="email" class="newsletter-input" id="newsletterEmail" name="email" placeholder="Email của bạn" aria-label="Email đăng ký nhận tin">
                        <button type="submit" class="newsletter-btn" aria-label="Gửi email đăng ký">GỬI</button>
                    </form>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© <?= date('Y') ?> BonBonWear. All rights reserved.</p>
                <div class="payment-methods">
                    <span class="me-3">Privacy Policy</span>
                    <span>Terms of Service</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const headerSearchWrapper = document.getElementById('headerSearchWrapper');
        const headerSearchInput = document.getElementById('headerSearchInput');
        const headerSearchBtn = document.getElementById('headerSearchBtn');
        const searchIconToggle = document.getElementById('searchIconToggle');

        // Mở thanh search (trượt ra)
        function openHeaderSearch() {
            headerSearchWrapper.classList.add('show');
            searchIconToggle.classList.add('hide');
            setTimeout(() => {
                headerSearchInput.focus();
            }, 400);
        }

        // Đóng thanh search với hiệu ứng slide vào
        function closeHeaderSearch() {
            headerSearchWrapper.classList.add('slide-in');
            searchIconToggle.classList.remove('hide');
            
            setTimeout(() => {
                headerSearchWrapper.classList.remove('show', 'slide-in');
                headerSearchInput.value = '';
            }, 500);
        }

        // Toggle search khi click icon
        searchIconToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (headerSearchWrapper.classList.contains('show')) {
                closeHeaderSearch();
            } else {
                openHeaderSearch();
            }
        });

        // Tìm kiếm khi nhấn Enter hoặc click nút search
        function performHeaderSearch() {
            const keyword = headerSearchInput.value.trim();
            if (keyword.length < 1) {
                return;
            }
            
            // Hiệu ứng slide vào
            headerSearchWrapper.classList.add('slide-in');
            searchIconToggle.classList.remove('hide');
            
            // Gọi API để tìm kiếm
            fetch(`<?= BASE_URL ?>?action=search-smart&q=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    setTimeout(() => {
                        headerSearchWrapper.classList.remove('show', 'slide-in');
                        headerSearchInput.value = '';
                        
                        if (data.success) {
                            if (data.type === 'product' && data.product_id) {
                                // Nếu tìm thấy 1 sản phẩm → chuyển đến chi tiết
                                window.location.href = `<?= BASE_URL ?>?action=product-detail&id=${data.product_id}`;
                            } else if (data.type === 'category' && data.category_id) {
                                // Nếu tìm thấy danh mục → chuyển đến danh mục
                                window.location.href = `<?= BASE_URL ?>?action=products&category_id=${data.category_id}`;
                            } else {
                                // Nếu nhiều kết quả → chuyển đến danh sách
                                window.location.href = `<?= BASE_URL ?>?action=products&q=${encodeURIComponent(keyword)}`;
                            }
                        } else {
                            // Không tìm thấy → chuyển đến danh sách với từ khóa
                            window.location.href = `<?= BASE_URL ?>?action=products&q=${encodeURIComponent(keyword)}`;
                        }
                    }, 500);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    setTimeout(() => {
                        headerSearchWrapper.classList.remove('show', 'slide-in');
                        headerSearchInput.value = '';
                        window.location.href = `<?= BASE_URL ?>?action=products&q=${encodeURIComponent(keyword)}`;
                    }, 500);
                });
        }

        // Event listeners
        headerSearchBtn.addEventListener('click', performHeaderSearch);
        
        headerSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performHeaderSearch();
            }
        });

        // Đóng khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (headerSearchWrapper.classList.contains('show') && 
                !headerSearchWrapper.contains(e.target) && 
                !searchIconToggle.contains(e.target)) {
                closeHeaderSearch();
            }
        });

        // Đóng khi nhấn ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && headerSearchWrapper.classList.contains('show')) {
                closeHeaderSearch();
            }
        });
    </script>

</body>

</html>