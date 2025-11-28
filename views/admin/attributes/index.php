<style>
    .attributes-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .attributes-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .btn-add {
        background: #3b82f6;
        color: #fff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }
    .btn-add:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        color: #fff;
    }
    .attributes-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .attribute-card {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .attribute-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .attribute-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }
    .attribute-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn-sm {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-edit-sm {
        background: #f59e0b;
        color: #fff;
    }
    .btn-edit-sm:hover {
        background: #d97706;
        transform: translateY(-2px);
    }
    .btn-delete-sm {
        background: #ef4444;
        color: #fff;
    }
    .btn-delete-sm:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }
    .values-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .value-item {
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e2e8f0;
    }
    .value-name {
        font-weight: 600;
        color: #1e293b;
    }
    .value-actions {
        display: flex;
        gap: 0.25rem;
    }
    .btn-icon {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .add-value-form {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }
    .form-inline {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }
    .form-inline .form-group {
        flex: 1;
    }
    .form-inline label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.25rem;
    }
    .form-inline input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
    }
    .form-inline input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .btn-add-value {
        background: #10b981;
        color: #fff;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-add-value:hover {
        background: #059669;
    }
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }
    .modal-form {
        display: none;
    }
    .modal-form.active {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
    }
</style>

<div class="attributes-container">
    <div class="attributes-header">
        <h1>Quản lý thuộc tính</h1>
        <button type="button" class="btn-add" onclick="showAddAttributeModal()">
            <i class="bi bi-plus-lg"></i>
            Thêm thuộc tính mới
        </button>
    </div>

    <?php if (empty($attributes)): ?>
    <div class="empty-state">
        <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
        <div>Chưa có thuộc tính nào. Hãy thêm thuộc tính đầu tiên (ví dụ: Size, Màu sắc)</div>
    </div>
<?php else: ?>
    <?php foreach ($attributes as $attribute): ?>
        <div class="attribute-card">
            <div class="attribute-header">
                <div class="attribute-name"><?= htmlspecialchars($attribute['attribute_name']) ?></div>
                <div class="attribute-actions">
                    <button type="button" class="btn-sm btn-edit-sm" onclick="showEditAttributeModal(<?= $attribute['attribute_id'] ?>, '<?= htmlspecialchars($attribute['attribute_name'], ENT_QUOTES) ?>')">
                        <i class="bi bi-pencil"></i> Sửa
                    </button>
                    <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-delete" style="display: inline;" onsubmit="return confirm('Xóa thuộc tính này sẽ xóa tất cả giá trị liên quan. Bạn có chắc?');">
                        <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                        <button type="submit" class="btn-sm btn-delete-sm">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>

            <div class="values-list">
                <?php if (empty($attribute['values'])): ?>
                    <div style="grid-column: 1 / -1; color: #94a3b8; font-style: italic;">Chưa có giá trị nào</div>
                <?php else: ?>
                    <?php foreach ($attribute['values'] as $value): ?>
                        <div class="value-item">
                            <span class="value-name"><?= htmlspecialchars($value['value_name']) ?></span>
                            <div class="value-actions">
                                <button type="button" class="btn-icon" style="background: #f59e0b; color: #fff;" onclick="showEditValueModal(<?= $value['value_id'] ?>, '<?= htmlspecialchars($value['value_name'], ENT_QUOTES) ?>')" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-value-delete" style="display: inline;" onsubmit="return confirm('Xóa giá trị này?');">
                                    <input type="hidden" name="value_id" value="<?= $value['value_id'] ?>">
                                    <button type="submit" class="btn-icon" style="background: #ef4444; color: #fff;" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="add-value-form">
                <form method="POST" action="<?= BASE_URL ?>?action=admin-attribute-value-store" class="form-inline">
                    <input type="hidden" name="attribute_id" value="<?= $attribute['attribute_id'] ?>">
                    <div class="form-group">
                        <label>Thêm giá trị mới</label>
                        <input type="text" name="value_name" class="form-control" placeholder="Ví dụ: S, M, L hoặc Đỏ, Xanh..." required>
                    </div>
                    <button type="submit" class="btn-add-value">
                        <i class="bi bi-plus"></i> Thêm
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal thêm/sửa thuộc tính -->
<div id="attributeModal" class="modal-form" onclick="if(event.target === this) closeAttributeModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <h3 id="modalTitle" style="margin-bottom: 1.5rem;">Thêm thuộc tính</h3>
        <form method="POST" id="attributeForm">
            <input type="hidden" name="attribute_id" id="modalAttributeId">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tên thuộc tính</label>
                <input type="text" name="name" id="modalAttributeName" class="form-control" required placeholder="Ví dụ: Size, Màu sắc">
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn-cancel" onclick="closeAttributeModal()">Hủy</button>
                <button type="submit" class="btn-submit">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal sửa giá trị -->
<div id="valueModal" class="modal-form" onclick="if(event.target === this) closeValueModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <h3 style="margin-bottom: 1.5rem;">Sửa giá trị</h3>
        <form method="POST" id="valueForm" action="<?= BASE_URL ?>?action=admin-attribute-value-update">
            <input type="hidden" name="value_id" id="modalValueId">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tên giá trị</label>
                <input type="text" name="value_name" id="modalValueName" class="form-control" required>
            </div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button" class="btn-cancel" onclick="closeValueModal()">Hủy</button>
                <button type="submit" class="btn-submit">Lưu</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
function showAddAttributeModal() {
    document.getElementById('modalTitle').textContent = 'Thêm thuộc tính';
    document.getElementById('attributeForm').action = '<?= BASE_URL ?>?action=admin-attribute-store';
    document.getElementById('modalAttributeId').value = '';
    document.getElementById('modalAttributeName').value = '';
    document.getElementById('attributeModal').classList.add('active');
}

function showEditAttributeModal(id, name) {
    document.getElementById('modalTitle').textContent = 'Sửa thuộc tính';
    document.getElementById('attributeForm').action = '<?= BASE_URL ?>?action=admin-attribute-update';
    document.getElementById('modalAttributeId').value = id;
    document.getElementById('modalAttributeName').value = name;
    document.getElementById('attributeModal').classList.add('active');
}

function closeAttributeModal() {
    document.getElementById('attributeModal').classList.remove('active');
}

function showEditValueModal(id, name) {
    document.getElementById('modalValueId').value = id;
    document.getElementById('modalValueName').value = name;
    document.getElementById('valueModal').classList.add('active');
}

function closeValueModal() {
    document.getElementById('valueModal').classList.remove('active');
}
</script>

