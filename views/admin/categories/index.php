<div class="categories-header">
    <h1>Quản lý danh mục</h1>
    <a href="<?= BASE_URL ?>?action=admin-category-create" class="btn-add-category">
        <i class="bi bi-plus-lg"></i>
        Thêm danh mục mới
    </a>
</div>

<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Hình ảnh</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox empty-icon-lg"></i>
                        <div>Chưa có danh mục nào</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($category['category_id']) ?></strong></td>
                        <td><strong><?= htmlspecialchars($category['category_name']) ?></strong></td>
                        <td><?= htmlspecialchars($category['description'] ?? '') ?: '<span class="text-muted">-</span>' ?></td>
                        <td>
                            <?php if (!empty($category['image_url'])): ?>
                                <img src="<?= htmlspecialchars($category['image_url']) ?>" alt="<?= htmlspecialchars($category['category_name']) ?>" class="category-image">
                            <?php else: ?>
                                <span class="no-image">Không có</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge active">Hoạt động</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= BASE_URL ?>?action=admin-category-edit&id=<?= $category['category_id'] ?>" class="btn-edit" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-category-delete" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                    <input type="hidden" name="category_id" value="<?= $category['category_id'] ?>">
                                    <button type="submit" class="btn-delete" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


