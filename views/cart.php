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
        border: 1px solid #000;
        width: fit-content;
        border-radius: 4px;
    }
    
    .qty-btn {
        background: #fff;
        border: none;
        padding: 5px 12px;
        cursor: pointer;
        color: #000;
        transition: all 0.3s;
        font-weight: 600;
    }
    
    .qty-btn:hover {
        background: #000;
        color: #fff;
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
        color: #666;
        background: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        padding: 8px;
        border-radius: 4px;
    }
    
    .remove-btn:hover {
        color: #000;
        background: #f5f5f5;
    }
    
    .coupon-section {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }
    
    .coupon-message {
        font-size: 0.85rem;
        margin-top: 8px;
    }
    
    .coupon-message.success {
        color: #28a745;
    }
    
    .coupon-message.error {
        color: #dc3545;
    }
    
    .coupon-item {
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #ddd;
        margin-bottom: 8px;
        border-radius: 4px;
    }
    
    .coupon-item:hover {
        border-color: #000;
        background: #f8f9fa;
    }
    
    .coupon-item.selected {
        border-color: #28a745;
        background: #d4edda;
    }
    
    .cart-summary {
        background: #fafafa;
        padding: 30px;
        border-radius: 4px;
        margin-top: 20px;
        border: 1px solid #e0e0e0;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }
    
    .summary-total {
        border-top: 2px solid #000;
        padding-top: 15px;
        margin-top: 15px;
        font-weight: 700;
        font-size: 1.2rem;
        color: #000;
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
        color: #999;
        margin-bottom: 20px;
    }
    
    .empty-cart h3 {
        color: #000;
        font-weight: 600;
    }

    .cart-item-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    #selectAll, #selectAllBottom {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    #checkoutBtn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #999 !important;
    }

    #deleteSelectedBtn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f5f5f5 !important;
        color: #999 !important;
        border-color: #ddd !important;
    }
    
    #deleteSelectedBtn:not(:disabled) {
        background: #000;
        color: #fff;
        border: 1px solid #000;
    }
    
    #deleteSelectedBtn:not(:disabled):hover {
        background: #1a1a1a;
        border-color: #1a1a1a;
    }
    
    .btn-outline-danger {
        border-color: #000;
        color: #000;
    }
    
    .btn-outline-danger:hover {
        background: #000;
        color: #fff;
        border-color: #000;
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
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" name="select_all" onchange="toggleSelectAll(this)" aria-label="Chọn tất cả">
                                    <label for="selectAll" class="visually-hidden">Chọn tất cả</label>
                                </th>
                                <th style="width: 50%">Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $cartKey => $item): 
                                $itemTotal = $item['price'] * $item['quantity'];
                            ?>
                                <tr data-cart-key="<?= htmlspecialchars($cartKey) ?>" data-item-price="<?= $item['price'] ?>" data-item-quantity="<?= $item['quantity'] ?>" data-item-total="<?= $itemTotal ?>">
                                    <td>
                                        <input type="checkbox" class="cart-item-checkbox" id="cartItem_<?= htmlspecialchars($cartKey) ?>" name="cart_items[]" value="<?= htmlspecialchars($cartKey) ?>" checked onchange="handleCheckboxChange(this)">
                                        <label for="cartItem_<?= htmlspecialchars($cartKey) ?>" class="visually-hidden">Chọn sản phẩm <?= htmlspecialchars($item['name']) ?></label>
                                    </td>
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
                                            <button class="qty-btn" onclick="updateQty('<?= htmlspecialchars($cartKey) ?>', -1)" aria-label="Giảm số lượng">-</button>
                                            <input type="number" class="qty-input" id="qty_<?= htmlspecialchars($cartKey) ?>" name="quantity[<?= htmlspecialchars($cartKey) ?>]" value="<?= $item['quantity'] ?>" min="1" readonly aria-label="Số lượng sản phẩm">
                                            <button class="qty-btn" onclick="updateQty('<?= htmlspecialchars($cartKey) ?>', 1)" aria-label="Tăng số lượng">+</button>
                                        </div>
                                    </td>
                                    <td class="fw-bold"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ</td>
                                    <td>
                                        <button type="button" class="remove-btn" onclick="deleteSingleItem('<?= htmlspecialchars($cartKey) ?>')" title="Xóa sản phẩm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div>
                            <input type="checkbox" id="selectAllBottom" onchange="toggleSelectAll(this)">
                            <label for="selectAllBottom" class="ms-2 fw-semibold">
                                Chọn tất cả (<span id="selectedCount">0</span>)
                            </label>
                        </div>
                        <button type="button" class="btn btn-outline-danger" id="deleteSelectedBtn" onclick="deleteSelected()" disabled>
                            <i class="bi bi-trash me-1"></i> Xóa đã chọn
                        </button>
                    </div>
                    <a href="<?= BASE_URL ?>" class="text-decoration-none text-dark fw-semibold">
                        <i class="bi bi-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <!-- Mã giảm giá -->
                    <div class="coupon-section mb-4">
                        <h5 class="mb-3 text-uppercase fs-6 fw-bold">Mã giảm giá</h5>
                        
                        <!-- Lựa chọn tốt nhất (hiển thị trong khung) -->
                        <div id="bestCouponSuggestionInBox" style="display: none;" class="mb-3 p-3 bg-light rounded border border-warning">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 2px 6px;">
                                    <i class="bi bi-trophy-fill"></i> Lựa chọn tốt nhất
                                </span>
                                <strong id="bestCouponCodeInBox" class="fs-6"></strong>
                            </div>
                            <div class="text-success fw-bold mb-1" id="bestCouponDiscountInBox"></div>
                            <small class="text-muted d-block mb-2" id="bestCouponNameInBox"></small>
                            <button type="button" class="btn btn-sm btn-dark" id="applyBestCouponBtnInBox" onclick="applyBestCoupon()">
                                Áp dụng ngay
                            </button>
                        </div>
                        
                        <!-- Form nhập mã -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="couponCodeInput" 
                                       placeholder="Nhập mã giảm giá"
                                       style="text-transform: uppercase;">
                                <button type="button" 
                                        class="btn btn-outline-dark" 
                                        id="applyCouponBtn"
                                        onclick="applyCoupon()">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="couponMessage" class="mt-2 small"></div>
                        </div>
                        
                        <!-- Danh sách mã khả dụng -->
                        <div id="availableCouponsSection" style="display: none;">
                            <p class="small text-muted mb-2">Hoặc chọn mã giảm giá:</p>
                            <div id="availableCouponsList" class="list-group"></div>
                        </div>
                        
                        <!-- Mã đã áp dụng -->
                        <div id="appliedCouponDisplay" style="display: none;" class="mt-3 p-2 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="appliedCouponCode"></strong>
                                    <small class="d-block text-success" id="appliedCouponDiscount"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCoupon()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mb-4 text-uppercase fs-6 fw-bold">Tóm tắt đơn hàng</h4>
                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <span id="subtotal"><?= number_format($total, 0, ',', '.') ?> đ</span>
                    </div>
                    <div class="summary-row" id="discountRow" style="display: none;">
                        <span>Giảm giá</span>
                        <span id="discountAmount" style="color: #28a745;">-0 đ</span>
                    </div>
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span>Miễn phí</span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Tổng cộng</span>
                        <span id="grandTotal"><?= number_format($total, 0, ',', '.') ?> đ</span>
                    </div>
                    <button type="button" class="btn-checkout" id="checkoutBtn" onclick="proceedToCheckout()">Thanh toán ngay</button>
                    
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
function updateQty(cartKey, change) {
    const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
    const input = row.querySelector('.qty-input');
    let newQty = parseInt(input.value) + change;
    
    if (newQty < 1) return;
    
    // Gọi API cập nhật
    fetch('<?= BASE_URL ?>?action=cart-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_key: cartKey,
            quantity: newQty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật data attributes và tổng tiền
            const itemPrice = parseFloat(row.dataset.itemPrice) || 0;
            const itemTotal = itemPrice * newQty;
            row.dataset.itemQuantity = newQty;
            row.dataset.itemTotal = itemTotal;
            
            // Cập nhật hiển thị tổng tiền của item
            const totalCell = row.querySelector('td:nth-child(6)');
            if (totalCell) {
                totalCell.textContent = formatCurrency(itemTotal);
            }
            
            // Cập nhật tổng tiền nếu item được chọn
            const checkbox = row.querySelector('.cart-item-checkbox');
            if (checkbox && checkbox.checked) {
                updateBuyTotal();
            }
        } else {
            alert('Có lỗi xảy ra khi cập nhật số lượng');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Có lỗi xảy ra khi cập nhật số lượng');
    });
}

