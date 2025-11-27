<style>
.product-image-thumbnail {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 8px;
}
.product-image-placeholder {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.product-card-hover {
    transition: all 0.2s ease;
}
.product-card-hover:hover {
    background-color: #f8f9fa !important;
}
.filter-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
</style>

<section class="admin-section">
    <div class="container-fluid">
        <!-- Header với thống kê -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Quản lý kho hàng</p>
                <h2 class="fw-bold mb-2">Quản lý sản phẩm</h2>
                <div class="d-flex gap-3">
                    <span class="badge bg-primary filter-badge"><i class="bi bi-box-seam me-1"></i><?= count($products) ?> sản phẩm</span>
                    <span class="badge bg-success filter-badge"><i class="bi bi-tags me-1"></i><?= count($categories) ?> danh mục</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>?action=admin-categories" class="btn btn-outline-primary">
                    <i class="bi bi-gear me-2"></i>Quản lý danh mục
                </a>
                <a href="<?= BASE_URL ?>?action=admin-attributes" class="btn btn-outline-secondary">
                    <i class="bi bi-sliders me-2"></i>Quản lý thuộc tính
                </a>
                <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-dark">
                    <i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm
                </a>
            </div>
        </div>

        <!-- Bộ lọc nâng cao -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="admin-products">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold"><i class="bi bi-search me-1"></i>Tìm kiếm</label>
                        <input type="text" class="form-control" name="keyword" 
                               value="<?= htmlspecialchars($keyword ?? '') ?>" 
                               placeholder="Nhập tên sản phẩm, mô tả hoặc ID...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold"><i class="bi bi-funnel me-1"></i>Danh mục</label>
                        <select class="form-select" name="category_id">
                            <option value="">🏷️ Tất cả danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>" 
                                    <?= (isset($categoryId) && (int)$categoryId === (int)$category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-funnel-fill me-1"></i>Áp dụng
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>Xóa lọc
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 text-uppercase small" width="10%">Hình ảnh</th>
                                <th class="border-0 text-uppercase small">Sản phẩm</th>
                                <th class="border-0 text-uppercase small" width="15%">Danh mục</th>
                                <th class="border-0 text-uppercase small" width="12%">Giá bán</th>
                                <th class="border-0 text-uppercase small" width="10%">Tồn kho</th>
                                <th class="border-0 text-uppercase small text-end" width="20%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 3rem;"></i>
                                        <p class="text-muted mb-0">Không tìm thấy sản phẩm phù hợp.</p>
                                        <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-sm btn-outline-primary mt-3">
                                            <i class="bi bi-plus-circle me-1"></i>Thêm sản phẩm đầu tiên
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr class="product-card-hover">
                                        <td class="p-3">
                                            <?php if (!empty($product['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                                     class="product-image-thumbnail shadow-sm" 
                                                     alt="<?= htmlspecialchars($product['product_name']) ?>">
                                            <?php else: ?>
                                                <div class="product-image-placeholder d-flex align-items-center justify-content-center shadow-sm">
                                                    <i class="bi bi-image text-white" style="font-size: 1.5rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($product['product_name']) ?></div>
                                            <small class="text-muted">
                                                <i class="bi bi-hash"></i><?= $product['product_id'] ?>
                                            </small>
                                            <?php if (!empty($product['description'])): ?>
                                                <div class="small text-muted mt-1" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= htmlspecialchars($product['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3">
                                            <?php if (!empty($product['category_name'])): ?>
                                                <span class="badge bg-light text-dark border">
                                                    <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($product['category_name']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3">
                                            <div class="fw-bold text-success"><?= number_format($product['price'], 0, ',', '.') ?> đ</div>
                                        </td>
                                        <td class="p-3">
                                            <?php
                                            $stock = (int)$product['stock'];
                                            $stockClass = $stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger');
                                            $stockIcon = $stock > 10 ? 'check-circle' : ($stock > 0 ? 'exclamation-triangle' : 'x-circle');
                                            ?>
                                            <span class="badge bg-<?= $stockClass ?> bg-opacity-10 text-<?= $stockClass ?> border border-<?= $stockClass ?>">
                                                <i class="bi bi-<?= $stockIcon ?> me-1"></i><?= $stock ?> sp
                                            </span>
                                        </td>
                                        <td class="p-3 text-end">
                                            <div class="btn-group" role="group">
                                                <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['product_id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['product_id'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Xem chi tiết" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                            <form method="POST" action="<?= BASE_URL ?>?action=admin-product-delete" 
                                                  class="d-inline ms-1" 
                                                  onsubmit="return confirm('⚠️ Xóa sản phẩm sẽ xóa tất cả biến thể và dữ liệu liên quan.\n\nBạn có chắc chắn muốn xóa sản phẩm này?');">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($products)): ?>
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">
                    Hiển thị <strong><?= count($products) ?></strong> sản phẩm
                    <?php if (isset($keyword) && $keyword): ?>
                        với từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>"
                    <?php endif; ?>
                    <?php if (isset($categoryId) && $categoryId): ?>
                        trong danh mục đã chọn
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>
