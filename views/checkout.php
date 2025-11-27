<style>
    .checkout-page {
        padding: 60px 0;
        min-height: 80vh;
        background-color: #f8f9fa;
    }
    
    .checkout-container {
        max-width: 1100px;
        margin: 0 auto;
    }
    
    .checkout-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 30px;
        height: 100%;
    }
    
    .checkout-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        color: #555;
    }
    
    .form-control {
        padding: 12px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .form-control:focus {
        border-color: #000;
        box-shadow: none;
    }
    
    .order-summary-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .order-img {
        width: 60px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 15px;
    }
    
    .order-info {
        flex: 1;
    }
    
    .order-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        display: block;
    }
    
    .order-meta {
        font-size: 0.85rem;
        color: #777;
    }
    
    .order-price {
        font-weight: 700;
    }
    
    .checkout-total-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }
    
    .checkout-final-total {
        border-top: 2px solid #000;
        padding-top: 15px;
        margin-top: 15px;
        font-weight: 800;
        font-size: 1.3rem;
    }
    
    .btn-place-order {
        background: #000;
        color: #fff;
        width: 100%;
        padding: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        margin-top: 20px;
        transition: all 0.3s;
    }
    
    .btn-place-order:hover {
        background: #333;
        transform: translateY(-2px);
    }
    
    .coupon-section {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 20px;
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
    
    #couponCode {
        text-transform: uppercase;
    }
</style>

<script>
let appliedCoupon = null;
let currentDiscount = 0;
let originalTotal = 0;

// Tính tổng tiền ban đầu khi trang load
document.addEventListener('DOMContentLoaded', function() {
    const subtotalElement = document.getElementById('subtotalAmount');
    if (subtotalElement) {
        const subtotalText = subtotalElement.textContent.replace(/[^\d]/g, '');
        originalTotal = parseFloat(subtotalText) || 0;
    }
});

function applyCoupon() {
    const code = document.getElementById('couponCode').value.trim().toUpperCase();
    const messageDiv = document.getElementById('couponMessage');
    const applyBtn = document.getElementById('applyCouponBtn');
    
    if (!code) {
        messageDiv.innerHTML = '<span class="coupon-message error">Vui lòng nhập mã giảm giá</span>';
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
            order_amount: originalTotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            appliedCoupon = data.coupon;
            currentDiscount = data.discount_amount;
            
            // Cập nhật UI
            document.getElementById('appliedCouponId').value = data.coupon.id;
            document.getElementById('appliedCouponCode').value = data.coupon.code;
            document.getElementById('discountAmount').value = currentDiscount;
            
            // Hiển thị thông báo
            messageDiv.innerHTML = `<span class="coupon-message success">✓ ${data.message}</span>`;
            
            // Cập nhật tổng tiền
            updateTotals();
            
            // Disable input và button
            document.getElementById('couponCode').disabled = true;
            applyBtn.textContent = 'Đã áp dụng';
            applyBtn.disabled = true;
            applyBtn.classList.remove('btn-outline-dark');
            applyBtn.classList.add('btn-success');
            
            // Thêm nút xóa mã
            if (!document.getElementById('removeCouponBtn')) {
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-outline-danger mt-2';
                removeBtn.id = 'removeCouponBtn';
                removeBtn.textContent = 'Xóa mã';
                removeBtn.onclick = removeCoupon;
                messageDiv.appendChild(document.createElement('br'));
                messageDiv.appendChild(removeBtn);
            }
        } else {
            messageDiv.innerHTML = `<span class="coupon-message error">${data.message}</span>`;
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

function removeCoupon() {
    appliedCoupon = null;
    currentDiscount = 0;
    
    document.getElementById('appliedCouponId').value = '';
    document.getElementById('appliedCouponCode').value = '';
    document.getElementById('discountAmount').value = '0';
    document.getElementById('couponCode').value = '';
    document.getElementById('couponCode').disabled = false;
    document.getElementById('couponMessage').innerHTML = '';
    
    const applyBtn = document.getElementById('applyCouponBtn');
    applyBtn.disabled = false;
    applyBtn.textContent = 'Áp dụng';
    applyBtn.classList.remove('btn-success');
    applyBtn.classList.add('btn-outline-dark');
    
    const removeBtn = document.getElementById('removeCouponBtn');
    if (removeBtn) {
        removeBtn.remove();
    }
    
    updateTotals();
}

function updateTotals() {
    const finalTotal = originalTotal - currentDiscount;
    
    document.getElementById('subtotalAmount').textContent = formatCurrency(originalTotal);
    
    if (currentDiscount > 0) {
        document.getElementById('discountRow').style.display = 'flex';
        document.getElementById('discountAmountDisplay').textContent = '-' + formatCurrency(currentDiscount);
    } else {
        document.getElementById('discountRow').style.display = 'none';
    }
    
    document.getElementById('finalTotalAmount').textContent = formatCurrency(finalTotal);
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
}

// Cho phép nhấn Enter để áp dụng mã
document.getElementById('couponCode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        applyCoupon();
    }
});
</script>

