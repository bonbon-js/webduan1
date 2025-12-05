<div class="admin-page-header">
    <h1>Quản lý sản phẩm</h1>
    <div class="admin-page-actions">
        <a href="<?= BASE_URL ?>?action=admin-products-trash" class="btn btn-light-soft">
            <i class="bi bi-trash"></i>
            Thùng rác
        </a>
        <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-light-soft">
            <i class="bi bi-plus-lg"></i>
            Thêm sản phẩm mới
        </a>
    </div>
</div>

<!-- Form tìm kiếm và lọc -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>" id="searchForm">
            <input type="hidden" name="action" value="admin-products">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-uppercase fw-bold">Tìm kiếm</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" 
                               name="keyword" 
                               class="form-control" 
                               placeholder="Tên sản phẩm..." 
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Danh mục</label>
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" 
                                    <?= ($_GET['category'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">Mức giá</label>
                    <select name="price_range" class="form-select">
                        <option value="">Tất cả mức giá</option>
                        <option value="0-500000" <?= ($_GET['price_range'] ?? '') == '0-500000' ? 'selected' : '' ?>>Dưới 500.000đ</option>
                        <option value="500000-1000000" <?= ($_GET['price_range'] ?? '') == '500000-1000000' ? 'selected' : '' ?>>500.000đ - 1.000.000đ</option>
                        <option value="1000000-2000000" <?= ($_GET['price_range'] ?? '') == '1000000-2000000' ? 'selected' : '' ?>>1.000.000đ - 2.000.000đ</option>
                        <option value="2000000-999999999" <?= ($_GET['price_range'] ?? '') == '2000000-999999999' ? 'selected' : '' ?>>Trên 2.000.000đ</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search"></i> Tìm
                        </button>
                        <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá gốc</th>
                <th>Giá khuyến mãi</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox empty-icon-lg"></i>
                        <div>Chưa có sản phẩm nào</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): 
                    $originalPrice = (float)($product['price'] ?? 0);
                    // For now, use the same price as promotional price
                    // In the future, you can add discount logic here
                    $promotionalPrice = $originalPrice;
                ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($product['product_id']) ?></strong></td>
                        <td>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
                            <?php else: ?>
                                <div class="placeholder-80-box">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                        </td>
                        <td>
                            <span class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></span>
                        </td>
                        <td>
                            <span class="price-original"><?= number_format($originalPrice, 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="price-promotional"><?= number_format($promotionalPrice, 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="stock-amount"><?= htmlspecialchars($product['stock'] ?? 0) ?></span>
                        </td>
                        <td>
                            <span class="status-badge active">Hoạt động</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['product_id'] ?>" class="btn-edit" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-delete" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit" class="btn-delete" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


