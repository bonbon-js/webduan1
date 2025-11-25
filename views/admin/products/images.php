<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý hình ảnh: <?= htmlspecialchars($product['product_name']) ?></h2>
        <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Upload hình ảnh mới</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?action=admin-product-upload-image" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Chọn hình ảnh (có thể chọn nhiều)</label>
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                    <small class="text-muted">Hỗ trợ: JPG, PNG, GIF. Tối đa 5MB mỗi ảnh.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </form>
        </div>
    </div>

    <!-- Images Grid -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Danh sách hình ảnh (<?= count($images) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($images)): ?>
                <p class="text-muted text-center py-4">Chưa có hình ảnh nào.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-3">
                            <div class="card h-100">
                                <img src="<?= BASE_URL . $image['image_url'] ?>" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <?php if ($image['is_primary']): ?>
                                        <span class="badge bg-success mb-2">Ảnh chính</span>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>?action=admin-product-set-primary&image_id=<?= $image['image_id'] ?>&product_id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-outline-primary mb-2"
                                           onclick="return confirm('Đặt làm ảnh chính?')">
                                            <i class="bi bi-star"></i> Đặt làm ảnh chính
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= BASE_URL ?>?action=admin-product-delete-image&image_id=<?= $image['image_id'] ?>&product_id=<?= $product['product_id'] ?>" 
                                       class="btn btn-sm btn-danger w-100"
                                       onclick="return confirm('Xóa hình ảnh này?')">
                                        <i class="bi bi-trash"></i> Xóa
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
