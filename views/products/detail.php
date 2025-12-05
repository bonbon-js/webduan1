<section class="container product-detail-section">
    <div class="row">
        <div class="col-md-6 product-gallery">
            <img id="mainProductImage" src="<?= $product['image'] ?? '' ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" class="product-main-image">
            <div class="product-thumbnails" id="productThumbnails">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $index => $img): ?>
                        <img src="<?= $img['image_url'] ?>" alt="Thumbnail <?= $index + 1 ?>" 
                             class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                             onclick="changeMainImage('<?= $img['image_url'] ?>', this)">
                    <?php endforeach; ?>
                <?php else: ?>
                    <img src="<?= $product['image'] ?? '' ?>" alt="Thumbnail" class="product-thumbnail active"
                         onclick="changeMainImage('<?= $product['image'] ?? '' ?>', this)">
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 product-info">
            <h1><?= htmlspecialchars($product['name'] ?? '') ?></h1>
            <p class="product-price"><?= number_format($product['price'] ?? 0, 0, ',', '.') ?> đ</p>
            <p class="product-description"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

            <form id="productDetailForm">
                <input type="hidden" id="productId" value="<?= $product['id'] ?? 0 ?>">
                
                <!-- Size Selector -->
                <div class="attribute-selector" id="sizeSelector">
                    <label for="productSize">Kích thước</label>
                    <input type="hidden" id="productSize" name="size">
                    <div class="size-options" id="sizeOptions" role="radiogroup" aria-labelledby="sizeSelector">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>

                <!-- Color Selector -->
                <div class="attribute-selector" id="colorSelector">
                    <label for="productColor">Màu sắc</label>
                    <input type="hidden" id="productColor" name="color">
                    <div class="color-options" id="colorOptions" role="radiogroup" aria-labelledby="colorSelector">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>

                <!-- Quantity Selector -->
                <div class="quantity-selector">
                    <label for="productQuantity">Số lượng</label>
                    <div class="quantity-input-group">
                        <button type="button" onclick="changeQuantity(-1)" aria-label="Giảm số lượng">-</button>
                        <input type="number" id="productQuantity" name="quantity" value="1" min="1" max="999" aria-label="Số lượng sản phẩm" onchange="validateQuantity(this)">
                        <button type="button" onclick="changeQuantity(1)" aria-label="Tăng số lượng">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <?php if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin'): ?>
                <div>
                    <button type="button" class="btn btn-add-to-cart" onclick="addToCart()">Thêm vào giỏ hàng</button>
                    <button type="button" class="btn btn-buy-now" onclick="buyNow()">Mua ngay</button>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tài khoản quản trị chỉ có thể xem sản phẩm, không thể mua hàng.
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section mt-5 pt-5 border-top">
        <h2 class="section-title mb-4">Đánh giá sản phẩm</h2>
        
        <?php if (!empty($reviewStats) && $reviewStats['total_reviews'] > 0): ?>
            <div class="review-summary mb-4 p-4 bg-light rounded">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <div class="average-rating">
                            <div class="display-4 fw-bold"><?= number_format($reviewStats['average_rating'], 1) ?></div>
                            <div class="rating-stars mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= round($reviewStats['average_rating']) ? '-fill text-warning' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="text-muted small">Dựa trên <?= $reviewStats['total_reviews'] ?> đánh giá</div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="rating-breakdown">
                            <?php for ($star = 5; $star >= 1; $star--): 
                                $count = $reviewStats['rating_' . $star] ?? 0;
                                $percentage = $reviewStats['total_reviews'] > 0 ? ($count / $reviewStats['total_reviews']) * 100 : 0;
                            ?>
                                <div class="rating-bar-item mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 small"><?= $star ?> sao</span>
                                        <div class="progress flex-grow-1 progress-thin">
                                            <div class="progress-bar bg-warning rating-progress-bar" role="progressbar" data-progress="<?= $percentage ?>"></div>
                                        </div>
                                        <span class="ms-2 small text-muted"><?= $count ?></span>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-light text-center">
                <p class="mb-0">Chưa có đánh giá nào cho sản phẩm này.</p>
            </div>
        <?php endif; ?>

        <!-- Reviews List -->
        <?php if (!empty($reviews)): ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): 
                    $reviewImages = $review['images'] ?? [];
                    if (is_string($reviewImages)) {
                        $reviewImages = json_decode($reviewImages, true);
                        if (!is_array($reviewImages)) $reviewImages = [];
                    }
                ?>
                    <div class="review-item mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?= htmlspecialchars($review['user_name'] ?? 'Khách hàng') ?></strong>
                                <div class="rating-stars mt-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill text-warning' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($review['created_at'])) ?></small>
                        </div>
                        <?php if (!empty($review['comment'])): ?>
                            <p class="mb-2"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($reviewImages)): ?>
                            <div class="review-images mt-2 mb-2">
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($reviewImages as $img): ?>
                                        <a href="<?= htmlspecialchars($img) ?>" target="_blank" class="review-image-link">
                                            <img src="<?= htmlspecialchars($img) ?>" alt="Review image" class="img-thumbnail review-thumb">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($review['reply'])): ?>
                            <div class="review-reply mt-3 p-3 bg-light rounded border-start border-3 border-dark">
                                <small class="text-muted d-block mb-1"><strong>Phản hồi từ cửa hàng:</strong></small>
                                <p class="mb-0 small"><?= nl2br(htmlspecialchars($review['reply'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Similar Products -->
    <?php if (!empty($similarProducts)): ?>
        <div class="similar-products-section mt-5 pt-5 border-top">
            <h2 class="section-title">Sản phẩm tương tự</h2>
            <div class="row g-4">
                <?php foreach ($similarProducts as $similar): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <article class="product-card">
                            <div class="product-card-image-wrapper">
                                <img src="<?= $similar['image'] ?>" alt="<?= htmlspecialchars($similar['name']) ?>">
                                <div class="product-card-overlay">
                                    <div class="product-card-icon" onclick="openQuickAdd(<?= $similar['id'] ?>, '<?= htmlspecialchars($similar['name']) ?>', <?= $similar['price'] ?>, '<?= $similar['image'] ?>')" title="Thêm vào giỏ hàng">
                                        <i class="bi bi-bag-plus"></i>
                                    </div>
                                    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $similar['id'] ?>" class="product-card-icon" title="Xem chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <p class="text-uppercase small text-muted mb-1 similar-card-pad"><?= $similar['category'] ?></p>
                            <h3 class="h6 similar-card-pad"><?= $similar['name'] ?></h3>
                            <p class="fw-semibold similar-card-pad-bottom"><?= number_format($similar['price'], 0, ',', '.') ?> đ</p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
    const colorMap = {
        'Black': '#000',
        'White': '#fff',
        'Beige': '#f5f5dc',
        'Red': '#ff0000',
        'Blue': '#0000ff',
        'Green': '#008000',
        'Yellow': '#ffff00',
        'Pink': '#ffc0cb',
        'Gray': '#808080',
        'Brown': '#a52a2a'
    };

    // Load attributes on page load
    document.addEventListener('DOMContentLoaded', function() {
        const productId = document.getElementById('productId').value;
    document.querySelectorAll('.rating-progress-bar').forEach(bar => {
        const width = bar.dataset.progress;
        if (width !== undefined) {
            bar.style.width = `${width}%`;
        }
    });
        if (productId) {
            loadProductAttributes(productId);
        }
    });

    // Load product attributes
    function loadProductAttributes(productId) {
        fetch(`<?= BASE_URL ?>?action=product-attributes&product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderSizeOptions(data.data.sizes || []);
                    renderColorOptions(data.data.colors || []);
                    
                    // Set default and update image
                    if (data.data.sizes && data.data.sizes.length > 0) {
                        document.querySelector('.size-option').classList.add('active');
                        document.getElementById('productSize').value = data.data.sizes[0];
                    }
                    if (data.data.colors && data.data.colors.length > 0) {
                        document.querySelector('.color-option').classList.add('active');
                        document.getElementById('productColor').value = data.data.colors[0];
                        updateProductImages(productId, data.data.sizes[0] || null, data.data.colors[0]);
                    }
                }
            })
            .catch(err => console.error('Error loading attributes:', err));
    }

    // Render size options
    function renderSizeOptions(sizes) {
        const container = document.getElementById('sizeOptions');
        container.innerHTML = '';
        
        sizes.forEach((size, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'size-option';
            btn.textContent = size;
            btn.dataset.size = size;
            if (index === 0) btn.classList.add('active');
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-option').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('productSize').value = size;
                const productId = document.getElementById('productId').value;
                const color = document.querySelector('.color-option.active')?.dataset.color;
                updateProductImages(productId, size, color);
            });
            container.appendChild(btn);
        });
    }

    // Render color options
    function renderColorOptions(colors) {
        const container = document.getElementById('colorOptions');
        container.innerHTML = '';
        
        colors.forEach((color, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'color-option';
            if (color.toLowerCase() === 'white') btn.classList.add('white');
            btn.style.backgroundColor = colorMap[color] || '#ccc';
            btn.dataset.color = color;
            btn.title = color;
            if (index === 0) btn.classList.add('active');
            btn.addEventListener('click', function() {
                document.querySelectorAll('.color-option').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('productColor').value = color;
                const productId = document.getElementById('productId').value;
                const size = document.querySelector('.size-option.active')?.dataset.size;
                updateProductImages(productId, size, color);
            });
            container.appendChild(btn);
        });
    }

    // Update product images based on selected attributes
    function updateProductImages(productId, size, color) {
        if (!productId) return;
        
        const params = new URLSearchParams({ product_id: productId });
        if (size) params.append('size', size);
        if (color) params.append('color', color);
        
        fetch(`<?= BASE_URL ?>?action=variant-images&${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    // Update main image
                    document.getElementById('mainProductImage').src = data.data[0];
                    
                    // Update thumbnails
                    const thumbnailsContainer = document.getElementById('productThumbnails');
                    thumbnailsContainer.innerHTML = '';
                    data.data.forEach((imgUrl, index) => {
                        const thumb = document.createElement('img');
                        thumb.src = imgUrl;
                        thumb.className = 'product-thumbnail' + (index === 0 ? ' active' : '');
                        thumb.alt = `Thumbnail ${index + 1}`;
                        thumb.onclick = function() {
                            changeMainImage(imgUrl, this);
                        };
                        thumbnailsContainer.appendChild(thumb);
                    });
                }
            })
            .catch(err => console.error('Error updating images:', err));
    }

    // Change main image
    function changeMainImage(src, element) {
        document.getElementById('mainProductImage').src = src;
        document.querySelectorAll('.product-thumbnail').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
    }

    // Change quantity
    function changeQuantity(change) {
        const input = document.getElementById('productQuantity');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        if (val > 999) val = 999;
        input.value = val;
    }

    // Validate quantity when user types manually
    function validateQuantity(input) {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 1) {
            input.value = 1;
        } else if (val > 999) {
            input.value = 999;
        } else {
            input.value = val;
        }
    }

    // Add to cart
    function addToCart() {
        const productId = document.getElementById('productId').value;
        const quantity = document.getElementById('productQuantity').value;
        const size = document.querySelector('.size-option.active')?.dataset.size;
        const color = document.querySelector('.color-option.active')?.dataset.color;
        
        if (!size || !color) {
            showToast('Vui lòng chọn đầy đủ thuộc tính sản phẩm', 'warning');
            return;
        }
        
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
                showToast('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng', 'warning');
                setTimeout(() => {
                    window.location.href = '<?= BASE_URL ?>?action=show-login';
                }, 1500);
                return;
            }
            
            if (res.success) {
                updateCartCount();
                showToast('Đã thêm sản phẩm vào giỏ hàng!');
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

    // Buy now
    function buyNow() {
        addToCart();
        setTimeout(() => {
            window.location.href = '<?= BASE_URL ?>?action=checkout';
        }, 500);
    }

    // Toast notification
    function showToast(message, type = 'success') {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1100';
            document.body.appendChild(toastContainer);
        }

        const bgClass = type === 'error' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-dark';
        const icon = type === 'error' ? 'bi-x-circle-fill' : type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill';

        const toastHtml = `
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${icon} me-2"></i> ${message}
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

    // Update cart count
    function updateCartCount() {
        fetch('<?= BASE_URL ?>?action=cart-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('cartBadge');
                if (badge) {
                    badge.textContent = data.count || 0;
                }
            })
            .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
    }

    // Quick Add function (for similar products)
    function openQuickAdd(id, name, price, image) {
        // Redirect to detail page or open modal
        window.location.href = `<?= BASE_URL ?>?action=product-detail&id=${id}`;
    }
</script>

