<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Sửa thuộc tính</h2>
    <a href="<?= BASE_URL ?>?action=admin-attributes" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <!-- Form sửa thuộc tính -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-pencil"></i> Thông tin thuộc tính</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-update" onsubmit="return validateForm()">
                    <input type="hidden" name="id" value="<?= $attribute['attribute_id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Tên thuộc tính <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($attribute['attribute_name']) ?>">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>?action=admin-attributes" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quản lý giá trị -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-list-ul"></i> Giá trị thuộc tính</h6>
            </div>
            <div class="card-body">
                <!-- Form thêm giá trị mới -->
                <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-add-value" class="mb-3">
                    <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                    
                    <div class="input-group">
                        <input type="text" name="value_name" class="form-control" placeholder="Nhập giá trị mới..." required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Thêm
                        </button>
                    </div>
                </form>

                <!-- Danh sách giá trị -->
                <?php if (empty($values)): ?>
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">Chưa có giá trị nào</p>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($values as $value): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary"><?= htmlspecialchars($value['value_name']) ?></span>
                                <button onclick="confirmDelete('<?= BASE_URL ?>?action=admin-attribute-delete-value&value_id=<?= $value['value_id'] ?>&attribute_id=<?= $attribute['attribute_id'] ?>', '<?= htmlspecialchars($value['value_name']) ?>')" 
                                        class="btn btn-sm btn-danger" 
                                        title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Ví dụ -->
<div class="card mt-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Ví dụ sử dụng</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Thuộc tính: Size</h6>
                <p class="mb-2">Giá trị:</p>
                <div>
                    <span class="badge bg-secondary me-1">S</span>
                    <span class="badge bg-secondary me-1">M</span>
                    <span class="badge bg-secondary me-1">L</span>
                    <span class="badge bg-secondary me-1">XL</span>
                    <span class="badge bg-secondary me-1">XXL</span>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Thuộc tính: Màu sắc</h6>
                <p class="mb-2">Giá trị:</p>
                <div>
                    <span class="badge bg-secondary me-1">Đen</span>
                    <span class="badge bg-secondary me-1">Trắng</span>
                    <span class="badge bg-secondary me-1">Xám</span>
                    <span class="badge bg-secondary me-1">Xanh Navy</span>
                    <span class="badge bg-secondary me-1">Đỏ Đô</span>
                </div>
            </div>
        </div>
        <hr>
        <p class="mb-0 text-muted">
            <i class="bi bi-info-circle"></i> 
            Sau khi tạo thuộc tính và giá trị, bạn có thể liên kết chúng với sản phẩm cụ thể.
        </p>
    </div>
</div>

<script>
    function validateForm() {
        const name = document.querySelector('input[name="name"]').value.trim();

        if (!name) {
            alert('Vui lòng nhập tên thuộc tính');
            return false;
        }

        return true;
    }
</script>
