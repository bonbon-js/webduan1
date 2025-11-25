<style>
    .product-detail {
        padding: 60px 0;
    }
    
    .product-image {
        width: 100%;
        border-radius: 12px;
        object-fit: cover;
    }
    
    .product-info {
        padding-left: 40px;
    }
    
    .product-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }
    
    .product-price {
        font-size: 2rem;
        font-weight: 700;
        color: #000;
        margin-bottom: 20px;
    }
    
    .product-description {
        line-height: 1.8;
        color: #666;
        margin-bottom: 30px;
    }
    
    .variant-selector {
        margin-bottom: 25px;
    }
    
    .variant-label {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .qty-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #000;
        background: #fff;
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    .qty-input {
        width: 80px;
        text-align: center;
        border: 1px solid #000;
        height: 40px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .btn-add-cart {
        flex: 1;
        padding: 15px;
        background: #000;
        color: #fff;
        border: none;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-add-cart:hover {
        background: #333;
    }
    
    .btn-buy-now {
        flex: 1;
        padding: 15px;
        background: #fff;
        color: #000;
        border: 2px solid #000;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-buy-now:hover {
        background: #000;
        color: #fff;
    }
    
    .product-meta {
        border-top: 1px solid #e0e0e0;
        padding-top: 20px;
        font-size: 0.9rem;
        color: #666;
    }
    
    .related-products {
        margin-top: 80px;
    }
</style>

<div class="container product-detail">
    <div class="row">
        <div class="col-md-6">
            <?php 
            $primaryImage = 'https://via.placeholder.com/600';
            if (!empty($images)) {
                foreach ($images as $img) {
                    if ($img['is_primary']) {
                        $primaryImage = BASE_URL . $img['image_url'];
                        break;
                    }
                }
                if ($primaryImage == 'https://via.placeholder.com/600' && !empty($images[0])) {
                    $primaryImage = BASE_URL . $images[0]['image_url'];
                }
            }
            ?>
            <img src="<?= $primaryImage ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
            
            <?php if (!empty($images) && count($images) > 1): ?>
                <div class="row mt-3 g-2">
                    <?php foreach ($images as $img): ?>
                        <div class="col-3">
                            <img src="<?= BASE_URL . $img['image_url'] ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="img-thumbnail" style="cursor: pointer;" onclick="document.querySelector('.product-image').src = this.src">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-6">
            <div class="product-info">
                <p class="text-uppercase small text-muted mb-2"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></p>
                <h1 class="product-title"><?= htmlspecialchars($product['product_name']) ?></h1>
                <p class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> đ</p>
                
                <?php if (!empty($product['description'])): ?>
                    <div class="product-description">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Variants -->
                <?php if (!empty($variants)): ?>
                    <div class="variant-selector">
                        <label class="variant-label">Chọn biến thể</label>
                        <select class="form-select" id="variantSelect">
                            <?php foreach ($variants as $variant): ?>
                                <option value="<?= $variant['variant_id'] ?>" data-price="<?= $variant['additional_price'] ?>">
                                    <?= htmlspecialchars($variant['sku']) ?> - +<?= number_format($variant['additional_price'], 0, ',', '.') ?> đ
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <!-- Attributes -->
                <?php if (!empty($attributes)): ?>
                    <?php 
                    $groupedAttrs = [];
                    foreach ($attributes as $attr) {
                        $groupedAttrs[$attr['attribute_name']][] = $attr;
                    }
                    ?>
                    
                    <?php foreach ($groupedAttrs as $attrName => $attrValues): ?>
                        <div class="variant-selector">
                            <label class="variant-label"><?= htmlspecialchars($attrName) ?></label>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php 
                                $uniqueValues = [];
                                foreach ($attrValues as $attrValue) {
                                    if (!in_array($attrValue['value_name'], $uniqueValues)) {
                                        $uniqueValues[] = $attrValue['value_name'];
                                ?>
                                    <input type="radio" class="btn-check" name="attr_<?= str_replace(' ', '_', $attrName) ?>" id="attr_<?= md5($attrName . $attrValue['value_name']) ?>" value="<?= htmlspecialchars($attrValue['value_name']) ?>" <?= empty($uniqueValues) || count($uniqueValues) == 1 ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-dark" for="attr_<?= md5($attrName . $attrValue['value_name']) ?>">
                                        <?= htmlspecialchars($attrValue['value_name']) ?>
                                    </label>
                                <?php 
                                    }
                                } 
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Quantity -->
                <div class="quantity-selector">
                    <label class="variant-label">Số lượng</label>
                    <button class="qty-btn" onclick="changeQuantity(-1)">-</button>
                    <input type="number" class="qty-input" id="quantity" value="1" min="1" max="<?= $product['stock'] ?? 999 ?>">
                    <button class="qty-btn" onclick="changeQuantity(1)">+</button>
                    <span class="text-muted">(Còn <?= $product['stock'] ?? 0 ?> sản phẩm)</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="btn-add-cart" onclick="addToCart()">
                        <i class="bi bi-bag-plus me-2"></i> Thêm vào giỏ
                    </button>
                    <button class="btn-buy-now" onclick="buyNow()">
                        Mua ngay
                    </button>
                </div>
                
                <!-- Product Meta -->
                <div class="product-meta">
                    <p><strong>Mã sản phẩm:</strong> SP<?= str_pad($product['product_id'], 6, '0', STR_PAD_LEFT) ?></p>
                    <p><strong>Danh mục:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="related-products">
            <h2 class="section-title">Sản phẩm liên quan</h2>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <article class="product-card">
                            <div class="product-card-image-wrapper">
                                <img src="<?= !empty($relatedProduct['primary_image']) ? BASE_URL . $relatedProduct['primary_image'] : 'https://via.placeholder.com/300' ?>" alt="<?= htmlspecialchars($relatedProduct['product_name']) ?>">
                                <div class="product-card-overlay">
                                    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $relatedProduct['product_id'] ?>" class="product-card-icon">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                            <h3 class="h6"><?= htmlspecialchars($relatedProduct['product_name']) ?></h3>
                            <p class="fw-semibold"><?= number_format($relatedProduct['price'], 0, ',', '.') ?> đ</p>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function changeQuantity(change) {
        const input = document.getElementById('quantity');
        let value = parseInt(input.value) + change;
        const max = parseInt(input.max);
        
        if (value < 1) value = 1;
        if (value > max) value = max;
        
        input.value = value;
    }
    
    function addToCart() {
        const productId = <?= $product['product_id'] ?>;
        const quantity = document.getElementById('quantity').value;
        const variantId = document.getElementById('variantSelect')?.value || null;
        
        const data = {
            product_id: productId,
            quantity: quantity,
            variant_id: variantId
        };
        
        fetch('<?= BASE_URL ?>?action=cart-add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                showToast('Đã thêm sản phẩm vào giỏ hàng!');
                updateCartCount();
            } else {
                alert('Có lỗi xảy ra: ' + res.message);
            }
        })
        .catch(err => console.error(err));
    }
    
    function buyNow() {
        addToCart();
        setTimeout(() => {
            window.location.href = '<?= BASE_URL ?>?action=checkout';
        }, 500);
    }
    
    function showToast(message) {
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
    
    function updateCartCount() {
        fetch('<?= BASE_URL ?>?action=cart-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('cartBadge');
                if (badge) {
                    badge.textContent = data.count;
                }
            })
            .catch(err => console.error('Lỗi cập nhật giỏ hàng:', err));
    }
</script>
