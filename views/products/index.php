<style>
    .products-section {
        background: #fff;
        padding: 80px 0;
        margin: 0 auto;
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
        box-shadow: none;
        margin: 0 auto;
    }

    .product-card:hover {
        border-color: #000;
    }

    .product-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: #000;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        z-index: 2;
    }

    .product-card-image-wrapper {
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
        border-radius: 12px 12px 0 0;
        background: #f5f5f5;
    }

    .product-card img {
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 0;
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
        transform: translateY(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 22px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
        border: 2px solid #000;
    }

    .product-card-icon:hover {
        background: #fff;
        color: #000;
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.4);
    }

    .product-card h3 {
        font-weight: 600;
        color: #000;
        margin-bottom: 8px;
        transition: color 0.3s;
        padding: 0 20px;
        letter-spacing: 0.5px;
    }

    .product-card .text-muted {
        color: #666 !important;
        padding: 0 20px;
    }

    .product-card .fw-semibold {
        color: #000;
        font-size: 1.2rem;
        font-weight: 700;
        padding: 0 20px 20px;
        margin: 0;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .filter-section {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 40px;
    }

    .filter-section h5 {
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }

    .price-filter label {
        display: block;
        padding: 10px;
        margin-bottom: 8px;
        cursor: pointer;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .price-filter label:hover {
        background: #e9ecef;
    }

    .price-filter input[type="radio"]:checked + label {
        background: #000;
        color: #fff;
    }

    /* Responsive Quick Add Modal */
    @media (max-width: 768px) {
        .modal-dialog.modal-lg {
            max-width: 95%;
            margin: 10px auto;
        }
        
        .modal-content .row {
            flex-direction: column;
        }
        
        .modal-content .col-md-6 {
            max-width: 100%;
        }
        
        #qaProductImage {
            max-height: 250px !important;
        }
    }
</style>

<section class="container products-section">
    <h2 class="section-title">Tất cả sản phẩm</h2>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Tìm kiếm</h5>
                <form method="GET" action="<?= BASE_URL ?>?action=products">
                    <input type="hidden" name="action" value="products">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button class="btn btn-dark" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <?php if (isset($_GET['category_id'])): ?>
                        <input type="hidden" name="category_id" value="<?= (int)$_GET['category_id'] ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['price'])): ?>
                        <input type="hidden" name="price" value="<?= htmlspecialchars($_GET['price']) ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Danh mục</h5>
                <form method="GET" action="<?= BASE_URL ?>?action=products" id="categoryForm">
                    <input type="hidden" name="action" value="products">
                    <select name="category_id" class="form-select" onchange="document.getElementById('categoryForm').submit();">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories ?? [] as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_GET['q'])): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['price'])): ?>
                        <input type="hidden" name="price" value="<?= htmlspecialchars($_GET['price']) ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Mức giá</h5>
                <form method="GET" action="<?= BASE_URL ?>?action=products" id="priceForm">
                    <input type="hidden" name="action" value="products">
                    <select name="price" class="form-select" onchange="document.getElementById('priceForm').submit();">
                        <option value="">Tất cả mức giá</option>
                        <option value="under300" <?= (isset($_GET['price']) && $_GET['price'] == 'under300') ? 'selected' : '' ?>>Dưới 300.000 đ</option>
                        <option value="300-500" <?= (isset($_GET['price']) && $_GET['price'] == '300-500') ? 'selected' : '' ?>>300.000 - 500.000 đ</option>
                        <option value="500-800" <?= (isset($_GET['price']) && $_GET['price'] == '500-800') ? 'selected' : '' ?>>500.000 - 800.000 đ</option>
                        <option value="above800" <?= (isset($_GET['price']) && $_GET['price'] == 'above800') ? 'selected' : '' ?>>Trên 800.000 đ</option>
                    </select>
                    <?php if (isset($_GET['q'])): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['category_id'])): ?>
                        <input type="hidden" name="category_id" value="<?= (int)$_GET['category_id'] ?>">
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product) : ?>
                <div class="col-12 col-sm-6 col-lg-4">
                    <article class="product-card">
                        <?php if (isset($product['id']) && $product['id'] <= 3) : ?>
                            <span class="product-badge">New</span>
                        <?php endif; ?>
                        <div class="product-card-image-wrapper">
                            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                            <div class="product-card-overlay">
                                <div class="product-card-icon" onclick="openQuickAdd(<?= $product['id'] ?? 0 ?>, '<?= htmlspecialchars($product['name']) ?>', <?= $product['price'] ?>, '<?= $product['image'] ?>')" title="Thêm vào giỏ hàng">
                                    <i class="bi bi-bag-plus"></i>
                                </div>
                                <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['id'] ?>" class="product-card-icon" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                        <p class="text-uppercase small text-muted mb-1"><?= $product['category'] ?></p>
                        <h3 class="h6"><?= $product['name'] ?></h3>
                        <p class="fw-semibold"><?= number_format($product['price'], 0, ',', '.') ?> đ</p>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php
    $currentPage = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, min(36, (int)($_GET['per_page'] ?? 12)));
    $totalPages = ceil(($totalProducts ?? 0) / $perPage);
    if ($totalPages > 1):
    ?>
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <?php if ($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= BASE_URL ?>?action=products&page=<?= $currentPage - 1 ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category_id']) ? '&category_id=' . (int)$_GET['category_id'] : '' ?><?= isset($_GET['price']) ? '&price=' . urlencode($_GET['price']) : '' ?>">Trước</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>?action=products&page=<?= $i ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category_id']) ? '&category_id=' . (int)$_GET['category_id'] : '' ?><?= isset($_GET['price']) ? '&price=' . urlencode($_GET['price']) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= BASE_URL ?>?action=products&page=<?= $currentPage + 1 ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category_id']) ? '&category_id=' . (int)$_GET['category_id'] : '' ?><?= isset($_GET['price']) ? '&price=' . urlencode($_GET['price']) : '' ?>">Sau</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
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
                            
                            <!-- Size Selector (Dynamic) -->
                            <div class="mb-4" id="sizeSelector">
                                <label class="form-label small text-uppercase fw-bold text-muted mb-2">Kích thước</label>
                                <div class="d-flex gap-2 flex-wrap" id="sizeOptions">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                            
                            <!-- Color Selector (Dynamic) -->
                            <div class="mb-4" id="colorSelector">
                                <label class="form-label small text-uppercase fw-bold text-muted mb-2">Màu sắc</label>
                                <div class="d-flex gap-2 flex-wrap" id="colorOptions">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                            
                            <!-- Quantity -->
                            <div class="mb-4">
                                <label class="form-label small text-uppercase fw-bold text-muted mb-2">Số lượng</label>
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary rounded-0" type="button" onclick="changeQaQty(-1)">-</button>
                                    <input type="number" class="form-control text-center border-secondary border-start-0 border-end-0" id="qaQuantity" value="1" min="1" readonly>
                                    <button class="btn btn-outline-secondary rounded-0" type="button" onclick="changeQaQty(1)">+</button>
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

