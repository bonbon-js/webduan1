
<?php
// Helper to get base64 image
function getBase64Image($path) {
    if (file_exists($path)) {
        $data = file_get_contents($path);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    return ''; // Return empty or placeholder
}

$banner1 = getBase64Image(PATH_ROOT . 'assets/images/banner-hero.jpg');
$banner2Path = PATH_ROOT . 'assets/images/banner2.jpg';
// Nếu chưa có banner2, dùng tạm banner1 hoặc placeholder
$banner2 = file_exists($banner2Path) ? getBase64Image($banner2Path) : $banner1;
// Sử dụng đường dẫn tương đối để tránh lỗi BASE_URL
$videoUrl = './assets/images/vdbanner.mp4';
$footerLogo = getBase64Image(PATH_ROOT . 'assets/images/logo.png');
?>

<section class="hero" id="heroSlideshow">
    <!-- Slide 1: Video -->
    <div class="hero-slide active" data-type="video" data-duration="video">
        <video class="slide-bg video-bg" id="heroVideo" muted autoplay playsinline preload="auto">
            <source src="<?= $videoUrl ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <p class="hero-subtitle">Bộ Sưu Tập Mới 2025</p>
            <h1 class="hero-title">PHONG CÁCH<br>ĐƯƠNG ĐẠI</h1>
            <p class="hero-description">Khám phá sự kết hợp hoàn hảo giữa thời trang cao cấp và sự thoải mái tuyệt đối. Video Campaign chính thức.</p>
            <div class="hero-cta">
                <a href="#" class="btn-hero">Xem Ngay</a>
            </div>
        </div>

      
        <div class="hero-controls-gucci">
            <div class="control-item play-pause-wrapper">
                <svg class="progress-ring" width="48" height="48">
                    <circle class="progress-ring__bg" stroke-width="2" fill="transparent" r="22" cx="24" cy="24"/>
                    <circle class="progress-ring__circle" id="progressRingCircle" stroke-width="2" fill="transparent" r="22" cx="24" cy="24"/>
                </svg>
                <button class="control-btn-circle" id="playPauseBtn">
                    <i class="bi bi-pause-fill"></i>
                </button>
            </div>
            <button class="control-btn-circle nav-btn" id="prevSlideBtn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button class="control-btn-circle nav-btn" id="nextSlideBtn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Slide 2: Image 1 -->
    <div class="hero-slide" data-type="image" data-duration="2000">
        <img src="<?= $banner1 ?>" class="slide-bg" alt="Banner 1">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <p class="hero-subtitle">Xu Hướng Mùa Hè</p>
            <h1 class="hero-title">THANH LỊCH<br>& TỰ DO</h1>
            <p class="hero-description">Thiết kế tối giản, chất liệu cao cấp mang lại trải nghiệm khác biệt.</p>
            <div class="hero-cta">
                <a href="#" class="btn-hero">Mua Sắm</a>
                <a href="#" class="btn-hero-secondary">Chi Tiết</a>
            </div>
        </div>
    </div>

    <!-- Slide 3: Image 2 (Placeholder for banner2.png) -->
    <div class="hero-slide" data-type="image" data-duration="2000">
        <img src="<?= $banner2 ?>" class="slide-bg" alt="Banner 2">
        <div class="hero-overlay"></div>
        <div class="hero-content text-dark">
            <p class="hero-subtitle">Limited Edition</p>
            <h1 class="hero-title">ĐẲNG CẤP<br>RIÊNG BIỆT</h1>
            <p class="hero-description">Bộ sưu tập giới hạn dành cho những người dẫn đầu xu hướng.</p>
            <div class="hero-cta">
                <a href="#" class="btn-hero">Khám Phá</a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="hero-nav">
        <div class="nav-dot active" onclick="goToSlide(0)"></div>
        <div class="nav-dot" onclick="goToSlide(1)"></div>
        <div class="nav-dot" onclick="goToSlide(2)"></div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.nav-dot');
        const video = document.getElementById('heroVideo');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const prevSlideBtn = document.getElementById('prevSlideBtn');
        const nextSlideBtn = document.getElementById('nextSlideBtn');
        const progressCircle = document.getElementById('progressRingCircle');
        
        // Setup Progress Ring
        const radius = progressCircle.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        
        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        progressCircle.style.strokeDashoffset = circumference;

        function setProgress(percent) {
            const offset = circumference - (percent / 100) * circumference;
            progressCircle.style.strokeDashoffset = offset;
        }
        
        let currentSlide = 0;
        let slideInterval;
        let isVideoPlaying = false;

        // --- Video Controls ---
        function togglePlay() {
            if (video.paused) {
                video.play();
                playPauseBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                isVideoPlaying = true;
            } else {
                video.pause();
                playPauseBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
                isVideoPlaying = false;
            }
        }

        playPauseBtn.addEventListener('click', togglePlay);
        
        // Navigation Buttons
        prevSlideBtn.addEventListener('click', () => {
            // If on video slide, pause it
            if (currentSlide === 0) {
                video.pause();
                isVideoPlaying = false;
                playPauseBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
            }
            showSlide(currentSlide - 1);
        });

        nextSlideBtn.addEventListener('click', () => {
            // If on video slide, pause it
            if (currentSlide === 0) {
                video.pause();
                isVideoPlaying = false;
                playPauseBtn.innerHTML = '<i class="bi bi-play-fill"></i>';
            }
            nextSlide();
        });

        video.addEventListener('timeupdate', () => {
            const percent = (video.currentTime / video.duration) * 100;
            setProgress(percent);
        });

        // --- Slideshow Logic ---
        function showSlide(index) {
            // Reset current slide
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            
            // Handle Video Slide Exit
            if (currentSlide === 0) {
                video.pause();
                video.currentTime = 0;
            }

            // Update index
            currentSlide = index;
            if (currentSlide >= slides.length) currentSlide = 0;
            if (currentSlide < 0) currentSlide = slides.length - 1;

            // Activate new slide
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');

            // Handle Video Slide Enter
            if (currentSlide === 0) {
                video.play();
                playPauseBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                isVideoPlaying = true;
                clearInterval(slideInterval); // Stop auto-slide timer, let video finish
            } else {
                // Start auto-slide timer for images
                startSlideTimer();
            }
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function startSlideTimer() {
            clearInterval(slideInterval);
            const duration = slides[currentSlide].getAttribute('data-duration') || 5000;
            slideInterval = setTimeout(nextSlide, duration);
        }

        // Global function for dots
        window.goToSlide = function(index) {
            showSlide(index);
        };

        // Video End Event -> Next Slide
        video.addEventListener('ended', () => {
            nextSlide();
        });

        // Initial Setup
        // Try to play video immediately
        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise.then(_ => {
                isVideoPlaying = true;
                playPauseBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
            }).catch(error => {
                console.error("Video play error:", error);
                // Auto-play was prevented
                video.muted = true;
                video.play().then(() => {
                     isVideoPlaying = true;
                     playPauseBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
                }).catch(e => console.error("Video play error (muted):", e));
            });
        }
        
        video.addEventListener('error', function(e) {
            console.error("Video loading error:", video.error);
        });
    });
    
    // Cập nhật số lượng giỏ hàng
    function updateCartCount() {
        // Gọi API lấy số lượng sản phẩm trong giỏ
        fetch('<?= BASE_URL ?>?action=cart-count')
            .then(response => response.json())
            .then(data => {
                // Cập nhật badge số lượng ở icon giỏ hàng
                const badge = document.getElementById('cartBadge');
                if (badge) {
                    badge.textContent = data.count;
                }
            })
            .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
    }
    
