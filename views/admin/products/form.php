<?php
$isEditing = isset($product);
$productId = $isEditing ? (int)$product['id'] : null;
?>

<section class="admin-section">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase text-muted mb-1 small"><?= $isEditing ? 'Chỉnh sửa' : 'Thêm mới' ?></p>
                <h2 class="fw-bold mb-0"><?= $isEditing ? 'Cập nhật sản phẩm' : 'Tạo sản phẩm mới' ?></h2>
            </div>
            <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-outline-dark">Quay lại danh sách</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>?action=<?= $isEditing ? 'admin-product-update' : 'admin-product-store' ?>">
                            <?php if ($isEditing): ?>
                                <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <?php endif; ?>
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Tên sản phẩm</label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Mô tả</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-uppercase">Giá bán</label>
                                    <input type="number" min="0" step="1000" name="price" class="form-control" required
                                           value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small text-uppercase">Tồn kho</label>
                                    <input type="number" min="0" name="stock" class="form-control" required
                                           value="<?= htmlspecialchars($product['stock'] ?? 0) ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Danh mục</label>
                                <select name="category_id" class="form-select">
                                    <option value="">— Chọn danh mục —</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['category_id'] ?>"
                                            <?= ($product['category_id'] ?? null) == $category['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-uppercase">Ảnh đại diện (URL)</label>
                                <input type="text" name="image_url" class="form-control" placeholder="https://..."
                                       value="<?= htmlspecialchars($product['image'] ?? '') ?>">
                            </div>
                            <button type="submit" class="btn btn-dark w-100"><?= $isEditing ? 'Lưu thay đổi' : 'Tạo sản phẩm' ?></button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($isEditing): ?>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Thêm biến thể mới</h5>
                            <form method="POST" action="<?= BASE_URL ?>?action=admin-product-variant-store">
                                <input type="hidden" name="product_id" value="<?= $productId ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small text-uppercase">SKU</label>
                                        <input type="text" name="sku" class="form-control" placeholder="Mã SKU">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label small text-uppercase">Giá cộng thêm</label>
                                        <input type="number" step="500" name="additional_price" class="form-control" value="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label small text-uppercase">Kho</label>
                                        <input type="number" min="0" name="stock" class="form-control" value="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <?php foreach ($attributes as $attribute): ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label small text-uppercase"><?= htmlspecialchars($attribute['attribute_name']) ?></label>
                                            <select class="form-select" name="attribute_values[<?= $attribute['attribute_id'] ?>]" required>
                                                <option value="">— Chọn giá trị —</option>
                                                <?php foreach ($attribute['values'] as $value): ?>
                                                    <option value="<?= $value['value_id'] ?>"><?= htmlspecialchars($value['value_name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="btn btn-outline-dark w-100">Thêm biến thể</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Biến thể hiện có</h5>
                                <span class="badge bg-secondary"><?= count($variants ?? []) ?> biến thể</span>
                            </div>
                            <?php if (empty($variants)): ?>
                                <p class="text-muted">Chưa có biến thể nào cho sản phẩm này.</p>
                            <?php else: ?>
                                <?php foreach ($variants as $variant): ?>
                                    <div class="border rounded-3 p-3 mb-3">
                                        <form method="POST" action="<?= BASE_URL ?>?action=admin-product-variant-update">
                                            <input type="hidden" name="variant_id" value="<?= $variant['variant_id'] ?>">
                                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small text-uppercase mb-1">SKU</label>
                                                    <input type="text" name="sku" class="form-control form-control-sm" value="<?= htmlspecialchars($variant['sku'] ?? '') ?>" placeholder="SKU">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small text-uppercase mb-1">Giá cộng thêm</label>
                                                    <input type="number" step="500" name="additional_price" class="form-control form-control-sm" value="<?= (float)$variant['additional_price'] ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small text-uppercase mb-1">Kho</label>
                                                    <input type="number" min="0" name="stock" class="form-control form-control-sm" value="<?= (int)$variant['stock'] ?>">
                                                </div>
                                            </div>
                                            <div class="row g-3 mt-1">
                                                <?php foreach ($attributes as $attribute):
                                                    $current = $variant['attributes'][$attribute['attribute_id']] ?? null;
                                                ?>
                                                    <div class="col-md-6">
                                                        <label class="form-label small text-uppercase mb-1"><?= htmlspecialchars($attribute['attribute_name']) ?></label>
                                                        <select name="attribute_values[<?= $attribute['attribute_id'] ?>]" class="form-select form-select-sm" required>
                                                            <option value="">— Chọn giá trị —</option>
                                                            <?php foreach ($attribute['values'] as $value): ?>
                                                                <option value="<?= $value['value_id'] ?>" <?= ($current['value_id'] ?? null) == $value['value_id'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($value['value_name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <small class="text-muted">Biến thể ID: <?= $variant['variant_id'] ?></small>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-dark" type="submit">Lưu</button>
                                        </form>
                                                    <form method="POST" action="<?= BASE_URL ?>?action=admin-product-variant-delete" onsubmit="return confirm('Xóa biến thể này?');">
                                                        <input type="hidden" name="variant_id" value="<?= $variant['variant_id'] ?>">
                                                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                                                        <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                                    </form>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-lg-6">
                    <div class="alert alert-light border">
                        <h5 class="fw-bold">Hướng dẫn tạo sản phẩm</h5>
                        <ol class="mb-0">
                            <li>Nhập thông tin cơ bản (tên, giá, mô tả...)</li>
                            <li>Nhấn "Tạo sản phẩm"</li>
                            <li>Sau khi lưu thành công bạn sẽ có thể thêm biến thể (Size/Color...)</li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

