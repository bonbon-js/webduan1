<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Sửa danh mục</h2>
    <a href="<?= BASE_URL ?>?action=admin-categories" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>?action=admin-category-update" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?= $category['category_id'] ?>">
            
            <div class="mb-3">
                <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['category_name']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>?action=admin-categories" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        const name = document.querySelector('input[name="name"]').value.trim();
        
        if (!name) {
            alert('Vui lòng nhập tên danh mục');
            return false;
        }
        
        return true;
    }
</script>