// Chọn/bỏ chọn tất cả
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    const isChecked = checkbox.checked;
    
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
    });
    
    // Đồng bộ cả 2 checkbox "Chọn tất cả"
    document.getElementById('selectAll').checked = isChecked;
    document.getElementById('selectAllBottom').checked = isChecked;
    
    updateSelectAllState();
    updateBuyTotal();
}

// Xử lý khi checkbox thay đổi
function handleCheckboxChange(checkbox) {
    updateSelectAllState();
    updateBuyTotal();
}

// Cập nhật trạng thái checkbox "Chọn tất cả" và nút xóa
function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
    const totalCount = checkboxes.length;
    const selectedCount = checkedBoxes.length;
    
    // Cập nhật số lượng đã chọn
    document.getElementById('selectedCount').textContent = selectedCount;
    
    // Cập nhật trạng thái checkbox "Chọn tất cả"
    const allChecked = totalCount > 0 && selectedCount === totalCount;
    document.getElementById('selectAll').checked = allChecked;
    document.getElementById('selectAllBottom').checked = allChecked;
    
    // Bật/tắt nút xóa
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    deleteBtn.disabled = selectedCount === 0;
}

// Xóa các sản phẩm đã chọn
function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để xóa');
        return;
    }
    
    if (!confirm(`Bạn có chắc muốn xóa ${checkedBoxes.length} sản phẩm đã chọn?`)) {
        return;
    }
    
    const cartKeys = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Gọi API xóa
    fetch('<?= BASE_URL ?>?action=cart-delete-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_keys: cartKeys
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            const errorMsg = data.message || 'Có lỗi xảy ra khi xóa sản phẩm';
            alert(errorMsg);
            console.error('Delete error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.');
    });
}

