<section class="admin-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Danh mục sản phẩm</p>
                <h2 class="fw-bold mb-0">Quản lý sản phẩm</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-dark">
                <i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="admin-products">
                    <div class="col-md-4">
                        <label class="form-label small text-uppercase">Từ khóa</label>
                        <input type="text" class="form-control" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tên sản phẩm, mô tả...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-uppercase">Danh mục</label>
                        <select class="form-select" name="category_id">
                            <option value="">Tất cả</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>" <?= (isset($categoryId) && (int)$categoryId === (int)$category['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-dark w-100">Lọc</button>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="<?= BASE_URL ?>?action=admin-products" class="text-decoration-none text-muted small">Xóa bộ lọc</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th width="8%"></th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th width="20%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Không tìm thấy sản phẩm phù hợp.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($product['image_url'])): ?>
                                                <img src="<?= $product['image_url'] ?>" class="rounded" style="width: 56px; height: 56px; object-fit: cover;" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?= htmlspecialchars($product['product_name']) ?></div>
                                            <small class="text-muted">ID: <?= $product['product_id'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($product['category_name'] ?? '—') ?></td>
                                        <td><?= number_format($product['price'], 0, ',', '.') ?> đ</td>
                                        <td><span class="badge bg-light text-dark"><?= (int)$product['stock'] ?> sp</span></td>
                                        <td class="text-end">
                                            <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['product_id'] ?>" class="btn btn-sm btn-outline-dark me-2">Chỉnh sửa</a>
                                            <form method="POST" action="<?= BASE_URL ?>?action=admin-product-delete" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?');">
                                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
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
    </div>
</section>