<style>
    /* Custom styles for color selector active state */
    .btn-check:checked + label {
        outline: 2px solid #000;
        outline-offset: 2px;
    }
    
    .color-option {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .color-option.white {
        border: 1px solid #ccc;
    }
</style>

<script>
    // Color mapping for display
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

    // Mở Modal Quick Add
    function openQuickAdd(id, name, price, image) {
        document.getElementById('qaProductId').value = id;
        document.getElementById('qaProductName').textContent = name;
        document.getElementById('qaProductPrice').textContent = new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        document.getElementById('qaProductImage').src = image;
        document.getElementById('qaQuantity').value = 1;
        
        // Load attributes from database
        loadProductAttributes(id);
        
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('quickAddModal'));
        modal.show();
    }
    
    // Load product attributes dynamically
    function loadProductAttributes(productId) {
        fetch(`<?= BASE_URL ?>?action=product-attributes&product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderSizeOptions(data.data.sizes || []);
                    renderColorOptions(data.data.colors || []);
                    
                    // Set default selections
                    if (data.data.sizes && data.data.sizes.length > 0) {
                        document.querySelector('input[name="qaSize"]').checked = true;
                    }
                    if (data.data.colors && data.data.colors.length > 0) {
                        document.querySelector('input[name="qaColor"]').checked = true;
                        // Update image when color is selected
                        updateProductImage(productId, null, data.data.colors[0]);
                    }
                }
            })
            .catch(err => {
                console.error('Error loading attributes:', err);
                // Fallback to default options
                renderSizeOptions(['S', 'M', 'L', 'XL']);
                renderColorOptions(['Black', 'White', 'Beige']);
            });
    }
    
    // Render size options
    function renderSizeOptions(sizes) {
        const container = document.getElementById('sizeOptions');
        container.innerHTML = '';
        
        sizes.forEach((size, index) => {
            const id = `size${size}${index}`;
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.className = 'btn-check';
            radio.name = 'qaSize';
            radio.id = id;
            radio.value = size;
            if (index === 0) radio.checked = true;
            radio.addEventListener('change', () => {
                const productId = document.getElementById('qaProductId').value;
                const color = document.querySelector('input[name="qaColor"]:checked')?.value;
                updateProductImage(productId, size, color);
            });
            
            const label = document.createElement('label');
            label.className = 'btn btn-outline-dark rounded-0 px-3';
            label.htmlFor = id;
            label.textContent = size;
            
            container.appendChild(radio);
            container.appendChild(label);
        });
    }
    
    // Render color options
    function renderColorOptions(colors) {
        const container = document.getElementById('colorOptions');
        container.innerHTML = '';
        
        colors.forEach((color, index) => {
            const id = `color${color.replace(/\s+/g, '')}${index}`;
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.className = 'btn-check';
            radio.name = 'qaColor';
            radio.id = id;
            radio.value = color;
            if (index === 0) radio.checked = true;
            radio.addEventListener('change', () => {
                const productId = document.getElementById('qaProductId').value;
                const size = document.querySelector('input[name="qaSize"]:checked')?.value;
                updateProductImage(productId, size, color);
            });
            
            const label = document.createElement('label');
            label.className = 'btn rounded-circle p-0 border border-2 border-white shadow-sm color-option';
            if (color.toLowerCase() === 'white') label.classList.add('white');
            label.htmlFor = id;
            label.style.width = '30px';
            label.style.height = '30px';
            label.style.backgroundColor = colorMap[color] || '#ccc';
            label.style.cursor = 'pointer';
            label.title = color;
            
            container.appendChild(radio);
            container.appendChild(label);
        });
    }
    
    // Update product image based on selected attributes
    function updateProductImage(productId, size, color) {
        if (!productId) return;
        
        const params = new URLSearchParams({
            product_id: productId
        });
        if (size) params.append('size', size);
        if (color) params.append('color', color);
        
        fetch(`<?= BASE_URL ?>?action=variant-images&${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    document.getElementById('qaProductImage').src = data.data[0];
                }
            })
            .catch(err => console.error('Error updating image:', err));
    }
    
    // Thay đổi số lượng trong modal
    function changeQaQty(change) {
        const input = document.getElementById('qaQuantity');
        let val = parseInt(input.value) + change;
        if (val < 1) val = 1;
        input.value = val;
    }
    
    // Submit Quick Add
    function submitQuickAdd(action) {
        const productId = document.getElementById('qaProductId').value;
        const quantity = document.getElementById('qaQuantity').value;
        const sizeEl = document.querySelector('input[name="qaSize"]:checked');
        const colorEl = document.querySelector('input[name="qaColor"]:checked');
        
        if (!sizeEl || !colorEl) {
            alert('Vui lòng chọn đầy đủ thuộc tính sản phẩm');
            return;
        }
        
        const size = sizeEl.value;
        const color = colorEl.value;
        
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
                // Show login modal or redirect
                const modal = bootstrap.Modal.getInstance(document.getElementById('quickAddModal'));
                if (modal) modal.hide();
                showToast('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng', 'warning');
                // Optionally trigger login modal
                setTimeout(() => {
                    window.location.href = '<?= BASE_URL ?>?action=show-login';
                }, 1500);
                return;
            }
            
            if (res.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('quickAddModal'));
                if (modal) modal.hide();
                
                // Cập nhật số lượng trên icon giỏ hàng
                updateCartCount();

                if (action === 'cart') {
                    showToast('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    window.location.href = '<?= BASE_URL ?>?action=checkout';
                }
            } else {
                showToast(res.message || 'Có lỗi xảy ra', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
        });
    }

    // Toast Notification Function
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
</script>