// Xóa một sản phẩm đơn lẻ
function deleteSingleItem(cartKey) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        return;
    }
    
    fetch('<?= BASE_URL ?>?action=cart-delete-multiple', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart_keys: [cartKey]
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            const errorMsg = data.message || 'Có lỗi xảy ra khi xóa sản phẩm';
            alert(errorMsg);
            console.error('Delete error:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.');
    });
}

// Cập nhật tổng tiền dựa trên các sản phẩm đã chọn
function updateBuyTotal() {
    // Tìm mã tốt nhất khi tổng tiền thay đổi
    findBestCoupon();
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let subtotal = 0;
    
    checkboxes.forEach(checkbox => {
        const cartKey = checkbox.value;
        const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
        if (row) {
            const itemTotal = parseFloat(row.dataset.itemTotal) || 0;
            subtotal += itemTotal;
        }
    });
    
    // Cập nhật hiển thị
    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    
    // Nếu có mã giảm giá đã áp dụng, validate lại
    if (appliedCouponData) {
        const code = appliedCouponData.code;
        fetch('<?= BASE_URL ?>?action=coupon-validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                coupon_code: code,
                order_amount: subtotal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appliedCouponData.discount_amount = data.discount_amount;
                updateTotalsWithCoupon(data.discount_amount);
            } else {
                // Mã không còn hợp lệ, xóa đi
                removeCoupon();
                document.getElementById('grandTotal').textContent = formatCurrency(subtotal);
            }
        })
        .catch(error => {
            console.error('Error validating coupon:', error);
            document.getElementById('grandTotal').textContent = formatCurrency(subtotal);
        });
    } else if (pendingCouponCode && subtotal > 0) {
        // Nếu có mã đang chờ và tổng tiền > 0, thử áp dụng tự động
        autoApplyPendingCoupon(subtotal);
    } else {
        document.getElementById('grandTotal').textContent = formatCurrency(subtotal);
        document.getElementById('discountRow').style.display = 'none';
    }
    
    // Tải lại danh sách mã giảm giá khả dụng
    loadAvailableCoupons();
    
    // Cập nhật trạng thái checkbox "Chọn tất cả"
    const allCheckboxes = document.querySelectorAll('.cart-item-checkbox');
    const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
    const allChecked = allCheckboxes.length > 0 && checkedBoxes.length === allCheckboxes.length;
    document.getElementById('selectAll').checked = allChecked;
    document.getElementById('selectAllBottom').checked = allChecked;
    
    // Bật/tắt nút thanh toán
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.disabled = checkedBoxes.length === 0;
    if (checkedBoxes.length === 0) {
        checkoutBtn.textContent = 'Vui lòng chọn sản phẩm để mua';
    } else {
        checkoutBtn.textContent = 'Thanh toán ngay';
    }
}