<div class="checkout-page">
    <div class="container checkout-container">
        <form action="<?= BASE_URL ?>?action=checkout-process" method="POST">
            <div class="row g-4">
                <!-- Thông tin giao hàng -->
                <div class="col-lg-7">
                    <div class="checkout-card">
                        <h2 class="checkout-title">Thông Tin Giao Hàng</h2>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" name="fullname" required placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" name="phone" required placeholder="0912345678">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required placeholder="email@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Địa chỉ nhận hàng</label>
                                <input type="text" class="form-control" name="address" required placeholder="Số nhà, tên đường, phường/xã...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh / Thành</label>
                                <select class="form-select form-control" name="city">
                                    <option selected>Hà Nội</option>
                                    <option>TP. Hồ Chí Minh</option>
                                    <option>Đà Nẵng</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quận / Huyện</label>
                                <select class="form-select form-control" name="district">
                                    <option selected>Quận 1</option>
                                    <option>Quận 2</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phường / Xã</label>
                                <select class="form-select form-control" name="ward">
                                    <option selected>Phường Bến Nghé</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                                <textarea class="form-control" name="note" rows="3" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tóm tắt đơn hàng -->
                <div class="col-lg-5">
                    <div class="checkout-card">
                        <h2 class="checkout-title">Đơn Hàng Của Bạn</h2>
                        
                        <div class="order-items mb-4">
                            <?php foreach ($cart as $item): ?>
                                <?php 
                                $imgSrc = $item['image'];
                                if (strpos($imgSrc, 'assets/') === 0) {
                                    if (file_exists(PATH_ROOT . $imgSrc)) {
                                        $data = file_get_contents(PATH_ROOT . $imgSrc);
                                        $type = pathinfo(PATH_ROOT . $imgSrc, PATHINFO_EXTENSION);
                                        $imgSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    } else {
                                        $imgSrc = BASE_URL . $imgSrc;
                                    }
                                }
                                ?>
                                <div class="order-summary-item">
                                    <img src="<?= $imgSrc ?>" alt="<?= $item['name'] ?>" class="order-img">
                                    <div class="order-info">
                                        <span class="order-name"><?= $item['name'] ?></span>
                                        <div class="order-meta">
                                            Size: <?= $item['size'] ?? 'M' ?> | Màu: <?= $item['color'] ?? 'Black' ?> <br>
                                            SL: <?= $item['quantity'] ?>
                                        </div>
                                    </div>
                                    <div class="order-price">
                                        <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> đ
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Thông tin mã giảm giá đã áp dụng -->
                        <?php 
                        $appliedCoupon = $_SESSION['applied_coupon'] ?? null;
                        $discountAmount = 0;
                        $finalTotal = $total;
                        
                        if ($appliedCoupon) {
                            $discountAmount = $appliedCoupon['discount_amount'] ?? 0;
                            $finalTotal = $total - $discountAmount;
                        }
                        ?>
                        
                        <?php if ($appliedCoupon): ?>
                        <div class="coupon-section mb-4" style="background: #e8f5e9; border: 1px solid #4caf50;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="form-label mb-1" style="color: #2e7d32; font-weight: 600;">
                                        <i class="bi bi-check-circle-fill text-success"></i> Mã giảm giá đã áp dụng
                                    </label>
                                    <div>
                                        <strong style="color: #000;"><?= htmlspecialchars($appliedCoupon['code']) ?></strong>
                                        <?php if (!empty($appliedCoupon['name'])): ?>
                                            <small class="d-block text-muted"><?= htmlspecialchars($appliedCoupon['name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div style="color: #28a745; font-weight: 700; font-size: 1.1rem;">
                                        -<?= number_format($discountAmount, 0, ',', '.') ?> đ
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="coupon_id" value="<?= $appliedCoupon['id'] ?? '' ?>">
                            <input type="hidden" name="applied_coupon_code" value="<?= htmlspecialchars($appliedCoupon['code']) ?>">
                            <input type="hidden" name="discount_amount" value="<?= $discountAmount ?>">
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="coupon_id" value="">
                            <input type="hidden" name="applied_coupon_code" value="">
                            <input type="hidden" name="discount_amount" value="0">
                        <?php endif; ?>
                        
                        <div class="checkout-summary">
                            <div class="checkout-total-row">
                                <span>Tạm tính</span>
                                <span id="subtotalAmount"><?= number_format($total, 0, ',', '.') ?> đ</span>
                            </div>
                            <?php if ($appliedCoupon && $discountAmount > 0): ?>
                            <div class="checkout-total-row">
                                <span>Giảm giá</span>
                                <span id="discountAmountDisplay" style="color: #28a745; font-weight: 600;">
                                    -<?= number_format($discountAmount, 0, ',', '.') ?> đ
                                </span>
                            </div>
                            <?php endif; ?>
                            <div class="checkout-total-row">
                                <span>Phí vận chuyển</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="checkout-total-row checkout-final-total">
                                <span>Tổng cộng</span>
                                <span id="finalTotalAmount"><?= number_format($finalTotal, 0, ',', '.') ?> đ</span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                <label class="form-check-label fw-semibold" for="cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="banking" value="banking">
                                <label class="form-check-label fw-semibold" for="banking">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-place-order">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
