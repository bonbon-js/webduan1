<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-sliders"></i> Quản lý thuộc tính</h2>
    <a href="<?= BASE_URL ?>?action=admin-attribute-create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm thuộc tính mới
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($attributes)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có thuộc tính nào</p>
                <a href="<?= BASE_URL ?>?action=admin-attribute-create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm thuộc tính đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th width="200">Tên thuộc tính</th>
                            <th>Giá trị</th>
                            <th width="150" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attributes as $attribute): ?>
                            <tr>
                                <td><?= $attribute['attribute_id'] ?></td>
                                <td><strong><?= htmlspecialchars($attribute['attribute_name']) ?></strong></td>
                                <td>
                                    <?php if (!empty($attribute['values'])): ?>
                                        <?php foreach ($attribute['values'] as $value): ?>
                                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($value['value_name']) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có giá trị</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-actions text-center">
                                    <a href="<?= BASE_URL ?>?action=admin-attribute-edit&id=<?= $attribute['attribute_id'] ?>" 
                                       class="btn btn-sm btn-warning btn-action" 
                                       title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="confirmDelete('<?= BASE_URL ?>?action=admin-attribute-delete&id=<?= $attribute['attribute_id'] ?>', '<?= htmlspecialchars($attribute['attribute_name']) ?>')" 
                                            class="btn btn-sm btn-danger btn-action" 
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> 
                <strong>Hướng dẫn:</strong> Thuộc tính là các đặc điểm của sản phẩm như Size, Màu sắc, Chất liệu... 
                Mỗi thuộc tính có nhiều giá trị (VD: Size có S, M, L, XL).
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ví dụ -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Ví dụ thuộc tính thường dùng</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6>Size (Kích thước)</h6>
                <p class="text-muted small">Giá trị: S, M, L, XL, XXL</p>
            </div>
            <div class="col-md-4">
                <h6>Màu sắc</h6>
                <p class="text-muted small">Giá trị: Đen, Trắng, Xám, Xanh, Đỏ</p>
            </div>
            <div class="col-md-4">
                <h6>Chất liệu</h6>
                <p class="text-muted small">Giá trị: Cotton, Polyester, Denim, Kaki</p>
            </div>
        </div>
    </div>
</div>