// Định dạng tiền tệ
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
}

// Mã giảm giá
let appliedCouponData = null;
let pendingCouponCode = null; // Mã đang chờ đủ điều kiện

// Tải danh sách mã giảm giá khả dụng
function loadAvailableCoupons() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let subtotal = 0;
    
    checkboxes.forEach(checkbox => {
        const cartKey = checkbox.value;
        const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
        if (row) {
            const itemTotal = parseFloat(row.dataset.itemTotal) || 0;
            subtotal += itemTotal;
        }
    });
    
    if (subtotal <= 0) {
        document.getElementById('availableCouponsSection').style.display = 'none';
        return;
    }
    
    fetch(`<?= BASE_URL ?>?action=coupon-available&order_amount=${subtotal}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.coupons.length > 0) {
                // Tính discount_amount cho mỗi mã và sắp xếp
                const couponPromises = data.coupons.map(coupon => {
                    return fetch('<?= BASE_URL ?>?action=coupon-validate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            coupon_code: coupon.code,
                            order_amount: subtotal
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            return {
                                coupon: coupon,
                                discount_amount: result.discount_amount
                            };
                        }
                        return null;
                    })
                    .catch(() => null);
                });
                
                Promise.all(couponPromises).then(results => {
                    const validCoupons = results.filter(r => r !== null);
                    
                    if (validCoupons.length > 0) {
                        // Sắp xếp theo discount_amount giảm dần (mã giảm nhiều nhất ở đầu)
                        validCoupons.sort((a, b) => b.discount_amount - a.discount_amount);
                        
                        // Loại bỏ mã "Lựa chọn tốt nhất" khỏi danh sách nếu đã hiển thị ở trên
                        const filteredCoupons = validCoupons.filter((itemData, index) => {
                            // Nếu là mã đầu tiên (lựa chọn tốt nhất) và đã được hiển thị ở trên, loại bỏ
                            if (index === 0 && bestCouponCode && itemData.coupon.code === bestCouponCode) {
                                return false;
                            }
                            return true;
                        });
                        
                        const listContainer = document.getElementById('availableCouponsList');
                        listContainer.innerHTML = '';
                        
                        filteredCoupons.forEach((itemData, index) => {
                            const coupon = itemData.coupon;
                            const discountAmount = itemData.discount_amount;
                            // Không còn hiển thị badge "Lựa chọn tốt nhất" trong danh sách vì đã hiển thị ở trên
                            const isBest = false;
                            
                            const item = document.createElement('div');
                            item.className = 'coupon-item p-2';
                            item.onclick = () => selectCoupon(coupon);
                            
                            const discountText = coupon.discount_type === 'percentage' 
                                ? `Giảm ${coupon.discount_value}%` 
                                : `Giảm ${formatCurrency(coupon.discount_value)}`;
                            
                            const maxDiscount = coupon.max_discount_amount 
                                ? ` (Tối đa ${formatCurrency(coupon.max_discount_amount)})` 
                                : '';
                            
                            const minOrderText = coupon.min_order_amount > 0
                                ? `Đơn tối thiểu: ${formatCurrency(coupon.min_order_amount)}`
                                : '';
                            
                            // Hiển thị số tiền tiết kiệm thực tế
                            const saveText = `Tiết kiệm ${formatCurrency(discountAmount)}`;
                            
                            item.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <div style="flex: 1;">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            ${isBest ? `<span class="badge bg-warning text-dark" style="font-size: 0.7rem; padding: 2px 6px;"><i class="bi bi-trophy-fill"></i> Lựa chọn tốt nhất</span>` : ''}
                                            <strong>${coupon.code}</strong>
                                        </div>
                                        <small class="d-block text-muted">${coupon.name || ''}</small>
                                        <small class="text-success d-block fw-bold">${saveText}</small>
                                        <small class="text-muted d-block">${discountText}${maxDiscount}</small>
                                        ${minOrderText ? `<small class="text-muted d-block mt-1"><i class="bi bi-info-circle"></i> ${minOrderText}</small>` : ''}
                                    </div>
                                    <i class="bi bi-chevron-right ms-2"></i>
                                </div>
                            `;
                            
                            listContainer.appendChild(item);
                        });
                        
                        document.getElementById('availableCouponsSection').style.display = 'block';
                    } else {
                        document.getElementById('availableCouponsSection').style.display = 'none';
                    }
                });
            } else {
                document.getElementById('availableCouponsSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading coupons:', error);
        });
}

