<style>
    .cart-page {
        padding: 60px 0;
        min-height: 60vh;
    }
    
    .cart-header {
        margin-bottom: 40px;
        text-align: center;
    }
    
    .cart-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    
    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .cart-table th {
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 20px;
        border-bottom: 2px solid #000;
        font-weight: 600;
        text-align: left;
    }
    
    .cart-table td {
        padding: 25px 20px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    
    .cart-product-img {
        width: 80px;
        height: 100px;
        object-fit: cover;
        margin-right: 20px;
    }
    
    .cart-product-name {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
        color: #000;
        text-decoration: none;
    }
    
    .qty-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #ddd;
        width: fit-content;
    }
    
    .qty-btn {
        background: none;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        color: #666;
    }
    
    .qty-input {
        width: 40px;
        text-align: center;
        border: none;
        font-weight: 600;
        appearance: none;
        -moz-appearance: textfield;
    }
    
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .remove-btn {
        color: #999;
        background: none;
        border: none;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .remove-btn:hover {
        color: #dc3545;
    }
    
    .cart-summary {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 4px;
        margin-top: 20px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }
    
    .summary-total {
        border-top: 1px solid #ddd;
        padding-top: 15px;
        margin-top: 15px;
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .btn-checkout {
        display: block;
        width: 100%;
        background: #000;
        color: #fff;
        text-align: center;
        padding: 15px;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-checkout:hover {
        background: #333;
        color: #fff;
    }
    
    .empty-cart {
        text-align: center;
        padding: 60px 0;
    }
    
    .empty-cart i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
</style>

<div class="container cart-page">
    <div class="cart-header">
        <h1 class="cart-title">Giỏ Hàng Của Bạn</h1>
        <p class="text-muted"><?= count($cart) ?> sản phẩm</p>
    </div>

    <?php if (empty($cart)): ?>
        <div class="empty-cart">
            <i class="bi bi-cart-x"></i>
            <h3>Giỏ hàng đang trống</h3>
            <p class="text-muted mb-4">Bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="<?= BASE_URL ?>" class="btn btn-dark text-uppercase px-4 py-2">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th style="width: 50%">Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $cartKey => $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            // Xử lý ảnh base64 nếu cần
                                            $imgSrc = $item['image'];
                                            if (strpos($imgSrc, 'assets/') === 0) {
                                                // Nếu là đường dẫn tương đối, chuyển thành base64 giống home
                                                if (file_exists(PATH_ROOT . $imgSrc)) {
                                                    $data = file_get_contents(PATH_ROOT . $imgSrc);
                                                    $type = pathinfo(PATH_ROOT . $imgSrc, PATHINFO_EXTENSION);
                                                    $imgSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                                } else {
                                                    $imgSrc = BASE_URL . $imgSrc;
                                                }
                                            }
                                            ?>
                                            <img src="<?= $imgSrc ?>" alt="<?= $item['name'] ?>" class="cart-product-img">
                                            <div>
                                                <a href="#" class="cart-product-name"><?= $item['name'] ?></a>
                                                <small class="text-muted d-block">Size: <?= $item['size'] ?? 'M' ?> | Màu: <?= $item['color'] ?? 'Black' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <div class="qty-input-group">
                                            <button class="qty-btn" onclick="updateQty('<?= $cartKey ?>', -1)">-</button>
                                            <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" readonly>
                                            <button class="qty-btn" onclick="updateQty('<?= $cartKey ?>', 1)">+</button>
                                        </div>
                                    </td>
                                    <td class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=cart-delete&id=<?= $cartKey ?>" class="remove-btn" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="<?= BASE_URL ?>" class="text-decoration-none text-dark fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 class="mb-4 text-uppercase fs-6 fw-bold">Tóm tắt đơn hàng</h4>
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span><?= number_format($total, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span>Miễn phí</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Tổng cộng</span>
                        <span><?= number_format($total, 0, ',', '.') ?> đ</span>
                    </div>
                    <a href="<?= BASE_URL ?>?action=checkout" class="btn-checkout">Thanh toán ngay</a>
                    
                    <div class="mt-4 small text-muted">
                        <p class="mb-2"><i class="bi bi-shield-check me-2"></i> Bảo mật thanh toán 100%</p>
                        <p class="mb-0"><i class="bi bi-truck me-2"></i> Miễn phí vận chuyển toàn quốc</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function updateQty(productId, change) {
    const input = event.target.parentElement.querySelector('input');
    let newQty = parseInt(input.value) + change;
    
    if (newQty < 1) return;
    
    // Gọi API cập nhật
    fetch('<?= BASE_URL ?>?action=cart-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: newQty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
