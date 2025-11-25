<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-box-seam"></i> Quản lý sản phẩm</h2>
    <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
    </a>
</div>

<!-- Filter -->
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="action" value="admin-products">
            
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
            
            <div class="col-md-3 text-end">
                <span class="text-muted">Tổng: <strong><?= $totalProducts ?></strong> sản phẩm</span>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Không có sản phẩm nào</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">ID</th>
                            <th width="100">Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th width="120">Giá</th>
                            <th width="80">Tồn kho</th>
                            <th width="250" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['product_id'] ?></td>
                                <td>
                                    <img src="<?= !empty($product['primary_image']) ? BASE_URL . $product['primary_image'] : 'https://via.placeholder.com/80' ?>" 
                                         alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['product_name']) ?></strong>
                                    <?php if (!empty($product['description'])): ?>
                                        <br><small class="text-muted"><?= mb_substr(strip_tags($product['description']), 0, 50) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></span>
                                </td>
                                <td><strong><?= number_format($product['price'], 0, ',', '.') ?>đ</strong></td>
                                <td>
                                    <?php if ($product['stock'] > 10): ?>
                                        <span class="badge bg-success"><?= $product['stock'] ?></span>
                                    <?php elseif ($product['stock'] > 0): ?>
                                        <span class="badge bg-warning"><?= $product['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-actions text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="Xem" 
                                           target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                    <div class="btn-group ms-1" role="group">
                                        <a href="<?= BASE_URL ?>?action=admin-product-images&product_id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="Hình ảnh">
                                            <i class="bi bi-images"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?action=admin-product-attributes&product_id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-success" 
                                           title="Thuộc tính">
                                            <i class="bi bi-tags"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>?action=admin-product-variants&product_id=<?= $product['product_id'] ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Biến thể">
                                            <i class="bi bi-list-ul"></i>
                                        </a>
                                    </div>
                                    <button onclick="confirmDelete('<?= BASE_URL ?>?action=admin-product-delete&id=<?= $product['product_id'] ?>', '<?= htmlspecialchars($product['product_name']) ?>')" 
                                            class="btn btn-sm btn-danger ms-1" 
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= BASE_URL ?>?action=admin-products&page=<?= $i ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= isset($_GET['category']) ? '&category=' . $_GET['category'] : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