// Áp dụng mã giảm giá từ input
function applyCoupon() {
    const code = document.getElementById('couponCodeInput').value.trim().toUpperCase();
    const messageDiv = document.getElementById('couponMessage');
    const applyBtn = document.getElementById('applyCouponBtn');
    
    if (!code) {
        messageDiv.innerHTML = '<span class="coupon-message error">Vui lòng nhập mã giảm giá</span>';
        return;
    }
    
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let subtotal = 0;
    
    checkboxes.forEach(checkbox => {
        const cartKey = checkbox.value;
        const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
        if (row) {
            const itemTotal = parseFloat(row.dataset.itemTotal) || 0;
            subtotal += itemTotal;
        }
    });
    
    if (subtotal <= 0) {
        messageDiv.innerHTML = '<span class="coupon-message error">Vui lòng chọn sản phẩm để áp dụng mã giảm giá</span>';
        return;
    }
    
    applyBtn.disabled = true;
    applyBtn.textContent = 'Đang kiểm tra...';
    messageDiv.innerHTML = '';
    
    fetch('<?= BASE_URL ?>?action=coupon-validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coupon_code: code,
            order_amount: subtotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appliedCouponData = data.coupon;
            appliedCouponData.discount_amount = data.discount_amount;
            pendingCouponCode = null; // Xóa mã chờ vì đã áp dụng thành công
            
            showAppliedCoupon(data.coupon.code, data.coupon.name, data.discount_amount);
            updateTotalsWithCoupon(data.discount_amount);
            
            document.getElementById('couponCodeInput').value = '';
            document.getElementById('couponCodeInput').disabled = true;
            applyBtn.disabled = true;
            applyBtn.textContent = 'Đã áp dụng';
            
            messageDiv.innerHTML = '<span class="coupon-message success">✓ ' + data.message + '</span>';
        } else {
            // Lưu mã vào pending để tự động áp dụng khi đủ điều kiện
            pendingCouponCode = code;
            messageDiv.innerHTML = '<span class="coupon-message error">' + data.message + '</span>';
            applyBtn.disabled = false;
            applyBtn.textContent = 'Áp dụng';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<span class="coupon-message error">Có lỗi xảy ra. Vui lòng thử lại.</span>';
        applyBtn.disabled = false;
        applyBtn.textContent = 'Áp dụng';
    });
}

// Chọn mã giảm giá từ danh sách
function selectCoupon(coupon) {
    appliedCouponData = {
        id: coupon.id,
        code: coupon.code,
        name: coupon.name,
        discount_amount: coupon.discount_amount
    };
    pendingCouponCode = null; // Xóa mã chờ vì đã chọn mã khác
    
    showAppliedCoupon(coupon.code, coupon.name, coupon.discount_amount);
    updateTotalsWithCoupon(coupon.discount_amount);
    
    // Gửi request để lưu vào session
    fetch('<?= BASE_URL ?>?action=coupon-validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coupon_code: coupon.code,
            order_amount: getSelectedSubtotal()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('couponCodeInput').value = coupon.code;
            document.getElementById('couponCodeInput').disabled = true;
            document.getElementById('applyCouponBtn').disabled = true;
            document.getElementById('applyCouponBtn').textContent = 'Đã áp dụng';
            document.getElementById('couponMessage').innerHTML = '<span class="coupon-message success">✓ ' + data.message + '</span>';
        }
    });
}

