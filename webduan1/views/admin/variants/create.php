<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="bi bi-plus-circle"></i> Thêm biến thể mới</h2>
        <p class="text-muted mb-0">Sản phẩm: <strong><?= htmlspecialchars($product['product_name']) ?></strong></p>
    </div>
    <a href="<?= BASE_URL ?>?action=admin-product-variants&product_id=<?= $product['product_id'] ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>?action=admin-variant-store" onsubmit="return validateForm()">
            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
            
            <div class="row">
                <!-- SKU -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">SKU (Mã biến thể) <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control" required placeholder="VD: ATHN-S-BLACK">
                        <small class="text-muted">Mã định danh duy nhất cho biến thể này</small>
                    </div>
                </div>

                <!-- Giá thêm -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Giá thêm (VNĐ)</label>
                        <input type="number" name="additional_price" class="form-control" value="0" step="1000">
                        <small class="text-muted">Giá thêm so với giá gốc (có thể âm)</small>
                    </div>
                </div>

                <!-- Tồn kho -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" required min="0" value="0">
                    </div>
                </div>
            </div>

            <!-- Thông tin sản phẩm gốc -->
            <div class="alert alert-light border">
                <h6 class="mb-2"><i class="bi bi-info-circle"></i> Thông tin sản phẩm gốc:</h6>
                <ul class="mb-0">
                    <li><strong>Tên:</strong> <?= htmlspecialchars($product['product_name']) ?></li>
                    <li><strong>Giá gốc:</strong> <?= number_format($product['price'], 0, ',', '.') ?>đ</li>
                    <li><strong>Tồn kho gốc:</strong> <?= $product['stock'] ?></li>
                </ul>
            </div>

            <!-- Ví dụ -->
            <div class="alert alert-info">
                <h6 class="mb-2"><i class="bi bi-lightbulb"></i> Ví dụ biến thể:</h6>
                <ul class="mb-0">
                    <li><strong>SKU:</strong> ATHN-S-BLACK (Áo Thun Nam - Size S - Màu Đen)</li>
                    <li><strong>Giá thêm:</strong> 0đ (Size S không tính thêm)</li>
                    <li><strong>Tồn kho:</strong> 20 (20 áo size S màu đen)</li>
                </ul>
                <hr>
                <ul class="mb-0">
                    <li><strong>SKU:</strong> ATHN-XL-BLACK (Áo Thun Nam - Size XL - Màu Đen)</li>
                    <li><strong>Giá thêm:</strong> +20,000đ (Size XL tính thêm 20k)</li>
                    <li><strong>Tồn kho:</strong> 15 (15 áo size XL màu đen)</li>
                </ul>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>?action=admin-product-variants&product_id=<?= $product['product_id'] ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Thêm biến thể
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        const sku = document.querySelector('input[name="sku"]').value.trim();
        const stock = document.querySelector('input[name="stock"]').value;

        if (!sku) {
            alert('Vui lòng nhập SKU');
            return false;
        }

        if (stock < 0) {
            alert('Số lượng tồn kho không được âm');
            return false;
        }

        return true;
    }
</script>
