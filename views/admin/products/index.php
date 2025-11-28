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
    .btn-add-product {
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
    .btn-add-product:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
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
    .admin-table tbody tr:last-child {
        border-bottom: none;
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
    .product-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .product-category {
        font-size: 0.85rem;
        color: #64748b;
    }
    .price-original {
        color: #64748b;
        text-decoration: line-through;
        font-size: 0.9rem;
    }
    .price-promotional {
        color: #ef4444;
        font-weight: 700;
        font-size: 1rem;
    }
    .stock-amount {
        font-weight: 600;
        color: #1e293b;
    }
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }
    .status-badge.active {
        background: #10b981;
        color: #fff;
    }
    .status-badge.inactive {
        background: #ef4444;
        color: #fff;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    .btn-edit {
        background: #f59e0b;
        color: #fff;
        border: none;
        padding: 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-edit:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
        color: #fff;
    }
    .btn-delete {
        background: #ef4444;
        color: #fff;
        border: none;
        padding: 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-delete:hover {
        background: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }
</style>

<div class="products-header">
    <h1>Quản lý sản phẩm</h1>
    <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn-add-product">
        <i class="bi bi-plus-lg"></i>
        Thêm sản phẩm mới
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
                <th>Giá gốc</th>
                <th>Giá khuyến mãi</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <div>Chưa có sản phẩm nào</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): 
                    $originalPrice = (float)($product['price'] ?? 0);
                    // For now, use the same price as promotional price
                    // In the future, you can add discount logic here
                    $promotionalPrice = $originalPrice;
                ?>
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
                            <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                        </td>
                        <td>
                            <span class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></span>
                        </td>
                        <td>
                            <span class="price-original"><?= number_format($originalPrice, 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="price-promotional"><?= number_format($promotionalPrice, 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="stock-amount"><?= htmlspecialchars($product['stock'] ?? 0) ?></span>
                        </td>
                        <td>
                            <span class="status-badge active">Hoạt động</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['product_id'] ?>" class="btn-edit" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-delete" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
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