// Hiển thị mã giảm giá đã áp dụng
function showAppliedCoupon(code, name, discountAmount) {
    document.getElementById('appliedCouponCode').textContent = code + ' - ' + name;
    document.getElementById('appliedCouponDiscount').textContent = 'Giảm ' + formatCurrency(discountAmount);
    document.getElementById('appliedCouponDisplay').style.display = 'block';
    document.getElementById('availableCouponsSection').style.display = 'none';
}

// Xóa mã giảm giá
function removeCoupon() {
    appliedCouponData = null;
    pendingCouponCode = null; // Xóa cả mã chờ
    
    fetch('<?= BASE_URL ?>?action=coupon-remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('appliedCouponDisplay').style.display = 'none';
        document.getElementById('couponCodeInput').value = '';
        document.getElementById('couponCodeInput').disabled = false;
        document.getElementById('applyCouponBtn').disabled = false;
        document.getElementById('applyCouponBtn').textContent = 'Áp dụng';
        document.getElementById('couponMessage').innerHTML = '';
        
        updateBuyTotal();
        loadAvailableCoupons();
    });
}

// Tự động áp dụng mã đang chờ khi đủ điều kiện
function autoApplyPendingCoupon(subtotal) {
    if (!pendingCouponCode) return;
    
    fetch('<?= BASE_URL ?>?action=coupon-validate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            coupon_code: pendingCouponCode,
            order_amount: subtotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Đủ điều kiện, áp dụng tự động
            appliedCouponData = data.coupon;
            appliedCouponData.discount_amount = data.discount_amount;
            pendingCouponCode = null; // Xóa mã chờ vì đã áp dụng thành công
            
            showAppliedCoupon(data.coupon.code, data.coupon.name, data.discount_amount);
            updateTotalsWithCoupon(data.discount_amount);
            
            // Cập nhật UI
            document.getElementById('couponCodeInput').value = data.coupon.code;
            document.getElementById('couponCodeInput').disabled = true;
            document.getElementById('applyCouponBtn').disabled = true;
            document.getElementById('applyCouponBtn').textContent = 'Đã áp dụng';
            
            // Hiển thị thông báo thành công
            document.getElementById('couponMessage').innerHTML = '<span class="coupon-message success">✓ ' + data.message + '</span>';
        } else {
            // Vẫn chưa đủ điều kiện, giữ nguyên mã chờ
            document.getElementById('grandTotal').textContent = formatCurrency(subtotal);
            document.getElementById('discountRow').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error auto-applying coupon:', error);
        document.getElementById('grandTotal').textContent = formatCurrency(subtotal);
        document.getElementById('discountRow').style.display = 'none';
    });
}

// Lấy tổng tiền các sản phẩm đã chọn
function getSelectedSubtotal() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let subtotal = 0;
    
    checkboxes.forEach(checkbox => {
        const cartKey = checkbox.value;
        const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
        if (row) {
            const itemTotal = parseFloat(row.dataset.itemTotal) || 0;
            subtotal += itemTotal;
        }
    });
    
    return subtotal;
}

// Cập nhật tổng tiền với mã giảm giá
function updateTotalsWithCoupon(discountAmount) {
    const subtotal = getSelectedSubtotal();
    const finalTotal = subtotal - discountAmount;
    
    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    
    if (discountAmount > 0) {
        document.getElementById('discountRow').style.display = 'flex';
        document.getElementById('discountAmount').textContent = '-' + formatCurrency(discountAmount);
    } else {
        document.getElementById('discountRow').style.display = 'none';
    }
    
    document.getElementById('grandTotal').textContent = formatCurrency(Math.max(0, finalTotal));
}

