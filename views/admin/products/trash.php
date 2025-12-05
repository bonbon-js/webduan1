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
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox empty-icon-lg"></i>
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
                            <span class="fw-semibold"><?= number_format((float)($product['price'] ?? 0), 0, ',', '.') ?> VNĐ</span>
                        </td>
                        <td>
                            <span class="deleted-badge">
                                <i class="bi bi-trash"></i> Đã xóa
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-restore" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                    <button type="submit" class="btn-restore" title="Khôi phục">
                                        <i class="bi bi-arrow-counterclockwise"></i> Khôi phục
                                    </button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>?action=admin-product-force-delete" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn sản phẩm này? Hành động này không thể hoàn tác!');">
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

