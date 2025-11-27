<section class="admin-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small">Biến thể & Thuộc tính</p>
                <h2 class="fw-bold mb-0">Quản lý thuộc tính sản phẩm</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-outline-dark">
                <i class="bi bi-collection me-2"></i>Danh sách sản phẩm
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Thêm thuộc tính mới</h5>
                        <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-store">
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Tên thuộc tính</label>
                                <input type="text" name="name" class="form-control" placeholder="Ví dụ: Size, Color..." required>
                            </div>
                            <button class="btn btn-dark w-100" type="submit">Thêm thuộc tính</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Danh sách thuộc tính</h5>
                            <span class="badge bg-secondary"><?= count($attributes ?? []) ?> mục</span>
                        </div>

                        <?php if (empty($attributes)): ?>
                            <div class="text-center text-muted py-5">
                                Chưa có thuộc tính nào. Hãy thêm mới ở khung bên trái.
                            </div>
                        <?php else: ?>
                            <div class="accordion" id="attributeAccordion">
                                <?php foreach ($attributes as $index => $attribute): ?>
                                    <div class="accordion-item mb-3 border-0 shadow-sm">
                                        <h2 class="accordion-header" id="heading-<?= $attribute['attribute_id'] ?>">
                                            <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-<?= $attribute['attribute_id'] ?>"
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
                                                <strong><?= htmlspecialchars($attribute['attribute_name']) ?></strong>
                                                <span class="ms-2 badge bg-light text-dark"><?= count($attribute['values']) ?> giá trị</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?= $attribute['attribute_id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                             data-bs-parent="#attributeAccordion">
                                            <div class="accordion-body">
                                                <form class="row gy-2 align-items-center" method="POST" action="<?= BASE_URL ?>?action=admin-attribute-update">
                                                    <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                                                    <div class="col-md-8">
                                                        <label class="form-label small text-uppercase">Tên thuộc tính</label>
                                                        <input type="text" name="name" class="form-control form-control-sm"
                                                            value="<?= htmlspecialchars($attribute['attribute_name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-4 d-flex gap-2 align-items-end">
                                                        <button class="btn btn-sm btn-dark w-100">Lưu</button>
                                                        <button class="btn btn-sm btn-outline-danger w-100" type="submit"
                                                            form="delete-attribute-<?= $attribute['attribute_id'] ?>"
                                                            onclick="return confirm('Xóa thuộc tính sẽ đồng thời xóa các giá trị và biến thể liên quan. Tiếp tục?');">
                                                            Xóa
                                                        </button>
                                                    </div>
                                                </form>
                                                <form id="delete-attribute-<?= $attribute['attribute_id'] ?>" method="POST"
                                                    action="<?= BASE_URL ?>?action=admin-attribute-delete">
                                                    <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                                                </form>
                                                <hr>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-2">Giá trị hiện có</h6>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <span class="text-muted small">ID: <?= $attribute['attribute_id'] ?></span>
                                                    </div>
                                                </div>
                                                <?php if (empty($attribute['values'])): ?>
                                                    <p class="text-muted fst-italic">Chưa có giá trị nào. Thêm mới bên dưới.</p>
                                                <?php else: ?>
                                                    <div class="list-group mb-3">
                                                        <?php foreach ($attribute['values'] as $value): ?>
                                                            <div class="list-group-item">
                                                                <div class="row g-2 align-items-center">
                                                                    <div class="col">
                                                                        <form class="d-flex gap-2" method="POST" action="<?= BASE_URL ?>?action=admin-attribute-value-update">
                                                                            <input type="hidden" name="value_id" value="<?= $value['value_id'] ?>">
                                                                            <input type="text" name="value_name" class="form-control form-control-sm"
                                                                                value="<?= htmlspecialchars($value['value_name']) ?>" required>
                                                                            <button class="btn btn-sm btn-outline-dark" type="submit" title="Lưu">
                                                                                <i class="bi bi-save"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm btn-outline-danger" type="submit"
                                                                                form="delete-value-<?= $value['value_id'] ?>"
                                                                                onclick="return confirm('Xóa giá trị này?');" title="Xóa">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                        <form id="delete-value-<?= $value['value_id'] ?>" method="POST"
                                                                            action="<?= BASE_URL ?>?action=admin-attribute-value-delete">
                                                                            <input type="hidden" name="value_id" value="<?= $value['value_id'] ?>">
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-value-store">
                                                    <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                                                    <div class="input-group">
                                                        <input type="text" name="value_name" class="form-control" placeholder="Thêm giá trị mới" required>
                                                        <button class="btn btn-dark" type="submit">Thêm</button>
                                                    </div>
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

