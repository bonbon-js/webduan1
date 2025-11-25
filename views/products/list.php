<style>
    .filter-section {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 40px;
    }
    
    .filter-title {
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .pagination {
        justify-content: center;
        margin-top: 40px;
    }
    
    .form-check {
        margin-bottom: 0.5rem;
    }
    
    .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    
    .form-check-input:checked + .form-check-label {
        font-weight: 600;
        color: #000;
    }
</style>

<div class="container py-5">
    <h1 class="section-title mb-5">Sản phẩm</h1>
    
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="" id="filterForm">
            <input type="hidden" name="action" value="products">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label filter-title">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label filter-title">Danh mục</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label filter-title">Giá từ</label>
                    <input type="number" name="min_price" class="form-control" placeholder="0" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label filter-title">Đến</label>
                    <input type="number" name="max_price" class="form-control" placeholder="1000000" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                </div>
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-dark w-100">Lọc</button>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-3">
                    <label class="form-label filter-title">Trạng thái</label>
                    <select name="in_stock" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" <?= (isset($_GET['in_stock']) && $_GET['in_stock'] == '1') ? 'selected' : '' ?>>Còn hàng</option>
                        <option value="0" <?= (isset($_GET['in_stock']) && $_GET['in_stock'] == '0') ? 'selected' : '' ?>>Hết hàng</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label filter-title">Sắp xếp</label>
                    <select name="order_by" class="form-select">
                        <option value="p.product_id" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'p.product_id') ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="p.price" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'p.price') ? 'selected' : '' ?>>Giá</option>
                        <option value="p.product_name" <?= (isset($_GET['order_by']) && $_GET['order_by'] == 'p.product_name') ? 'selected' : '' ?>>Tên</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label filter-title">Thứ tự</label>
                    <select name="order_dir" class="form-select">
                        <option value="DESC" <?= (isset($_GET['order_dir']) && $_GET['order_dir'] == 'DESC') ? 'selected' : '' ?>>Giảm dần</option>
                        <option value="ASC" <?= (isset($_GET['order_dir']) && $_GET['order_dir'] == 'ASC') ? 'selected' : '' ?>>Tăng dần</option>
                    </select>
                </div>
            </div>
            
            <!-- Lọc theo thuộc tính -->
            <?php if (!empty($filterableAttributes)): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-label filter-title">Lọc theo thuộc tính</label>
                    </div>
                    <?php foreach ($filterableAttributes as $attribute): ?>
                        <div class="col-md-3 mb-2">
                            <label class="form-label small"><?= htmlspecialchars($attribute['attribute_name']) ?></label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($attribute['values'] as $value): ?>
                                    <?php 
                                    $isChecked = isset($_GET['attr']) && in_array($value['value_id'], $_GET['attr']);
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="attr[]" value="<?= $value['value_id'] ?>" id="attr_<?= $value['value_id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="attr_<?= $value['value_id'] ?>">
                                            <?= htmlspecialchars($value['value_name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Products Grid -->
    <div class="product-grid">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <div class="product-card-image-wrapper">
                        <img src="<?= !empty($product['primary_image']) ? BASE_URL . $product['primary_image'] : 'https://via.placeholder.com/300' ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <div class="product-card-overlay">
                            <div class="product-card-icon" onclick="openQuickAdd(<?= $product['product_id'] ?>, '<?= htmlspecialchars($product['product_name']) ?>', <?= $product['price'] ?>, '<?= !empty($product['primary_image']) ? BASE_URL . $product['primary_image'] : 'https://via.placeholder.com/300' ?>')" title="Thêm vào giỏ hàng">
                                <i class="bi bi-bag-plus"></i>
                            </div>
                            <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['product_id'] ?>" class="product-card-icon" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                    <p class="text-uppercase small text-muted mb-1"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></p>
                    <h3 class="h6"><?= htmlspecialchars($product['product_name']) ?></h3>
                    <p class="fw-semibold"><?= number_format($product['price'], 0, ',', '.') ?> đ</p>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="<?= BASE_URL ?>?action=products&page=<?= $i ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['min_price']) ? '&min_price=' . $_GET['min_price'] : '' ?><?= isset($_GET['max_price']) ? '&max_price=' . $_GET['max_price'] : '' ?><?= isset($_GET['order_by']) ? '&order_by=' . $_GET['order_by'] : '' ?><?= isset($_GET['order_dir']) ? '&order_dir=' . $_GET['order_dir'] : '' ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
