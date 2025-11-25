<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý thuộc tính: <?= htmlspecialchars($product['product_name']) ?></h2>
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

    <div class="row">
        <!-- Gán thuộc tính mới -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Gán thuộc tính cho sản phẩm</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($allAttributes)): ?>
                        <p class="text-muted">Chưa có thuộc tính nào. <a href="<?= BASE_URL ?>?action=admin-attributes">Tạo thuộc tính mới</a></p>
                    <?php else: ?>
                        <form method="POST" action="<?= BASE_URL ?>?action=admin-product-assign-attribute">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            
                            <?php foreach ($allAttributes as $attr): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?= htmlspecialchars($attr['attribute_name']) ?></label>
                                    <?php if (empty($attr['values'])): ?>
                                        <p class="text-muted small">Chưa có giá trị</p>
                                    <?php else: ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($attr['values'] as $value): ?>
                                                <?php
                                                // Kiểm tra xem giá trị này đã được gán chưa
                                                $isAssigned = false;
                                                foreach ($assignedAttributes as $assigned) {
                                                    if ($assigned['value_name'] == $value['value_name'] && $assigned['attribute_name'] == $attr['attribute_name']) {
                                                        $isAssigned = true;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="value_ids[]" 
                                                           value="<?= $value['value_id'] ?>" 
                                                           id="value_<?= $value['value_id'] ?>"
                                                           <?= $isAssigned ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="value_<?= $value['value_id'] ?>">
                                                        <?= htmlspecialchars($value['value_name']) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật thuộc tính
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Thuộc tính đã gán -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thuộc tính đã gán</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($assignedAttributes)): ?>
                        <p class="text-muted">Chưa có thuộc tính nào được gán.</p>
                    <?php else: ?>
                        <?php
                        // Nhóm theo attribute_name
                        $grouped = [];
                        foreach ($assignedAttributes as $attr) {
                            $grouped[$attr['attribute_name']][] = $attr;
                        }
                        ?>
                        
                        <?php foreach ($grouped as $attrName => $values): ?>
                            <div class="mb-3">
                                <h6 class="fw-bold"><?= htmlspecialchars($attrName) ?></h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php 
                                    $uniqueValues = [];
                                    foreach ($values as $val) {
                                        if (!in_array($val['value_name'], $uniqueValues)) {
                                            $uniqueValues[] = $val['value_name'];
                                    ?>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($val['value_name']) ?>
                                        </span>
                                    <?php 
                                        }
                                    } 
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check {
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background: #f8f9fa;
    }
    
    .form-check:hover {
        background: #e9ecef;
    }
    
    .form-check-input:checked + .form-check-label {
        font-weight: 600;
    }
</style>
