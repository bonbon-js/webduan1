<style>
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .products-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .btn-back {
        background: #64748b;
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
    }
    .btn-back:hover {
        background: #475569;
        color: #fff;
    }
    .admin-table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .admin-table table {
        margin: 0;
        width: 100%;
    }
    .admin-table thead {
        background: #f8fafc;
    }
    .admin-table th {
        font-weight: 600;
        color: #1e293b;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        text-align: left;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .admin-table tbody tr {
        transition: background 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }
    .admin-table tbody tr:nth-child(even) {
        background: #fafbfc;
    }
    .admin-table tbody tr:hover {
        background: #f1f5f9;
    }
    .admin-table td {
        padding: 1rem;
        vertical-align: middle;
        color: #475569;
        font-size: 0.9rem;
    }
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .btn-restore {
        background: #10b981;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .btn-restore:hover {
        background: #059669;
        transform: translateY(-2px);
    }
    .btn-force-delete {
        background: #ef4444;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .btn-force-delete:hover {
        background: #dc2626;
        transform: translateY(-2px);
    }
    .deleted-badge {
        background: #fef3c7;
        color: #92400e;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>

<div class="products-header">
    <h1>Thùng rác sản phẩm</h1>
    <a href="<?= BASE_URL ?>?action=admin-products" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Quay lại danh sách
    </a>
</div>

<div class="admin-table">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Hình ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <div>Thùng rác trống</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($product['product_id']) ?></strong></td>
                        <td>
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                    <i class="bi bi-image" style="font-size: 1.5rem;"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($product['product_name']) ?></div>
                        </td>
                        <td>
                            <span style="color: #64748b;"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></span>
                        </td>
                        <td>
                            <span style="font-weight: 600;"><?= number_format((float)($product['price'] ?? 0), 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="deleted-badge">
                                <i class="bi bi-trash"></i> Đã xóa
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-restore" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit" class="btn-restore" title="Khôi phục">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-force-delete" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này? Hành động này không thể hoàn tác!');">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit" class="btn-force-delete" title="Xóa vĩnh viễn">
                                        <i class="bi bi-trash-fill"></i> Xóa vĩnh viễn
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

