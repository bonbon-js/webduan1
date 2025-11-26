<style>
    .product-detail-section {
        padding: 80px 0;
    }

    .product-gallery {
        position: relative;
    }

    .product-main-image {
        width: 100%;
        height: 600px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .product-thumbnails {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .product-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .product-thumbnail:hover {
        border-color: #000;
        transform: scale(1.05);
    }

    .product-thumbnail.active {
        border-color: #000;
    }

    .product-info h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        letter-spacing: 1px;
    }

    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 30px;
    }

    .product-description {
        color: #666;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .attribute-selector {
        margin-bottom: 25px;
    }

    .attribute-selector label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1px;
    }

    .size-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .size-option {
        padding: 10px 20px;
        border: 2px solid #e0e0e0;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s;
        text-transform: uppercase;
        font-weight: 600;
    }

    .size-option:hover {
        border-color: #000;
    }

    .size-option.active {
        background: #000;
        color: #fff;
        border-color: #000;
    }

    .color-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .color-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s;
        position: relative;
    }

    .color-option:hover {
        transform: scale(1.1);
    }

    .color-option.active {
        border-color: #000;
        outline: 2px solid #000;
        outline-offset: 2px;
    }

    .color-option.white {
        border: 2px solid #ccc;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }

    .quantity-input-group {
        display: flex;
        width: 120px;
    }

    .quantity-input-group button {
        width: 40px;
        border: 1px solid #e0e0e0;
        background: #fff;
        cursor: pointer;
    }

    .quantity-input-group input {
        flex: 1;
        text-align: center;
        border: 1px solid #e0e0e0;
        border-left: none;
        border-right: none;
    }

    .btn-add-to-cart {
        padding: 15px 40px;
        background: #000;
        color: #fff;
        border: none;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s;
        margin-right: 15px;
    }

    .btn-add-to-cart:hover {
        background: #333;
        transform: translateY(-2px);
    }

    .btn-buy-now {
        padding: 15px 40px;
        background: transparent;
        color: #000;
        border: 2px solid #000;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s;
    }

    .btn-buy-now:hover {
        background: #000;
        color: #fff;
    }

    .similar-products-section {
        margin-top: 80px;
        padding-top: 80px;
        border-top: 1px solid #e0e0e0;
    }

    .product-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 0;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .product-card:hover {
        border-color: #000;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .product-card-image-wrapper {
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
        border-radius: 12px 12px 0 0;
    }

    .product-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: all 0.4s;
    }

    .product-card:hover img {
        opacity: 0.2;
        transform: scale(1.02);
    }

    .product-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        opacity: 0;
        transition: all 0.3s;
    }

    .product-card:hover .product-card-overlay {
        opacity: 1;
    }

    .product-card-icon {
        width: 56px;
        height: 56px;
        background: #000;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 22px;
    }

    .product-card-icon:hover {
        background: #fff;
        color: #000;
        transform: scale(1.1);
    }
</style>

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
                        <input type="number" id="productQuantity" name="quantity" value="1" min="1" readonly aria-label="Số lượng sản phẩm">
                        <button type="button" onclick="changeQuantity(1)" aria-label="Tăng số lượng">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <div>
                    <button type="button" class="btn btn-add-to-cart" onclick="addToCart()">Thêm vào giỏ hàng</button>
                    <button type="button" class="btn btn-buy-now" onclick="buyNow()">Mua ngay</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Similar Products -->
    <?php if (!empty($similarProducts)): ?>
        <div class="similar-products-section">
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
                            <p class="text-uppercase small text-muted mb-1" style="padding: 0 20px;"><?= $similar['category'] ?></p>
                            <h3 class="h6" style="padding: 0 20px;"><?= $similar['name'] ?></h3>
                            <p class="fw-semibold" style="padding: 0 20px 20px;"><?= number_format($similar['price'], 0, ',', '.') ?> đ</p>
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
        input.value = val;
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

