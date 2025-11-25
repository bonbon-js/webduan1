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
</style>

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
                        
                        <div class="checkout-summary">
                            <div class="checkout-total-row">
                                <span>Tạm tính</span>
                                <span><?= number_format($total, 0, ',', '.') ?> đ</span>
                            </div>
                            <div class="checkout-total-row">
                                <span>Phí vận chuyển</span>
                                <span>Miễn phí</span>
                            </div>
                            <div class="checkout-total-row checkout-final-total">
                                <span>Tổng cộng</span>
                                <span><?= number_format($total, 0, ',', '.') ?> đ</span>
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
