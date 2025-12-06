<div class="admin-page-header">
    <div class="title-wrap">
        <p class="text-uppercase mb-1 small">Bảng điều khiển</p>
        <h2 class="d-flex align-items-center gap-2 mb-0">
            <i class="bi bi-ticket-perforated"></i>
            <span>Thêm mã giảm giá</span>
        </h2>
    </div>
    <div class="admin-page-actions">
        <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-light-soft">
            <i class="bi bi-arrow-left"></i> Danh sách
        </a>
    </div>
</div>

<form class="card p-3" method="POST" action="<?= BASE_URL ?>?action=admin-coupon-create">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Mã giảm giá <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" required placeholder="WELCOME10">
            <small class="text-muted">Mã áp dụng cho toàn bộ sản phẩm (không giới hạn danh mục/sản phẩm).</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tên mã giảm giá <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="Mã chào mừng 10%">
        </div>
        <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
            <select name="discount_type" class="form-select" required>
                <option value="percentage">Phần trăm (%)</option>
                <option value="fixed">Cố định (VNĐ)</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
            <input type="number" name="discount_value" class="form-control" min="0" step="0.01" required placeholder="10 hoặc 50000">
        </div>
        <div class="col-md-6">
            <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
            <input type="number" name="min_order_amount" class="form-control" min="0" step="1000" value="0">
            <small class="text-muted">Nếu để 0: áp dụng mọi đơn.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giảm tối đa (VNĐ) - chỉ áp dụng %</label>
            <input type="number" name="max_discount_amount" id="maxDiscountAmount" class="form-control" min="0" step="1000" placeholder="Để trống nếu không giới hạn">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
            <input type="datetime-local" name="start_date" class="form-control" required value="<?= date('Y-m-d\TH:i') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
            <input type="datetime-local" name="end_date" class="form-control" required value="<?= date('Y-m-d\TH:i', strtotime('+30 days')) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Giới hạn số lần sử dụng</label>
            <input type="number" name="usage_limit" class="form-control" min="1" placeholder="Để trống nếu không giới hạn">
            <small class="text-muted">Ví dụ: 5 lượt tổng toàn hệ thống.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Giới hạn mỗi khách hàng</label>
            <input type="number" name="per_user_limit" class="form-control" min="1" placeholder="Để trống nếu không giới hạn">
            <small class="text-muted">Ví dụ: 1 = mỗi khách chỉ dùng 1 lần.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Nhóm khách hàng</label>
            <select class="form-select" name="customer_group" id="customerGroup">
                <option value="">Tất cả</option>
                <option value="vip_today">VIP (đơn giao thành công ≥ 2.000.000đ)</option>
            </select>
            <small class="text-muted" id="vipHelp">VIP được xác định khi có đơn giao thành công đạt ngưỡng và tự động gán hạng.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select class="form-select" name="status" required>
                <option value="active">Hoạt động</option>
                <option value="inactive">Ngừng hoạt động</option>
            </select>
        </div>
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="newCustomerOnly" name="new_customer_only">
                        <label class="form-check-label" for="newCustomerOnly">Chỉ khách mới</label>
                        <small class="text-muted d-block">Khách mới = chưa có đơn giao thành công.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="returnOnRefund" name="return_on_refund">
                        <label class="form-check-label" for="returnOnRefund">Hoàn lượt khi hoàn tiền</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-4 gap-2">
        <a href="<?= BASE_URL ?>?action=admin-coupons" class="btn btn-outline-secondary">Hủy</a>
        <button type="submit" class="btn btn-primary">Lưu</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.querySelector('select[name="discount_type"]');
    const maxDiscountInput = document.getElementById('maxDiscountAmount');
    const customerGroupSelect = document.getElementById('customerGroup');
    const vipHelp = document.getElementById('vipHelp');

    function toggleMaxDiscount() {
        if (!discountType || !maxDiscountInput) return;
        if (discountType.value === 'fixed') {
            maxDiscountInput.value = '';
            maxDiscountInput.disabled = true;
            maxDiscountInput.placeholder = 'Không áp dụng cho giảm cố định';
        } else {
            maxDiscountInput.disabled = false;
            maxDiscountInput.placeholder = 'Để trống nếu không giới hạn';
        }
    }

    function toggleVipHelp() {
        if (!customerGroupSelect || !vipHelp) return;
        vipHelp.style.display = customerGroupSelect.value === 'vip_today' ? 'block' : 'none';
    }

    if (discountType) {
        discountType.addEventListener('change', toggleMaxDiscount);
        toggleMaxDiscount();
    }

    if (customerGroupSelect) {
        customerGroupSelect.addEventListener('change', toggleVipHelp);
        toggleVipHelp();
    }
});
</script>