// Cập nhật số lượng khi load trang
// Được gọi sau khi DOM đã sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
    // Existing initialization code ... (keep existing code above)
    // ...
    // Cuối cùng, cập nhật số lượng giỏ hàng
    updateCartCount();
});

// Remove previous standalone call to updateCartCount();

    // Mở Modal Quick Add
    function openQuickAdd(id, name, price, image) {
        document.getElementById('qaProductId').value = id;
        document.getElementById('qaProductName').textContent = name;
        document.getElementById('qaProductPrice').textContent = new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        document.getElementById('qaProductImage').src = image;
        document.getElementById('qaQuantity').value = 1;
        
        // Reset selections
        document.getElementById('sizeS').checked = true;
        document.getElementById('colorBlack').checked = true;
        
        const modal = new bootstrap.Modal(document.getElementById('quickAddModal'));
        modal.show();
    }
    
    // Thay đổi số lượng trong modal
    function changeQaQty(change) {
        const input = document.getElementById('qaQuantity');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        if (val > 999) val = 999;
        input.value = val;
    }
    
    // Validate quantity when user types manually
    function validateQaQuantity(input) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            input.value = 1;
        } else if (val > 999) {
            input.value = 999;
        } else {
            input.value = val;
        }
    }
    
    // Submit Quick Add
    function submitQuickAdd(action) {
        const productId = document.getElementById('qaProductId').value;
        const quantity = document.getElementById('qaQuantity').value;
        const size = document.querySelector('input[name="qaSize"]:checked').value;
        const color = document.querySelector('input[name="qaColor"]:checked').value;
        
        const data = {
            product_id: productId,
            quantity: quantity,
            size: size,
            color: color
        };
        
        fetch('<?= BASE_URL ?>?action=cart-add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(res => {
            if (res.require_login) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('quickAddModal'));
                modal.hide();
                showToast('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng', 'warning');
                setTimeout(() => {
                    window.location.href = '<?= BASE_URL ?>?action=show-login';
                }, 1500);
                return;
            }
            
            if (res.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('quickAddModal'));
                modal.hide();
                
                // Cập nhật số lượng trên icon giỏ hàng
                updateCartCount();

                if (action === 'cart') {
                    // Nếu là thêm vào giỏ: Ở lại trang và hiện thông báo
                    showToast('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    // Nếu là mua ngay: Chuyển trang thanh toán
                    window.location.href = '<?= BASE_URL ?>?action=checkout';
                }
            } else {
                showToast(res.message || 'Có lỗi xảy ra', 'error');
                console.error('Add to cart error:', res);
            }
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            showToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
        });
    }

    // Toast Notification Function
    function showToast(message) {
        // Tạo toast element nếu chưa có
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1100';
            document.body.appendChild(toastContainer);
        }

        const toastHtml = `
            <div class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.innerHTML = toastHtml;
        const toastEl = toastContainer.querySelector('.toast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }
    
    // Hàm xem nhanh sản phẩm
    function quickView(productId) {
        window.location.href = '<?= BASE_URL ?>?action=product-detail&id=' + productId;
    }
</script>

<section class="container products-section">
    <h2 class="section-title">Sản phẩm mới</h2>

    <div class="row g-4">
        <?php if (isset($products) && is_array($products) && !empty($products)) : ?>
            <?php foreach ($products as $product) : ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <article class="product-card">
                    <?php if (isset($product['id']) && isset($newProductIds) && in_array((int)$product['id'], $newProductIds, true)) : ?>
                        <span class="product-badge">New</span>
                    <?php endif; ?>
                    <div class="product-card-image-wrapper">
                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                        <div class="product-card-overlay">
                            <?php if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin'): ?>
                            <div class="product-card-icon" onclick="openQuickAdd(<?= $product['id'] ?? 0 ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>, '<?= $product['image'] ?>')" title="Thêm vào giỏ hàng">
                                <i class="bi bi-bag-plus"></i>
                            </div>
                            <?php endif; ?>
                            <div class="product-card-icon" onclick="quickView(<?= $product['id'] ?? 0 ?>)" title="Xem nhanh">
                                <i class="bi bi-eye"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-uppercase small text-muted mb-1"><?= $product['category'] ?></p>
                    <h3 class="h6"><?= $product['name'] ?></h3>
                    <p class="fw-semibold"><?= number_format($product['price'], 0, ',', '.') ?> đ</p>
                </article>
            </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="col-12">
                <p class="text-center text-muted">Chưa có sản phẩm nào.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
    <a href="<?= BASE_URL ?>?action=products" class="btn btn-outline-dark">Xem thêm</a>
    </div>
</section>

<section class="container">
    <h2 class="section-title">Tin tức</h2>

    <div class="row g-4">
        <?php foreach ($news as $item) : ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <article class="news-card">
                    <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>">
                    <p class="small text-muted mb-1"><?= $item['date'] ?></p>
                    <h3 class="h6 mb-2"><?= $item['title'] ?></h3>
                    <p class="small text-muted mb-0"><?= $item['excerpt'] ?></p>
                </article>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="<?= BASE_URL ?>?action=posts" class="btn btn-outline-dark">Xem thêm</a>
    </div>
</section>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="row g-0">
                <div class="col-md-6">
                    <div class="h-100 bg-light d-flex align-items-center justify-content-center p-4">
                        <img id="qaProductImage" src="" alt="Product" class="img-fluid" style="max-height: 400px; object-fit: contain;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 p-lg-5">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                        
                        <h3 id="qaProductName" class="h4 fw-bold mb-2">Product Name</h3>
                        <p id="qaProductPrice" class="h5 text-muted mb-4">0 đ</p>
                        
                        <form id="quickAddForm">
                            <input type="hidden" id="qaProductId">
                            
                            <!-- Size Selector -->
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-bold text-muted mb-2" id="qaSizeLabel">Kích thước</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qaSize" id="sizeS" value="S" checked>
                                    <label class="btn btn-outline-dark rounded-0 px-3" for="sizeS">S</label>
                                    
                                    <input type="radio" class="btn-check" name="qaSize" id="sizeM" value="M">
                                    <label class="btn btn-outline-dark rounded-0 px-3" for="sizeM">M</label>
                                    
                                    <input type="radio" class="btn-check" name="qaSize" id="sizeL" value="L">
                                    <label class="btn btn-outline-dark rounded-0 px-3" for="sizeL">L</label>
                                    
                                    <input type="radio" class="btn-check" name="qaSize" id="sizeXL" value="XL">
                                    <label class="btn btn-outline-dark rounded-0 px-3" for="sizeXL">XL</label>
                                </div>
                            </div>
                            
                            <!-- Color Selector -->
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-bold text-muted mb-2" id="qaColorLabel">Màu sắc</label>
                                <div class="d-flex gap-2">
                                    <input type="radio" class="btn-check" name="qaColor" id="colorBlack" value="Black" checked>
                                    <label class="btn rounded-circle p-0 border border-2 border-white shadow-sm" for="colorBlack" style="width: 30px; height: 30px; background-color: #000; cursor: pointer;"></label>
                                    
                                    <input type="radio" class="btn-check" name="qaColor" id="colorWhite" value="White">
                                    <label class="btn rounded-circle p-0 border border-1 border-secondary shadow-sm" for="colorWhite" style="width: 30px; height: 30px; background-color: #fff; cursor: pointer;"></label>
                                    
                                    <input type="radio" class="btn-check" name="qaColor" id="colorBeige" value="Beige">
                                    <label class="btn rounded-circle p-0 border border-2 border-white shadow-sm" for="colorBeige" style="width: 30px; height: 30px; background-color: #f5f5dc; cursor: pointer;"></label>
                                </div>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="mb-4">
                                <label for="qaQuantity" class="form-label small text-uppercase fw-bold text-muted mb-2">Số lượng</label>
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary rounded-0" type="button" onclick="changeQaQty(-1)" aria-label="Giảm số lượng">-</button>
                                    <input type="number" class="form-control text-center border-secondary border-start-0 border-end-0" id="qaQuantity" name="quantity" value="1" min="1" max="999" aria-label="Số lượng sản phẩm" onchange="validateQaQuantity(this)">
                                    <button class="btn btn-outline-secondary rounded-0" type="button" onclick="changeQaQty(1)" aria-label="Tăng số lượng">+</button>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-dark rounded-0 py-3 text-uppercase fw-bold" onclick="submitQuickAdd('cart')">
                                    Thêm vào giỏ hàng
                                </button>
                                <button type="button" class="btn btn-outline-dark rounded-0 py-3 text-uppercase fw-bold" onclick="submitQuickAdd('checkout')">
                                    Mua ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
