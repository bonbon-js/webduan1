<div class="products-header">
    <h1>Quản lý sản phẩm</h1>
    <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>?action=admin-products-trash" class="btn-back">
            <i class="bi bi-trash"></i>
            Thùng rác
        </a>
        <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn-add-product">
            <i class="bi bi-plus-lg"></i>
            Thêm sản phẩm mới
        </a>
    </div>
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
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox empty-icon-lg"></i>
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
                                <div class="placeholder-80-box">
                                    <i class="bi bi-image"></i>
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
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-delete" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
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


