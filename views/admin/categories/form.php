<?php
$isEditing = isset($category);
$categoryId = $isEditing ? (int)$category['category_id'] : null;
?>

<style>
    .admin-section {
        padding: 0;
    }
    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .form-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .form-card {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
    }
    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    .btn-submit {
        background: #3b82f6;
        color: #fff;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-submit:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        margin-right: 1rem;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
</style>

<div class="form-header">
    <h2><?= $isEditing ? 'Chỉnh sửa danh mục' : 'Thêm danh mục mới' ?></h2>
    <a href="<?= BASE_URL ?>?action=admin-categories" class="btn-cancel">Quay lại</a>
</div>

<div class="form-card">
    <form method="POST" action="<?= BASE_URL ?>?action=<?= $isEditing ? 'admin-category-update' : 'admin-category-store' ?>">
        <?php if ($isEditing): ?>
            <input type="hidden" name="category_id" value="<?= $categoryId ?>">
        <?php endif; ?>
        
        <div style="margin-bottom: 1.5rem;">
            <label class="form-label" for="name">Tên danh mục <span style="color: #ef4444;">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control" 
                required
                value="<?= htmlspecialchars($category['category_name'] ?? '') ?>"
                placeholder="Nhập tên danh mục"
            >
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label class="form-label" for="description">Mô tả</label>
            <textarea 
                id="description" 
                name="description" 
                class="form-control"
                placeholder="Nhập mô tả danh mục (tùy chọn)"
            ><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="<?= BASE_URL ?>?action=admin-categories" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-submit">
                <?= $isEditing ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
        </div>
    </form>
</div>

