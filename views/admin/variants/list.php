<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-list-ul"></i> Quản lý biến thể</h2>
        <p class="text-muted mb-0">Sản phẩm: <strong><?= htmlspecialchars($product['product_name']) ?></strong></p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>?action=admin-variant-create&product_id=<?= $product['product_id'] ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm biến thể
        </a>
        <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($variants)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có biến thể nào</p>
                <a href="<?= BASE_URL ?>?action=admin-variant-create&product_id=<?= $product['product_id'] ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm biến thể đầu tiên
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th>SKU</th>
                            <th width="150">Giá thêm</th>
                            <th width="100">Tồn kho</th>
                            <th width="150" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($variants as $variant): ?>
                            <tr>
                                <td><?= $variant['variant_id'] ?></td>
                                <td><strong><?= htmlspecialchars($variant['sku']) ?></strong></td>
                                <td>
                                    <?php if ($variant['additional_price'] > 0): ?>
                                        <span class="text-success">+<?= number_format($variant['additional_price'], 0, ',', '.') ?>đ</span>
                                    <?php elseif ($variant['additional_price'] < 0): ?>
                                        <span class="text-danger"><?= number_format($variant['additional_price'], 0, ',', '.') ?>đ</span>
                                    <?php else: ?>
                                        <span class="text-muted">0đ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($variant['stock'] > 10): ?>
                                        <span class="badge bg-success"><?= $variant['stock'] ?></span>
                                    <?php elseif ($variant['stock'] > 0): ?>
                                        <span class="badge bg-warning"><?= $variant['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-actions text-center">
                                    <a href="<?= BASE_URL ?>?action=admin-variant-edit&id=<?= $variant['variant_id'] ?>" 
                                       class="btn btn-sm btn-warning btn-action" 
                                       title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="confirmDelete('<?= BASE_URL ?>?action=admin-variant-delete&id=<?= $variant['variant_id'] ?>', '<?= htmlspecialchars($variant['sku']) ?>')" 
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
            
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> 
                <strong>Lưu ý:</strong> Giá cuối cùng = Giá sản phẩm (<?= number_format($product['price'], 0, ',', '.') ?>đ) + Giá thêm của biến thể
            </div>
        <?php endif; ?>
    </div>
</div>
