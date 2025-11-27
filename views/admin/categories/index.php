<section class="admin-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Danh mục sản phẩm</p>
                <h2 class="fw-bold mb-0">Quản lý danh mục</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-outline-dark">Tới danh sách sản phẩm</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Thêm danh mục mới</h5>
                        <form method="POST" action="<?= BASE_URL ?>?action=admin-category-store">
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Tên danh mục</label>
                                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Áo khoác" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Mô tả</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Mô tả ngắn gọn"></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Thêm danh mục</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Danh sách hiện có</h5>
                            <span class="badge bg-secondary"><?= count($categories) ?> mục</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th width="25%">Tên</th>
                                        <th>Mô tả</th>
                                        <th width="20%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categories)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <form method="POST" action="<?= BASE_URL ?>?action=admin-category-update">
                                                        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                                        <input type="text" name="name" class="form-control form-control-sm mb-2" value="<?= htmlspecialchars($category['category_name']) ?>" required>
                                                        <textarea name="description" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                                        <div class="d-flex gap-2 mt-2">
                                                            <button class="btn btn-sm btn-dark">Lưu</button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                onclick="if(confirm('Bạn có chắc chắn muốn xóa danh mục này?')) { document.getElementById('delete-cat-<?= $category['category_id'] ?>').submit(); }">
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    </form>
                                                    <form id="delete-cat-<?= $category['category_id'] ?>" method="POST" class="d-none" action="<?= BASE_URL ?>?action=admin-category-delete">
                                                        <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                                    </form>
                                                </td>
                                                <td><?= htmlspecialchars($category['description'] ?? '—') ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark">ID: <?= $category['category_id'] ?></span>
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
        </div>
    </div>
</section>

