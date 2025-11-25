<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-tags"></i> Quản lý danh mục</h2>
    <a href="<?= BASE_URL ?>?action=admin-category-create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm danh mục mới
    </a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có danh mục nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th width="150">Ngày tạo</th>
                            <th width="150" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['category_id'] ?></td>
                                <td><strong><?= htmlspecialchars($category['category_name']) ?></strong></td>
                                <td><?= htmlspecialchars($category['description'] ?? '') ?></td>
                                <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                                <td class="table-actions text-center">
                                    <a href="<?= BASE_URL ?>?action=admin-category-edit&id=<?= $category['category_id'] ?>" 
                                       class="btn btn-sm btn-warning btn-action" 
                                       title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="confirmDelete('<?= BASE_URL ?>?action=admin-category-delete&id=<?= $category['category_id'] ?>', '<?= htmlspecialchars($category['category_name']) ?>')" 
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
        <?php endif; ?>
    </div>
</div>