// Chuyển đến trang thanh toán với các sản phẩm đã chọn
function proceedToCheckout() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để mua');
        return;
    }
    
    const selectedItems = Array.from(checkboxes).map(cb => cb.value);
    
    // Lưu vào sessionStorage để checkout page có thể đọc
    sessionStorage.setItem('selectedCartItems', JSON.stringify(selectedItems));
    
    // Lưu vào session qua API
    fetch('<?= BASE_URL ?>?action=cart-set-selected', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            selected_items: selectedItems
        })
    })
    .then(() => {
        // Chuyển đến trang checkout
        window.location.href = '<?= BASE_URL ?>?action=checkout';
    })
    .catch(err => {
        console.error('Error saving selected items:', err);
        // Vẫn chuyển đến checkout
        window.location.href = '<?= BASE_URL ?>?action=checkout';
    });
}

// Lưu trạng thái checkbox vào sessionStorage
function saveBuyCheckboxes() {
    const checkedBoxes = document.querySelectorAll('.cart-item-checkbox:checked');
    const selectedItems = Array.from(checkedBoxes).map(cb => cb.value);
    sessionStorage.setItem('selectedCartItems', JSON.stringify(selectedItems));
}

// Khôi phục trạng thái checkbox từ sessionStorage
function restoreBuyCheckboxes() {
    try {
        const saved = sessionStorage.getItem('selectedCartItems');
        if (saved) {
            const selectedItems = JSON.parse(saved);
            selectedItems.forEach(cartKey => {
                const checkbox = document.querySelector(`input.cart-item-checkbox[value="${cartKey}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        } else {
            // Nếu không có trong sessionStorage, check tất cả mặc định
            document.querySelectorAll('.cart-item-checkbox').forEach(cb => {
                cb.checked = true;
            });
        }
    } catch (e) {
        console.error('Error restoring checkboxes:', e);
        // Nếu có lỗi, check tất cả mặc định
        document.querySelectorAll('.cart-item-checkbox').forEach(cb => {
            cb.checked = true;
        });
    }
}

// Cập nhật event listener cho các checkbox
document.addEventListener('DOMContentLoaded', function() {
    updateSelectAllState();
    restoreBuyCheckboxes();
    updateBuyTotal();
    
    // Lưu trạng thái khi checkbox thay đổi
    document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            saveBuyCheckboxes();
            updateBuyTotal();
        });
    });
    
    // Lưu trạng thái khi "chọn tất cả" thay đổi
    const selectAll = document.getElementById('selectAll');
    const selectAllBottom = document.getElementById('selectAllBottom');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            saveBuyCheckboxes();
        });
    }
    if (selectAllBottom) {
        selectAllBottom.addEventListener('change', function() {
            saveBuyCheckboxes();
        });
    }
});

// Tìm mã giảm giá tốt nhất
let bestCouponData = null;
let bestCouponCode = null; // Lưu mã code của "Lựa chọn tốt nhất" để loại bỏ khỏi danh sách

function findBestCoupon() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    let subtotal = 0;
    
    checkboxes.forEach(checkbox => {
        const cartKey = checkbox.value;
        const row = document.querySelector(`tr[data-cart-key="${cartKey}"]`);
        if (row) {
            const itemTotal = parseFloat(row.dataset.itemTotal) || 0;
            subtotal += itemTotal;
        }
    });
    
    if (subtotal <= 0) {
        const suggestionDiv = document.getElementById('bestCouponSuggestionInBox');
        if (suggestionDiv) suggestionDiv.style.display = 'none';
        return;
    }
    
    // Nếu đã có mã áp dụng, không hiển thị gợi ý
    if (appliedCouponData) {
        const suggestionDiv = document.getElementById('bestCouponSuggestionInBox');
        if (suggestionDiv) suggestionDiv.style.display = 'none';
        return;
    }
    
    fetch(`<?= BASE_URL ?>?action=coupon-available&order_amount=${subtotal}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.coupons.length > 0) {
                // Tính discount cho mỗi mã và tìm mã tốt nhất
                const couponPromises = data.coupons.map(coupon => {
                    return fetch('<?= BASE_URL ?>?action=coupon-validate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            coupon_code: coupon.code,
                            order_amount: subtotal
                        })
                    })
                    .then(res => res.json())
                    .then(result => {
                        if (result.success) {
                            return {
                                coupon: coupon,
                                discount_amount: result.discount_amount,
                                final_amount: result.final_amount
                            };
                        }
                        return null;
                    })
                    .catch(() => null);
                });
                
                Promise.all(couponPromises).then(results => {
                    const validCoupons = results.filter(r => r !== null);
                    if (validCoupons.length > 0) {
                        // Sắp xếp theo discount_amount giảm dần (mã giảm nhiều nhất lên đầu)
                        validCoupons.sort((a, b) => b.discount_amount - a.discount_amount);
                        
                        // Lấy mã đầu tiên (giảm nhiều nhất) - Lựa chọn tốt nhất
                        const best = validCoupons[0];
                        
                        bestCouponData = best;
                        displayBestCoupon(best);
                    } else {
                        const suggestionDiv = document.getElementById('bestCouponSuggestionInBox');
                        if (suggestionDiv) suggestionDiv.style.display = 'none';
                    }
                });
            } else {
                const suggestionDiv = document.getElementById('bestCouponSuggestionInBox');
                if (suggestionDiv) suggestionDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error finding best coupon:', error);
            const suggestionDiv = document.getElementById('bestCouponSuggestionInBox');
            if (suggestionDiv) suggestionDiv.style.display = 'none';
        });
}

