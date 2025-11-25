<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil"></i> Sửa sản phẩm</h2>
    <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>?action=admin-product-update" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="id" value="<?= $product['product_id'] ?>">
            
            <div class="row">
                <!-- Tên sản phẩm -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['product_name']) ?>">
                    </div>
                </div>

                <!-- Danh mục -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Mô tả sản phẩm</label>
                        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Giá -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" required min="0" step="1000" value="<?= $product['price'] ?>">
                    </div>
                </div>

                <!-- Số lượng -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control" required min="0" value="<?= $product['stock'] ?>">
                    </div>
                </div>

                <!-- Hình ảnh -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh mới (để trống nếu không đổi)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)">
                        <small class="text-muted">Định dạng: JPG, PNG, GIF. Tối đa 2MB</small>
                    </div>
                </div>

                <!-- Ảnh hiện tại -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Hình ảnh hiện tại:</label>
                        <div>
                            <?php 
                            $currentImage = '';
                            if (!empty($images)) {
                                foreach ($images as $img) {
                                    if ($img['is_primary']) {
                                        $currentImage = $img['image_url'];
                                        break;
                                    }
                                }
                                if (empty($currentImage) && !empty($images[0])) {
                                    $currentImage = $images[0]['image_url'];
                                }
                            }
                            ?>
                            <img id="currentImage" 
                                 src="<?= !empty($currentImage) ? BASE_URL . $currentImage : 'https://via.placeholder.com/200' ?>" 
                                 alt="Current" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                            <img id="imagePreview" src="" alt="Preview" style="max-width: 200px; display: none; margin-left: 10px;" class="img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const preview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }

    function validateForm() {
        const name = document.querySelector('input[name="name"]').value.trim();
        const price = document.querySelector('input[name="price"]').value;
        const category = document.querySelector('select[name="category_id"]').value;

        if (!name) {
            alert('Vui lòng nhập tên sản phẩm');
            return false;
        }

        if (!category) {
            alert('Vui lòng chọn danh mục');
            return false;
        }

        if (!price || price < 0) {
            alert('Vui lòng nhập giá hợp lệ');
            return false;
        }

        return true;
    }
</script>
