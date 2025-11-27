<section class="admin-section">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Cấu hình hệ thống</p>
                <h2 class="fw-bold mb-0">Quản lý danh mục</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-dark">
                <i class="bi bi-box-seam me-2"></i>Danh sách sản phẩm
            </a>
        </div>

        <div class="row g-4">
            <!-- Form thêm danh mục -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <h5 class="fw-bold mb-0"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Thêm danh mục mới</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>?action=admin-category-store">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Áo khoác, Quần jean..." required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả ngắn gọn về danh mục này..."></textarea>
                                <small class="text-muted">Tùy chọn: Giúp khách hàng hiểu rõ hơn về danh mục</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-plus-lg me-2"></i>Thêm danh mục
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách danh mục -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-success"></i>Danh sách danh mục</h5>
                            <span class="badge bg-primary rounded-pill px-3 py-2"><?= count($categories) ?> danh mục</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-3 mb-0">Chưa có danh mục nào. Hãy thêm mới ở khung bên trái.</p>
                            </div>
                        <?php else: ?>
                            <div class="accordion" id="categoryAccordion">
                                <?php foreach ($categories as $index => $category): ?>
                                    <div class="accordion-item border rounded-3 mb-3 overflow-hidden">
                                        <h2 class="accordion-header" id="heading-cat-<?= $category['category_id'] ?>">
                                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?> fw-semibold" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-cat-<?= $category['category_id'] ?>"
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                                <i class="bi bi-tag-fill me-2 text-primary"></i>
                                                <?= htmlspecialchars($category['category_name']) ?>
                                                <span class="badge bg-light text-dark ms-2">ID: <?= $category['category_id'] ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapse-cat-<?= $category['category_id'] ?>" 
                                             class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                             data-bs-parent="#categoryAccordion">
                                            <div class="accordion-body bg-light">
                                                <form method="POST" action="<?= BASE_URL ?>?action=admin-category-update">
                                                    <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Tên danh mục</label>
                                                        <input type="text" name="name" class="form-control" 
                                                               value="<?= htmlspecialchars($category['category_name']) ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Mô tả</label>
                                                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                                                    </div>

                                                    <?php if (!empty($category['description'])): ?>
                                                        <div class="alert alert-info mb-3">
                                                            <small><strong>Mô tả hiện tại:</strong><br><?= nl2br(htmlspecialchars($category['description'])) ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-success flex-fill">
                                                            <i class="bi bi-check-circle me-1"></i>Lưu thay đổi
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger"
                                                            onclick="if(confirm('⚠️ Xóa danh mục này sẽ ảnh hưởng đến các sản phẩm liên quan.\n\nBạn có chắc chắn muốn tiếp tục?')) { document.getElementById('delete-cat-<?= $category['category_id'] ?>').submit(); }">
                                                            <i class="bi bi-trash me-1"></i>Xóa
                                                        </button>
                                                    </div>
                                                </form>
                                                <form id="delete-cat-<?= $category['category_id'] ?>" method="POST" class="d-none" 
                                                      action="<?= BASE_URL ?>?action=admin-category-delete">
                                                    <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
