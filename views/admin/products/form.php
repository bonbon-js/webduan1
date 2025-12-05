<?php
$isEditing = isset($product);
$productId = $isEditing ? (int)$product['id'] : null;
?>

<div class="form-header">
    <h2><?= $isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h2>
    <a href="<?= BASE_URL ?>?action=admin-products" class="btn-cancel">Quay lại danh sách</a>
</div>

<!-- Form thông tin sản phẩm -->
<div class="form-card">
    <h3 class="mb-4 fs-125 fw-bold">Thông tin cơ bản</h3>
    <form method="POST" action="<?= BASE_URL ?>?action=<?= $isEditing ? 'admin-product-update' : 'admin-product-store' ?>" enctype="multipart/form-data">
        <?php if ($isEditing): ?>
            <input type="hidden" name="product_id" value="<?= $productId ?>">
        <?php endif; ?>
        
        <div class="form-grid-2">
            <div>
                <label class="form-label" for="name">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required
                       value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="Nhập tên sản phẩm">
            </div>
            <div>
                <label class="form-label" for="category_id">Danh mục</label>
                <select id="category_id" name="category_id" class="form-select">
                    <option value="">— Chọn danh mục —</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>"
                            <?= ($product['category_id'] ?? null) == $category['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="description">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4" 
                      placeholder="Mô tả chi tiết sản phẩm"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="form-grid-2">
            <div>
                <label class="form-label" for="price">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" id="price" name="price" min="0" step="1000" class="form-control" required
                       value="<?= htmlspecialchars($product['price'] ?? '') ?>" placeholder="0">
            </div>
            <div>
                <label class="form-label" for="stock">Tồn kho <span class="text-danger">*</span></label>
                <input type="number" id="stock" name="stock" min="0" class="form-control" required
                       value="<?= htmlspecialchars($product['stock'] ?? 0) ?>" placeholder="0">
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label class="form-label" for="image">Ảnh đại diện sản phẩm</label>
            <div class="image-upload-wrapper">
                <?php if ($isEditing && !empty($product['image'])): ?>
                    <div class="current-image-preview">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="Current image" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
                        <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.5rem;">Ảnh hiện tại</div>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" 
                       onchange="previewImage(this)" style="padding: 0.5rem;">
                <div id="imagePreview" style="margin-top: 1rem;"></div>
                <small style="color: #64748b; display: block; margin-top: 0.5rem;">
                    <i class="bi bi-info-circle"></i> Chọn file ảnh từ máy tính (JPG, PNG, GIF). Kích thước tối đa: 5MB
                </small>
            </div>
        </div>

        <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '200px';
                    img.style.borderRadius = '8px';
                    img.style.border = '1px solid #e2e8f0';
                    preview.appendChild(img);
                    
                    const label = document.createElement('div');
                    label.textContent = 'Ảnh mới (chưa lưu)';
                    label.style.fontSize = '0.85rem';
                    label.style.color = '#3b82f6';
                    label.style.marginTop = '0.5rem';
                    label.style.fontWeight = '600';
                    preview.appendChild(label);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

        <div class="flex-end-actions">
            <a href="<?= BASE_URL ?>?action=admin-products" class="btn-cancel">Hủy</a>
            <button type="submit" class="btn-submit">
                <?= $isEditing ? 'Cập nhật sản phẩm' : 'Tạo sản phẩm' ?>
            </button>
        </div>
    </form>
</div>

<?php if ($isEditing && !empty($attributes)): ?>
    <!-- Form thêm biến thể -->
    <div class="form-card">
        <h3 class="mb-4 fs-125 fw-bold">Thêm biến thể mới (Size/Màu sắc)</h3>
        <form method="POST" action="<?= BASE_URL ?>?action=admin-product-variant-store">
            <input type="hidden" name="product_id" value="<?= $productId ?>">
            
            <div class="form-grid-3-compact">
                <div>
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" placeholder="Mã SKU (tùy chọn)">
                </div>
                <div>
                    <label class="form-label">Giá cộng thêm (VNĐ)</label>
                    <input type="number" step="500" name="additional_price" class="form-control" value="0">
                </div>
                <div>
                    <label class="form-label">Tồn kho</label>
                    <input type="number" min="0" name="stock" class="form-control" value="0" required>
                </div>
            </div>

            <?php 
            // Lọc chỉ lấy các attribute có values
            $attributesWithValues = array_filter($attributes, function($attr) {
                return !empty($attr['values']);
            });
            if (!empty($attributesWithValues)): 
            ?>
            <div class="dynamic-attributes-grid cols-<?= min(count($attributesWithValues), 3) ?>">
                <?php foreach ($attributesWithValues as $attribute): ?>
                    <div>
                        <label class="form-label"><?= htmlspecialchars($attribute['attribute_name']) ?> <span class="text-danger">*</span></label>
                        <select class="form-select" name="attribute_values[<?= $attribute['attribute_id'] ?>]" required>
                            <option value="">— Chọn —</option>
                            <?php foreach ($attribute['values'] as $value): ?>
                                <option value="<?= $value['value_id'] ?>"><?= htmlspecialchars($value['value_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <button type="submit" class="btn-submit w-100">
                <i class="bi bi-plus-lg"></i> Thêm biến thể
            </button>
        </form>
    </div>

    <!-- Danh sách biến thể hiện có -->
    <div class="form-card variants-section">
        <h3 class="mb-4 fs-125 fw-bold">
            Biến thể hiện có 
            <span class="text-slate small">(<?= count($variants ?? []) ?>)</span>
        </h3>
        
        <?php if (empty($variants)): ?>
            <div class="empty-variants">
                <i class="bi bi-inbox empty-icon-lg"></i>
                <div>Chưa có biến thể nào. Hãy thêm biến thể đầu tiên.</div>
            </div>
        <?php else: ?>
            <?php foreach ($variants as $variant): 
                $variantAttrs = [];
                foreach ($variant['attributes'] ?? [] as $attr) {
                    $variantAttrs[] = $attr['value_name'] ?? '';
                }
                $variantName = implode(' / ', $variantAttrs);
            ?>
                <div class="variant-item">
                    <div class="variant-header">
                        <div class="variant-info">
                            <?= htmlspecialchars($variantName ?: 'Biến thể #' . $variant['variant_id']) ?>
                            <?php if ($variant['sku']): ?>
                                <span class="text-slate small">(SKU: <?= htmlspecialchars($variant['sku']) ?>)</span>
                            <?php endif; ?>
                        </div>
                        <form method="POST" action="<?= BASE_URL ?>?action=admin-product-variant-delete" 
                              class="d-inline" 
                              onsubmit="return confirm('Xóa biến thể này?');">
                            <input type="hidden" name="variant_id" value="<?= $variant['variant_id'] ?>">
                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <button type="submit" class="btn-sm btn-danger-sm">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </form>
                    </div>
                    <div class="dynamic-attributes-grid cols-3 small">
                        <div>
                            <strong class="text-slate">Giá cộng thêm:</strong><br>
                            <?= number_format((float)($variant['additional_price'] ?? 0), 0, ',', '.') ?> VNĐ
                        </div>
                        <div>
                            <strong class="text-slate">Tồn kho:</strong><br>
                            <?= htmlspecialchars($variant['stock'] ?? 0) ?>
                        </div>
                        <div>
                            <strong class="text-slate">ID:</strong><br>
                            #<?= htmlspecialchars($variant['variant_id']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
<?php elseif (!$isEditing && !empty($attributes)): ?>
    <!-- Hướng dẫn khi tạo mới -->
    <div class="form-card info-card-alt">
        <h4 class="mb-3 text-primary">
            <i class="bi bi-info-circle"></i> Hướng dẫn
        </h4>
        <ol class="mb-0 ps-4 text-slate">
            <li>Nhập thông tin cơ bản của sản phẩm (tên, giá, mô tả...)</li>
            <li>Nhấn "Tạo sản phẩm" để lưu</li>
            <li>Sau khi tạo thành công, bạn sẽ có thể thêm các biến thể (Size, Màu sắc...)</li>
            <li>Quản lý thuộc tính (Size, Màu sắc) tại <a href="<?= BASE_URL ?>?action=admin-attributes" class="text-primary fw-semibold">Quản lý thuộc tính</a></li>
        </ol>
    </div>
<?php endif; ?>