function displayBestCoupon(best) {
    // Lưu mã code để loại bỏ khỏi danh sách
    bestCouponCode = best.coupon.code;
    
    // Hiển thị trong khung mã giảm giá
    const suggestionDivInBox = document.getElementById('bestCouponSuggestionInBox');
    const codeDivInBox = document.getElementById('bestCouponCodeInBox');
    const discountDivInBox = document.getElementById('bestCouponDiscountInBox');
    const nameDivInBox = document.getElementById('bestCouponNameInBox');
    
    if (suggestionDivInBox && codeDivInBox && discountDivInBox && nameDivInBox) {
        codeDivInBox.textContent = best.coupon.code;
        discountDivInBox.textContent = `Tiết kiệm ${formatCurrency(best.discount_amount)}`;
        nameDivInBox.textContent = best.coupon.name || '';
        suggestionDivInBox.style.display = 'block';
    }
    
    // Tải lại danh sách mã giảm giá để loại bỏ mã đã hiển thị ở trên
    loadAvailableCoupons();
}

function applyBestCoupon() {
    if (!bestCouponData) return;
    
    // Đặt mã vào input và áp dụng
    document.getElementById('couponCodeInput').value = bestCouponData.coupon.code;
    applyCoupon();
}

// Tải danh sách mã giảm giá khi trang load
document.addEventListener('DOMContentLoaded', function() {
    loadAvailableCoupons();
    findBestCoupon();
    
    // Cho phép nhấn Enter để áp dụng mã
    document.getElementById('couponCodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            applyCoupon();
        }
    });
    
    // Kiểm tra mã giảm giá đã áp dụng từ session (nếu có)
    <?php if (isset($_SESSION['applied_coupon'])): ?>
    appliedCouponData = <?= json_encode($_SESSION['applied_coupon'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const subtotal = getSelectedSubtotal();
    if (subtotal > 0) {
        fetch('<?= BASE_URL ?>?action=coupon-validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                coupon_code: appliedCouponData.code,
                order_amount: subtotal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                appliedCouponData.discount_amount = data.discount_amount;
                showAppliedCoupon(data.coupon.code, data.coupon.name, data.discount_amount);
                updateTotalsWithCoupon(data.discount_amount);
                document.getElementById('couponCodeInput').value = data.coupon.code;
                document.getElementById('couponCodeInput').disabled = true;
                document.getElementById('applyCouponBtn').disabled = true;
                document.getElementById('applyCouponBtn').textContent = 'Đã áp dụng';
            } else {
                // Mã không còn hợp lệ, xóa khỏi session
                fetch('<?= BASE_URL ?>?action=coupon-remove', { method: 'POST' });
            }
        });
    }
    <?php endif; ?>
});
</script>
