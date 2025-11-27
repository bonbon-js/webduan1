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
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=profile"><i class="bi bi-person me-2"></i>Thông tin cá nhân</a></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=order-history"><i class="bi bi-bag-check me-2"></i>Đơn hàng của tôi</a></li>
                            <?php if (($_SESSION['user']['role'] ?? null) === 'admin'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=admin-dashboard"><i class="bi bi-speedometer2 me-2"></i>Quản lý</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>?action=logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
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